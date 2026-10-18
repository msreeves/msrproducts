<?php
/**
 * Product media resolve + icon fallbacks (image or icon, not bare placeholder).
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load workspace image lib when available (shared with WP-CLI seeds).
 */
function msrproducts_load_product_image_lib(): bool {
	if ( function_exists( 'msrproducts_resolve_best_thumbnail_attachment' ) ) {
		return true;
	}
	$lib = dirname( ABSPATH, 3 ) . '/scripts/seeds/products/msrproducts-product-image-lib.php';
	if ( ! is_readable( $lib ) ) {
		return false;
	}
	require_once $lib;
	return function_exists( 'msrproducts_resolve_best_thumbnail_attachment' );
}

/**
 * @return string Category icon slug for product post.
 */
function msrproducts_get_product_category_icon_slug( int $post_id ): string {
	$terms = get_the_terms( $post_id, 'product_cat' );
	if ( ! is_array( $terms ) || $terms === array() ) {
		return 'product';
	}
	$cfg  = function_exists( 'msrproducts_image_keywords_config' ) ? msrproducts_image_keywords_config() : array();
	$map  = isset( $cfg['categoryIcons'] ) && is_array( $cfg['categoryIcons'] ) ? $cfg['categoryIcons'] : array();
	$term = $terms[0];
	if ( $term instanceof WP_Term && isset( $map[ $term->slug ] ) && is_string( $map[ $term->slug ] ) ) {
		return $map[ $term->slug ];
	}
	return 'product';
}

/**
 * @return string Theme URI for category / type icon SVG.
 */
function msrproducts_category_icon_url( string $slug ): string {
	$slug = sanitize_title( $slug );
	if ( $slug === '' ) {
		$slug = 'product';
	}
	$path = get_template_directory() . '/assets/icons/category-' . $slug . '.svg';
	if ( is_readable( $path ) ) {
		return get_template_directory_uri() . '/assets/icons/category-' . $slug . '.svg';
	}
	return get_template_directory_uri() . '/assets/icons/category-product.svg';
}

/**
 * Resolve a usable attachment id for a product (library keyword + SKU scoring).
 *
 * @return int Attachment ID or 0.
 */
function msrproducts_resolve_product_thumbnail_id( int $post_id ): int {
	$thumb = (int) get_post_meta( $post_id, "_thumbnail_id", true );
	$sku   = (string) get_post_meta( $post_id, '_sku', true );
	if ( $sku === '' || ! msrproducts_load_product_image_lib() ) {
		return $thumb;
	}
	$min    = defined( 'MSR_PRODUCT_IMAGE_MIN_BYTES' ) ? MSR_PRODUCT_IMAGE_MIN_BYTES : 15000;
	$thumb_sz = $thumb ? msrproducts_attachment_filesize( $thumb ) : 0;
	$resolved = msrproducts_resolve_best_thumbnail_attachment( $post_id, $sku );
	if ( $resolved <= 0 ) {
		return $thumb_sz >= $min ? $thumb : 0;
	}
	if ( $thumb_sz < $min ) {
		return $resolved;
	}
	$cur_score = msrproducts_score_attachment_for_sku( $thumb, $sku, $post_id );
	$new_score = msrproducts_score_attachment_for_sku( $resolved, $sku, $post_id );
	return $new_score > $cur_score ? $resolved : $thumb;
}

/**
 * @param int $post_id Post ID.
 */
function msrproducts_render_post_media( int $post_id ): void {
	$post_id   = absint( $post_id );
	$post_type = get_post_type( $post_id );
	$thumb_id  = (int) get_post_meta( $post_id, "_thumbnail_id", true );

	if ( $post_type === 'product' ) {
		$thumb_id = msrproducts_resolve_product_thumbnail_id( $post_id );
	}

	$has_file = $thumb_id > 0
		&& function_exists( 'msrproducts_attachment_file_exists' )
		&& msrproducts_attachment_file_exists( $thumb_id );

	if ( ! post_password_required( $post_id ) && $has_file ) {
		echo wp_get_attachment_image( $thumb_id, 'woocommerce_thumbnail', false, array( 'class' => 'wp-post-image' ) );
		return;
	}

	$icon_slug = 'product';
	$label     = get_the_title( $post_id );
	$classes   = 'msr-media-icon';

	if ( $post_type === 'partner' ) {
		$classes .= ' msr-media-icon--partner';
		$initials = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $label ), 0, 2 ) );
		if ( $initials === '' ) {
			$initials = 'PR';
		}
		echo '<div class="' . esc_attr( $classes ) . '" role="img" aria-label="' . esc_attr( $label ) . '">';
		echo '<span class="msr-media-icon__initials" aria-hidden="true">' . esc_html( $initials ) . '</span>';
		echo '</div>';
		return;
	}

	if ( $post_type === 'publication' ) {
		$icon_slug = 'publication';
	} elseif ( $post_type === 'product' ) {
		$icon_slug = msrproducts_get_product_category_icon_slug( $post_id );
	}

	$icon_url = msrproducts_category_icon_url( $icon_slug );
	echo '<img class="' . esc_attr( $classes ) . '" src="' . esc_url( $icon_url ) . '" alt="" role="presentation" loading="lazy" decoding="async" />';
}

add_filter(
	'post_thumbnail_id',
	static function ( $thumbnail_id, $post ) {
		if ( ! $post instanceof WP_Post || $post->post_type !== 'product' ) {
			return $thumbnail_id;
		}
		$resolved = msrproducts_resolve_product_thumbnail_id( (int) $post->ID );
		return $resolved > 0 ? $resolved : $thumbnail_id;
	},
	20,
	2
);

/**
 * Icon slug for a product_cat term.
 */
function msrproducts_category_icon_slug_for_term( string $slug ): string {
	$slug = sanitize_title( $slug );
	if ( function_exists( 'msrproducts_image_keywords_config' ) ) {
		$cfg = msrproducts_image_keywords_config();
		$map = isset( $cfg['categoryIcons'] ) && is_array( $cfg['categoryIcons'] ) ? $cfg['categoryIcons'] : array();
		if ( isset( $map[ $slug ] ) && is_string( $map[ $slug ] ) ) {
			return $map[ $slug ];
		}
	}
	return 'product';
}

/**
 * First product in a category with a real on-disk thumbnail.
 *
 * @return int Attachment ID or 0.
 */
function msrproducts_best_product_thumbnail_for_term( int $term_id ): int {
	$sample = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => array( $term_id ),
				),
			),
		)
	);
	foreach ( $sample as $pid ) {
		$pid = (int) $pid;
		$tid = function_exists( 'msrproducts_resolve_product_thumbnail_id' )
			? msrproducts_resolve_product_thumbnail_id( $pid )
			: (int) get_post_meta( $pid, '_thumbnail_id', true );
		if ( $tid > 0 && msrproducts_attachment_file_exists( $tid ) ) {
			return $tid;
		}
	}
	return 0;
}

/**
 * Best image URL for a category tile: term thumb, sample product, then icon.
 */
function msrproducts_term_tile_image_url( WP_Term $term ): string {
	$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb_id > 0 && function_exists( 'msrproducts_attachment_file_exists' ) && msrproducts_attachment_file_exists( $thumb_id ) ) {
		$url = wp_get_attachment_image_url( $thumb_id, 'medium_large' );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}
	$tid = msrproducts_best_product_thumbnail_for_term( (int) $term->term_id );
	if ( $tid > 0 ) {
		$url = wp_get_attachment_image_url( $tid, 'medium_large' );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}
	return msrproducts_category_icon_url( msrproducts_category_icon_slug_for_term( $term->slug ) );
}

/**
 * Editorial / marketing card image: ACF image array, else icon.
 *
 * @param mixed  $acf_image ACF image field.
 * @param string $icon_slug Icon basename slug.
 */
function msrproducts_marketing_card_image_url( $acf_image, string $icon_slug = 'publication' ): string {
	if ( is_array( $acf_image ) && ! empty( $acf_image['url'] ) && is_string( $acf_image['url'] ) ) {
		return $acf_image['url'];
	}
	return msrproducts_category_icon_url( $icon_slug );
}

add_filter(
	'woocommerce_product_get_image_id',
	static function ( $image_id, $product ) {
		if ( ! $product instanceof WC_Product || $product->get_type() === 'variation' ) {
			return $image_id;
		}
		$resolved = msrproducts_resolve_product_thumbnail_id( (int) $product->get_id() );
		return $resolved > 0 ? $resolved : $image_id;
	},
	20,
	2
);

add_filter(
	'woocommerce_product_get_gallery_image_ids',
	static function ( $ids, $product ) {
		if ( ! $product instanceof WC_Product || ! msrproducts_load_product_image_lib() ) {
			return $ids;
		}
		$product_id = (int) $product->get_id();
		$sku        = (string) $product->get_sku();
		if ( $sku === '' ) {
			return $ids;
		}
		if ( function_exists( 'msrproducts_sku_uses_demo_image' ) && msrproducts_sku_uses_demo_image( $product_id, $sku ) ) {
			return array();
		}
		$thumb = (int) $product->get_image_id();
		if ( $thumb > 0 ) {
			$path = get_attached_file( $thumb );
			$base = is_string( $path ) ? strtolower( basename( $path ) ) : '';
			if ( $base !== '' && strpos( $base, 'msr-demo-' ) === 0 ) {
				return array();
			}
		}
		$ordered = msrproducts_gallery_attachment_ids_for_product( $product_id, $sku );
		if ( count( $ordered ) <= 1 ) {
			return $ids;
		}
		return array_slice( $ordered, 1 );
	},
	20,
	2
);

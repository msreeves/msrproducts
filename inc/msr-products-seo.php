<?php
/**
 * Products SEO — meta description fallbacks.
 *
 * @package msrproducts
 */

/**
 * Normalise and trim a meta description string.
 *
 * @param string $description Raw description.
 * @return string
 */
function msrproducts_seo_normalize_description( $description ) {
	$description = wp_strip_all_tags( (string) $description );
	$description = strip_shortcodes( $description );
	$description = preg_replace( '/\s+/', ' ', $description );
	$description = trim( (string) $description );

	if ( '' === $description ) {
		return '';
	}

	return mb_substr( $description, 0, 300, 'UTF-8' );
}

/**
 * @return void
 */
function msrproducts_render_meta_description() {
	if ( is_admin() ) {
		return;
	}

	if ( is_singular() ) {
		global $post;
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$description = msrproducts_seo_normalize_description( $post->post_excerpt );
		if ( '' === $description ) {
			$description = msrproducts_seo_normalize_description( $post->post_content );
		}
		if ( '' !== $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		return;
	}

	if ( is_front_page() || is_home() ) {
		$description = msrproducts_seo_normalize_description( (string) get_bloginfo( 'description' ) );
		if ( '' === $description ) {
			$description = msrproducts_seo_normalize_description( msrproducts_get_seo_home_description() );
		}
		if ( '' !== $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		return;
	}

	if ( function_exists( 'is_shop' ) && ( is_shop() || is_post_type_archive( 'product' ) ) ) {
		$description = msrproducts_seo_normalize_description( msrproducts_get_seo_shop_description() );
		if ( '' !== $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		return;
	}

	if ( is_search() ) {
		$description = msrproducts_seo_normalize_description( msrproducts_get_seo_search_description() );
		if ( '' !== $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
	}
}
add_action( 'wp_head', 'msrproducts_render_meta_description', 1 );

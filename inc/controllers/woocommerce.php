<?php
/**
 * WooCommerce theme integration.
 *
 * Keeps WooCommerce structure/layout decisions in PHP hooks while SCSS handles presentation.
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'msrproducts_woocommerce_support' ) ) {
	function msrproducts_woocommerce_support() {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 480,
				'single_image_width'    => 900,
				'product_grid'          => array(
					'default_rows'    => 3,
					'min_rows'        => 1,
					'default_columns' => 4,
					'min_columns'     => 2,
					'max_columns'     => 4,
				),
			)
		);

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'msrproducts_woocommerce_support' );

if ( ! function_exists( 'msrproducts_woo_wrapper_start' ) ) {
	function msrproducts_woo_wrapper_start() {
		echo '<main id="primary" class="site-main woocommerce-main">';
		echo '<div class="container msr-woo-container">';
	}
}

if ( ! function_exists( 'msrproducts_woo_wrapper_end' ) ) {
	function msrproducts_woo_wrapper_end() {
		echo '</div>';
		echo '</main>';
	}
}

if ( ! function_exists( 'msrproducts_woo_setup_wrappers' ) ) {
	function msrproducts_woo_setup_wrappers() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		add_action( 'woocommerce_before_main_content', 'msrproducts_woo_wrapper_start', 10 );
		add_action( 'woocommerce_after_main_content', 'msrproducts_woo_wrapper_end', 10 );
	}
}
add_action( 'init', 'msrproducts_woo_setup_wrappers' );

if ( ! function_exists( 'msrproducts_woocommerce_columns' ) ) {
	function msrproducts_woocommerce_columns() {
		return 4;
	}
}
add_filter( 'loop_shop_columns', 'msrproducts_woocommerce_columns' );

if ( ! function_exists( 'msrproducts_woocommerce_products_per_page' ) ) {
	function msrproducts_woocommerce_products_per_page() {
		return 12;
	}
}
add_filter( 'loop_shop_per_page', 'msrproducts_woocommerce_products_per_page', 20 );

if ( ! function_exists( 'msrproducts_related_products_args' ) ) {
	function msrproducts_related_products_args( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
}
add_filter( 'woocommerce_output_related_products_args', 'msrproducts_related_products_args' );

if ( ! function_exists( 'msrproducts_sku_family_prefix' ) ) {
	/**
	 * Shared SKU family for related products (e.g. 24-WB01 → 24-WB, MH02 → MH).
	 *
	 * @param string $sku Product SKU.
	 */
	function msrproducts_sku_family_prefix( $sku ) {
		$sku = strtoupper( trim( (string) $sku ) );
		if ( $sku === '' ) {
			return '';
		}
		if ( preg_match( '/^(24-[A-Z]{2})/', $sku, $matches ) ) {
			return $matches[1];
		}
		if ( preg_match( '/^([A-Z]{2})\d/', $sku, $matches ) ) {
			return $matches[1];
		}
		return '';
	}
}

if ( ! function_exists( 'msrproducts_related_products_by_sku_family' ) ) {
	/**
	 * Prefer same SKU family over broad shared categories (e.g. Accessories).
	 *
	 * @param array<int> $related_ids Related product IDs.
	 * @param int        $product_id  Current product ID.
	 * @param array      $args        Query args.
	 * @return array<int>
	 */
	function msrproducts_related_products_by_sku_family( $related_ids, $product_id, $args ) {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return $related_ids;
		}

		$limit = isset( $args['limit'] ) ? max( 1, (int) $args['limit'] ) : 4;
		$sku   = (string) get_post_meta( (int) $product_id, '_sku', true );
		$prefix = msrproducts_sku_family_prefix( $sku );
		if ( $prefix === '' ) {
			return $related_ids;
		}

		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'post__not_in'   => array( (int) $product_id ),
				'fields'         => 'ids',
				'orderby'        => 'rand',
				'meta_query'     => array(
					array(
						'key'     => '_sku',
						'value'   => $prefix,
						'compare' => 'LIKE',
					),
				),
			)
		);

		$family_ids = is_array( $query->posts ) ? array_map( 'intval', $query->posts ) : array();
		wp_reset_postdata();

		if ( $family_ids === array() ) {
			return $related_ids;
		}
		if ( count( $family_ids ) >= $limit ) {
			return array_slice( $family_ids, 0, $limit );
		}

		foreach ( $related_ids as $rid ) {
			$rid = (int) $rid;
			if ( $rid === (int) $product_id || in_array( $rid, $family_ids, true ) ) {
				continue;
			}
			$family_ids[] = $rid;
			if ( count( $family_ids ) >= $limit ) {
				break;
			}
		}

		return $family_ids;
	}
}
add_filter( 'woocommerce_related_products', 'msrproducts_related_products_by_sku_family', 20, 3 );

if ( ! function_exists( 'msrproducts_woo_single_product_layout_hooks' ) ) {
	function msrproducts_woo_single_product_layout_hooks() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Explicitly keep this order: tabs, upsells, related.
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

		add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		add_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
}
add_action( 'init', 'msrproducts_woo_single_product_layout_hooks' );

if ( ! function_exists( 'msrproducts_dequeue_woocommerce_block_styles' ) ) {
	function msrproducts_dequeue_woocommerce_block_styles() {
		// Keep visual control in theme SCSS to avoid mixed layout systems.
		wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'wc-blocks-style' );
		wp_dequeue_style( 'woocommerce-inline' );
		wp_deregister_style( 'wc-block-style' );
		wp_deregister_style( 'wc-blocks-style' );
	}
}
add_action( 'wp_enqueue_scripts', 'msrproducts_dequeue_woocommerce_block_styles', 99 );

if ( ! function_exists( 'msrproducts_dequeue_woocommerce_block_styles_print' ) ) {
	function msrproducts_dequeue_woocommerce_block_styles_print() {
		// Secondary safety pass in case plugins enqueue late.
		wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'wc-blocks-style' );
	}
}
add_action( 'wp_print_styles', 'msrproducts_dequeue_woocommerce_block_styles_print', 99 );

if ( ! function_exists( 'msrproducts_simplify_stock_text' ) ) {
	function msrproducts_simplify_stock_text( $availability, $product ) {
		if ( ! is_object( $product ) || ! method_exists( $product, 'is_in_stock' ) ) {
			return $availability;
		}

		$availability['availability'] = $product->is_in_stock() ? __( 'In stock', 'msrproducts' ) : __( 'Out of stock', 'msrproducts' );
		return $availability;
	}
}
add_filter( 'woocommerce_get_availability', 'msrproducts_simplify_stock_text', 10, 2 );

if ( ! function_exists( 'msrproducts_limit_quantity_to_five' ) ) {
	function msrproducts_limit_quantity_to_five( $args, $product ) {
		$max_limit = 5;
		if ( ! is_object( $product ) || ! method_exists( $product, 'get_stock_quantity' ) ) {
			$args['max_value'] = $max_limit;
			return $args;
		}

		$stock_qty = $product->get_stock_quantity();
		if ( is_numeric( $stock_qty ) ) {
			$args['max_value'] = max( 1, min( (int) $stock_qty, $max_limit ) );
		} else {
			// For products without managed stock, cap at site rule.
			$args['max_value'] = $max_limit;
		}

		return $args;
	}
}
add_filter( 'woocommerce_quantity_input_args', 'msrproducts_limit_quantity_to_five', 10, 2 );

if ( ! function_exists( 'msrproducts_single_product_gallery_visible' ) ) {
	/**
	 * Woo sets inline opacity:0 until Flexslider runs; theme disables default WC CSS so show the hero immediately.
	 */
	function msrproducts_single_product_gallery_visible() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}
		$js = "(function(){var g=document.querySelector('.woocommerce-product-gallery');if(g){g.style.opacity='1';}})();";
		if ( wp_script_is( 'wc-single-product', 'enqueued' ) ) {
			wp_add_inline_script( 'wc-single-product', $js, 'after' );
		} else {
			wp_register_script( 'msrproducts-gallery-visible', '', array(), '1.0', true );
			wp_enqueue_script( 'msrproducts-gallery-visible' );
			wp_add_inline_script( 'msrproducts-gallery-visible', $js );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'msrproducts_single_product_gallery_visible', 100 );


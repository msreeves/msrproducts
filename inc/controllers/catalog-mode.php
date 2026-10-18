<?php
/**
 * Portfolio-first catalog mode controls.
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'msrproducts_get_inquiry_url' ) ) {
	function msrproducts_get_inquiry_url( $product_id = 0 ) {
		$base_url = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'contact', home_url( '/' ) ) : home_url( '/' );
		$product  = $product_id ? get_the_title( $product_id ) : '';

		if ( ! $product ) {
			return $base_url;
		}

		return add_query_arg(
			array(
				'project' => rawurlencode( sanitize_text_field( $product ) ),
			),
			$base_url
		);
	}
}

if ( ! function_exists( 'msrproducts_get_portfolio_only_url' ) ) {
	function msrproducts_get_portfolio_only_url() {
		$page = get_page_by_path( 'portfolio-only' );
		if ( $page instanceof WP_Post ) {
			$url = get_permalink( (int) $page->ID );
			if ( is_string( $url ) && $url !== '' ) {
				return $url;
			}
		}
		return function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'returns-refunds', home_url( '/' ) ) : home_url( '/' );
	}
}

if ( ! function_exists( 'msrproducts_catalog_loop_inquiry_cta' ) ) {
	function msrproducts_catalog_loop_inquiry_cta() {
		global $product;

		if ( ! is_object( $product ) || ! method_exists( $product, 'get_id' ) ) {
			return;
		}
		$url = msrproducts_get_inquiry_url( (int) $product->get_id() );
		$product_id = (int) $product->get_id();
		echo '<div class="msr-loop-card-actions">';
		echo '<a class="button msr-inquiry-btn" href="' . esc_url( $url ) . '">' . esc_html__( 'Enquire', 'msrproducts' ) . '</a>';
		get_template_part( 'template-parts/buttons/compare-toggle', null, array( 'product_id' => $product_id ) );
		echo '</div>';
	}
}

if ( ! function_exists( 'msrproducts_catalog_single_inquiry_cta' ) ) {
	function msrproducts_catalog_single_inquiry_cta() {
		global $product;

		$product_id = is_object( $product ) && method_exists( $product, 'get_id' ) ? (int) $product->get_id() : get_the_ID();
		$url        = msrproducts_get_inquiry_url( $product_id );

		echo '<p class="msr-inquiry-wrap"><a class="button msr-inquiry-btn" href="' . esc_url( $url ) . '">' . esc_html__( 'Enquire about this product', 'msrproducts' ) . '</a></p>';
	}
}

if ( ! function_exists( 'msrproducts_catalog_register_demo_store_hooks' ) ) {
	/**
	 * Enquiry-led demo: hide cart/checkout UI, surface contact CTAs.
	 */
	function msrproducts_catalog_register_demo_store_hooks() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

		add_action( 'woocommerce_after_shop_loop_item', 'msrproducts_catalog_loop_inquiry_cta', 12 );
		add_action( 'woocommerce_single_product_summary', 'msrproducts_catalog_single_inquiry_cta', 32 );
	}
}
add_action( 'init', 'msrproducts_catalog_register_demo_store_hooks', 40 );

if ( ! function_exists( 'msrproducts_catalog_loop_inquiry_label' ) ) {
	function msrproducts_catalog_loop_inquiry_label() {
		return __( 'Enquire', 'msrproducts' );
	}
}

if ( ! function_exists( 'msrproducts_catalog_demo_store_notice' ) ) {
	function msrproducts_catalog_demo_store_notice() {
		if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() || is_product() ) ) {
			return;
		}
		echo '<p class="msr-demo-store-notice" role="status">';
		echo esc_html__( 'Demonstration store — browse products and send an enquiry. Checkout and payment are not enabled.', 'msrproducts' );
		echo '</p>';
	}
}
add_action( 'woocommerce_before_main_content', 'msrproducts_catalog_demo_store_notice', 6 );

if ( ! function_exists( 'msrproducts_catalog_hide_product_reviews' ) ) {
	function msrproducts_catalog_hide_product_reviews( $tabs ) {
		if ( is_array( $tabs ) && isset( $tabs['reviews'] ) ) {
			unset( $tabs['reviews'] );
		}
		return $tabs;
	}
}
add_filter( 'woocommerce_product_tabs', 'msrproducts_catalog_hide_product_reviews', 99 );

if ( ! function_exists( 'msrproducts_catalog_redirect_purchase_routes' ) ) {
	function msrproducts_catalog_redirect_purchase_routes() {
		if ( is_admin() ) {
			return;
		}

		$portfolio_url = function_exists( 'msrproducts_get_portfolio_only_url' ) ? msrproducts_get_portfolio_only_url() : home_url( '/' );

		$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		if ( ! is_string( $request_path ) ) {
			return;
		}

		$normalized = untrailingslashit( $request_path );
		$blocked    = array(
			untrailingslashit( '/sites/wp/main/cart' ),
			untrailingslashit( '/sites/wp/main/basket' ),
			untrailingslashit( '/sites/wp/main/checkout' ),
			untrailingslashit( '/sites/wp/main/my-account' ),
		);
		$has_add_to_cart = isset( $_GET['add-to-cart'] ) && absint( wp_unslash( $_GET['add-to-cart'] ) ) > 0;

		if ( in_array( $normalized, $blocked, true ) || ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || $has_add_to_cart ) {
			wp_safe_redirect( esc_url_raw( $portfolio_url ), 302 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'msrproducts_catalog_redirect_purchase_routes', 1 );

if ( ! function_exists( 'msrproducts_catalog_loop_add_to_cart_text' ) ) {
	function msrproducts_catalog_loop_add_to_cart_text( $text ) {
		return __( 'View', 'msrproducts' );
	}
}
add_filter( 'woocommerce_product_add_to_cart_text', 'msrproducts_catalog_loop_add_to_cart_text', 20 );

if ( ! function_exists( 'msrproducts_catalog_single_add_to_cart_text' ) ) {
	function msrproducts_catalog_single_add_to_cart_text( $text ) {
		return __( 'Enquire about this product', 'msrproducts' );
	}
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'msrproducts_catalog_single_add_to_cart_text', 20 );

if ( ! function_exists( 'msrproducts_catalog_is_purchasable' ) ) {
	function msrproducts_catalog_is_purchasable( $purchasable, $product ) {
		return false;
	}
}
add_filter( 'woocommerce_is_purchasable', 'msrproducts_catalog_is_purchasable', 20, 2 );

if ( ! function_exists( 'msrproducts_related_heading' ) ) {
	function msrproducts_related_heading() {
		return __( 'Related products', 'msrproducts' );
	}
}
add_filter( 'woocommerce_product_related_products_heading', 'msrproducts_related_heading' );

if ( ! function_exists( 'msrproducts_comments_product_only' ) ) {
	function msrproducts_comments_product_only( $open, $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type === 'product' ) {
			return true;
		}
		return false;
	}
}
add_filter( 'comments_open', 'msrproducts_comments_product_only', 20, 2 );

if ( ! function_exists( 'msrproducts_product_schema' ) ) {
	function msrproducts_product_schema() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		global $product;
		if ( ! is_object( $product ) || ! method_exists( $product, 'get_name' ) ) {
			return;
		}

		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Product',
			'name'        => $product->get_name(),
			'description' => wp_strip_all_tags( get_the_excerpt() ),
			'url'         => get_permalink(),
			'image'       => wp_get_attachment_image_url( $product->get_image_id(), 'large' ),
			'brand'       => array(
				'@type' => 'Brand',
				'name'  => get_bloginfo( 'name' ),
			),
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
	}
}
add_action( 'wp_head', 'msrproducts_product_schema', 40 );

<?php
/**
 * Shop-first footer menu fallbacks when WP menus are unset or still portfolio-labelled.
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return string
 */
function msrproducts_shop_url(): string {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'shop' );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}
	return home_url( '/?post_type=product' );
}

/**
 * @param array<string, mixed> $args wp_nav_menu args.
 */
function msrproducts_footer_explore_fallback( $args ) {
	$shop = msrproducts_shop_url();
	$faq  = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'faq', home_url( '/' ) ) : home_url( '/' );
	$compare = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'compare-projects', home_url( '/' ) ) : home_url( '/' );
	$links   = array(
		array( __( 'Shop all products', 'msrproducts' ), $shop ),
		array( __( 'Compare products', 'msrproducts' ), $compare ),
		array( __( 'FAQ', 'msrproducts' ), $faq ),
	);
	echo '<ul class="' . esc_attr( $args['menu_class'] ?? 'menu' ) . '">';
	foreach ( $links as $link ) {
		echo '<li><a href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * @param array<string, mixed> $args wp_nav_menu args.
 */
function msrproducts_footer_services_fallback( $args ) {
	$contact = function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'contact', home_url( '/' ) ) : home_url( '/' );
	$links   = array(
		array( __( 'Contact & enquiries', 'msrproducts' ), $contact ),
		array( __( 'Search catalogue', 'msrproducts' ), function_exists( 'msrproducts_get_page_url_by_path' ) ? msrproducts_get_page_url_by_path( 'search', home_url( '/' ) ) : home_url( '/' ) ),
	);
	echo '<ul class="' . esc_attr( $args['menu_class'] ?? 'menu' ) . '">';
	foreach ( $links as $link ) {
		echo '<li><a href="' . esc_url( $link[1] ) . '">' . esc_html( $link[0] ) . '</a></li>';
	}
	echo '</ul>';
}

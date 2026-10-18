<?php
/**
 * Programme body class and theme supports.
 *
 * @package msrproducts
 */

/**
 * Programme body class for scoped CSS.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function msrproducts_body_classes( $classes ) {
	$classes[] = 'msr-products';
	return $classes;
}
add_filter( 'body_class', 'msrproducts_body_classes' );

/**
 * Decode HTML entities stored in WP/Woo terms, menus, and legacy ACF (then escape once).
 *
 * @param mixed $text Raw string from DB or Woo.
 * @return string
 */
function msrproducts_plain_text( $text ) {
	if ( ! is_string( $text ) || $text === '' ) {
		return '';
	}
	return html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/**
 * @param mixed $text Raw label.
 * @return string Escaped HTML.
 */
function msrproducts_esc_html_text( $text ) {
	return esc_html( msrproducts_plain_text( $text ) );
}

/**
 * @param mixed $text Raw label.
 * @return string Escaped attribute.
 */
function msrproducts_esc_attr_text( $text ) {
	return esc_attr( msrproducts_plain_text( $text ) );
}

/**
 * Nav + taxonomy menus often store "&amp;" literally in titles.
 *
 * @param string   $title Menu item title.
 * @param WP_Post  $item  Menu item.
 * @return string
 */
function msrproducts_decode_nav_menu_title( $title, $item ) {
	unset( $item );
	return msrproducts_plain_text( $title );
}
add_filter( 'nav_menu_item_title', 'msrproducts_decode_nav_menu_title', 20, 2 );

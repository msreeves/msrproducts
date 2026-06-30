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

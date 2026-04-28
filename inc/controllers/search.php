<?php
/**
 * Safe search highlighting helpers.
 *
 * @package msrproducts
 */

if ( ! function_exists( 'msrproducts_search_pattern' ) ) {
	function msrproducts_search_pattern() {
		$query = trim( (string) get_search_query() );
		if ( $query === '' ) {
			return '';
		}
		$parts = preg_split( '/\s+/', $query );
		$parts = array_filter( array_map( 'preg_quote', $parts ) );
		return empty( $parts ) ? '' : '/(' . implode( '|', $parts ) . ')/iu';
	}
}

function search_excerpt_highlight() {
	$excerpt = get_the_excerpt();
	echo '<p>' . wp_kses_post( $excerpt ) . '</p>';
}

function search_title_highlight() {
	$title = get_the_title();
	echo wp_kses_post( $title );
}

function search_content_highlight() {
	$content = get_the_content();
	echo '<p>' . wp_kses_post( wp_trim_words( wp_strip_all_tags( $content ), 45, '...' ) ) . '</p>';
}

function remove_pages_from_search() {
	global $wp_post_types;
	if ( isset( $wp_post_types['page'] ) ) {
		$wp_post_types['page']->exclude_from_search = true;
	}
}
add_action( 'init', 'remove_pages_from_search' );
?>
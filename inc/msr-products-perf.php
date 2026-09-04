<?php
/**
 * Front-end performance helpers — critical font preload (CONS-B4b, ≤2 families).
 *
 * @package msrproducts
 */

/**
 * Preload self-hosted display + body fonts (Vite dist woff2).
 *
 * @return void
 */
function msrproducts_preload_theme_fonts() {
	if ( is_admin() ) {
		return;
	}

	$fonts = array(
		'archivo-black-latin-400-normal.woff2',
		'fira-sans-latin-400-normal.woff2',
	);

	foreach ( $fonts as $file ) {
		$path = get_template_directory() . '/dist/' . $file;
		if ( ! is_readable( $path ) ) {
			continue;
		}

		$url = get_template_directory_uri() . '/dist/' . $file;
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
			esc_url( $url )
		);
	}
}
add_action( 'wp_head', 'msrproducts_preload_theme_fonts', 2 );

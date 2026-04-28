<?php

/**
 * Register styles and scripts for WP Theme.
 */

 function theme_scripts() {
	wp_register_style('googlefonts', 'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Fira+Sans&display=swap', array(), null);
	wp_enqueue_style('bootstrapcss', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
	// Content-hash cache-busting avoids same-second mtime collisions.
	$app_css_path = get_template_directory() . '/dist/app.css';
	$app_js_path  = get_template_directory() . '/dist/app.js';
	$app_ver      = @md5_file( $app_css_path );
	$app_js_ver   = @md5_file( $app_js_path );
	$app_ver      = $app_ver ? substr( (string) $app_ver, 0, 12 ) : null;
	$app_js_ver   = $app_js_ver ? substr( (string) $app_js_ver, 0, 12 ) : null;
	wp_enqueue_style('googlefonts');
	wp_enqueue_style('bootstrapcss');
	wp_enqueue_style('appcss', get_template_directory_uri() . '/dist/app.css' , array('bootstrapcss'), $app_ver);

	wp_register_script( 'fontawesomejs', 'https://kit.fontawesome.com/2c48647809.js' );
	wp_register_script( 'bootstrapjs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js' );
	wp_enqueue_script('appjs', get_template_directory_uri() . '/dist/app.js', array('jquery'), $app_js_ver, true );

	wp_enqueue_script('jquery');
	wp_enqueue_script('fontawesomejs');
	wp_enqueue_script('bootstrapjs');
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );
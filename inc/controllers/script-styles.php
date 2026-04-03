<?php

/**
 * Register styles and scripts for WP Theme.
 */

 function theme_scripts() {
	wp_register_style('googlefonts', 'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Fira+Sans&display=swap', array(), null);
	wp_enqueue_style('basecss', 'https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/base-min.css');
	wp_enqueue_style('animatecss', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
	wp_enqueue_style('fancyboxcss', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css');
	wp_enqueue_style('bootstrapcss', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
	// Stable cache-busting: change only when files change (improves CWV vs time()).
	$style_ver = @filemtime( get_template_directory() . '/style.css' );
	$app_ver   = @filemtime( get_template_directory() . '/dist/app.css' );
	$style_ver = $style_ver ? (int) $style_ver : null;
	$app_ver   = $app_ver ? (int) $app_ver : null;
	wp_enqueue_style('stylecss', get_template_directory_uri() . '/style.css' , array(), $style_ver);
	wp_register_style('appcss', get_template_directory_uri() . '/dist/app.css' , array(), $app_ver);
	wp_enqueue_style('googlefonts');
	wp_enqueue_style('basecss');
    wp_enqueue_style('appcss');

	wp_register_script( 'fontawesomejs', 'https://kit.fontawesome.com/2c48647809.js' );
	wp_register_script( 'lottiejs', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js' );
	wp_register_script( 'fancyboxjs', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js', array(), null, true );
	wp_register_script( 'bootstrapjs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js' );
	wp_enqueue_script('animationjs', get_template_directory_uri() . '/src/js/animation.js', array('jquery'));
	wp_enqueue_script('navjs', get_template_directory_uri() . '/src/js/navigation.js', array(), _S_VERSION, true );
    wp_register_script('appjs', get_template_directory_uri() . '/dist/app.js' , ['jquery'], 1 , true);

	wp_enqueue_script('jquery');
	wp_enqueue_script('fontawesomejs');
	wp_enqueue_script('lottiejs');
	wp_enqueue_script('fancyboxjs');
	wp_enqueue_script('bootstrapjs');
    wp_enqueue_script('appjs');
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );
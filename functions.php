<?php
/**
 * msrproducts functions and definitions
 *
 * @package msrproducts
 */

 if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

require_once('inc/controllers/breadcrumbs.php');
require_once('inc/controllers/cpt.php');
require_once('inc/controllers/cpt-admin.php');
require_once('inc/controllers/search.php');
require_once('inc/controllers/wp-menus.php');
require_once('inc/controllers/script-styles.php');
require_once('inc/controllers/woocommerce.php');

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function msrproducts_setup() {
	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		*/
	add_theme_support( 'post-thumbnails' );

	set_post_thumbnail_size( 1200, 9999 );



	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'msrproducts_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'msrproducts_setup' );

/**
 * Custom Logo for WP Theme.
 */

add_filter( 'get_custom_logo', 'add_custom_logo_url' );
function add_custom_logo_url() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $html = sprintf( '<a href="%1$s" class="navbar-brand" rel="home" itemprop="url">%2$s</a>',
            esc_url( '/' ),
            wp_get_attachment_image( $custom_logo_id, 'full', false, array(
                'class'    => 'custom-logo',
            ) )
        );
    return $html;   
} 

function enable_svg_upload( $upload_mimes ) {
	
    $upload_mimes['svg'] = 'image/svg+xml';

    $upload_mimes['svgz'] = 'image/svg+xml';

    return $upload_mimes;

}

add_filter( 'upload_mimes', 'enable_svg_upload', 10, 1 );

if ( ! function_exists( 'tenweb_meta_description' ) ) {
    function tenweb_meta_description() { 
        global $post; 
 
        if ( is_singular() ) 
        { 
            $des_post = strip_tags( $post->post_content ); 
            $des_post = strip_shortcodes( $des_post ); 
            $des_post = str_replace( array("\n", "\r", "\t"), ' ', $des_post ); 
            $des_post = mb_substr( $des_post, 0, 300, 'utf8' ); 
            echo '<meta name="description" content="' . $des_post . '" />'. "\n"; 
        } 
 
        if ( is_home() ) 
        { 
            echo '<meta name="description" content="' . get_bloginfo( "description" ) . '" />' . "\n"; 
        } 
 
        if ( is_category() ) {
            $des_cat = strip_tags(category_description());
            echo '<meta name="description" content="' . $des_cat . '" />'. "\n";
        } 
    } 
}
add_action( 'wp_head', 'tenweb_meta_description');

function post_per_page_control( $query ) {
     if ( is_archive() ) {
          $query->set( 'posts_per_page', 18 );
          return;
     }
  }
  add_action( 'pre_get_posts', 'post_per_page_control' );

function wpse_custom_excerpts($limit) {
    return wp_trim_words(get_the_excerpt(), $limit, '[...]');
}

add_filter('get_the_terms', function ($terms, $post_id, $taxonomy) {
    $exclude_categories = array(6);
    if (!is_admin() && is_array($terms)) {
        foreach($terms as $key => $term){
            if($term->taxonomy == "category" && in_array($term->term_id, $exclude_categories)) {
                unset($terms[$key]);
            }
        }
    }
    return $terms;
}, 100, 3);
add_action( 'wp_dashboard_setup', 'remove_draft_widget', 999 );
add_filter( 'register_post_type_args', 'remove_default_post_type', 0, 2 );

/**
 * Remove Quick Draft Dashboard Widget
 */
function remove_draft_widget() {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}

/**
 * Backward-compat callback expected by legacy media code.
 */
if ( ! function_exists( 'custom_save_attachment_compat' ) ) {
	function custom_save_attachment_compat() {
		if ( function_exists( 'wp_ajax_save_attachment_compat' ) ) {
			wp_ajax_save_attachment_compat();
			return;
		}
		wp_send_json_error( array( 'message' => 'Attachment compat handler unavailable.' ) );
	}
}

/**
 * Fallback taxonomy term count callback expected by legacy folder taxonomy.
 */
if ( ! function_exists( 'rudr_update_folder_attachment_count' ) ) {
	function rudr_update_folder_attachment_count( $terms = array(), $taxonomy = '' ) {
		if ( empty( $terms ) || empty( $taxonomy ) ) {
			return;
		}
		if ( function_exists( 'wp_update_term_count_now' ) ) {
			wp_update_term_count_now( (array) $terms, $taxonomy );
		}
	}
}

/**
 * Ensure WooCommerce "Special Offers" page exists for promo strip links.
 */
if ( ! function_exists( 'msrproducts_ensure_special_offers_page' ) ) {
	function msrproducts_ensure_special_offers_page() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$existing_page = get_page_by_path( 'special-offers' );
		$shortcode_content = '[products on_sale="true" limit="12" columns="3" paginate="true"]';

		if ( $existing_page && ! empty( $existing_page->ID ) ) {
			$current_content = (string) get_post_field( 'post_content', (int) $existing_page->ID );
			if ( trim( $current_content ) === '' ) {
				wp_update_post(
					array(
						'ID'           => (int) $existing_page->ID,
						'post_content' => $shortcode_content,
					)
				);
			}
			return;
		}

		wp_insert_post(
			array(
				'post_title'   => 'Special Offers',
				'post_name'    => 'special-offers',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $shortcode_content,
			)
		);
	}
}
add_action( 'after_switch_theme', 'msrproducts_ensure_special_offers_page' );
add_action( 'admin_init', 'msrproducts_ensure_special_offers_page' );
add_action( 'init', 'msrproducts_ensure_special_offers_page' );

if ( ! function_exists( 'msrproducts_get_special_offers_url' ) ) {
	function msrproducts_get_special_offers_url() {
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' );
		$fallback_url = add_query_arg( 'on_sale', '1', $shop_url );
		$special_offers_page = get_page_by_path( 'special-offers' );

		if ( $special_offers_page && ! empty( $special_offers_page->ID ) && get_post_status( (int) $special_offers_page->ID ) === 'publish' ) {
			$permalink = get_permalink( (int) $special_offers_page->ID );
			return $permalink ? $permalink : $fallback_url;
		}

		return $fallback_url;
	}
}

if ( ! function_exists( 'msrproducts_filter_shop_on_sale' ) ) {
	function msrproducts_filter_shop_on_sale( $query ) {
		if ( is_admin() || ! $query->is_main_query() || empty( $_GET['on_sale'] ) ) {
			return;
		}
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_product_ids_on_sale' ) ) {
			return;
		}

		$is_product_archive = function_exists( 'is_shop' ) ? ( is_shop() || is_post_type_archive( 'product' ) || is_product_taxonomy() ) : is_post_type_archive( 'product' );
		if ( ! $is_product_archive ) {
			return;
		}

		$sale_ids = wc_get_product_ids_on_sale();
		$query->set( 'post__in', ! empty( $sale_ids ) ? array_map( 'intval', $sale_ids ) : array( 0 ) );
	}
}
add_action( 'pre_get_posts', 'msrproducts_filter_shop_on_sale', 20 );

/**
 * Disable WooCommerce default styles; use theme SCSS only.
 */
if ( ! function_exists( 'msrproducts_disable_woocommerce_styles' ) ) {
	function msrproducts_disable_woocommerce_styles( $styles ) {
		return array();
	}
}
add_filter( 'woocommerce_enqueue_styles', 'msrproducts_disable_woocommerce_styles' );
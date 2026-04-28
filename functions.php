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
require_once('inc/controllers/catalog-mode.php');
require_once('inc/controllers/subdir-upload-urls.php');
require_once('inc/controllers/home-acf.php');

/**
 * WordPress often stores SVG dimensions as 1×1; strip width/height so layout/CSS can size them.
 */
function msrproducts_fix_svg_attachment_image_dimensions( $attr, $attachment, $size ) {
	if ( ! $attachment instanceof WP_Post ) {
		$attr['loading']  = isset( $attr['loading'] ) ? $attr['loading'] : 'lazy';
		$attr['decoding'] = 'async';
		return $attr;
	}
	if ( $attachment->post_mime_type !== 'image/svg+xml' ) {
		$attr['loading']  = isset( $attr['loading'] ) ? $attr['loading'] : 'lazy';
		$attr['decoding'] = 'async';
		return $attr;
	}
	unset( $attr['width'], $attr['height'] );
	$attr['loading']  = isset( $attr['loading'] ) ? $attr['loading'] : 'lazy';
	$attr['decoding'] = 'async';
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'msrproducts_fix_svg_attachment_image_dimensions', 10, 3 );

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
            echo '<meta name="description" content="' . esc_attr( $des_post ) . '" />' . "\n"; 
        } 
 
        if ( is_home() ) 
        { 
            $des_home = strip_tags( (string) get_bloginfo( "description" ) );
            echo '<meta name="description" content="' . esc_attr( $des_home ) . '" />' . "\n"; 
        } 
 
        if ( is_category() ) {
            $des_cat = strip_tags(category_description());
            echo '<meta name="description" content="' . esc_attr( $des_cat ) . '" />' . "\n";
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

if ( ! function_exists( 'msrproducts_get_page_url_by_path' ) ) {
	function msrproducts_get_page_url_by_path( $path, $fallback = '' ) {
		$page = get_page_by_path( trim( (string) $path, '/' ) );
		if ( $page instanceof WP_Post ) {
			$permalink = get_permalink( $page->ID );
			if ( $permalink ) {
				return $permalink;
			}
		}
		return $fallback ? $fallback : home_url( '/' );
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

if ( ! function_exists( 'msrproducts_include_products_in_search' ) ) {
	function msrproducts_include_products_in_search( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
			return;
		}
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] !== '' ) {
			return;
		}
		$query->set( 'post_type', array( 'product', 'page' ) );
	}
}
add_action( 'pre_get_posts', 'msrproducts_include_products_in_search', 25 );

if ( ! function_exists( 'msrproducts_canonicalize_product_search_url' ) ) {
	function msrproducts_canonicalize_product_search_url() {
		if ( ! is_search() ) {
			return;
		}
		if ( empty( $_GET['post_type'] ) || $_GET['post_type'] !== 'product' ) {
			return;
		}
		$query = get_search_query();
		$target = home_url( '/' );
		if ( $query !== '' ) {
			$target = add_query_arg( 's', rawurlencode( $query ), $target );
		}
		wp_safe_redirect( $target, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'msrproducts_canonicalize_product_search_url', 2 );

/**
 * Disable WooCommerce default styles; use theme SCSS only.
 */
if ( ! function_exists( 'msrproducts_disable_woocommerce_styles' ) ) {
	function msrproducts_disable_woocommerce_styles( $styles ) {
		return array();
	}
}
add_filter( 'woocommerce_enqueue_styles', 'msrproducts_disable_woocommerce_styles' );

/**
 * Some plugins or legacy code can leave $wp_query->posts as a WP_Query object instead of an array,
 * which fatals in have_posts() / rewind_posts() on PHP 8+. Normalize before templates run.
 */
function msrproducts_fix_main_query_posts_array() {
	global $wp_query;
	if ( ! isset( $wp_query ) || ! ( $wp_query instanceof WP_Query ) ) {
		return;
	}
	if ( ! isset( $wp_query->posts ) ) {
		return;
	}
	if ( is_array( $wp_query->posts ) ) {
		return;
	}
	if ( $wp_query->posts instanceof WP_Query ) {
		$inner = $wp_query->posts;
		$wp_query->posts = ( isset( $inner->posts ) && is_array( $inner->posts ) ) ? $inner->posts : array();
	} else {
		$wp_query->posts = array();
	}
	$wp_query->post_count = count( $wp_query->posts );
	$wp_query->current_post = -1;
	if ( $wp_query->post_count > 0 && isset( $wp_query->posts[0] ) ) {
		$wp_query->post = $wp_query->posts[0];
	}

	// If recovery left no posts but this URL is the static front page, re-inject that page.
	if ( $wp_query->post_count === 0 && get_option( 'show_on_front' ) === 'page' && is_front_page() ) {
		$page_id = (int) get_option( 'page_on_front' );
		if ( $page_id > 0 ) {
			$p = get_post( $page_id );
			if ( $p instanceof WP_Post ) {
				$wp_query->posts         = array( $p );
				$wp_query->post_count    = 1;
				$wp_query->current_post  = -1;
				$wp_query->post          = $p;
				$wp_query->queried_object = $p;
				$wp_query->queried_object_id = $page_id;
			}
		}
	}
}
add_action( 'template_redirect', 'msrproducts_fix_main_query_posts_array', 0 );

/**
 * Page used for rich homepage in index.php (content + Woo sections + partners).
 */
function msrproducts_index_rich_home_post() {
	if ( get_option( 'show_on_front' ) !== 'page' ) {
		return null;
	}
	$page_id = (int) get_option( 'page_on_front' );
	if ( $page_id < 1 ) {
		return null;
	}
	if ( is_front_page() || is_page( $page_id ) ) {
		$p = get_post( $page_id );
		return $p instanceof WP_Post ? $p : null;
	}
	return null;
}

if ( ! function_exists( 'msrproducts_get_term_archive_url' ) ) {
	function msrproducts_get_term_archive_url( $term ) {
		if ( ! ( $term instanceof WP_Term ) ) {
			return home_url( '/' );
		}
		$permalink_structure = (string) get_option( 'permalink_structure' );
		if ( trim( $permalink_structure ) === '' ) {
			return add_query_arg(
				array(
					'post_type'   => 'product',
					'product_cat' => $term->slug,
				),
				home_url( '/' )
			);
		}
		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			return add_query_arg(
				array(
					'post_type'   => 'product',
					'product_cat' => $term->slug,
				),
				home_url( '/' )
			);
		}
		return $link;
	}
}

if ( ! function_exists( 'msrproducts_placeholder_image_url' ) ) {
	function msrproducts_placeholder_image_url() {
		return get_template_directory_uri() . '/assets/placeholder-product.svg';
	}
}

if ( ! function_exists( 'msrproducts_attachment_file_exists' ) ) {
	function msrproducts_attachment_file_exists( $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		if ( $attachment_id < 1 ) {
			return false;
		}
		$file = get_attached_file( $attachment_id );
		return is_string( $file ) && $file !== '' && file_exists( $file );
	}
}

if ( ! function_exists( 'msrproducts_fallback_broken_attachment_urls' ) ) {
	function msrproducts_fallback_broken_attachment_urls( $url, $attachment_id ) {
		if ( ! msrproducts_attachment_file_exists( $attachment_id ) ) {
			return msrproducts_placeholder_image_url();
		}
		return $url;
	}
}
add_filter( 'wp_get_attachment_url', 'msrproducts_fallback_broken_attachment_urls', 100, 2 );

if ( ! function_exists( 'msrproducts_fallback_broken_attachment_attrs' ) ) {
	function msrproducts_fallback_broken_attachment_attrs( $attr, $attachment, $size ) {
		if ( $attachment instanceof WP_Post && ! msrproducts_attachment_file_exists( $attachment->ID ) ) {
			$attr['src'] = msrproducts_placeholder_image_url();
			if ( isset( $attr['srcset'] ) ) {
				unset( $attr['srcset'] );
			}
			if ( isset( $attr['sizes'] ) ) {
				unset( $attr['sizes'] );
			}
		}
		return $attr;
	}
}
add_filter( 'wp_get_attachment_image_attributes', 'msrproducts_fallback_broken_attachment_attrs', 100, 3 );

if ( ! function_exists( 'msrproducts_woocommerce_placeholder_src' ) ) {
	/**
	 * Force WooCommerce placeholder URL to theme placeholder asset.
	 *
	 * @param string $src Existing Woo placeholder URL.
	 * @return string
	 */
	function msrproducts_woocommerce_placeholder_src( $src ) {
		if ( function_exists( 'msrproducts_placeholder_image_url' ) ) {
			return msrproducts_placeholder_image_url();
		}
		return $src;
	}
}
add_filter( 'woocommerce_placeholder_img_src', 'msrproducts_woocommerce_placeholder_src', 20, 1 );
add_filter( 'woocommerce_placeholder_image', 'msrproducts_woocommerce_placeholder_src', 20, 1 );

if ( ! function_exists( 'msrproducts_woocommerce_placeholder_img_html' ) ) {
	/**
	 * Render deterministic Woo placeholder img HTML without missing srcset variants.
	 *
	 * @param string $html Existing image HTML.
	 * @return string
	 */
	function msrproducts_woocommerce_placeholder_img_html( $html ) {
		$src = function_exists( 'msrproducts_placeholder_image_url' ) ? msrproducts_placeholder_image_url() : '';
		if ( $src === '' ) {
			return $html;
		}
		return '<img src="' . esc_url( $src ) . '" alt="' . esc_attr__( 'Placeholder image', 'msrproducts' ) . '" class="woocommerce-placeholder wp-post-image" loading="lazy" decoding="async" />';
	}
}
add_filter( 'woocommerce_placeholder_img', 'msrproducts_woocommerce_placeholder_img_html', 20, 1 );

/**
 * Ensure legal and FAQ pages exist for portfolio-mode footer IA.
 */
if ( ! function_exists( 'msrproducts_ensure_core_pages' ) ) {
	function msrproducts_ensure_core_pages() {
		$pages = array(
			'privacy-policy'     => array(
				'title'   => 'Privacy Policy',
				'content' => 'This portfolio website collects minimal analytics and enquiry form data only for service communication.',
			),
			'cookie-policy'      => array(
				'title'   => 'Cookie Policy',
				'content' => 'Cookies are used to improve navigation and measure anonymized engagement.',
			),
			'terms-conditions'   => array(
				'title'   => 'Terms & Conditions',
				'content' => 'Content shown is portfolio material for demonstration and collaboration discussions only.',
			),
			'returns-refunds'    => array(
				'title'   => 'Returns & Refunds',
				'content' => 'No purchases are processed on this website. Checkout is intentionally disabled.',
			),
			'portfolio-only'     => array(
				'title'   => 'Showcase Mode',
				'content' => 'This website runs in showcase mode. Purchases, cart, and checkout are disabled. Use contact or inquiry links to discuss collaboration.',
			),
			'faq'                => array(
				'title'   => 'FAQ',
				'content' => '[msrproducts_faq]',
			),
			'compare-projects'   => array(
				'title'   => 'Compare Projects',
				'content' => '[msrproducts_compare]',
			),
			'contact'            => array(
				'title'   => 'Contact',
				'content' => 'Use this page to submit collaboration inquiries, project requests, and partnership questions.',
			),
			'search'             => array(
				'title'   => 'Search',
				'content' => '[msrproducts_search_hub]',
			),
			'account-info'       => array(
				'title'   => 'Account Info',
				'content' => '<section class="msr-info-page"><h2>Account information</h2><p>This showcase website does not process live account orders. Use this area as a reference for account-related flows and profile sections in production WooCommerce implementations.</p><ul><li>Profile and account settings</li><li>Order history pattern</li><li>Saved addresses and preferences</li></ul><p><a class="button" href="/sites/wp/main/contact/">Need help? Contact us</a></p></section>',
			),
			'checkout-overview'  => array(
				'title'   => 'Checkout Overview',
				'content' => '<section class="msr-info-page"><h2>Checkout and basket overview</h2><p>Checkout is intentionally disabled on this portfolio site. This page demonstrates where cart and checkout routes would be surfaced in a full commerce build.</p><ul><li>Basket review and item summary</li><li>Shipping and billing details</li><li>Payment and order confirmation flow</li></ul><p><a class="button" href="/sites/wp/main/contact/">Discuss implementation</a></p></section>',
			),
		);

		foreach ( $pages as $slug => $payload ) {
			$existing = get_page_by_path( $slug );
			if ( $existing instanceof WP_Post ) {
				continue;
			}
			wp_insert_post(
				array(
					'post_title'   => $payload['title'],
					'post_name'    => $slug,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => $payload['content'],
				)
			);
		}
	}
}
add_action( 'after_switch_theme', 'msrproducts_ensure_core_pages' );
add_action( 'admin_init', 'msrproducts_ensure_core_pages' );

if ( ! function_exists( 'msrproducts_faq_shortcode' ) ) {
	function msrproducts_faq_shortcode() {
		$items = array(
			array(
				'q' => 'Can I buy products on this site?',
				'a' => 'No. This is a portfolio catalog. Use the inquiry button to discuss project collaboration.',
			),
			array(
				'q' => 'How quickly can a project start?',
				'a' => 'Typical kickoff is within 1-2 weeks after scope confirmation.',
			),
			array(
				'q' => 'Do you provide source files and handover docs?',
				'a' => 'Yes. Handover packages and implementation notes are part of each delivery.',
			),
		);

		$html = '<section class="msr-faq" aria-label="Frequently asked questions"><h2>Frequently Asked Questions</h2><div class="msr-faq-list">';
		foreach ( $items as $item ) {
			$html .= '<details class="msr-faq-item"><summary>' . esc_html( $item['q'] ) . '</summary><p>' . esc_html( $item['a'] ) . '</p></details>';
		}
		$html .= '</div></section>';
		return $html;
	}
}
add_shortcode( 'msrproducts_faq', 'msrproducts_faq_shortcode' );

if ( ! function_exists( 'msrproducts_popular_search_queries' ) ) {
	/**
	 * Popular search query labels.
	 *
	 * @return array<int, string>
	 */
	function msrproducts_popular_search_queries() {
		$defaults = array( 'Crossbody', 'Messenger', 'Backpack', 'Travel', 'Leather', 'Accessories' );
		$out      = array();

		$products = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		foreach ( $products as $pid ) {
			$title = trim( (string) get_the_title( (int) $pid ) );
			if ( $title === '' ) {
				continue;
			}
			$token = strtok( $title, ' ' );
			$token = is_string( $token ) ? trim( $token ) : '';
			if ( $token !== '' ) {
				$out[] = $token;
			}
		}

		$merged = array_values( array_unique( array_filter( array_merge( $out, $defaults ) ) ) );
		return array_slice( $merged, 0, 8 );
	}
}

if ( ! function_exists( 'msrproducts_search_hub_shortcode' ) ) {
	/**
	 * Render full search landing hub.
	 *
	 * @return string
	 */
	function msrproducts_search_hub_shortcode() {
		$suggestions = function_exists( 'msrproducts_search_suggestion_items' ) ? msrproducts_search_suggestion_items( 20 ) : array();
		$popular     = function_exists( 'msrproducts_popular_search_queries' ) ? msrproducts_popular_search_queries() : array();
		$terms       = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => 8,
			)
		);
		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}

		ob_start();
		?>
		<section class="search-hub" aria-labelledby="search-hub-title">
			<header class="search-hub__header">
				<p class="search-hub__eyebrow">Search</p>
				<h1 id="search-hub-title">Find projects faster</h1>
				<p>Use predictive suggestions, popular queries, or category quick links to jump directly into relevant portfolio work.</p>
			</header>

			<div class="search-hub__panel" data-predictive-search>
				<form role="search" method="get" class="search-hub__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="search-hub-input"><?php esc_html_e( 'Search for:', 'msrproducts' ); ?></label>
					<input id="search-hub-input" type="search" name="s" placeholder="Search by project, category, or keyword" autocomplete="off" data-predictive-search-input />
					<button type="submit"><?php esc_html_e( 'Search', 'msrproducts' ); ?></button>
				</form>
				<div class="site-search-autocomplete search-hub__autocomplete" data-predictive-search-box hidden>
					<p class="site-search-autocomplete__title"><?php esc_html_e( 'Suggestions', 'msrproducts' ); ?></p>
					<ul class="site-search-autocomplete__list" data-predictive-search-list></ul>
				</div>
				<script type="application/json" data-search-items-json><?php echo wp_json_encode( $suggestions ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
			</div>

			<section class="search-hub__popular" aria-label="<?php esc_attr_e( 'Popular searches', 'msrproducts' ); ?>">
				<h2><?php esc_html_e( 'Popular searches', 'msrproducts' ); ?></h2>
				<div class="search-hub__chips">
					<?php foreach ( $popular as $query ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 's', rawurlencode( (string) $query ), home_url( '/' ) ) ); ?>"><?php echo esc_html( (string) $query ); ?></a>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="search-hub__categories" aria-label="<?php esc_attr_e( 'Category quick links', 'msrproducts' ); ?>">
				<h2><?php esc_html_e( 'Category quick links', 'msrproducts' ); ?></h2>
				<ul>
					<?php foreach ( $terms as $term ) : ?>
						<?php if ( ! ( $term instanceof WP_Term ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<?php $term_url = function_exists( 'msrproducts_get_term_archive_url' ) ? msrproducts_get_term_archive_url( $term ) : home_url( '/?post_type=product' ); ?>
						<li><a href="<?php echo esc_url( $term_url ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</section>
		</section>
		<?php
		return (string) ob_get_clean();
	}
}
add_shortcode( 'msrproducts_search_hub', 'msrproducts_search_hub_shortcode' );

if ( ! function_exists( 'msrproducts_force_search_hub_route' ) ) {
	/**
	 * Ensure /search/ path resolves in local plain-permalink setups.
	 *
	 * @return void
	 */
	function msrproducts_force_search_hub_route() {
		if ( is_admin() ) {
			return;
		}
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		if ( $request_uri === '' ) {
			return;
		}
		$path = wp_parse_url( $request_uri, PHP_URL_PATH );
		$path = is_string( $path ) ? trim( $path, '/' ) : '';
		if ( substr( $path, -6 ) !== 'search' ) {
			return;
		}
		if ( ! is_404() ) {
			return;
		}
		$page = get_page_by_path( 'search' );
		if ( ! ( $page instanceof WP_Post ) ) {
			return;
		}
		$target = get_permalink( (int) $page->ID );
		if ( ! is_string( $target ) || $target === '' ) {
			$target = add_query_arg( 'page_id', (int) $page->ID, home_url( '/' ) );
		}
		wp_safe_redirect( $target, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'msrproducts_force_search_hub_route', 1 );

if ( ! function_exists( 'msrproducts_compare_shortcode' ) ) {
	function msrproducts_compare_shortcode() {
		$ids = array();
		if ( isset( $_GET['ids'] ) ) {
			$ids = array_slice( array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) ) ), 0, 3 );
		}

		if ( empty( $ids ) ) {
			return '<p class="msr-empty-state">Select up to three projects from archive cards to compare key specs.</p>';
		}

		$products = array();
		foreach ( $ids as $id ) {
			if ( get_post_type( $id ) !== 'product' ) {
				continue;
			}
			$products[] = get_post( $id );
		}
		if ( empty( $products ) ) {
			return '<p class="msr-empty-state">No valid projects were found for comparison.</p>';
		}

		$out = '<section class="msr-compare-table-wrap"><h2>Compare Projects</h2><table class="msr-compare-table"><thead><tr><th>Field</th>';
		foreach ( $products as $product_post ) {
			$out .= '<th>' . esc_html( get_the_title( $product_post ) ) . '</th>';
		}
		$out .= '</tr></thead><tbody>';

		$rows = array(
			'Category' => function ( $pid ) {
				$terms = get_the_terms( $pid, 'product_cat' );
				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					return 'N/A';
				}
				return $terms[0]->name;
			},
			'Summary'  => function ( $pid ) {
				$excerpt = get_the_excerpt( $pid );
				return $excerpt ? wp_trim_words( $excerpt, 16 ) : 'No summary yet';
			},
		);

		foreach ( $rows as $label => $callback ) {
			$out .= '<tr><th>' . esc_html( $label ) . '</th>';
			foreach ( $products as $product_post ) {
				$out .= '<td>' . esc_html( call_user_func( $callback, (int) $product_post->ID ) ) . '</td>';
			}
			$out .= '</tr>';
		}

		$out .= '</tbody></table></section>';
		return $out;
	}
}
add_shortcode( 'msrproducts_compare', 'msrproducts_compare_shortcode' );

if ( ! function_exists( 'msrproducts_filter_primary_nav_items' ) ) {
	function msrproducts_filter_primary_nav_items( $items, $args ) {
		if ( ! isset( $args->theme_location ) || $args->theme_location !== 'menu-1' ) {
			return $items;
		}

		$blocked_slugs = array( 'cart', 'basket', 'checkout', 'my-account', 'my account', 'sample-page', 'sample page' );
		$filtered      = array();
		foreach ( $items as $item ) {
			$title = strtolower( trim( wp_strip_all_tags( (string) $item->title ) ) );
			$url   = strtolower( trim( (string) $item->url ) );
			$drop  = false;
			foreach ( $blocked_slugs as $slug ) {
				if ( $title === $slug || strpos( $url, '/' . str_replace( ' ', '-', $slug ) ) !== false ) {
					$drop = true;
					break;
				}
			}
			if ( $drop ) {
				continue;
			}
			$filtered[] = $item;
		}
		return $filtered;
	}
}
add_filter( 'wp_nav_menu_objects', 'msrproducts_filter_primary_nav_items', 20, 2 );

if ( ! function_exists( 'msrproducts_search_suggestions' ) ) {
	/**
	 * Structured suggestion items for predictive search UI.
	 *
	 * @param int $limit Maximum suggestions.
	 * @return array<int, array<string, string>>
	 */
	function msrproducts_search_suggestion_items( $limit = 10 ) {
		$limit = max( 1, (int) $limit );
		$seen  = array();
		$out   = array();

		$products = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		foreach ( $products as $product_id ) {
			$product_id = (int) $product_id;
			$title      = trim( (string) get_the_title( $product_id ) );
			if ( $title === '' ) {
				continue;
			}
			$key = 'product:' . strtolower( $title );
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$url = get_permalink( $product_id );
			if ( ! is_string( $url ) || $url === '' ) {
				continue;
			}
			$seen[ $key ] = true;
			$out[] = array(
				'label' => $title,
				'type'  => 'Project',
				'url'   => $url,
				'icon'  => 'project',
			);
			if ( count( $out ) >= $limit ) {
				return $out;
			}
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => $limit,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $out;
		}

		foreach ( $terms as $term ) {
			if ( ! ( $term instanceof WP_Term ) ) {
				continue;
			}
			$label = trim( (string) $term->name );
			if ( $label === '' ) {
				continue;
			}
			$key = 'category:' . strtolower( $label );
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$url = function_exists( 'msrproducts_get_term_archive_url' ) ? msrproducts_get_term_archive_url( $term ) : '';
			if ( ! is_string( $url ) || $url === '' ) {
				$url = add_query_arg(
					array(
						'post_type'   => 'product',
						'product_cat' => $term->slug,
					),
					home_url( '/' )
				);
			}
			$seen[ $key ] = true;
			$out[] = array(
				'label' => $label,
				'type'  => 'Category',
				'url'   => $url,
				'icon'  => 'category',
			);
			if ( count( $out ) >= $limit ) {
				break;
			}
		}

		return $out;
	}

	/**
	 * Build lightweight search suggestions from products and categories.
	 *
	 * @param int $limit Maximum suggestions.
	 * @return array<int, string>
	 */
	function msrproducts_search_suggestions( $limit = 10 ) {
		$items = function_exists( 'msrproducts_search_suggestion_items' ) ? msrproducts_search_suggestion_items( $limit ) : array();
		$out   = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) || empty( $item['label'] ) ) {
				continue;
			}
			$out[] = (string) $item['label'];
		}
		return $out;
	}
}
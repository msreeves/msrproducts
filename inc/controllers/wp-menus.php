<?php 

/**
 * Register navigation menus uses wp_nav_menu.
 */

function msrproducts_menus() {

	$locations = array(
		'menu-1'                => __( 'Primary', 'msrproducts' ),
		'header-utility-menu'   => __( 'Header Utility Menu', 'msrproducts' ),
		'header-actions-menu'   => __( 'Header Actions Menu', 'msrproducts' ),
		'footer-explore-menu'   => __( 'Footer Explore Menu', 'msrproducts' ),
		'footer-services-menu'  => __( 'Footer Services Menu', 'msrproducts' ),
		'footer-legal-menu'     => __( 'Footer Legal Menu', 'msrproducts' ),
		'footer'                => __( 'Footer Menu', 'msrproducts' ),
		'social'                => __( 'Social Menu', 'msrproducts' ),
	);

	register_nav_menus( $locations );
}

if ( ! class_exists( 'MSRProducts_Header_Action_Walker' ) ) {
	class MSRProducts_Header_Action_Walker extends Walker_Nav_Menu {

		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$has_icon = in_array( 'icon-account', $classes, true ) || in_array( 'icon-basket', $classes, true );
			$link_class = $has_icon ? 'header-action-link header-action-link--icon-only' : 'header-action-link';
			$icon_markup = $this->get_icon_markup( $classes );
			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$li_classes = array_merge( array( 'menu-item', 'menu-item-' . (string) $item->ID ), $classes );
			$li_class_attr = implode(
				' ',
				array_filter(
					array_map(
						'sanitize_html_class',
						array_map( 'strval', $li_classes )
					)
				)
			);

			$output .= '<li class="' . esc_attr( $li_class_attr ) . '">';
			$output .= '<a class="' . esc_attr( $link_class ) . '" href="' . esc_url( (string) $item->url ) . '">';
			$output .= $icon_markup;

			if ( $has_icon ) {
				$output .= '<span class="screen-reader-text">' . esc_html( $title ) . '</span>';
			} else {
				$output .= '<span>' . esc_html( $title ) . '</span>';
			}

			$output .= '</a>';
		}

		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= '</li>';
		}

		private function get_icon_markup( $classes ) {
			$icon_account = '<span class="header-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M18 20a6 6 0 0 0-12 0m9-12a3 3 0 1 1-6 0a3 3 0 0 1 6 0Z"/></svg></span>';
			$icon_basket = '<span class="header-action-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M6 8h12l-1.2 11H7.2L6 8Zm3-1V6a3 3 0 0 1 6 0v1"/></svg></span>';

			if ( in_array( 'icon-account', $classes, true ) ) {
				return $icon_account;
			}
			if ( in_array( 'icon-basket', $classes, true ) ) {
				return $icon_basket;
			}

			return '';
		}
	}
}

class CSS_Menu_Walker extends Walker {

	var $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');
	
	function start_lvl(&$output, $depth = 0, $args = null) {
		$indent = str_repeat("\t", $depth);
		$menu_class = $depth === 0 ? 'mega-menu' : 'sub-menu';
		$output .= "\n$indent<ul class=\"" . esc_attr($menu_class) . "\">\n";
	}
	
	function end_lvl(&$output, $depth = 0, $args = null) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
	
	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
	
		global $wp_query;
		$indent = ($depth) ? str_repeat("\t", $depth) : '';
		$class_names = $value = '';
		$classes = empty($item->classes) ? array() : (array) $item->classes;
		
		/* Add active class */
		if (in_array('current-menu-item', $classes)) {
			$classes[] = 'active';
			unset($classes['current-menu-item']);
		}
		
		/* Check for children */
		$children = get_posts(array('post_type' => 'nav_menu_item', 'nopaging' => true, 'numberposts' => 1, 'meta_key' => '_menu_item_menu_item_parent', 'meta_value' => $item->ID));
		if (!empty($children)) {
			$classes[] = 'has-sub';
		}
		$classes = $this->append_panel_variant_classes($classes);
		$classes = $this->append_inferred_panel_variant($classes, $item, $depth, $children);
		
		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
		
		$id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
		$id = $id ? ' id="' . esc_attr($id) . '"' : '';
		
		$output .= $indent . '<li' . $id . $value . $class_names .'>';
		
		$item_url = ! empty($item->url) ? (string) $item->url : '';
		if ($depth === 0 && !empty($children) && ($item_url === '#' || $item_url === '')) {
			if (function_exists('wc_get_page_permalink')) {
				$shop_url = wc_get_page_permalink('shop');
				if (is_string($shop_url) && $shop_url !== '') {
					$item_url = $shop_url;
				}
			}
		}

		$attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
		$attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"' : '';
		$attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) .'"' : '';
		$attributes .= $item_url !== '' ? ' href="' . esc_url($item_url) . '"' : '';
		
		$before = is_object($args) ? ($args->before ?? '') : ((is_array($args) && isset($args['before'])) ? $args['before'] : '');
		$after = is_object($args) ? ($args->after ?? '') : ((is_array($args) && isset($args['after'])) ? $args['after'] : '');

		$item_output = $before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $this->get_menu_item_markup($item, $depth, $args);
		if ($depth === 0 && !empty($children)) {
			$item_output .= '<span class="menu-caret" aria-hidden="true"></span>';
		}
		$item_output .= '</a>';
		$item_output .= $after;
		
		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
	
	function end_el(&$output, $item, $depth = 0, $args = null) {
		$output .= "</li>\n";
	}

	private function get_menu_item_markup($item, $depth, $args) {
		$link_before = is_object($args) ? ($args->link_before ?? '') : ((is_array($args) && isset($args['link_before'])) ? $args['link_before'] : '');
		$link_after = is_object($args) ? ($args->link_after ?? '') : ((is_array($args) && isset($args['link_after'])) ? $args['link_after'] : '');
		$title = $link_before . apply_filters('the_title', $item->title, $item->ID) . $link_after;
		$description = !empty($item->description) ? wp_strip_all_tags($item->description) : '';
		$badge = $this->extract_badge_text($item);

		// Keep top-level items as simple labels.
		if ($depth === 0) {
			return '<span class="menu-label">' . $title . '</span>' . $this->render_badge($badge);
		}

		// Enrich product category child items with image + price card.
		if ($this->is_product_category_menu_item($item)) {
			return $this->build_product_category_menu_item($item, $title, $description, $badge);
		}

		// Enrich product child items with current product image + price.
		if ($this->is_product_post_menu_item($item)) {
			return $this->build_product_post_menu_item($item, $title, $description, $badge);
		}

		return '<span class="menu-label">' . $title . '</span>' .
			$this->render_badge($badge) .
			$this->render_description($description) .
			$this->render_preview_meta('', $title, '');
	}

	private function is_product_category_menu_item($item) {
		return isset($item->type, $item->object) && $item->type === 'taxonomy' && $item->object === 'product_cat';
	}

	private function is_product_post_menu_item($item) {
		return isset($item->type, $item->object) && $item->type === 'post_type' && $item->object === 'product';
	}

	private function build_product_category_menu_item($item, $title, $description = '', $badge = '') {
		$term_id = isset($item->object_id) ? (int) $item->object_id : 0;
		if (!$term_id) {
			return '<span>' . $title . '</span>';
		}

		$latest_product = $this->get_latest_product_for_category($term_id);
		$preview_image_url = $this->get_product_category_preview_image_url($term_id, $title, $latest_product);
		$preview_title = is_object($latest_product) ? $latest_product->get_name() : $title;
		$preview_price = $this->format_product_price($latest_product);

		return '<span class="menu-label">' . $title . '</span>' .
			$this->render_badge($badge) .
			$this->render_description($description) .
			$this->render_preview_meta($preview_image_url, $preview_title, $preview_price);
	}

	private function build_product_post_menu_item($item, $title, $description = '', $badge = '') {
		$product_id = isset($item->object_id) ? (int) $item->object_id : 0;
		$product = ($product_id > 0 && function_exists('wc_get_product')) ? wc_get_product($product_id) : null;

		if (!is_object($product)) {
			return '<span class="menu-label">' . $title . '</span>' .
				$this->render_badge($badge) .
				$this->render_description($description) .
				$this->render_preview_meta('', $title, '');
		}

		$image_id = method_exists($product, 'get_image_id') ? (int) $product->get_image_id() : 0;
		$image_url = '';
		if ($image_id > 0 && function_exists('msrproducts_attachment_file_exists') && msrproducts_attachment_file_exists($image_id)) {
			$image_url = (string) wp_get_attachment_image_url($image_id, 'medium_large');
		}
		if ($image_url === '' && function_exists('msrproducts_placeholder_image_url')) {
			$image_url = (string) msrproducts_placeholder_image_url();
		}

		$product_title = method_exists($product, 'get_name') ? (string) $product->get_name() : $title;
		$product_price = $this->format_product_price($product);

		$media = $image_url !== ''
			? '<span class="nav-product-media"><img class="nav-product-image" src="' . esc_url($image_url) . '" alt="' . esc_attr($product_title) . '" loading="lazy" decoding="async"></span>'
			: '';

		return '<span class="nav-product-card">' .
			$media .
			'<span class="nav-product-copy">' .
				'<span class="nav-product-title">' . esc_html($product_title) . '</span>' .
				( $product_price !== '' ? '<span class="nav-product-price">' . esc_html($product_price) . '</span>' : '' ) .
			'</span>' .
		'</span>' .
		$this->render_badge($badge) .
		$this->render_description($description) .
		$this->render_preview_meta($image_url, $product_title, $product_price);
	}

	private function append_panel_variant_classes($classes) {
		if (empty($classes) || !is_array($classes)) {
			return $classes;
		}
		foreach ($classes as $class_name) {
			if (strpos($class_name, 'panel-') === 0) {
				$classes[] = 'has-panel-variant';
				break;
			}
		}
		return $classes;
	}

	private function append_inferred_panel_variant($classes, $item, $depth, $children) {
		if ($depth !== 0 || empty($children)) {
			return $classes;
		}
		foreach ($classes as $class_name) {
			if (strpos($class_name, 'panel-') === 0) {
				return $classes;
			}
		}

		$title = strtolower(trim(wp_strip_all_tags($item->title ?? '')));
		$shop_titles = array('shop', 'makeup', 'skincare', 'products', 'catalog');
		$explore_titles = array('explore', 'inspiration', 'about', 'services', 'guides');
		$offers_titles = array('offers', 'sale', 'deals', 'gifts', 'featured');

		if (in_array($title, $shop_titles, true)) {
			$classes[] = 'panel-shop';
			return $classes;
		}
		if (in_array($title, $explore_titles, true)) {
			$classes[] = 'panel-explore';
			return $classes;
		}
		if (in_array($title, $offers_titles, true)) {
			$classes[] = 'panel-offers';
			return $classes;
		}

		$classes[] = 'panel-default';
		return $classes;
	}

	private function extract_badge_text($item) {
		if (empty($item->classes) || !is_array($item->classes)) {
			return '';
		}
		foreach ($item->classes as $class_name) {
			if (strpos($class_name, 'badge-') === 0) {
				$badge = trim(str_replace('badge-', '', $class_name));
				return strtoupper(str_replace('-', ' ', $badge));
			}
		}
		return '';
	}

	private function render_badge($badge) {
		if (empty($badge)) {
			return '';
		}
		return '<span class="menu-badge">' . esc_html($badge) . '</span>';
	}

	private function render_description($description) {
		if (empty($description)) {
			return '';
		}
		return '<span class="menu-description">' . esc_html($description) . '</span>';
	}

	private function get_product_category_preview_image_url($term_id, $title, $latest_product = null) {
		$thumbnail_id = (int) get_term_meta($term_id, 'thumbnail_id', true);
		if (!$thumbnail_id) {
			$thumbnail_id = $this->get_latest_product_image_id_for_category($term_id, $latest_product);
		}

		if ($thumbnail_id && function_exists('msrproducts_attachment_file_exists') && !msrproducts_attachment_file_exists($thumbnail_id)) {
			$thumbnail_id = 0;
		}

		if ($thumbnail_id) {
			$image_url = wp_get_attachment_image_url($thumbnail_id, 'medium_large');
			if ($image_url) {
				return $image_url;
			}
		}

		if (function_exists('msrproducts_placeholder_image_url')) {
			return msrproducts_placeholder_image_url();
		}

		return '';
	}

	private function get_latest_product_for_category($term_id) {
		if (!function_exists('wc_get_products')) {
			return null;
		}

		$products = wc_get_products(
			array(
				'status' => 'publish',
				'limit' => 1,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'term_id',
						'terms' => array($term_id),
					),
				),
			)
		);

		if (empty($products) || !is_object($products[0])) {
			return null;
		}

		return $products[0];
	}

	private function get_latest_product_image_id_for_category($term_id, $latest_product = null) {
		if (is_object($latest_product) && method_exists($latest_product, 'get_image_id')) {
			return (int) $latest_product->get_image_id();
		}

		$latest_product = $this->get_latest_product_for_category($term_id);
		if (!is_object($latest_product) || !method_exists($latest_product, 'get_image_id')) {
			return 0;
		}

		return (int) $latest_product->get_image_id();
	}

	private function format_product_price($product) {
		if (!is_object($product) || !method_exists($product, 'get_price_html')) {
			return '';
		}
		$price_html = (string) $product->get_price_html();
		return $price_html !== '' ? wp_strip_all_tags($price_html) : '';
	}

	private function render_preview_meta($image_url, $title, $price) {
		$image_attr = $image_url ? esc_url($image_url) : '';
		$title_attr = $title ? wp_strip_all_tags($title) : '';
		$price_attr = $price ? wp_strip_all_tags($price) : '';

		return '<span class="menu-preview-meta" data-preview-image="' . esc_attr($image_attr) . '" data-preview-title="' . esc_attr($title_attr) . '" data-preview-price="' . esc_attr($price_attr) . '" aria-hidden="true"></span>';
	}
}

add_action( 'init', 'msrproducts_menus' );

if ( ! function_exists( 'msrproducts_inject_dynamic_product_children' ) ) {
	if ( ! function_exists( 'msrproducts_menu_products_cache_version' ) ) {
		function msrproducts_menu_products_cache_version() {
			$version = (int) get_option( 'msrproducts_menu_products_cache_version', 1 );
			return $version > 0 ? $version : 1;
		}
	}

	if ( ! function_exists( 'msrproducts_bust_menu_products_cache' ) ) {
		function msrproducts_bust_menu_products_cache() {
			$version = msrproducts_menu_products_cache_version();
			update_option( 'msrproducts_menu_products_cache_version', $version + 1, false );
		}
	}

	if ( ! function_exists( 'msrproducts_get_cached_category_product_ids' ) ) {
		/**
		 * Fetch latest product IDs for a category with transient caching.
		 *
		 * @param int $term_id Category term ID.
		 * @param int $limit   Number of products.
		 * @return int[]
		 */
		function msrproducts_get_cached_category_product_ids( $term_id, $limit = 4 ) {
			$term_id = (int) $term_id;
			$limit   = max( 1, (int) $limit );
			if ( $term_id <= 0 || ! function_exists( 'wc_get_products' ) ) {
				return array();
			}

			$cache_key = sprintf(
				'msr_menu_cat_%d_l%d_v%d',
				$term_id,
				$limit,
				msrproducts_menu_products_cache_version()
			);
			$cached = get_transient( $cache_key );
			if ( is_array( $cached ) ) {
				return array_map( 'intval', $cached );
			}

			$products = wc_get_products(
				array(
					'status'   => 'publish',
					'limit'    => $limit,
					'orderby'  => 'date',
					'order'    => 'DESC',
					'tax_query'=> array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => array( $term_id ),
						),
					),
					'return'   => 'ids',
				)
			);

			$product_ids = is_array( $products ) ? array_map( 'intval', $products ) : array();
			set_transient( $cache_key, $product_ids, HOUR_IN_SECONDS );

			return $product_ids;
		}
	}

	/**
	 * Auto-generate dropdown children for top-level product categories.
	 *
	 * Keeps WP Admin menu maintenance focused on top-level category items only.
	 *
	 * @param array    $items Menu item objects.
	 * @param stdClass $args  wp_nav_menu() args.
	 * @return array
	 */
	function msrproducts_inject_dynamic_product_children( $items, $args ) {
		if ( ! is_array( $items ) || empty( $items ) ) {
			return $items;
		}

		if ( ! isset( $args->theme_location ) || $args->theme_location !== 'menu-1' ) {
			return $items;
		}

		if ( ! function_exists( 'wc_get_products' ) ) {
			return $items;
		}

		$top_level_product_cats = array();
		foreach ( $items as $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}
			if ( (int) ( $item->menu_item_parent ?? 0 ) !== 0 ) {
				continue;
			}
			if ( ( $item->type ?? '' ) !== 'taxonomy' || ( $item->object ?? '' ) !== 'product_cat' ) {
				continue;
			}
			$top_level_product_cats[ (int) $item->ID ] = array(
				'term_id'    => (int) ( $item->object_id ?? 0 ),
				'menu_order' => (int) ( $item->menu_order ?? 0 ),
			);
		}

		if ( empty( $top_level_product_cats ) ) {
			return $items;
		}

		// Strip existing child items under these categories; regenerated below.
		$filtered_items = array();
		foreach ( $items as $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}
			$parent_id = (int) ( $item->menu_item_parent ?? 0 );
			if ( $parent_id > 0 && isset( $top_level_product_cats[ $parent_id ] ) ) {
				continue;
			}

			$item_id = (int) ( $item->ID ?? 0 );
			if ( $item_id > 0 && isset( $top_level_product_cats[ $item_id ] ) ) {
				$current_classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : array();
				if ( ! in_array( 'has-sub', $current_classes, true ) ) {
					$current_classes[] = 'has-sub';
				}
				if ( ! in_array( 'menu-item-has-children', $current_classes, true ) ) {
					$current_classes[] = 'menu-item-has-children';
				}
				$item->classes = $current_classes;
			}

			$filtered_items[] = $item;
		}

		$dynamic_items = array();
		$virtual_id    = -1000;
		$menu_order    = 10000;

		foreach ( $top_level_product_cats as $parent_item_id => $meta ) {
			$term_id = (int) $meta['term_id'];
			if ( $term_id <= 0 ) {
				continue;
			}

			$term = get_term( $term_id, 'product_cat' );
			if ( ! ( $term instanceof WP_Term ) ) {
				continue;
			}

			$product_ids = msrproducts_get_cached_category_product_ids( $term_id, 4 );
			if ( ! empty( $product_ids ) ) {
				foreach ( $product_ids as $product_id ) {
					$product_id  = (int) $product_id;
					$product_url = get_permalink( $product_id );
					if ( ! is_string( $product_url ) || $product_url === '' ) {
						continue;
					}

					$product_title = get_the_title( $product_id );
					if ( ! is_string( $product_title ) || $product_title === '' ) {
						continue;
					}

					$menu_item = new stdClass();
					$menu_item->ID               = $virtual_id--;
					$menu_item->db_id            = $menu_item->ID;
					$menu_item->menu_item_parent = $parent_item_id;
					$menu_item->object_id        = $product_id;
					$menu_item->object           = 'product';
					$menu_item->type             = 'post_type';
					$menu_item->type_label       = 'Product';
					$menu_item->title            = $product_title;
					$menu_item->url              = $product_url;
					$menu_item->target           = '';
					$menu_item->attr_title       = '';
					$menu_item->xfn              = '';
					$menu_item->description      = '';
					$menu_item->classes          = array( 'menu-item', 'menu-item-type-post_type', 'menu-item-object-product' );
					$menu_item->menu_order       = $menu_order++;
					$menu_item->post_status      = 'publish';
					$menu_item->post_type        = 'nav_menu_item';

					$dynamic_items[] = $menu_item;
				}
				continue;
			}

			$fallback_url = get_term_link( $term_id, 'product_cat' );
			if ( is_wp_error( $fallback_url ) || ! is_string( $fallback_url ) || $fallback_url === '' ) {
				$fallback_url = home_url( '/?post_type=product' );
			}

			$fallback_item = new stdClass();
			$fallback_item->ID               = $virtual_id--;
			$fallback_item->db_id            = $fallback_item->ID;
			$fallback_item->menu_item_parent = $parent_item_id;
			$fallback_item->object_id        = 0;
			$fallback_item->object           = 'custom';
			$fallback_item->type             = 'custom';
			$fallback_item->type_label       = 'Custom Link';
			$fallback_item->title            = sprintf( __( 'View all in %s', 'msrproducts' ), $term->name );
			$fallback_item->url              = $fallback_url;
			$fallback_item->target           = '';
			$fallback_item->attr_title       = '';
			$fallback_item->xfn              = '';
			$fallback_item->description      = '';
			$fallback_item->classes          = array( 'menu-item', 'menu-item-type-custom' );
			$fallback_item->menu_order       = $menu_order++;
			$fallback_item->post_status      = 'publish';
			$fallback_item->post_type        = 'nav_menu_item';

			$dynamic_items[] = $fallback_item;
		}

		return array_merge( $filtered_items, $dynamic_items );
	}
}
add_filter( 'wp_nav_menu_objects', 'msrproducts_inject_dynamic_product_children', 20, 2 );

add_action( 'save_post_product', 'msrproducts_bust_menu_products_cache' );
add_action( 'deleted_post', 'msrproducts_bust_menu_products_cache' );
add_action( 'created_product_cat', 'msrproducts_bust_menu_products_cache' );
add_action( 'edited_product_cat', 'msrproducts_bust_menu_products_cache' );
add_action( 'delete_product_cat', 'msrproducts_bust_menu_products_cache' );
add_action( 'wp_update_nav_menu', 'msrproducts_bust_menu_products_cache' );
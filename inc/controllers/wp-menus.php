<?php 

/**
 * Register navigation menus uses wp_nav_menu.
 */

function msrproducts_menus() {

	$locations = array(
		'primary'  => __( 'Desktop Horizontal Menu', 'msrproducts' ),
		'footer'   => __( 'Footer Menu', 'msrproducts' ),
		'social'   => __( 'Social Menu', 'msrproducts' ),
	);

	register_nav_menus( $locations );
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
		
		$attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
		$attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"' : '';
		$attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) .'"' : '';
		$attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url       ) .'"' : '';
		
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

		return '<span class="menu-label">' . $title . '</span>' .
			$this->render_badge($badge) .
			$this->render_description($description) .
			$this->render_preview_meta('', $title, '');
	}

	private function is_product_category_menu_item($item) {
		return isset($item->type, $item->object) && $item->type === 'taxonomy' && $item->object === 'product_cat';
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

		if (!$thumbnail_id) {
			return '';
		}

		$image_url = wp_get_attachment_image_url($thumbnail_id, 'medium_large');
		return $image_url ? $image_url : '';
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
		if (!is_object($product) || !method_exists($product, 'get_price') || !function_exists('wc_price')) {
			return '';
		}
		$raw_price = (float) $product->get_price();
		if ($raw_price <= 0) {
			return '';
		}
		return wp_strip_all_tags(wc_price($raw_price));
	}

	private function render_preview_meta($image_url, $title, $price) {
		$image_attr = $image_url ? esc_url($image_url) : '';
		$title_attr = $title ? wp_strip_all_tags($title) : '';
		$price_attr = $price ? wp_strip_all_tags($price) : '';

		return '<span class="menu-preview-meta" data-preview-image="' . esc_attr($image_attr) . '" data-preview-title="' . esc_attr($title_attr) . '" data-preview-price="' . esc_attr($price_attr) . '" aria-hidden="true"></span>';
	}
}

add_action( 'init', 'msrproducts_menus' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'msrproducts' ),
		)
	);
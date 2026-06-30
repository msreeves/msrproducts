<?php
/**
 * ACF options page and local fields — MSR Products site copy (home bands stay on front page ACF).
 *
 * @package msrproducts
 */

/**
 * @return void
 */
function msrproducts_register_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'MSR Products settings', 'msrproducts' ),
			'menu_title' => __( 'MSR Products', 'msrproducts' ),
			'menu_slug'  => 'msr-products-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
			'icon_url'   => 'dashicons-cart',
			'position'   => 58,
		)
	);
}
add_action( 'acf/init', 'msrproducts_register_acf_options_page' );

/**
 * @return void
 */
function msrproducts_register_acf_options_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_msr_products_programme_urls',
			'title'  => 'Programme URLs',
			'fields' => array(
				array(
					'key'   => 'field_msr_prd_opt_hub_url',
					'label' => 'MSR Events hub URL',
					'name'  => 'msr_programme_hub_url',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_msr_prd_opt_awards_url',
					'label' => 'MSR Awards URL',
					'name'  => 'msr_programme_awards_url',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_msr_prd_opt_seminars_url',
					'label' => 'MSR Seminars URL',
					'name'  => 'msr_programme_seminars_url',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_msr_prd_opt_publishing_url',
					'label' => 'Atlas Briefing URL',
					'name'  => 'msr_programme_publishing_url',
					'type'  => 'url',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'msr-products-settings',
					),
				),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_msr_products_site_copy',
			'title'  => 'Site copy',
			'fields' => array(
				array(
					'key'   => 'field_msr_prd_promo_text',
					'label' => 'Header promo message',
					'name'  => 'promo_strip_text',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_prd_promo_cta',
					'label' => 'Header promo CTA',
					'name'  => 'promo_strip_cta',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_hero_primary_cta',
					'label' => 'Hero primary CTA',
					'name'  => 'hero_primary_cta',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_hero_secondary_cta',
					'label' => 'Hero secondary CTA',
					'name'  => 'hero_secondary_cta',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_featured_title',
					'label' => 'Featured grid title',
					'name'  => 'featured_grid_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_featured_link',
					'label' => 'Featured grid link label',
					'name'  => 'featured_grid_link_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_category_title',
					'label' => 'Category tiles title',
					'name'  => 'category_tiles_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_category_link',
					'label' => 'Category tiles link label',
					'name'  => 'category_tiles_link_label',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_faq_title',
					'label' => 'Home FAQ section title',
					'name'  => 'home_faq_section_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_partners_title',
					'label' => 'Partners band title',
					'name'  => 'partners_band_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_partners_lead',
					'label' => 'Partners band lead',
					'name'  => 'partners_band_lead',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_prd_catalog_filter',
					'label' => 'Catalog filter title',
					'name'  => 'catalog_filter_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_catalog_empty',
					'label' => 'Catalog empty message',
					'name'  => 'catalog_empty_message',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_search_placeholder',
					'label' => 'Catalog search placeholder',
					'name'  => 'catalog_search_placeholder',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_msr_prd_footer_showcase_toggle',
					'label'         => 'Show footer showcase disclaimer',
					'name'          => 'show_footer_showcase_note',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
				),
				array(
					'key'   => 'field_msr_prd_footer_showcase',
					'label' => 'Footer showcase disclaimer',
					'name'  => 'footer_showcase_note',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_cookie_message',
					'label' => 'Cookie consent message',
					'name'  => 'cookie_consent_message',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_prd_compare_title',
					'label' => 'Compare modal title',
					'name'  => 'compare_modal_title',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_msr_prd_compare_lead',
					'label' => 'Compare modal lead',
					'name'  => 'compare_modal_lead',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_prd_compare_cta',
					'label' => 'Compare modal CTA',
					'name'  => 'compare_modal_cta',
					'type'  => 'text',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'msr-products-settings',
					),
				),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_msr_products_seo_copy',
			'title'  => 'SEO descriptions',
			'fields' => array(
				array(
					'key'   => 'field_msr_prd_seo_home',
					'label' => 'Home meta description',
					'name'  => 'seo_home_description',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_prd_seo_shop',
					'label' => 'Shop archive meta description',
					'name'  => 'seo_shop_description',
					'type'  => 'textarea',
					'rows'  => 2,
				),
				array(
					'key'   => 'field_msr_prd_seo_search',
					'label' => 'Search meta description',
					'name'  => 'seo_search_description',
					'type'  => 'textarea',
					'rows'  => 2,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'msr-products-settings',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'msrproducts_register_acf_options_fields' );

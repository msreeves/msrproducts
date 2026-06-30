<?php
/**
 * MSR Products ACF options — admin-first site copy and programme URLs.
 *
 * @package msrproducts
 */

/**
 * @param string $field ACF field name.
 * @param string $default Fallback when empty.
 * @return string
 */
function msrproducts_get_option_string( $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $field, 'option' );
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return $default;
	}
	return trim( $value );
}

/**
 * @param string $field ACF field name.
 * @param bool   $default Fallback.
 * @return bool
 */
function msrproducts_get_option_bool( $field, $default = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $field, 'option' );
	if ( null === $value || '' === $value ) {
		return $default;
	}
	return (bool) $value;
}

/**
 * Header promo strip message.
 *
 * @return string
 */
function msrproducts_get_promo_strip_text() {
	return msrproducts_get_option_string(
		'promo_strip_text',
		__( 'Portfolio case studies, technical specs, and collaboration-ready project pages.', 'msrproducts' )
	);
}

/**
 * Header promo strip CTA label.
 *
 * @return string
 */
function msrproducts_get_promo_strip_cta() {
	return msrproducts_get_option_string(
		'promo_strip_cta',
		__( 'Explore projects', 'msrproducts' )
	);
}

/**
 * Hero primary CTA label (home showcase).
 *
 * @return string
 */
function msrproducts_get_hero_primary_cta() {
	return msrproducts_get_option_string(
		'hero_primary_cta',
		__( 'Explore projects', 'msrproducts' )
	);
}

/**
 * Hero secondary CTA label (home showcase).
 *
 * @return string
 */
function msrproducts_get_hero_secondary_cta() {
	return msrproducts_get_option_string(
		'hero_secondary_cta',
		__( 'Start a conversation', 'msrproducts' )
	);
}

/**
 * Featured projects grid section title.
 *
 * @return string
 */
function msrproducts_get_featured_grid_title() {
	return msrproducts_get_option_string(
		'featured_grid_title',
		__( 'Featured project snapshots', 'msrproducts' )
	);
}

/**
 * Featured projects grid link label.
 *
 * @return string
 */
function msrproducts_get_featured_grid_link_label() {
	return msrproducts_get_option_string(
		'featured_grid_link_label',
		__( 'View all projects', 'msrproducts' )
	);
}

/**
 * Category tiles section title.
 *
 * @return string
 */
function msrproducts_get_category_tiles_title() {
	return msrproducts_get_option_string(
		'category_tiles_title',
		__( 'Browse by category', 'msrproducts' )
	);
}

/**
 * Category tiles link label.
 *
 * @return string
 */
function msrproducts_get_category_tiles_link_label() {
	return msrproducts_get_option_string(
		'category_tiles_link_label',
		__( 'Explore categories', 'msrproducts' )
	);
}

/**
 * Home FAQ band title.
 *
 * @return string
 */
function msrproducts_get_home_faq_section_title() {
	return msrproducts_get_option_string(
		'home_faq_section_title',
		__( 'Common questions', 'msrproducts' )
	);
}

/**
 * Partners showcase band title.
 *
 * @return string
 */
function msrproducts_get_partners_band_title() {
	return msrproducts_get_option_string(
		'partners_band_title',
		__( 'Trusted by brands and retailers', 'msrproducts' )
	);
}

/**
 * Partners showcase band lead.
 *
 * @return string
 */
function msrproducts_get_partners_band_lead() {
	return msrproducts_get_option_string(
		'partners_band_lead',
		__( 'Selected collaborators across product, retail, and digital design programs.', 'msrproducts' )
	);
}

/**
 * Catalog filter sidebar title.
 *
 * @return string
 */
function msrproducts_get_catalog_filter_title() {
	return msrproducts_get_option_string(
		'catalog_filter_title',
		__( 'Categories', 'msrproducts' )
	);
}

/**
 * Catalog empty state message.
 *
 * @return string
 */
function msrproducts_get_catalog_empty_message() {
	return msrproducts_get_option_string(
		'catalog_empty_message',
		__( 'No projects found yet.', 'msrproducts' )
	);
}

/**
 * Catalog search placeholder.
 *
 * @return string
 */
function msrproducts_get_catalog_search_placeholder() {
	return msrproducts_get_option_string(
		'catalog_search_placeholder',
		__( 'Search project title, category, or keyword', 'msrproducts' )
	);
}

/**
 * Footer showcase-mode disclaimer.
 *
 * @return string
 */
function msrproducts_get_footer_showcase_note() {
	return msrproducts_get_option_string(
		'footer_showcase_note',
		__( 'Showcase mode active. No checkout is available.', 'msrproducts' )
	);
}

/**
 * Whether the footer showcase disclaimer is shown.
 *
 * @return bool
 */
function msrproducts_show_footer_showcase_note() {
	return msrproducts_get_option_bool( 'show_footer_showcase_note', true );
}

/**
 * Cookie consent banner message (HTML allowed in admin; stripped on output).
 *
 * @return string
 */
function msrproducts_get_cookie_consent_message() {
	return msrproducts_get_option_string(
		'cookie_consent_message',
		__( 'This site uses cookies for navigation and anonymized analytics.', 'msrproducts' )
	);
}

/**
 * Compare modal title.
 *
 * @return string
 */
function msrproducts_get_compare_modal_title() {
	return msrproducts_get_option_string(
		'compare_modal_title',
		__( 'Project compare list', 'msrproducts' )
	);
}

/**
 * Compare modal lead copy.
 *
 * @return string
 */
function msrproducts_get_compare_modal_lead() {
	return msrproducts_get_option_string(
		'compare_modal_lead',
		__( 'Select up to three projects, then open the comparison page.', 'msrproducts' )
	);
}

/**
 * Compare modal primary CTA label.
 *
 * @return string
 */
function msrproducts_get_compare_modal_cta() {
	return msrproducts_get_option_string(
		'compare_modal_cta',
		__( 'Open compare page', 'msrproducts' )
	);
}

/**
 * Home meta description fallback.
 *
 * @return string
 */
function msrproducts_get_seo_home_description() {
	return msrproducts_get_option_string(
		'seo_home_description',
		__( 'MSR Products — portfolio-first WooCommerce showcase for collaboration-ready project pages, technical specs, and inquiry-led catalog browsing.', 'msrproducts' )
	);
}

/**
 * Shop archive meta description fallback.
 *
 * @return string
 */
function msrproducts_get_seo_shop_description() {
	return msrproducts_get_option_string(
		'seo_shop_description',
		__( 'Browse MSR Products project catalog — filter by category, compare approaches, and request collaboration details.', 'msrproducts' )
	);
}

/**
 * Search meta description fallback.
 *
 * @return string
 */
function msrproducts_get_seo_search_description() {
	return msrproducts_get_option_string(
		'seo_search_description',
		__( 'Search MSR Products portfolio projects by title, category, or keyword.', 'msrproducts' )
	);
}

/**
 * Programme outbound URL from options (ACF).
 *
 * @param string $slug hub|awards|seminars|publishing.
 * @return string
 */
function msrproducts_get_programme_url_option( $slug ) {
	$acf_fields = array(
		'hub'        => 'msr_programme_hub_url',
		'awards'     => 'msr_programme_awards_url',
		'seminars'   => 'msr_programme_seminars_url',
		'publishing' => 'msr_programme_publishing_url',
	);

	if ( ! isset( $acf_fields[ $slug ] ) ) {
		return '';
	}

	$url = msrproducts_get_option_string( $acf_fields[ $slug ], '' );
	if ( '' !== $url ) {
		return esc_url_raw( $url );
	}

	return '';
}

<?php
/**
 * Register ACF fields for homepage showcase sections.
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'msrproducts_register_homepage_acf_fields' ) ) {
	/**
	 * Register homepage ACF fields on static front page.
	 *
	 * @return void
	 */
	function msrproducts_register_homepage_acf_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'                   => 'group_msr_homepage_showcase',
				'title'                 => 'Homepage Showcase',
				'fields'                => array(
					array(
						'key'   => 'field_msr_home_tab_hero',
						'label' => 'Hero',
						'type'  => 'tab',
					),
					array(
						'key'           => 'field_msr_home_hero_eyebrow',
						'label'         => 'Hero Eyebrow',
						'name'          => 'home_hero_eyebrow',
						'type'          => 'text',
						'default_value' => 'Portfolio-first product showcase',
					),
					array(
						'key'           => 'field_msr_home_hero_title',
						'label'         => 'Hero Title',
						'name'          => 'home_hero_title',
						'type'          => 'text',
						'default_value' => 'Modern product experiences with technical depth and measurable outcomes.',
					),
					array(
						'key'           => 'field_msr_home_hero_copy',
						'label'         => 'Hero Description',
						'name'          => 'home_hero_copy',
						'type'          => 'textarea',
						'rows'          => 3,
						'default_value' => 'Browse project work, compare approaches, and request collaboration details directly from each product page.',
					),
					array(
						'key'           => 'field_msr_home_tab_process',
						'label'         => 'Process',
						'type'          => 'tab',
						'placement'     => 'top',
					),
					array(
						'key'           => 'field_msr_home_process_title',
						'label'         => 'Process Section Title',
						'name'          => 'home_process_title',
						'type'          => 'text',
						'default_value' => 'How collaboration works',
					),
					array(
						'key'           => 'field_msr_home_process_1_icon',
						'label'         => 'Process 1 Icon Class',
						'name'          => 'home_process_1_icon',
						'type'          => 'text',
						'default_value' => 'fa-solid fa-magnifying-glass',
					),
					array(
						'key'           => 'field_msr_home_process_1_title',
						'label'         => 'Process 1 Title',
						'name'          => 'home_process_1_title',
						'type'          => 'text',
						'default_value' => '1. Discover',
					),
					array(
						'key'           => 'field_msr_home_process_1_copy',
						'label'         => 'Process 1 Description',
						'name'          => 'home_process_1_copy',
						'type'          => 'textarea',
						'rows'          => 2,
						'default_value' => 'Review portfolio projects, technical blueprints, and business impact summaries.',
					),
					array(
						'key'           => 'field_msr_home_process_2_icon',
						'label'         => 'Process 2 Icon Class',
						'name'          => 'home_process_2_icon',
						'type'          => 'text',
						'default_value' => 'fa-solid fa-pen-ruler',
					),
					array(
						'key'           => 'field_msr_home_process_2_title',
						'label'         => 'Process 2 Title',
						'name'          => 'home_process_2_title',
						'type'          => 'text',
						'default_value' => '2. Define',
					),
					array(
						'key'           => 'field_msr_home_process_2_copy',
						'label'         => 'Process 2 Description',
						'name'          => 'home_process_2_copy',
						'type'          => 'textarea',
						'rows'          => 2,
						'default_value' => 'Align scope, constraints, and timeline with a practical delivery roadmap.',
					),
					array(
						'key'           => 'field_msr_home_process_3_icon',
						'label'         => 'Process 3 Icon Class',
						'name'          => 'home_process_3_icon',
						'type'          => 'text',
						'default_value' => 'fa-solid fa-rocket',
					),
					array(
						'key'           => 'field_msr_home_process_3_title',
						'label'         => 'Process 3 Title',
						'name'          => 'home_process_3_title',
						'type'          => 'text',
						'default_value' => '3. Deliver',
					),
					array(
						'key'           => 'field_msr_home_process_3_copy',
						'label'         => 'Process 3 Description',
						'name'          => 'home_process_3_copy',
						'type'          => 'textarea',
						'rows'          => 2,
						'default_value' => 'Ship polished outcomes with reusable assets, handover notes, and support options.',
					),
					array(
						'key'           => 'field_msr_home_tab_editorial',
						'label'         => 'Editorial Banners',
						'type'          => 'tab',
						'placement'     => 'top',
					),
					array(
						'key'           => 'field_msr_home_editorial_title',
						'label'         => 'Editorial Section Title',
						'name'          => 'home_editorial_title',
						'type'          => 'text',
						'default_value' => 'Design stories and collaboration highlights',
					),
					array(
						'key'           => 'field_msr_home_editorial_1_image',
						'label'         => 'Editorial Card 1 Image',
						'name'          => 'home_editorial_1_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
					array(
						'key'           => 'field_msr_home_editorial_1_title',
						'label'         => 'Editorial Card 1 Title',
						'name'          => 'home_editorial_1_title',
						'type'          => 'text',
						'default_value' => 'Case Study Spotlight',
					),
					array(
						'key'           => 'field_msr_home_editorial_1_copy',
						'label'         => 'Editorial Card 1 Description',
						'name'          => 'home_editorial_1_copy',
						'type'          => 'textarea',
						'rows'          => 2,
						'default_value' => 'From problem framing to implementation impact, every project page includes practical context for technical and design decisions.',
					),
					array(
						'key'           => 'field_msr_home_editorial_1_label',
						'label'         => 'Editorial Card 1 Link Label',
						'name'          => 'home_editorial_1_label',
						'type'          => 'text',
						'default_value' => 'Read project stories',
					),
					array(
						'key'           => 'field_msr_home_editorial_1_url',
						'label'         => 'Editorial Card 1 Link URL',
						'name'          => 'home_editorial_1_url',
						'type'          => 'url',
					),
					array(
						'key'           => 'field_msr_home_editorial_2_image',
						'label'         => 'Editorial Card 2 Image',
						'name'          => 'home_editorial_2_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
					array(
						'key'           => 'field_msr_home_editorial_2_title',
						'label'         => 'Editorial Card 2 Title',
						'name'          => 'home_editorial_2_title',
						'type'          => 'text',
						'default_value' => 'Partnership Programs',
					),
					array(
						'key'           => 'field_msr_home_editorial_2_copy',
						'label'         => 'Editorial Card 2 Description',
						'name'          => 'home_editorial_2_copy',
						'type'          => 'textarea',
						'rows'          => 2,
						'default_value' => 'Collaborate on product UX, front-end delivery, and scalable content systems with a portfolio-first workflow.',
					),
					array(
						'key'           => 'field_msr_home_editorial_2_label',
						'label'         => 'Editorial Card 2 Link Label',
						'name'          => 'home_editorial_2_label',
						'type'          => 'text',
						'default_value' => 'Discuss partnerships',
					),
					array(
						'key'           => 'field_msr_home_editorial_2_url',
						'label'         => 'Editorial Card 2 Link URL',
						'name'          => 'home_editorial_2_url',
						'type'          => 'url',
					),
					array(
						'key'           => 'field_msr_home_tab_trust',
						'label'         => 'Trust Cards',
						'type'          => 'tab',
						'placement'     => 'top',
					),
					array(
						'key'           => 'field_msr_home_trust_1_icon',
						'label'         => 'Trust 1 Icon Class',
						'name'          => 'home_trust_1_icon',
						'type'          => 'text',
						'default_value' => 'fa-regular fa-clock',
					),
					array(
						'key'           => 'field_msr_home_trust_1_stat',
						'label'         => 'Trust 1 Stat',
						'name'          => 'home_trust_1_stat',
						'type'          => 'text',
						'default_value' => '48h',
					),
					array(
						'key'           => 'field_msr_home_trust_1_label',
						'label'         => 'Trust 1 Label',
						'name'          => 'home_trust_1_label',
						'type'          => 'text',
						'default_value' => 'Average response time',
					),
					array(
						'key'           => 'field_msr_home_trust_2_icon',
						'label'         => 'Trust 2 Icon Class',
						'name'          => 'home_trust_2_icon',
						'type'          => 'text',
						'default_value' => 'fa-solid fa-briefcase',
					),
					array(
						'key'           => 'field_msr_home_trust_2_stat',
						'label'         => 'Trust 2 Stat',
						'name'          => 'home_trust_2_stat',
						'type'          => 'text',
						'default_value' => 'Portfolio',
					),
					array(
						'key'           => 'field_msr_home_trust_2_label',
						'label'         => 'Trust 2 Label',
						'name'          => 'home_trust_2_label',
						'type'          => 'text',
						'default_value' => 'No checkout, collaboration-only',
					),
					array(
						'key'           => 'field_msr_home_trust_3_icon',
						'label'         => 'Trust 3 Icon Class',
						'name'          => 'home_trust_3_icon',
						'type'          => 'text',
						'default_value' => 'fa-solid fa-diagram-project',
					),
					array(
						'key'           => 'field_msr_home_trust_3_stat',
						'label'         => 'Trust 3 Stat',
						'name'          => 'home_trust_3_stat',
						'type'          => 'text',
						'default_value' => 'End-to-end',
					),
					array(
						'key'           => 'field_msr_home_trust_3_label',
						'label'         => 'Trust 3 Label',
						'name'          => 'home_trust_3_label',
						'type'          => 'text',
						'default_value' => 'Research, design, implementation',
					),
					array(
						'key'           => 'field_msr_home_trust_4_icon',
						'label'         => 'Trust 4 Icon Class',
						'name'          => 'home_trust_4_icon',
						'type'          => 'text',
						'default_value' => 'fa-solid fa-universal-access',
					),
					array(
						'key'           => 'field_msr_home_trust_4_stat',
						'label'         => 'Trust 4 Stat',
						'name'          => 'home_trust_4_stat',
						'type'          => 'text',
						'default_value' => 'Accessible',
					),
					array(
						'key'           => 'field_msr_home_trust_4_label',
						'label'         => 'Trust 4 Label',
						'name'          => 'home_trust_4_label',
						'type'          => 'text',
						'default_value' => 'Mobile-first and WCAG-focused delivery',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'page_type',
							'operator' => '==',
							'value'    => 'front_page',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
				'description'           => 'Editable homepage content blocks used by the home-showcase component.',
			)
		);
	}
}
add_action( 'acf/init', 'msrproducts_register_homepage_acf_fields' );

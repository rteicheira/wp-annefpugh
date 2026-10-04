<?php
/**
 * Customizer: colors, images, homepage copy, crisis wording, page links.
 * (Therapist and practice details are on the Practice Info admin screen.)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_sanitize_checkbox( $value ) {
	return (bool) $value;
}

function annefpugh_sanitize_opacity( $value ) {
	return max( 0, min( 90, absint( $value ) ) );
}

function annefpugh_sanitize_services_count( $value ) {
	return max( 1, min( 12, absint( $value ) ) );
}

/**
 * Register one setting + control. $control_args['type'] selects the
 * control: color, media, dropdown-pages, or any core input type.
 */
function annefpugh_add_field( WP_Customize_Manager $wp_customize, $id, $section, $sanitize, $control_args ) {
	$defaults = annefpugh_defaults();

	$wp_customize->add_setting(
		$id,
		array(
			'default'           => isset( $defaults[ $id ] ) ? $defaults[ $id ] : '',
			'sanitize_callback' => $sanitize,
		)
	);

	$control_args['section'] = $section;
	$type                    = isset( $control_args['type'] ) ? $control_args['type'] : 'text';

	if ( 'color' === $type ) {
		unset( $control_args['type'] );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $control_args ) );
	} elseif ( 'media' === $type ) {
		unset( $control_args['type'] );
		$control_args['mime_type'] = 'image';
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, $control_args ) );
	} else {
		$wp_customize->add_control( $id, $control_args );
	}
}

function annefpugh_customize_register( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_panel(
		'annefpugh_options',
		array(
			'title'       => __( 'Practice Theme Options', 'annefpugh' ),
			'description' => __( 'Your name, photo, phone, address, fees, and license are edited under "Practice Info" in the dashboard menu.', 'annefpugh' ),
			'priority'    => 25,
		)
	);

	$sections = array(
		'annefpugh_hero'      => __( 'Homepage: Hero', 'annefpugh' ),
		'annefpugh_homepage'  => __( 'Homepage: Sections', 'annefpugh' ),
		'annefpugh_safety'    => __( 'Crisis & Safety', 'annefpugh' ),
		'annefpugh_pages'     => __( 'Page Links', 'annefpugh' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'annefpugh_options' ) );
	}

	// Colors: core "Colors" section (it already holds the background color).
	$wp_customize->get_section( 'colors' )->description = __( 'Pick any colors you like. To keep the site readable for everyone (WCAG AA), text, links, and focus outlines are automatically darkened or lightened where needed so they always contrast with the background behind them.', 'annefpugh' );

	$colors = array(
		'color_primary'      => array( __( 'Primary (buttons, links)', 'annefpugh' ), __( 'Button text is set to white or black automatically. Links use a darker shade if this color is too light to read.', 'annefpugh' ) ),
		'color_secondary'    => array( __( 'Secondary accent', 'annefpugh' ), '' ),
		'color_text'         => array( __( 'Body text', 'annefpugh' ), '' ),
		'color_heading'      => array( __( 'Headings', 'annefpugh' ), '' ),
		'color_surface'      => array( __( 'Alternate section background', 'annefpugh' ), '' ),
		'color_header_bg'    => array( __( 'Header background', 'annefpugh' ), '' ),
		'color_card_bg'      => array( __( 'Card and form background', 'annefpugh' ), __( 'Service cards and the call-back form.', 'annefpugh' ) ),
		'color_footer_bg'    => array( __( 'Footer and crisis bar background', 'annefpugh' ), __( 'Text color is set automatically.', 'annefpugh' ) ),
		'hero_overlay_color' => array( __( 'Hero image overlay', 'annefpugh' ), '' ),
	);
	foreach ( $colors as $id => $labels ) {
		annefpugh_add_field(
			$wp_customize,
			$id,
			'colors',
			'sanitize_hex_color',
			array(
				'type'        => 'color',
				'label'       => $labels[0],
				'description' => $labels[1],
			)
		);
	}
	annefpugh_add_field(
		$wp_customize,
		'hero_overlay_opacity',
		'colors',
		'annefpugh_sanitize_opacity',
		array(
			'type'        => 'range',
			'label'       => __( 'Hero overlay strength (%)', 'annefpugh' ),
			'description' => __( 'Darkens the hero image so the heading stays readable. 40% or more is recommended.', 'annefpugh' ),
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 90,
				'step' => 5,
			),
		)
	);

	// Hero.
	annefpugh_add_field( $wp_customize, 'hero_image', 'annefpugh_hero', 'absint', array( 'type' => 'media', 'label' => __( 'Hero image', 'annefpugh' ), 'description' => __( 'Wide landscape photo, at least 1920×1080. Leave empty for a solid color.', 'annefpugh' ) ) );
	annefpugh_add_field( $wp_customize, 'hero_heading', 'annefpugh_hero', 'sanitize_text_field', array( 'label' => __( 'Heading', 'annefpugh' ) ) );
	annefpugh_add_field( $wp_customize, 'hero_subheading', 'annefpugh_hero', 'sanitize_textarea_field', array( 'type' => 'textarea', 'label' => __( 'Subheading', 'annefpugh' ) ) );
	annefpugh_add_field( $wp_customize, 'hero_button_text', 'annefpugh_hero', 'sanitize_text_field', array( 'label' => __( 'Button text (links to Contact page)', 'annefpugh' ) ) );

	// Therapist and practice details live on the Practice Info admin screen (inc/admin-practice-info.php).

	// Homepage sections.
	annefpugh_add_field( $wp_customize, 'services_heading', 'annefpugh_homepage', 'sanitize_text_field', array( 'label' => __( 'Services heading', 'annefpugh' ) ) );
	annefpugh_add_field( $wp_customize, 'services_intro', 'annefpugh_homepage', 'sanitize_textarea_field', array( 'type' => 'textarea', 'label' => __( 'Services introduction', 'annefpugh' ) ) );
	annefpugh_add_field( $wp_customize, 'services_count', 'annefpugh_homepage', 'annefpugh_sanitize_services_count', array( 'type' => 'number', 'label' => __( 'Number of services on the homepage', 'annefpugh' ), 'input_attrs' => array( 'min' => 1, 'max' => 12 ) ) );
	annefpugh_add_field( $wp_customize, 'cta_heading', 'annefpugh_homepage', 'sanitize_text_field', array( 'label' => __( 'Closing call-to-action heading', 'annefpugh' ) ) );
	annefpugh_add_field( $wp_customize, 'cta_text', 'annefpugh_homepage', 'sanitize_textarea_field', array( 'type' => 'textarea', 'label' => __( 'Closing call-to-action text', 'annefpugh' ) ) );

	// Crisis & safety.
	annefpugh_add_field(
		$wp_customize,
		'show_crisis_bar',
		'annefpugh_safety',
		'annefpugh_sanitize_checkbox',
		array(
			'type'        => 'checkbox',
			'label'       => __( 'Show the crisis bar at the top of every page', 'annefpugh' ),
			'description' => __( 'The crisis notice in the footer is always shown and can\'t be turned off.', 'annefpugh' ),
		)
	);

	$wp_customize->get_section( 'annefpugh_safety' )->description = __( 'Phone numbers you type here (like 988, 911, or (555) 123-4567) automatically become tap-to-call links. Text HOME to 741741 becomes a tap-to-text link. Any field left blank goes back to the standard wording, so crisis information can\'t disappear by accident.', 'annefpugh' );

	$crisis_fields = array(
		'crisis_bar_text'     => array( 'text', __( 'Crisis bar text (top of page)', 'annefpugh' ), '' ),
		'crisis_heading'      => array( 'text', __( 'Footer crisis heading', 'annefpugh' ), '' ),
		'crisis_items'        => array( 'textarea', __( 'Footer crisis resources', 'annefpugh' ), __( 'One resource per line. Each line becomes a bullet point.', 'annefpugh' ) ),
		'crisis_disclaimer'   => array( 'text', __( 'Footer note', 'annefpugh' ), __( 'Shown below the resources.', 'annefpugh' ) ),
		'contact_form_notice' => array( 'textarea', __( 'Contact form safety note', 'annefpugh' ), __( 'Shown above both forms: the Contact page form and the "Request a call back" form on the homepage and Services page.', 'annefpugh' ) ),
	);
	foreach ( $crisis_fields as $id => $field ) {
		annefpugh_add_field(
			$wp_customize,
			$id,
			'annefpugh_safety',
			'textarea' === $field[0] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			array(
				'type'        => $field[0],
				'label'       => $field[1],
				'description' => $field[2],
			)
		);
	}

	// Page links.
	$page_links = array(
		'page_about'    => __( 'About page', 'annefpugh' ),
		'page_services' => __( 'Services page', 'annefpugh' ),
		'page_contact'  => __( 'Contact page', 'annefpugh' ),
	);
	foreach ( $page_links as $id => $label ) {
		annefpugh_add_field( $wp_customize, $id, 'annefpugh_pages', 'absint', array( 'type' => 'dropdown-pages', 'label' => $label ) );
	}
}
add_action( 'customize_register', 'annefpugh_customize_register' );

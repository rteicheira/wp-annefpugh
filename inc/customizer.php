<?php
/**
 * Customizer: colors, images, practice details, homepage copy, page links.
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
	$contrast_note = __( 'Button and footer text colors are chosen automatically for readable contrast.', 'annefpugh' );
	$colors        = array(
		'color_primary'      => array( __( 'Primary (buttons, links)', 'annefpugh' ), $contrast_note ),
		'color_secondary'    => array( __( 'Secondary accent', 'annefpugh' ), '' ),
		'color_text'         => array( __( 'Body text', 'annefpugh' ), __( 'Keep this dark against your background for accessibility (4.5:1 contrast).', 'annefpugh' ) ),
		'color_heading'      => array( __( 'Headings', 'annefpugh' ), '' ),
		'color_surface'      => array( __( 'Alternate section background', 'annefpugh' ), '' ),
		'color_footer_bg'    => array( __( 'Footer background', 'annefpugh' ), $contrast_note ),
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
			'label'       => __( 'Show the 988/911 crisis bar at the top of every page', 'annefpugh' ),
			'description' => __( 'The crisis notice in the footer is always shown and can\'t be turned off.', 'annefpugh' ),
		)
	);

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

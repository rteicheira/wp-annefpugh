<?php
/**
 * Customizer settings for practice contact info.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_customize_register( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section(
		'annefpugh_practice_info',
		array(
			'title'    => __( 'Practice Info', 'annefpugh' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'annefpugh_phone'   => array(
			'label'             => __( 'Phone', 'annefpugh' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		'annefpugh_email'   => array(
			'label'             => __( 'Contact Email', 'annefpugh' ),
			'sanitize_callback' => 'sanitize_email',
		),
		'annefpugh_address' => array(
			'label'             => __( 'Office Address', 'annefpugh' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		'annefpugh_hours'   => array(
			'label'             => __( 'Office Hours', 'annefpugh' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		'annefpugh_service_area' => array(
			'label'             => __( 'Service Area (shown in hero)', 'annefpugh' ),
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => __( 'Now seeing clients in Berkeley, San Francisco & the East Bay — in person and via telehealth.', 'annefpugh' ),
		),
		'annefpugh_meta_description' => array(
			'label'             => __( 'Homepage Meta Description (SEO, ~155 characters)', 'annefpugh' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		'annefpugh_psychology_today_url' => array(
			'label'             => __( 'Psychology Today Profile URL', 'annefpugh' ),
			'sanitize_callback' => 'esc_url_raw',
		),
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => isset( $field['default'] ) ? $field['default'] : '',
				'sanitize_callback' => $field['sanitize_callback'],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'section' => 'annefpugh_practice_info',
				'label'   => $field['label'],
				'type'    => 'text',
			)
		);
	}

	// Homepage images.
	$wp_customize->add_section(
		'annefpugh_homepage_images',
		array(
			'title'    => __( 'Homepage Images', 'annefpugh' ),
			'priority' => 31,
		)
	);

	$wp_customize->add_setting(
		'annefpugh_hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'annefpugh_hero_image',
			array(
				'section'     => 'annefpugh_homepage_images',
				'label'       => __( 'Splash Image (large, top of homepage)', 'annefpugh' ),
				'description' => __( 'A wide, bright California landscape or office photo works well here. Falls back to a plain color if left empty.', 'annefpugh' ),
			)
		)
	);
	$wp_customize->add_setting(
		'annefpugh_hero_image_alt',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'annefpugh_hero_image_alt',
		array(
			'section' => 'annefpugh_homepage_images',
			'label'   => __( 'Splash Image alt text (for screen readers)', 'annefpugh' ),
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'annefpugh_therapist_photo',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'annefpugh_therapist_photo',
			array(
				'section'     => 'annefpugh_homepage_images',
				'label'       => __( 'Therapist Photo', 'annefpugh' ),
				'description' => __( 'A warm, approachable headshot. Shows a placeholder until one is added.', 'annefpugh' ),
			)
		)
	);
	$wp_customize->add_setting(
		'annefpugh_therapist_photo_alt',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'annefpugh_therapist_photo_alt',
		array(
			'section' => 'annefpugh_homepage_images',
			'label'   => __( 'Therapist Photo alt text (for screen readers)', 'annefpugh' ),
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'annefpugh_customize_register' );

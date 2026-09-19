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
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
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
}
add_action( 'customize_register', 'annefpugh_customize_register' );

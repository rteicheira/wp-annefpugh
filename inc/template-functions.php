<?php
/**
 * Defaults and small helpers shared across templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Every Customizer setting's default, in one place.
 */
function annefpugh_defaults() {
	return array(
		// Colors.
		'color_primary'        => '#1d5fa8',
		'color_secondary'      => '#4f5e1f',
		'color_text'           => '#1b2a4a',
		'color_heading'        => '#1b2a4a',
		'color_surface'        => '#f7f4ec',
		'color_header_bg'      => '#ffffff',
		'color_card_bg'        => '#ffffff',
		'color_footer_bg'      => '#1b2a4a',
		'hero_overlay_color'   => '#1b2a4a',
		'hero_overlay_opacity' => 45,

		// Hero.
		'hero_image'           => 0,
		'hero_heading'         => __( 'Warm, practical support for life\'s hardest seasons', 'annefpugh' ),
		'hero_subheading'      => __( 'Individual therapy for adults — in person and via telehealth.', 'annefpugh' ),
		'hero_button_text'     => __( 'Request a free consultation', 'annefpugh' ),

		// Therapist.
		'therapist_name'        => '',
		'therapist_credentials' => '',
		'therapist_photo'       => 0,
		'therapist_intro'       => __( 'Write a short, welcoming introduction here: who you work with, how you work, and what clients can expect from a first session.', 'annefpugh' ),

		// Practice info.
		'phone'                => '',
		'email'                => '',
		'address'              => '',
		'map_url'              => '',
		'session_format'       => __( 'In person and telehealth', 'annefpugh' ),
		'hours'                => '',
		'fees'                 => '',
		'insurance'            => '',
		'license'              => '',

		// Homepage sections.
		'services_heading'     => __( 'How I can help', 'annefpugh' ),
		'services_intro'       => '',
		'services_count'       => 6,
		'cta_heading'          => __( 'Ready to take the first step?', 'annefpugh' ),
		'cta_text'             => __( 'Reach out for a free 15-minute consultation to see whether we\'re a good fit.', 'annefpugh' ),

		// Crisis & safety.
		'show_crisis_bar'      => true,
		'crisis_bar_text'      => __( 'In crisis? Call or text 988, or call 911 in an emergency.', 'annefpugh' ),
		'crisis_heading'       => __( 'If you are in crisis', 'annefpugh' ),
		'crisis_items'         => implode(
			"\n",
			array(
				__( 'Call or text 988 — the Suicide & Crisis Lifeline, free and available 24/7.', 'annefpugh' ),
				__( 'If you or someone else is in immediate danger, call 911 or go to the nearest emergency room.', 'annefpugh' ),
				__( 'Prefer texting? Text HOME to 741741 (Crisis Text Line).', 'annefpugh' ),
			)
		),
		'crisis_disclaimer'    => __( 'This website, email, and contact form are not monitored for emergencies.', 'annefpugh' ),
		'contact_form_notice'  => __( 'Please don\'t include sensitive health details in this form — we can discuss those privately. This form is not monitored for emergencies; if you are in crisis, call or text 988.', 'annefpugh' ),

		// Page links.
		'page_about'           => 0,
		'page_services'        => 0,
		'page_contact'         => 0,
	);
}

/**
 * get_theme_mod() with the theme's own default applied.
 */
function annefpugh_mod( $key ) {
	$defaults = annefpugh_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * A text setting that must never render empty (safety wording): a cleared
 * field falls back to the theme's default instead of disappearing.
 */
function annefpugh_required_text( $key ) {
	$value    = trim( (string) annefpugh_mod( $key ) );
	$defaults = annefpugh_defaults();
	return '' !== $value ? $value : $defaults[ $key ];
}

/**
 * Escape plain text and turn phone numbers into tap-to-call links:
 * 988, 911, full US numbers like (555) 123-4567, and 741741 (as a text link).
 */
function annefpugh_linkify_phone_numbers( $text ) {
	return preg_replace_callback(
		'/(?<![\w$])(?:(?:\+?1[\s.\-]?)?\(?\d{3}\)?[\s.\-]?\d{3}[\s.\-]?\d{4}|988|911|741741)(?![\w])/',
		function ( $match ) {
			$digits = preg_replace( '/[^\d+]/', '', $match[0] );
			$href   = '741741' === $digits ? 'sms:741741' : 'tel:' . $digits;
			return '<a href="' . esc_attr( $href ) . '">' . $match[0] . '</a>';
		},
		esc_html( $text )
	);
}

function annefpugh_therapist_name() {
	$name = annefpugh_mod( 'therapist_name' );
	return $name ? $name : get_bloginfo( 'name' );
}

/**
 * Permalink for a page chosen under Customizer → Page Links, or '' if unset.
 */
function annefpugh_page_url( $key ) {
	$page_id = absint( annefpugh_mod( $key ) );
	if ( ! $page_id || 'publish' !== get_post_status( $page_id ) ) {
		return '';
	}
	return get_permalink( $page_id );
}

function annefpugh_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

function annefpugh_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return array( 0, 0, 0 );
	}
	return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
}

/**
 * WCAG relative luminance of a hex color.
 */
function annefpugh_luminance( $hex ) {
	$channels = array_map(
		function ( $c ) {
			$c = $c / 255;
			return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		},
		annefpugh_hex_to_rgb( $hex )
	);
	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * WCAG contrast ratio between two hex colors (1–21).
 */
function annefpugh_contrast( $a, $b ) {
	$la = annefpugh_luminance( $a );
	$lb = annefpugh_luminance( $b );
	return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}

/**
 * Pick white or near-black text for a background, whichever contrasts
 * more — keeps buttons and the footer legible whatever colors are chosen.
 */
function annefpugh_readable_text_color( $background ) {
	return annefpugh_contrast( $background, '#ffffff' ) >= annefpugh_contrast( $background, '#111111' ) ? '#ffffff' : '#111111';
}

/**
 * Mix $hex toward $toward by $amount (0–1).
 */
function annefpugh_mix( $hex, $toward, $amount ) {
	$from = annefpugh_hex_to_rgb( $hex );
	$to   = annefpugh_hex_to_rgb( $toward );
	$rgb  = array();
	foreach ( array( 0, 1, 2 ) as $i ) {
		$rgb[] = (int) round( $from[ $i ] + ( $to[ $i ] - $from[ $i ] ) * $amount );
	}
	return vsprintf( '#%02x%02x%02x', $rgb );
}

/**
 * Return $color unchanged if it reaches $ratio contrast against every
 * background; otherwise the closest darker (or lighter) shade that does.
 * Keeps the chosen hue where possible, so brand colors stay recognizable.
 */
function annefpugh_accessible_color( $color, array $backgrounds, $ratio = 4.5 ) {
	$passes = function ( $candidate ) use ( $backgrounds, $ratio ) {
		foreach ( $backgrounds as $bg ) {
			if ( annefpugh_contrast( $candidate, $bg ) < $ratio ) {
				return false;
			}
		}
		return true;
	};

	if ( $passes( $color ) ) {
		return $color;
	}

	$avg_luminance = array_sum( array_map( 'annefpugh_luminance', $backgrounds ) ) / count( $backgrounds );
	$directions    = $avg_luminance > 0.18 ? array( '#000000', '#ffffff' ) : array( '#ffffff', '#000000' );

	foreach ( $directions as $toward ) {
		for ( $step = 0.05; $step <= 1.0001; $step += 0.05 ) {
			$candidate = annefpugh_mix( $color, $toward, $step );
			if ( $passes( $candidate ) ) {
				return $candidate;
			}
		}
	}

	return annefpugh_readable_text_color( $backgrounds[0] );
}

/**
 * Visitor IP for rate limiting. Uses Cloudflare's CF-Connecting-IP only
 * when the site opts in (the header is spoofable when not behind Cloudflare):
 * add_filter( 'annefpugh_trust_cloudflare_ip', '__return_true' );
 */
function annefpugh_client_ip() {
	if ( apply_filters( 'annefpugh_trust_cloudflare_ip', false ) && ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		$cf_ip = filter_var( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ), FILTER_VALIDATE_IP ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated by filter_var.
		if ( $cf_ip ) {
			return $cf_ip;
		}
	}
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated by filter_var.
	return $ip ? $ip : '0.0.0.0';
}

/**
 * Cache-busting version for a theme asset: its modification time, so
 * browsers always pick up changes without bumping a version by hand.
 */
function annefpugh_asset_version( $relative_path ) {
	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : ANNEFPUGH_VERSION;
}

/**
 * Output an attachment image, or nothing when no image is set.
 */
function annefpugh_image( $attachment_id, $size, $attr = array() ) {
	$attachment_id = absint( $attachment_id );
	if ( ! $attachment_id ) {
		return '';
	}
	return wp_get_attachment_image( $attachment_id, $size, false, $attr );
}

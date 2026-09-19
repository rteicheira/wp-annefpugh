<?php
/**
 * AnneFPugh Therapy theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNEFPUGH_VERSION', '0.1.0' );

/**
 * Theme support and nav menus.
 */
function annefpugh_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'annefpugh' ),
			'footer'  => __( 'Footer Menu', 'annefpugh' ),
		)
	);
}
add_action( 'after_setup_theme', 'annefpugh_setup' );

/**
 * Enqueue styles and scripts.
 */
function annefpugh_assets() {
	wp_enqueue_style( 'annefpugh-main', get_template_directory_uri() . '/css/main.css', array(), ANNEFPUGH_VERSION );
	wp_enqueue_script( 'annefpugh-main', get_template_directory_uri() . '/js/main.js', array(), ANNEFPUGH_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'annefpugh_assets' );

/**
 * No blog on this site — turn comments off entirely to shrink the
 * spam/attack surface (site has no post type that needs them).
 */
function annefpugh_disable_comments_support() {
	remove_post_type_support( 'page', 'comments' );
	remove_post_type_support( 'post', 'comments' );
}
add_action( 'init', 'annefpugh_disable_comments_support', 100 );

function annefpugh_disable_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'annefpugh_disable_comments_admin_menu' );

add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/contact-form.php';
require get_template_directory() . '/inc/seo.php';

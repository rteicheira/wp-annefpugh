<?php
/**
 * Theme supports, menus, image sizes, assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_setup() {
	load_theme_textdomain( 'annefpugh', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ffffff',
		)
	);

	add_image_size( 'annefpugh-hero', 1920, 1080, true );
	add_image_size( 'annefpugh-card', 720, 480, true );
	add_image_size( 'annefpugh-portrait', 640, 800, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'annefpugh' ),
			'footer'  => __( 'Footer Quick Links', 'annefpugh' ),
			'legal'   => __( 'Footer Legal Links', 'annefpugh' ),
		)
	);
}
add_action( 'after_setup_theme', 'annefpugh_setup' );

function annefpugh_assets() {
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'annefpugh-main', $uri . '/assets/css/main.css', array(), annefpugh_asset_version( 'assets/css/main.css' ) );
	wp_enqueue_script( 'annefpugh-navigation', $uri . '/assets/js/navigation.js', array(), annefpugh_asset_version( 'assets/js/navigation.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	// Registered here, enqueued by template-parts/home/about.php (collapses a long bio).
	wp_register_script( 'annefpugh-about-bio', $uri . '/assets/js/about-bio.js', array(), annefpugh_asset_version( 'assets/js/about-bio.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	// Registered here, enqueued by template-parts/home/services.php ("Read more" overlays).
	wp_register_script( 'annefpugh-service-dialogs', $uri . '/assets/js/service-dialogs.js', array(), annefpugh_asset_version( 'assets/js/service-dialogs.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	// Registered here, enqueued by template-parts/home/cta.php only on pages that show it.
	wp_register_script( 'annefpugh-callback-toggle', $uri . '/assets/js/callback-toggle.js', array(), annefpugh_asset_version( 'assets/js/callback-toggle.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
}
add_action( 'wp_enqueue_scripts', 'annefpugh_assets' );

/**
 * Menu links to a section of a page (e.g. "/#practice-info-heading") point
 * at part of a page, not a page. WordPress ignores the "#…" when deciding
 * the current item, so on the homepage every such link would be marked
 * active (and announced as "current page"). Undo that for anchor links.
 */
function annefpugh_is_anchor_menu_item( $item ) {
	return isset( $item->url ) && false !== strpos( $item->url, '#' ) && '#' !== substr( $item->url, -1 );
}

function annefpugh_anchor_menu_item_classes( $classes, $item ) {
	if ( ! annefpugh_is_anchor_menu_item( $item ) ) {
		return $classes;
	}
	$current = array( 'current-menu-item', 'current_page_item', 'current-menu-parent', 'current_page_parent', 'current-menu-ancestor', 'current_page_ancestor' );
	return array_values( array_diff( (array) $classes, $current ) );
}
add_filter( 'nav_menu_css_class', 'annefpugh_anchor_menu_item_classes', 10, 2 );

function annefpugh_anchor_menu_item_attributes( $atts, $item ) {
	if ( annefpugh_is_anchor_menu_item( $item ) ) {
		unset( $atts['aria-current'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'annefpugh_anchor_menu_item_attributes', 10, 2 );

/**
 * No blog on a single-provider site: turn comments off everywhere.
 */
function annefpugh_disable_comments() {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'annefpugh_disable_comments', 100 );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );

function annefpugh_remove_comments_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'annefpugh_remove_comments_menu' );

function annefpugh_remove_comments_admin_bar( WP_Admin_Bar $admin_bar ) {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'annefpugh_remove_comments_admin_bar', 999 );

// No comment feeds to advertise.
add_filter( 'feed_links_show_comments_feed', '__return_false' );
remove_action( 'wp_head', 'feed_links_extra', 3 );

<?php
/**
 * Comments are off everywhere — a single-provider practice site has no
 * blog, and an open comment form on a therapist's site invites spam and
 * people posting private health details in public.
 *
 * Covers every way in: theme templates, the comment form handler,
 * the REST API, XML-RPC (pingbacks and wp.newComment), feeds, the
 * Recent Comments widget, and the admin screens.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Closed everywhere
 * ---------------------------------------------------------------------- */

function annefpugh_remove_comment_support() {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'annefpugh_remove_comment_support', 100 );

// Every post/page reports comments and pings as closed (blocks wp-comments-post.php,
// REST, and XML-RPC submissions), and nothing existing is displayed.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );
add_filter( 'get_comments_number', '__return_zero', 20 );

// New content defaults to closed too (without changing the saved settings).
add_filter( 'pre_option_default_comment_status', fn() => 'closed' );
add_filter( 'pre_option_default_ping_status', fn() => 'closed' );
add_filter( 'pre_option_default_pingback_flag', '__return_zero' );

/* -------------------------------------------------------------------------
 * APIs and feeds
 * ---------------------------------------------------------------------- */

// REST API: no comment endpoints at all.
function annefpugh_remove_comment_endpoints( $endpoints ) {
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( $route, '/wp/v2/comments' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'annefpugh_remove_comment_endpoints' );

// XML-RPC: no pingbacks or comment methods.
function annefpugh_remove_comment_xmlrpc_methods( $methods ) {
	foreach ( array( 'pingback.ping', 'pingback.extensions.getPingbacks', 'wp.newComment', 'wp.getComment', 'wp.getComments', 'wp.editComment', 'wp.deleteComment', 'wp.getCommentCount', 'wp.getCommentStatusList' ) as $method ) {
		unset( $methods[ $method ] );
	}
	return $methods;
}
add_filter( 'xmlrpc_methods', 'annefpugh_remove_comment_xmlrpc_methods' );

function annefpugh_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'annefpugh_remove_pingback_header' );

// Comment feeds: don't advertise them, and send direct visits home.
add_filter( 'feed_links_show_comments_feed', '__return_false' );
remove_action( 'wp_head', 'feed_links_extra', 3 );

function annefpugh_redirect_comment_feeds() {
	if ( is_comment_feed() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'annefpugh_redirect_comment_feeds', 1 );

/* -------------------------------------------------------------------------
 * Widgets (comment blocks need no handling: with comments closed and none
 * displayed, they render nothing)
 * ---------------------------------------------------------------------- */

function annefpugh_unregister_comment_widget() {
	unregister_widget( 'WP_Widget_Recent_Comments' );
}
add_action( 'widgets_init', 'annefpugh_unregister_comment_widget', 20 );

/* -------------------------------------------------------------------------
 * Admin
 * ---------------------------------------------------------------------- */

function annefpugh_remove_comment_admin_menus() {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'annefpugh_remove_comment_admin_menus', 999 );

// Direct visits to the Comments or Discussion screens go to the Dashboard.
function annefpugh_redirect_comment_admin_screens() {
	global $pagenow;
	if ( in_array( $pagenow, array( 'edit-comments.php', 'comment.php', 'options-discussion.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'annefpugh_redirect_comment_admin_screens' );

function annefpugh_remove_comment_dashboard_widgets() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'annefpugh_remove_comment_dashboard_widgets' );

function annefpugh_remove_comments_admin_bar( WP_Admin_Bar $admin_bar ) {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'annefpugh_remove_comments_admin_bar', 999 );

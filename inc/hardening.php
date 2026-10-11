<?php
/**
 * Site hardening for a single-provider practice site with no blog.
 *
 * - XML-RPC is switched off entirely. It isn't used (no pingbacks, apps, or
 *   Jetpack), and its login methods would let bots guess passwords around the
 *   Turnstile/reCAPTCHA login check (Settings → Spam Protection).
 * - Usernames aren't published: no REST user list for visitors, no author
 *   archives or ?author= redirects, no author links in oEmbed data, no users
 *   sitemap, and the login page doesn't say whether a username exists.
 * - Blog-style pages a no-blog site doesn't need (search results, author,
 *   category, tag, and date archives) return a real 404, and post feeds send
 *   visitors to the homepage.
 */

defined( 'ABSPATH' ) || exit;


// ── XML-RPC: OFF ──────────────────────────────────────────────
add_filter( 'xmlrpc_enabled', '__return_false' );       // Login-based methods refuse to run.
add_filter( 'xmlrpc_methods', '__return_empty_array' ); // No methods at all (incl. via system.multicall).
remove_action( 'wp_head', 'rsd_link' );                 // Don't advertise the endpoint.

/**
 * Answer any request to xmlrpc.php with 403 before it's processed.
 */
function annefpugh_block_xmlrpc_requests() {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		status_header( 403 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo 'XML-RPC is disabled on this site.';
		exit;
	}
}
add_action( 'init', 'annefpugh_block_xmlrpc_requests', 0 );


// ── USERNAMES STAY PRIVATE ────────────────────────────────────
/**
 * No REST user routes for logged-out visitors. Logged-in editors keep them
 * (the block editor uses them for the author picker).
 */
function annefpugh_hide_rest_users( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'annefpugh_hide_rest_users' );

// oEmbed data would otherwise include the author's name and /author/<username>/ link.
function annefpugh_oembed_without_author( $data ) {
	unset( $data['author_name'], $data['author_url'] );
	return $data;
}
add_filter( 'oembed_response_data', 'annefpugh_oembed_without_author' );

// No users sitemap (it lists /author/<username>/ URLs).
function annefpugh_no_users_sitemap( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'annefpugh_no_users_sitemap', 10, 2 );

/**
 * Same message whether the username or the password was wrong, so the login
 * form can't be used to test which usernames exist. Other errors (empty
 * fields, the spam check) are left as they are.
 */
function annefpugh_generic_login_errors( $errors ) {
	if ( ! is_wp_error( $errors ) ) {
		return $errors;
	}
	$revealing = array( 'invalid_username', 'invalid_email', 'incorrect_password' );
	if ( ! array_intersect( $revealing, $errors->get_error_codes() ) ) {
		return $errors;
	}
	foreach ( $revealing as $code ) {
		$errors->remove( $code );
	}
	$errors->add( 'annefpugh_login_failed', __( '<strong>Error:</strong> The username, email address, or password is incorrect.', 'annefpugh' ) );
	return $errors;
}
add_filter( 'wp_login_errors', 'annefpugh_generic_login_errors' );


// ── NO BLOG PAGES ─────────────────────────────────────────────
/**
 * Runs before WordPress's canonical redirect (priority 10), which would
 * otherwise turn ?author=1 into a redirect that reveals /author/<username>/.
 */
function annefpugh_no_blog_pages() {
	if ( is_admin() ) {
		return;
	}

	// Post feeds have nothing in them; comment feeds are handled in disable-comments.php.
	if ( is_feed() && ! is_comment_feed() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}

	if ( is_search() || is_author() || is_category() || is_tag() || is_date() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'annefpugh_no_blog_pages', 1 );

// Belt and braces: never let the canonical redirect turn ?author=1 into
// /author/<username>/. Checks the query vars, since set_404() above clears is_author().
function annefpugh_no_author_canonical( $redirect_url ) {
	return get_query_var( 'author' ) || get_query_var( 'author_name' ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'annefpugh_no_author_canonical' );

// Nothing to advertise: no post or category feeds in <head>.
remove_action( 'wp_head', 'feed_links', 2 );

<?php
/**
 * "Service" post type — each service has an image (featured image),
 * title, short summary (excerpt), and full description (content).
 *
 * Services are not public on their own: they appear on the homepage and
 * the Services page only, so they never add pages beyond the site's five.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_register_service_post_type() {
	register_post_type(
		'annefpugh_service',
		array(
			'labels'              => array(
				'name'               => __( 'Services', 'annefpugh' ),
				'singular_name'      => __( 'Service', 'annefpugh' ),
				'add_new_item'       => __( 'Add New Service', 'annefpugh' ),
				'edit_item'          => __( 'Edit Service', 'annefpugh' ),
				'new_item'           => __( 'New Service', 'annefpugh' ),
				'all_items'          => __( 'All Services', 'annefpugh' ),
				'not_found'          => __( 'No services yet.', 'annefpugh' ),
				'featured_image'     => __( 'Service image', 'annefpugh' ),
				'set_featured_image' => __( 'Set service image', 'annefpugh' ),
			),
			'description'         => __( 'Services offered by the practice. Excerpt = short summary on the homepage; content = full description on the Services page. Reorder under Services → Change Order.', 'annefpugh' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-heart',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		)
	);
}
add_action( 'init', 'annefpugh_register_service_post_type' );

/**
 * Services in display order. $limit of -1 returns all.
 */
function annefpugh_get_services( $limit = -1 ) {
	return get_posts(
		array(
			'post_type'      => 'annefpugh_service',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

/**
 * Anchor id for a service on the Services page, so homepage cards can
 * link straight to the matching description.
 */
function annefpugh_service_anchor( $service ) {
	return 'service-' . $service->post_name;
}

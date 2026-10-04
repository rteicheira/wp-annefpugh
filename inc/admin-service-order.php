<?php
/**
 * Services → Change Order: drag-and-drop (plus keyboard-friendly Move up /
 * Move down buttons) to set the order services appear on the website.
 * Order is stored in menu_order, which annefpugh_get_services() sorts by.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ANNEFPUGH_SERVICE_ORDER_PAGE = 'annefpugh-service-order';

function annefpugh_service_order_menu() {
	$GLOBALS['annefpugh_service_order_hook'] = add_submenu_page(
		'edit.php?post_type=annefpugh_service',
		__( 'Change Service Order', 'annefpugh' ),
		__( 'Change Order', 'annefpugh' ),
		'edit_others_posts',
		ANNEFPUGH_SERVICE_ORDER_PAGE,
		'annefpugh_render_service_order_page'
	);
}
add_action( 'admin_menu', 'annefpugh_service_order_menu' );

function annefpugh_service_order_url() {
	return admin_url( 'edit.php?post_type=annefpugh_service&page=' . ANNEFPUGH_SERVICE_ORDER_PAGE );
}

function annefpugh_service_order_assets( $hook ) {
	if ( empty( $GLOBALS['annefpugh_service_order_hook'] ) || $GLOBALS['annefpugh_service_order_hook'] !== $hook ) {
		return;
	}
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'annefpugh-admin', $uri . '/assets/css/admin.css', array(), annefpugh_asset_version( 'assets/css/admin.css' ) );
	wp_enqueue_script( 'annefpugh-admin-service-order', $uri . '/assets/js/admin-service-order.js', array( 'jquery', 'jquery-ui-sortable', 'wp-a11y', 'wp-i18n' ), annefpugh_asset_version( 'assets/js/admin-service-order.js' ), true );
	wp_set_script_translations( 'annefpugh-admin-service-order', 'annefpugh', get_template_directory() . '/languages' );
}
add_action( 'admin_enqueue_scripts', 'annefpugh_service_order_assets' );

function annefpugh_render_service_order_page() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		return;
	}
	$services = get_posts(
		array(
			'post_type'      => 'annefpugh_service',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
	$updated  = isset( $_GET['updated'] ); // phpcs:ignore WordPress.Security.NonceVerification -- display-only flag.
	?>
	<div class="wrap afp-service-order">
		<h1><?php esc_html_e( 'Change Service Order', 'annefpugh' ); ?></h1>
		<p class="afp-lead"><?php esc_html_e( 'Drag services into the order you want them to appear on your website, or use the Move up and Move down buttons. Then click "Save order".', 'annefpugh' ); ?></p>

		<?php if ( $updated ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php esc_html_e( 'The new order was saved.', 'annefpugh' ); ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View your website', 'annefpugh' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'annefpugh' ); ?></span></a>
				</p>
			</div>
		<?php endif; ?>

		<?php if ( ! $services ) : ?>
			<p>
				<?php esc_html_e( 'You haven\'t added any services yet.', 'annefpugh' ); ?>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=annefpugh_service' ) ); ?>"><?php esc_html_e( 'Add your first service', 'annefpugh' ); ?></a>
			</p>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="annefpugh_save_service_order">
				<?php wp_nonce_field( 'annefpugh_save_service_order' ); ?>

				<ol class="afp-order-list" id="afp-order-list">
					<?php foreach ( $services as $service ) : ?>
						<?php $title = get_the_title( $service ) ? get_the_title( $service ) : __( '(no title)', 'annefpugh' ); ?>
						<li class="afp-order-item" data-title="<?php echo esc_attr( $title ); ?>">
							<input type="hidden" name="service_order[]" value="<?php echo esc_attr( $service->ID ); ?>">
							<span class="afp-order-item__handle dashicons dashicons-menu" aria-hidden="true" title="<?php esc_attr_e( 'Drag to reorder', 'annefpugh' ); ?>"></span>
							<span class="afp-order-item__position" aria-hidden="true"></span>
							<span class="afp-order-item__thumb">
								<?php echo has_post_thumbnail( $service ) ? get_the_post_thumbnail( $service, array( 48, 48 ), array( 'alt' => '' ) ) : ''; ?>
							</span>
							<span class="afp-order-item__title">
								<strong><?php echo esc_html( $title ); ?></strong>
								<?php if ( 'publish' !== $service->post_status ) : ?>
									<span class="afp-order-item__status">— <?php echo esc_html( get_post_status_object( $service->post_status )->label ); ?></span>
								<?php endif; ?>
							</span>
							<span class="afp-order-item__buttons">
								<?php /* translators: %s: service title */ ?>
								<button type="button" class="button afp-move" data-direction="up" aria-label="<?php echo esc_attr( sprintf( __( 'Move %s up', 'annefpugh' ), $title ) ); ?>"><?php esc_html_e( 'Move up', 'annefpugh' ); ?></button>
								<?php /* translators: %s: service title */ ?>
								<button type="button" class="button afp-move" data-direction="down" aria-label="<?php echo esc_attr( sprintf( __( 'Move %s down', 'annefpugh' ), $title ) ); ?>"><?php esc_html_e( 'Move down', 'annefpugh' ); ?></button>
							</span>
						</li>
					<?php endforeach; ?>
				</ol>

				<p class="afp-submit">
					<?php submit_button( __( 'Save order', 'annefpugh' ), 'primary large', 'submit', false ); ?>
				</p>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

function annefpugh_save_service_order() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to reorder services.', 'annefpugh' ), 403 );
	}
	check_admin_referer( 'annefpugh_save_service_order' );

	$ids = isset( $_POST['service_order'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['service_order'] ) ) : array();

	foreach ( array_values( array_unique( $ids ) ) as $position => $id ) {
		if ( 'annefpugh_service' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'         => $id,
				'menu_order' => $position + 1,
			)
		);
	}

	wp_safe_redirect( add_query_arg( 'updated', '1', annefpugh_service_order_url() ) );
	exit;
}
add_action( 'admin_post_annefpugh_save_service_order', 'annefpugh_save_service_order' );

/**
 * Show the Services list in website order by default.
 */
function annefpugh_service_admin_list_order( WP_Query $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'annefpugh_service' !== $query->get( 'post_type' ) || $query->get( 'orderby' ) ) {
		return;
	}
	$query->set(
		'orderby',
		array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		)
	);
}
add_action( 'pre_get_posts', 'annefpugh_service_admin_list_order' );

/**
 * "Change order" link above the Services list (next to All | Published).
 */
function annefpugh_service_list_views( $views ) {
	if ( current_user_can( 'edit_others_posts' ) ) {
		$views['annefpugh-order'] = '<a href="' . esc_url( annefpugh_service_order_url() ) . '"><strong>' . esc_html__( 'Change order', 'annefpugh' ) . '</strong></a>';
	}
	return $views;
}
add_filter( 'views_edit-annefpugh_service', 'annefpugh_service_list_views' );

/**
 * New services go to the end of the list instead of the top.
 */
function annefpugh_new_service_goes_last( $data, $postarr ) {
	if ( 'annefpugh_service' !== $data['post_type'] || ! empty( $postarr['ID'] ) ) {
		return $data;
	}
	global $wpdb;
	$max                = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(menu_order) FROM {$wpdb->posts} WHERE post_type = %s", 'annefpugh_service' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$data['menu_order'] = $max + 1;
	return $data;
}
add_filter( 'wp_insert_post_data', 'annefpugh_new_service_goes_last', 10, 2 );

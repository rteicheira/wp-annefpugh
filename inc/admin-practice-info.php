<?php
/**
 * "Practice Info" admin screen — a plain, form-based place to keep the
 * therapist and practice details current without the Customizer.
 *
 * Values are stored as theme mods (the same keys templates read via
 * annefpugh_mod()), so nothing else needs to know where they were edited.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ANNEFPUGH_PRACTICE_PAGE = 'annefpugh-practice-info';

/**
 * Field definitions, grouped into the boxes shown on the screen.
 */
function annefpugh_practice_field_groups() {
	return array(
		'therapist' => array(
			'title'       => __( 'About you', 'annefpugh' ),
			'description' => __( 'How you appear on the homepage and in the footer.', 'annefpugh' ),
			'fields'      => array(
				'therapist_name'        => array(
					'label'       => __( 'Your name', 'annefpugh' ),
					'type'        => 'text',
					'placeholder' => __( 'Jane Smith', 'annefpugh' ),
					'help'        => __( 'As clients should see it. If left blank, the site title is used.', 'annefpugh' ),
				),
				'therapist_credentials' => array(
					'label'       => __( 'Credentials', 'annefpugh' ),
					'type'        => 'text',
					'placeholder' => __( 'LCSW', 'annefpugh' ),
					'help'        => __( 'The letters after your name.', 'annefpugh' ),
				),
				'therapist_photo'       => array(
					'label' => __( 'Your photo', 'annefpugh' ),
					'type'  => 'image',
					'help'  => __( 'A warm, friendly headshot. Portrait (taller than wide) photos look best.', 'annefpugh' ),
				),
				'therapist_intro'       => array(
					'label'       => __( 'Short introduction', 'annefpugh' ),
					'type'        => 'textarea',
					'rows'        => 6,
					'placeholder' => __( 'I help adults navigating…', 'annefpugh' ),
					'help'        => __( 'A few welcoming sentences shown next to your photo on the homepage. Leave a blank line between paragraphs.', 'annefpugh' ),
				),
			),
		),
		'contact'   => array(
			'title'       => __( 'How clients reach you', 'annefpugh' ),
			'description' => __( 'Shown in the header, homepage, contact page, and footer.', 'annefpugh' ),
			'fields'      => array(
				'phone' => array(
					'label'       => __( 'Phone number', 'annefpugh' ),
					'type'        => 'tel',
					'placeholder' => __( '(555) 123-4567', 'annefpugh' ),
					'help'        => __( 'Visitors on a phone can tap it to call.', 'annefpugh' ),
				),
				'email' => array(
					'label'       => __( 'Email address', 'annefpugh' ),
					'type'        => 'email',
					'placeholder' => __( 'you@yourpractice.com', 'annefpugh' ),
					'help'        => __( 'Contact form messages are sent here. It\'s also shown in the footer.', 'annefpugh' ),
				),
			),
		),
		'location'  => array(
			'title'       => __( 'Location', 'annefpugh' ),
			'description' => __( 'Leave these blank if you only see clients by telehealth.', 'annefpugh' ),
			'fields'      => array(
				'address' => array(
					'label'       => __( 'Office address', 'annefpugh' ),
					'type'        => 'textarea',
					'rows'        => 3,
					'placeholder' => __( "123 Main Street, Suite 200\nBerkeley, CA 94707", 'annefpugh' ),
					'help'        => __( 'Put each line of the address on its own line.', 'annefpugh' ),
				),
				'map_url' => array(
					'label'       => __( 'Map link', 'annefpugh' ),
					'type'        => 'url',
					'placeholder' => 'https://maps.app.goo.gl/…',
					'help'        => __( 'Find your office in Google Maps, click "Share", then "Copy link", and paste it here. Adds a "Get directions" link.', 'annefpugh' ),
				),
			),
		),
		'sessions'  => array(
			'title'       => __( 'Sessions, fees & insurance', 'annefpugh' ),
			'description' => __( 'Shown in the "Practice details" section of the homepage.', 'annefpugh' ),
			'fields'      => array(
				'session_format' => array(
					'label'       => __( 'How you meet with clients', 'annefpugh' ),
					'type'        => 'text',
					'placeholder' => __( 'In person and telehealth', 'annefpugh' ),
				),
				'hours'          => array(
					'label'       => __( 'Hours', 'annefpugh' ),
					'type'        => 'textarea',
					'rows'        => 3,
					'placeholder' => __( "Monday–Thursday, 9am–6pm\nFriday by appointment", 'annefpugh' ),
				),
				'fees'           => array(
					'label'       => __( 'Fees', 'annefpugh' ),
					'type'        => 'textarea',
					'rows'        => 3,
					'placeholder' => __( '$200 per 50-minute session. Sliding scale available.', 'annefpugh' ),
				),
				'insurance'      => array(
					'label'       => __( 'Insurance', 'annefpugh' ),
					'type'        => 'textarea',
					'rows'        => 3,
					'placeholder' => __( 'In-network with Aetna and Cigna. Superbills available for out-of-network reimbursement.', 'annefpugh' ),
				),
			),
		),
		'license'   => array(
			'title'       => __( 'License', 'annefpugh' ),
			'description' => '',
			'fields'      => array(
				'license' => array(
					'label'       => __( 'License information', 'annefpugh' ),
					'type'        => 'text',
					'placeholder' => __( 'LCSW, California #12345', 'annefpugh' ),
					'help'        => __( 'Shown in the footer on every page. Many state licensing boards require this on your website.', 'annefpugh' ),
				),
			),
		),
	);
}

function annefpugh_practice_sanitize( $type, $value ) {
	switch ( $type ) {
		case 'image':
			$id = absint( $value );
			return ( $id && wp_attachment_is_image( $id ) ) ? $id : 0;
		case 'email':
			return sanitize_email( $value );
		case 'url':
			return esc_url_raw( $value );
		case 'textarea':
			return sanitize_textarea_field( $value );
		default:
			return sanitize_text_field( $value );
	}
}

function annefpugh_practice_admin_menu() {
	add_menu_page(
		__( 'Practice Info', 'annefpugh' ),
		__( 'Practice Info', 'annefpugh' ),
		'edit_theme_options',
		ANNEFPUGH_PRACTICE_PAGE,
		'annefpugh_render_practice_page',
		'dashicons-id-alt',
		3
	);
}
add_action( 'admin_menu', 'annefpugh_practice_admin_menu' );

function annefpugh_practice_admin_assets( $hook ) {
	if ( 'toplevel_page_' . ANNEFPUGH_PRACTICE_PAGE !== $hook ) {
		return;
	}
	wp_enqueue_media();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'annefpugh-admin', $uri . '/assets/css/admin.css', array(), ANNEFPUGH_VERSION );
	wp_enqueue_script( 'annefpugh-admin-practice-info', $uri . '/assets/js/admin-practice-info.js', array( 'jquery' ), ANNEFPUGH_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'annefpugh_practice_admin_assets' );

function annefpugh_save_practice_info() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to change practice information.', 'annefpugh' ), 403 );
	}
	check_admin_referer( 'annefpugh_save_practice_info' );

	$warnings = array();

	foreach ( annefpugh_practice_field_groups() as $group ) {
		foreach ( $group['fields'] as $key => $field ) {
			$raw   = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized below per field type.
			$clean = annefpugh_practice_sanitize( $field['type'], $raw );

			if ( 'email' === $field['type'] && '' !== trim( $raw ) && ! is_email( $clean ) ) {
				$warnings[] = 'email';
				continue; // Keep the previous address rather than blanking it.
			}
			if ( 'url' === $field['type'] && '' !== trim( $raw ) && '' === $clean ) {
				$warnings[] = 'map_url';
				continue;
			}

			set_theme_mod( $key, $clean );
		}
	}

	// Photo alt text lives on the image itself, so it's reused wherever the photo appears.
	$photo_id = absint( get_theme_mod( 'therapist_photo' ) );
	if ( $photo_id && isset( $_POST['therapist_photo_alt'] ) ) {
		update_post_meta( $photo_id, '_wp_attachment_image_alt', sanitize_text_field( wp_unslash( $_POST['therapist_photo_alt'] ) ) );
	}

	$args = array(
		'page'    => ANNEFPUGH_PRACTICE_PAGE,
		'updated' => '1',
	);
	if ( $warnings ) {
		$args['warn'] = implode( ',', $warnings );
	}
	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_annefpugh_save_practice_info', 'annefpugh_save_practice_info' );

function annefpugh_render_practice_field( $key, $field ) {
	$value       = annefpugh_mod( $key );
	$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$help_id     = $key . '-help';
	$described   = ! empty( $field['help'] ) ? ' aria-describedby="' . esc_attr( $help_id ) . '"' : '';

	echo '<tr><th scope="row">';
	if ( 'image' === $field['type'] ) {
		echo esc_html( $field['label'] );
	} else {
		echo '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label>';
	}
	echo '</th><td>';

	if ( 'textarea' === $field['type'] ) {
		printf(
			'<textarea id="%1$s" name="%1$s" rows="%2$d" class="large-text" placeholder="%3$s"%4$s>%5$s</textarea>',
			esc_attr( $key ),
			isset( $field['rows'] ) ? absint( $field['rows'] ) : 4,
			esc_attr( $placeholder ),
			$described, // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr above.
			esc_textarea( $value )
		);
	} elseif ( 'image' === $field['type'] ) {
		$image_id = absint( $value );
		$alt      = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
		?>
		<div class="afp-image-field">
			<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $image_id ? $image_id : '' ); ?>" class="afp-image-field__id">
			<div class="afp-image-field__preview">
				<?php echo $image_id ? wp_get_attachment_image( $image_id, 'medium' ) : ''; ?>
			</div>
			<p>
				<button type="button" class="button afp-image-field__select" data-title="<?php esc_attr_e( 'Choose your photo', 'annefpugh' ); ?>" data-button="<?php esc_attr_e( 'Use this photo', 'annefpugh' ); ?>">
					<?php echo $image_id ? esc_html__( 'Change photo', 'annefpugh' ) : esc_html__( 'Choose photo', 'annefpugh' ); ?>
				</button>
				<button type="button" class="button-link button-link-delete afp-image-field__remove"<?php echo $image_id ? '' : ' hidden'; ?>>
					<?php esc_html_e( 'Remove photo', 'annefpugh' ); ?>
				</button>
			</p>
			<p class="afp-image-field__alt"<?php echo $image_id ? '' : ' hidden'; ?>>
				<label for="<?php echo esc_attr( $key ); ?>_alt"><?php esc_html_e( 'Describe the photo', 'annefpugh' ); ?></label><br>
				<input type="text" id="<?php echo esc_attr( $key ); ?>_alt" name="<?php echo esc_attr( $key ); ?>_alt" value="<?php echo esc_attr( $alt ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Jane Smith smiling in her office', 'annefpugh' ); ?>">
				<span class="description"><?php esc_html_e( 'Read aloud to visitors who use screen readers.', 'annefpugh' ); ?></span>
			</p>
		</div>
		<?php
	} else {
		printf(
			'<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" class="regular-text" placeholder="%4$s"%5$s>',
			esc_attr( $field['type'] ),
			esc_attr( $key ),
			'url' === $field['type'] ? esc_url( $value ) : esc_attr( $value ),
			esc_attr( $placeholder ),
			$described // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr above.
		);
	}

	if ( ! empty( $field['help'] ) ) {
		echo '<p class="description" id="' . esc_attr( $help_id ) . '">' . esc_html( $field['help'] ) . '</p>';
	}
	echo '</td></tr>';
}

function annefpugh_render_practice_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$updated  = isset( $_GET['updated'] ); // phpcs:ignore WordPress.Security.NonceVerification -- display-only flag.
	$warnings = isset( $_GET['warn'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['warn'] ) ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap afp-practice-info">
		<h1><?php esc_html_e( 'Practice Info', 'annefpugh' ); ?></h1>
		<p class="afp-lead"><?php esc_html_e( 'Keep your details up to date here. Changes appear across your whole website as soon as you click "Save changes".', 'annefpugh' ); ?></p>

		<?php if ( $updated ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php esc_html_e( 'Your practice information was saved.', 'annefpugh' ); ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View your website', 'annefpugh' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'annefpugh' ); ?></span></a>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( in_array( 'email', $warnings, true ) ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'That email address didn\'t look right, so the previous one was kept. Please check it and save again.', 'annefpugh' ); ?></p></div>
		<?php endif; ?>
		<?php if ( in_array( 'map_url', $warnings, true ) ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'The map link didn\'t look like a web address, so the previous one was kept. It should start with https://', 'annefpugh' ); ?></p></div>
		<?php endif; ?>

		<div class="afp-layout">
			<form class="afp-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="annefpugh_save_practice_info">
				<?php wp_nonce_field( 'annefpugh_save_practice_info' ); ?>

				<?php foreach ( annefpugh_practice_field_groups() as $group_id => $group ) : ?>
					<div class="postbox afp-box">
						<h2 class="afp-box__title" id="afp-group-<?php echo esc_attr( $group_id ); ?>"><?php echo esc_html( $group['title'] ); ?></h2>
						<div class="inside">
							<?php if ( $group['description'] ) : ?>
								<p class="afp-box__description"><?php echo esc_html( $group['description'] ); ?></p>
							<?php endif; ?>
							<table class="form-table" role="presentation">
								<?php
								foreach ( $group['fields'] as $key => $field ) {
									annefpugh_render_practice_field( $key, $field );
								}
								?>
							</table>
						</div>
					</div>
				<?php endforeach; ?>

				<p class="afp-submit">
					<?php submit_button( __( 'Save changes', 'annefpugh' ), 'primary large', 'submit', false ); ?>
				</p>
			</form>

			<aside class="afp-sidebar">
				<div class="postbox afp-box">
					<h2 class="afp-box__title"><?php esc_html_e( 'Other things you can update', 'annefpugh' ); ?></h2>
					<div class="inside">
						<?php annefpugh_render_quick_links(); ?>
					</div>
				</div>
			</aside>
		</div>
	</div>
	<?php
}

/**
 * Shared list of "where do I change X" links (Practice Info sidebar + Dashboard widget).
 */
function annefpugh_render_quick_links( $include_practice_info = false ) {
	$links = array();
	if ( $include_practice_info ) {
		$links[] = array( admin_url( 'admin.php?page=' . ANNEFPUGH_PRACTICE_PAGE ), __( 'Practice Info', 'annefpugh' ), __( 'Your name, photo, phone, address, fees, and license', 'annefpugh' ) );
	}
	$links[] = array( admin_url( 'edit.php?post_type=annefpugh_service' ), __( 'Services', 'annefpugh' ), __( 'Add, edit, or reorder the services you offer', 'annefpugh' ) );
	$links[] = array( admin_url( 'edit.php?post_type=page' ), __( 'Pages', 'annefpugh' ), __( 'About, Fees & FAQ, Contact, and legal pages', 'annefpugh' ) );
	$links[] = array( admin_url( 'customize.php' ), __( 'Homepage, colors & images', 'annefpugh' ), __( 'Homepage headline and photo, colors, logo', 'annefpugh' ) );
	?>
	<ul class="afp-quick-links">
		<?php foreach ( $links as $link ) : ?>
			<li>
				<a href="<?php echo esc_url( $link[0] ); ?>"><strong><?php echo esc_html( $link[1] ); ?></strong></a>
				<span><?php echo esc_html( $link[2] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function annefpugh_admin_bar_link( WP_Admin_Bar $admin_bar ) {
	if ( is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$admin_bar->add_node(
		array(
			'id'    => 'annefpugh-practice-info',
			'title' => __( 'Edit Practice Info', 'annefpugh' ),
			'href'  => admin_url( 'admin.php?page=' . ANNEFPUGH_PRACTICE_PAGE ),
		)
	);
}
add_action( 'admin_bar_menu', 'annefpugh_admin_bar_link', 80 );

function annefpugh_dashboard_widget() {
	wp_add_dashboard_widget(
		'annefpugh_quick_links',
		__( 'Update your website', 'annefpugh' ),
		function () {
			echo '<p>' . esc_html__( 'Where to change things on your site:', 'annefpugh' ) . '</p>';
			annefpugh_render_quick_links( true );
		}
	);
}
add_action( 'wp_dashboard_setup', 'annefpugh_dashboard_widget' );

function annefpugh_dashboard_widget_styles( $hook ) {
	if ( 'index.php' === $hook ) {
		wp_enqueue_style( 'annefpugh-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), ANNEFPUGH_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'annefpugh_dashboard_widget_styles' );

<?php
/**
 * Google reCAPTCHA for the contact forms (Settings → Spam Protection).
 *
 * v3 (invisible, score-based) is the default; v2 "I'm not a robot" checkbox
 * is optional. Google's script is only loaded on pages that show a form, and
 * for v3 not until the visitor starts filling it in.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ANNEFPUGH_RECAPTCHA_OPTION = 'annefpugh_recaptcha';

function annefpugh_recaptcha_settings() {
	return wp_parse_args(
		get_option( ANNEFPUGH_RECAPTCHA_OPTION, array() ),
		array(
			'enabled'    => false,
			'version'    => 'v3',
			'site_key'   => '',
			'secret_key' => '',
			'threshold'  => 0.5,
		)
	);
}

function annefpugh_recaptcha_active() {
	$s = annefpugh_recaptcha_settings();
	return $s['enabled'] && '' !== $s['site_key'] && '' !== $s['secret_key'];
}

/* -------------------------------------------------------------------------
 * Settings screen
 * ---------------------------------------------------------------------- */

function annefpugh_recaptcha_sanitize( $input ) {
	$current = annefpugh_recaptcha_settings();
	$input   = is_array( $input ) ? $input : array();

	$secret = isset( $input['secret_key'] ) ? sanitize_text_field( $input['secret_key'] ) : '';

	return array(
		'enabled'    => ! empty( $input['enabled'] ),
		'version'    => ( isset( $input['version'] ) && 'v2' === $input['version'] ) ? 'v2' : 'v3',
		'site_key'   => isset( $input['site_key'] ) ? sanitize_text_field( $input['site_key'] ) : '',
		// A blank secret field means "keep the saved one" — it's never echoed back.
		'secret_key' => ( '' === $secret && empty( $input['clear_secret'] ) ) ? $current['secret_key'] : $secret,
		'threshold'  => isset( $input['threshold'] ) ? min( 0.9, max( 0.1, round( (float) $input['threshold'], 1 ) ) ) : 0.5,
	);
}

function annefpugh_recaptcha_register_settings() {
	register_setting(
		'annefpugh_recaptcha',
		ANNEFPUGH_RECAPTCHA_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'annefpugh_recaptcha_sanitize',
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'annefpugh_recaptcha_register_settings' );

function annefpugh_recaptcha_admin_menu() {
	add_options_page(
		__( 'Spam Protection', 'annefpugh' ),
		__( 'Spam Protection', 'annefpugh' ),
		'manage_options',
		'annefpugh-spam-protection',
		'annefpugh_recaptcha_render_settings'
	);
}
add_action( 'admin_menu', 'annefpugh_recaptcha_admin_menu' );

function annefpugh_recaptcha_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s    = annefpugh_recaptcha_settings();
	$name = ANNEFPUGH_RECAPTCHA_OPTION;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Spam Protection', 'annefpugh' ); ?></h1>
		<p><?php esc_html_e( 'Google reCAPTCHA helps stop automated spam from reaching your inbox through the contact forms. The forms already include a hidden spam trap; reCAPTCHA adds a stronger check.', 'annefpugh' ); ?></p>

		<?php if ( $s['enabled'] && ! annefpugh_recaptcha_active() ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'reCAPTCHA is turned on but isn\'t running yet — add both the site key and the secret key below.', 'annefpugh' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'annefpugh_recaptcha' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Status', 'annefpugh' ); ?></th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[enabled]" value="1" <?php checked( $s['enabled'] ); ?>> <?php esc_html_e( 'Use reCAPTCHA on the contact forms', 'annefpugh' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Type', 'annefpugh' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php esc_html_e( 'reCAPTCHA type', 'annefpugh' ); ?></legend>
							<label><input type="radio" name="<?php echo esc_attr( $name ); ?>[version]" value="v3" <?php checked( $s['version'], 'v3' ); ?>> <strong><?php esc_html_e( 'v3 — invisible (recommended)', 'annefpugh' ); ?></strong></label>
							<p class="description"><?php esc_html_e( 'No puzzles or checkboxes for visitors, which is best for accessibility.', 'annefpugh' ); ?></p>
							<br>
							<label><input type="radio" name="<?php echo esc_attr( $name ); ?>[version]" value="v2" <?php checked( $s['version'], 'v2' ); ?>> <strong><?php esc_html_e( 'v2 — "I\'m not a robot" checkbox', 'annefpugh' ); ?></strong></label>
							<p class="description"><?php esc_html_e( 'Visitors tick a box and are sometimes shown image puzzles.', 'annefpugh' ); ?></p>
						</fieldset>
						<p class="description"><?php esc_html_e( 'Your keys must be created for the same type you choose here.', 'annefpugh' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="annefpugh-recaptcha-site"><?php esc_html_e( 'Site key', 'annefpugh' ); ?></label></th>
					<td>
						<input type="text" id="annefpugh-recaptcha-site" class="regular-text code" name="<?php echo esc_attr( $name ); ?>[site_key]" value="<?php echo esc_attr( $s['site_key'] ); ?>" autocomplete="off">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="annefpugh-recaptcha-secret"><?php esc_html_e( 'Secret key', 'annefpugh' ); ?></label></th>
					<td>
						<input type="password" id="annefpugh-recaptcha-secret" class="regular-text code" name="<?php echo esc_attr( $name ); ?>[secret_key]" value="" autocomplete="new-password"
							placeholder="<?php echo $s['secret_key'] ? esc_attr__( 'Saved — leave blank to keep it', 'annefpugh' ) : ''; ?>">
						<?php if ( $s['secret_key'] ) : ?>
							<p><label><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[clear_secret]" value="1"> <?php esc_html_e( 'Remove the saved secret key', 'annefpugh' ); ?></label></p>
						<?php endif; ?>
						<p class="description"><?php esc_html_e( 'Kept private: it\'s never shown again after saving.', 'annefpugh' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="annefpugh-recaptcha-threshold"><?php esc_html_e( 'Strictness (v3 only)', 'annefpugh' ); ?></label></th>
					<td>
						<input type="number" id="annefpugh-recaptcha-threshold" name="<?php echo esc_attr( $name ); ?>[threshold]" value="<?php echo esc_attr( $s['threshold'] ); ?>" min="0.1" max="0.9" step="0.1" class="small-text">
						<p class="description"><?php esc_html_e( 'Google scores each submission from 0.0 (likely a bot) to 1.0 (likely a person). Submissions below this score are blocked. 0.5 is a good default; lower it if real people are being blocked.', 'annefpugh' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<h2><?php esc_html_e( 'Getting your keys', 'annefpugh' ); ?></h2>
		<ol>
			<li>
				<?php
				printf(
					/* translators: %s: link to the Google reCAPTCHA admin console */
					esc_html__( 'Sign in to the %s with a Google account.', 'annefpugh' ),
					'<a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener">' . esc_html__( 'Google reCAPTCHA admin console', 'annefpugh' ) . '</a>'
				);
				?>
			</li>
			<li><?php esc_html_e( 'Choose the same type you picked above (score-based v3, or v2 "I\'m not a robot" checkbox).', 'annefpugh' ); ?></li>
			<?php /* translators: %s: this site's domain */ ?>
			<li><?php printf( esc_html__( 'Add your website\'s domain: %s', 'annefpugh' ), '<code>' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</code>' ); ?></li>
			<li><?php esc_html_e( 'Copy the site key and secret key into the fields above and save.', 'annefpugh' ); ?></li>
		</ol>
		<p><strong><?php esc_html_e( 'Privacy:', 'annefpugh' ); ?></strong> <?php esc_html_e( 'reCAPTCHA sends visitor and device information to Google. Mention it in your Privacy Policy.', 'annefpugh' ); ?></p>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------- */

/**
 * data-* attributes for the <form> tag (empty when reCAPTCHA is off).
 */
function annefpugh_recaptcha_form_attributes() {
	if ( ! annefpugh_recaptcha_active() ) {
		return '';
	}
	$s = annefpugh_recaptcha_settings();
	return sprintf( ' data-recaptcha="%s" data-sitekey="%s"', esc_attr( $s['version'] ), esc_attr( $s['site_key'] ) );
}

/**
 * Widget or hidden token field, plus the attribution Google requires when
 * the v3 badge is hidden. Also enqueues the scripts this page needs.
 */
function annefpugh_recaptcha_form_fields() {
	if ( ! annefpugh_recaptcha_active() ) {
		return;
	}
	$s = annefpugh_recaptcha_settings();

	if ( 'v2' === $s['version'] ) {
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, array( 'strategy' => 'async', 'in_footer' => true ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google versions its own script.
		echo '<div class="form-field form-field--recaptcha"><div class="g-recaptcha" data-sitekey="' . esc_attr( $s['site_key'] ) . '"></div></div>';
		return;
	}

	wp_enqueue_script( 'annefpugh-recaptcha', get_template_directory_uri() . '/assets/js/recaptcha.js', array(), annefpugh_asset_version( 'assets/js/recaptcha.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	echo '<input type="hidden" name="g-recaptcha-response" value="">';
	printf(
		'<p class="form-recaptcha-note">%s</p>',
		sprintf(
			/* translators: 1: Google Privacy Policy link, 2: Google Terms of Service link */
			esc_html__( 'This form is protected by reCAPTCHA and the Google %1$s and %2$s apply.', 'annefpugh' ),
			'<a href="https://policies.google.com/privacy" target="_blank" rel="noopener">' . esc_html__( 'Privacy Policy', 'annefpugh' ) . '</a>',
			'<a href="https://policies.google.com/terms" target="_blank" rel="noopener">' . esc_html__( 'Terms of Service', 'annefpugh' ) . '</a>'
		)
	);
}

/**
 * Verify a submission with Google. Returns:
 *  - 'pass'       reCAPTCHA is off, or Google confirmed a person.
 *  - 'fail'       Google returned an invalid/low-score/wrong-site token: reject.
 *  - 'unverified' no token (Google's script was blocked by the visitor's
 *                 browser) or Google unreachable: let it through, flagged, so
 *                 a real inquiry is never lost. Rate limiting still applies.
 */
function annefpugh_recaptcha_verify( $token ) {
	if ( ! annefpugh_recaptcha_active() ) {
		return 'pass';
	}
	if ( '' === $token ) {
		return 'unverified';
	}

	$s        = annefpugh_recaptcha_settings();
	$response = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 8,
			'body'    => array(
				'secret'   => $s['secret_key'],
				'response' => $token,
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		error_log( 'annefpugh reCAPTCHA: verification service unreachable, message allowed through: ' . ( is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_response_code( $response ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return 'unverified';
	}

	$result = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $result ) || empty( $result['success'] ) ) {
		return 'fail';
	}

	// Token must have been issued on this site (matters if Google's own
	// domain check is ever turned off for the key).
	$expected_host = apply_filters( 'annefpugh_recaptcha_hostname', wp_parse_url( home_url(), PHP_URL_HOST ) );
	if ( ! empty( $result['hostname'] ) && $expected_host && strtolower( $result['hostname'] ) !== strtolower( $expected_host ) ) {
		return 'fail';
	}

	if ( 'v3' === $s['version'] ) {
		$score_ok  = isset( $result['score'] ) && (float) $result['score'] >= (float) $s['threshold'];
		$action_ok = isset( $result['action'] ) && 'contact' === $result['action'];
		return ( $score_ok && $action_ok ) ? 'pass' : 'fail';
	}
	return 'pass';
}

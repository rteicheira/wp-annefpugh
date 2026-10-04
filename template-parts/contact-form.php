<?php
/**
 * Contact form, shared by the Contact page and the homepage call to action.
 * Submissions go to the email set under Practice Info (inc/contact-form.php).
 *
 * @var array $args {
 *     @type string $id      Unique id for this form on the page; also the anchor
 *                           the visitor returns to after submitting.
 *     @type string $variant 'full' (name, email, phone, message) or
 *                           'quick' (name, phone, what they're looking for).
 * }
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_form_id = sanitize_key( $args['id'] );
$annefpugh_quick   = 'quick' === $args['variant'];
$annefpugh_field   = function ( $name ) use ( $annefpugh_form_id ) {
	return $annefpugh_form_id . '-' . $name;
};

// phpcs:disable WordPress.Security.NonceVerification -- display-only status flags.
$annefpugh_status = ( isset( $_GET['form'], $_GET['contact'] ) && sanitize_key( wp_unslash( $_GET['form'] ) ) === $annefpugh_form_id )
	? sanitize_key( wp_unslash( $_GET['contact'] ) )
	: '';
// phpcs:enable

$annefpugh_messages = array(
	'sent'    => array( 'success', 'status', __( 'Thank you — your message was sent. I\'ll be in touch soon.', 'annefpugh' ) ),
	'invalid' => array(
		'error',
		'alert',
		$annefpugh_quick
			? __( 'Please fill in your name, a phone number, and a short description of what you\'re looking for.', 'annefpugh' )
			: __( 'Please fill in your name, a valid email address, and a message.', 'annefpugh' ),
	),
	'error'   => array( 'error', 'alert', __( 'Sorry, your message couldn\'t be sent. Please try again or call instead.', 'annefpugh' ) ),
	'expired' => array( 'error', 'alert', __( 'Sorry, something went wrong with the form. Please reload the page and try again, or call instead.', 'annefpugh' ) ),
	'busy'    => array(
		'error',
		'alert',
		annefpugh_mod( 'phone' )
			/* translators: %s: practice phone number */
			? sprintf( __( 'You\'ve sent several messages in a short time. Please wait about 10 minutes and try again, or call %s.', 'annefpugh' ), annefpugh_mod( 'phone' ) )
			: __( 'You\'ve sent several messages in a short time. Please wait about 10 minutes and try again.', 'annefpugh' ),
	),
	'spam'    => array(
		'error',
		'alert',
		annefpugh_mod( 'phone' )
			/* translators: %s: practice phone number */
			? sprintf( __( 'Sorry, we couldn\'t confirm this message came from a person, so it wasn\'t sent. Please try again, or call %s.', 'annefpugh' ), annefpugh_mod( 'phone' ) )
			: __( 'Sorry, we couldn\'t confirm this message came from a person, so it wasn\'t sent. Please try again.', 'annefpugh' ),
	),
);
?>
<div class="contact-form-wrap" id="<?php echo esc_attr( $annefpugh_form_id ); ?>">
	<?php if ( isset( $annefpugh_messages[ $annefpugh_status ] ) ) : ?>
		<?php list( $annefpugh_kind, $annefpugh_role, $annefpugh_text ) = $annefpugh_messages[ $annefpugh_status ]; ?>
		<p class="notice notice--<?php echo esc_attr( $annefpugh_kind ); ?>" role="<?php echo esc_attr( $annefpugh_role ); ?>"><?php echo esc_html( $annefpugh_text ); ?></p>
	<?php endif; ?>

	<p class="form-privacy-note">
		<?php echo annefpugh_linkify_phone_numbers( annefpugh_required_text( 'contact_form_notice' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>
	</p>

	<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"<?php echo annefpugh_recaptcha_form_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>>
		<input type="hidden" name="action" value="annefpugh_contact">
		<input type="hidden" name="annefpugh_form" value="<?php echo esc_attr( $annefpugh_form_id ); ?>">
		<input type="hidden" name="annefpugh_variant" value="<?php echo esc_attr( $annefpugh_quick ? 'quick' : 'full' ); ?>">
		<input type="hidden" name="annefpugh_ts" value="<?php echo esc_attr( annefpugh_form_token() ); ?>">
		<?php wp_referer_field(); ?>

		<?php // Spam trap: hidden from people; deliberately not named like anything browsers autofill. ?>
		<div class="form-field form-field--hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $annefpugh_field( 'extra' ) ); ?>">Leave this field empty</label>
			<input type="text" id="<?php echo esc_attr( $annefpugh_field( 'extra' ) ); ?>" name="<?php echo esc_attr( ANNEFPUGH_HONEYPOT_FIELD ); ?>" value="" tabindex="-1" autocomplete="new-password">
		</div>

		<div class="form-row">
			<div class="form-field">
				<label for="<?php echo esc_attr( $annefpugh_field( 'name' ) ); ?>"><?php esc_html_e( 'Name', 'annefpugh' ); ?> <span aria-hidden="true">*</span></label>
				<input type="text" id="<?php echo esc_attr( $annefpugh_field( 'name' ) ); ?>" name="annefpugh_name" autocomplete="name" required aria-required="true" maxlength="100">
			</div>

			<?php if ( $annefpugh_quick ) : ?>
				<div class="form-field">
					<label for="<?php echo esc_attr( $annefpugh_field( 'phone' ) ); ?>"><?php esc_html_e( 'Phone', 'annefpugh' ); ?> <span aria-hidden="true">*</span></label>
					<input type="tel" id="<?php echo esc_attr( $annefpugh_field( 'phone' ) ); ?>" name="annefpugh_phone" autocomplete="tel" required aria-required="true" maxlength="30">
				</div>
			<?php else : ?>
				<div class="form-field">
					<label for="<?php echo esc_attr( $annefpugh_field( 'email' ) ); ?>"><?php esc_html_e( 'Email', 'annefpugh' ); ?> <span aria-hidden="true">*</span></label>
					<input type="email" id="<?php echo esc_attr( $annefpugh_field( 'email' ) ); ?>" name="annefpugh_email" autocomplete="email" required aria-required="true" maxlength="150">
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! $annefpugh_quick ) : ?>
			<div class="form-field">
				<label for="<?php echo esc_attr( $annefpugh_field( 'phone' ) ); ?>"><?php esc_html_e( 'Phone (optional)', 'annefpugh' ); ?></label>
				<input type="tel" id="<?php echo esc_attr( $annefpugh_field( 'phone' ) ); ?>" name="annefpugh_phone" autocomplete="tel" maxlength="30">
			</div>
		<?php endif; ?>

		<div class="form-field">
			<label for="<?php echo esc_attr( $annefpugh_field( 'message' ) ); ?>">
				<?php $annefpugh_quick ? esc_html_e( 'Briefly, what are you looking for?', 'annefpugh' ) : esc_html_e( 'How can I help?', 'annefpugh' ); ?>
				<span aria-hidden="true">*</span>
			</label>
			<textarea id="<?php echo esc_attr( $annefpugh_field( 'message' ) ); ?>" name="annefpugh_message" rows="<?php echo $annefpugh_quick ? 4 : 6; ?>" required aria-required="true" maxlength="2000"></textarea>
		</div>

		<?php annefpugh_recaptcha_form_fields(); ?>

		<p class="form-required-note"><?php esc_html_e( '* Required', 'annefpugh' ); ?></p>
		<button class="button button--large" type="submit">
			<?php $annefpugh_quick ? esc_html_e( 'Send request', 'annefpugh' ) : esc_html_e( 'Send message', 'annefpugh' ); ?>
		</button>
	</form>
</div>

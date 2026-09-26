<?php
/**
 * Template Name: Contact
 *
 * Page intro, contact form, and practice details side by side.
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/page' );
endwhile;

$annefpugh_status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display-only status flag.
$annefpugh_phone  = annefpugh_mod( 'phone' );
$annefpugh_email  = annefpugh_mod( 'email' );
?>

<section class="section contact-section">
	<div class="container contact-grid">
		<div id="contact-form" class="contact-form-wrap">
			<h2><?php esc_html_e( 'Send a message', 'annefpugh' ); ?></h2>

			<?php if ( 'sent' === $annefpugh_status ) : ?>
				<p class="notice notice--success" role="status"><?php esc_html_e( 'Thank you — your message was sent. I\'ll be in touch soon.', 'annefpugh' ); ?></p>
			<?php elseif ( 'invalid' === $annefpugh_status ) : ?>
				<p class="notice notice--error" role="alert"><?php esc_html_e( 'Please fill in your name, a valid email address, and a message.', 'annefpugh' ); ?></p>
			<?php elseif ( 'error' === $annefpugh_status ) : ?>
				<p class="notice notice--error" role="alert"><?php esc_html_e( 'Sorry, your message couldn\'t be sent. Please try again or call instead.', 'annefpugh' ); ?></p>
			<?php endif; ?>

			<p class="form-privacy-note">
				<?php esc_html_e( 'Please don\'t include sensitive health details in this form — we can discuss those privately. This form is not monitored for emergencies; if you are in crisis, call or text 988.', 'annefpugh' ); ?>
			</p>

			<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="annefpugh_contact">
				<?php wp_nonce_field( 'annefpugh_contact', 'annefpugh_contact_nonce' ); ?>

				<div class="form-field form-field--hp" aria-hidden="true">
					<label for="annefpugh_website">Website</label>
					<input type="text" id="annefpugh_website" name="annefpugh_website" tabindex="-1" autocomplete="off">
				</div>

				<div class="form-field">
					<label for="annefpugh_name"><?php esc_html_e( 'Name', 'annefpugh' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" id="annefpugh_name" name="annefpugh_name" autocomplete="name" required aria-required="true" maxlength="100">
				</div>

				<div class="form-row">
					<div class="form-field">
						<label for="annefpugh_email"><?php esc_html_e( 'Email', 'annefpugh' ); ?> <span aria-hidden="true">*</span></label>
						<input type="email" id="annefpugh_email" name="annefpugh_email" autocomplete="email" required aria-required="true" maxlength="150">
					</div>
					<div class="form-field">
						<label for="annefpugh_phone"><?php esc_html_e( 'Phone (optional)', 'annefpugh' ); ?></label>
						<input type="tel" id="annefpugh_phone" name="annefpugh_phone" autocomplete="tel" maxlength="30">
					</div>
				</div>

				<div class="form-field">
					<label for="annefpugh_message"><?php esc_html_e( 'How can I help?', 'annefpugh' ); ?> <span aria-hidden="true">*</span></label>
					<textarea id="annefpugh_message" name="annefpugh_message" rows="6" required aria-required="true" maxlength="2000"></textarea>
				</div>

				<p class="form-required-note"><?php esc_html_e( '* Required', 'annefpugh' ); ?></p>
				<button class="button button--large" type="submit"><?php esc_html_e( 'Send message', 'annefpugh' ); ?></button>
			</form>
		</div>

		<aside class="contact-details" aria-label="<?php esc_attr_e( 'Contact details', 'annefpugh' ); ?>">
			<h2><?php esc_html_e( 'Other ways to reach me', 'annefpugh' ); ?></h2>
			<ul class="contact-details__list">
				<?php if ( $annefpugh_phone ) : ?>
					<li><strong><?php esc_html_e( 'Phone', 'annefpugh' ); ?></strong><br><a href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>"><?php echo esc_html( $annefpugh_phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( is_email( $annefpugh_email ) ) : ?>
					<li><strong><?php esc_html_e( 'Email', 'annefpugh' ); ?></strong><br><a href="mailto:<?php echo esc_attr( antispambot( $annefpugh_email ) ); ?>"><?php echo esc_html( antispambot( $annefpugh_email ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( annefpugh_mod( 'address' ) ) : ?>
					<li><strong><?php esc_html_e( 'Office', 'annefpugh' ); ?></strong><br><address><?php echo nl2br( esc_html( annefpugh_mod( 'address' ) ) ); ?></address></li>
				<?php endif; ?>
				<?php if ( annefpugh_mod( 'hours' ) ) : ?>
					<li><strong><?php esc_html_e( 'Hours', 'annefpugh' ); ?></strong><br><?php echo nl2br( esc_html( annefpugh_mod( 'hours' ) ) ); ?></li>
				<?php endif; ?>
			</ul>
		</aside>
	</div>
</section>

<?php
get_footer();

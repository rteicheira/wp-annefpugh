<?php
/**
 * Template Name: Contact Form
 *
 * Contact page with a native contact form (no third-party plugin
 * dependency). Submission is handled by inc/contact-form.php.
 */

get_header();

$sent  = isset( $_GET['annefpugh_sent'] ) && '1' === $_GET['annefpugh_sent'];
$error = isset( $_GET['annefpugh_error'] ) ? sanitize_key( wp_unslash( $_GET['annefpugh_error'] ) ) : '';
?>

<article <?php post_class( 'page-content contact-page' ); ?>>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<h1 class="page-title"><?php the_title(); ?></h1>
		<div class="page-body">
			<?php the_content(); ?>
		</div>
		<?php
	endwhile;
	?>

	<?php if ( $sent ) : ?>
		<p class="form-notice form-notice--success">
			<?php esc_html_e( 'Thank you — your message has been sent.', 'annefpugh' ); ?>
		</p>
	<?php elseif ( $error ) : ?>
		<p class="form-notice form-notice--error">
			<?php esc_html_e( 'Sorry, your message could not be sent. Please try again.', 'annefpugh' ); ?>
		</p>
	<?php endif; ?>

	<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="annefpugh_contact">
		<?php wp_nonce_field( 'annefpugh_contact', 'annefpugh_contact_nonce' ); ?>

		<div class="form-field form-field--hp" aria-hidden="true">
			<label for="annefpugh_website">Website</label>
			<input type="text" id="annefpugh_website" name="annefpugh_website" tabindex="-1" autocomplete="off">
		</div>

		<div class="form-field">
			<label for="annefpugh_name"><?php esc_html_e( 'Name', 'annefpugh' ); ?></label>
			<input type="text" id="annefpugh_name" name="annefpugh_name" required maxlength="100">
		</div>

		<div class="form-field">
			<label for="annefpugh_email"><?php esc_html_e( 'Email', 'annefpugh' ); ?></label>
			<input type="email" id="annefpugh_email" name="annefpugh_email" required maxlength="150">
		</div>

		<div class="form-field">
			<label for="annefpugh_message"><?php esc_html_e( 'Message', 'annefpugh' ); ?></label>
			<textarea id="annefpugh_message" name="annefpugh_message" rows="6" required maxlength="2000"></textarea>
		</div>

		<button type="submit"><?php esc_html_e( 'Send', 'annefpugh' ); ?></button>
	</form>
</article>

<?php
get_footer();

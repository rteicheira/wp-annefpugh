<?php
/**
 * Template Name: Contact
 *
 * Page intro, contact form, and practice details side by side.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/page' );
endwhile;

$annefpugh_phone = annefpugh_mod( 'phone' );
$annefpugh_email = annefpugh_mod( 'email' );
?>

<section class="section contact-section">
	<div class="container contact-grid">
		<div>
			<h2><?php esc_html_e( 'Send a message', 'annefpugh' ); ?></h2>
			<?php
			get_template_part(
				'template-parts/contact-form',
				null,
				array(
					'id'      => 'contact-form',
					'variant' => 'full',
				)
			);
			?>
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

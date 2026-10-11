<?php
/**
 * Site footer: crisis notice, practice details, quick links, legal links.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_phone   = annefpugh_mod( 'phone' );
$annefpugh_email   = annefpugh_public_email();
$annefpugh_address = annefpugh_mod( 'address' );
$annefpugh_license = annefpugh_mod( 'license' );
$annefpugh_credit  = trim( (string) annefpugh_mod( 'dev_credit_name' ) );
$annefpugh_credit  = annefpugh_mod( 'show_dev_credit' ) ? $annefpugh_credit : '';
?>
</main>

<footer class="site-footer">
	<div class="container">
		<?php get_template_part( 'template-parts/crisis-notice' ); ?>

		<div class="footer-grid">
			<section class="footer-col" aria-labelledby="footer-practice-heading">
				<?php echo annefpugh_site_icon( 'footer', 96 ); // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() output; decorative (the name follows). ?>
				<h2 id="footer-practice-heading" class="footer-heading"><?php echo esc_html( annefpugh_therapist_name() ); ?></h2>
				<?php if ( annefpugh_mod( 'therapist_credentials' ) ) : ?>
					<p><?php echo esc_html( annefpugh_mod( 'therapist_credentials' ) ); ?></p>
				<?php endif; ?>
				<?php if ( $annefpugh_address ) : ?>
					<address><?php echo nl2br( esc_html( $annefpugh_address ) ); ?></address>
				<?php endif; ?>
				<ul class="footer-contact">
					<?php if ( $annefpugh_phone ) : ?>
						<li><a href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>"><?php echo esc_html( $annefpugh_phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $annefpugh_email ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( $annefpugh_email ) ); ?>"><?php echo esc_html( antispambot( $annefpugh_email ) ); ?></a></li>
					<?php endif; ?>
				</ul>
			</section>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="footer-col" aria-labelledby="footer-links-heading">
					<h2 id="footer-links-heading" class="footer-heading"><?php esc_html_e( 'Quick links', 'annefpugh' ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'legal' ) || get_privacy_policy_url() ) : ?>
				<nav class="footer-col" aria-labelledby="footer-legal-heading">
					<h2 id="footer-legal-heading" class="footer-heading"><?php esc_html_e( 'Policies', 'annefpugh' ); ?></h2>
					<?php
					if ( has_nav_menu( 'legal' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'legal',
								'container'      => false,
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
					} else {
						echo '<ul class="footer-menu"><li>' . get_the_privacy_policy_link() . '</li></ul>'; // phpcs:ignore WordPress.Security.EscapeOutput -- core returns escaped markup.
					}
					?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
			<?php if ( $annefpugh_license ) : ?>
				<p><?php echo esc_html( $annefpugh_license ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( '' !== $annefpugh_credit ) : ?>
			<p class="footer-credit">
				<?php echo esc_html( annefpugh_required_text( 'dev_credit_text' ) ); ?>
				<?php if ( annefpugh_mod( 'dev_credit_url' ) ) : ?>
					<a href="<?php echo esc_url( annefpugh_mod( 'dev_credit_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $annefpugh_credit ); ?><span aria-hidden="true"> &#8599;</span><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'annefpugh' ); ?></span></a>
				<?php else : ?>
					<?php echo esc_html( $annefpugh_credit ); ?>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

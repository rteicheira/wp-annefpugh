<?php
/**
 * Site footer.
 */
?>
</main>

<footer class="site-footer">
	<div class="crisis-notice">
		<p>
			<strong><?php esc_html_e( 'In crisis right now?', 'annefpugh' ); ?></strong>
			<?php esc_html_e( 'Call or text', 'annefpugh' ); ?>
			<a href="tel:988">988</a>
			<?php esc_html_e( '(Suicide & Crisis Lifeline), or call', 'annefpugh' ); ?>
			<a href="tel:911">911</a>
			<?php esc_html_e( 'if you or someone else is in immediate danger. This website and email are not monitored for emergencies.', 'annefpugh' ); ?>
		</p>
	</div>

	<div class="footer-columns">
		<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'annefpugh' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<p class="site-contact-line">
			<?php
			$phone  = get_theme_mod( 'annefpugh_phone' );
			$email  = get_theme_mod( 'annefpugh_email' );
			$pt_url = get_theme_mod( 'annefpugh_psychology_today_url' );
			if ( $phone ) {
				echo '<span class="phone">' . esc_html( $phone ) . '</span>';
			}
			if ( $email ) {
				echo ' <a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>';
			}
			if ( $pt_url ) {
				echo ' <a href="' . esc_url( $pt_url ) . '" rel="noopener">' . esc_html__( 'Psychology Today', 'annefpugh' ) . '</a>';
			}
			?>
		</p>
	</div>

	<div class="footer-bottom">
		<p class="site-copyright">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
		</p>
		<nav class="footer-nav-legal" aria-label="<?php esc_attr_e( 'Legal', 'annefpugh' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'legal',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

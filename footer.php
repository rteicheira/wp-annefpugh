<?php
/**
 * Site footer.
 */
?>
</main>

<footer class="site-footer">
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
		$phone = get_theme_mod( 'annefpugh_phone' );
		$email = get_theme_mod( 'annefpugh_email' );
		if ( $phone ) {
			echo '<span class="phone">' . esc_html( $phone ) . '</span>';
		}
		if ( $email ) {
			echo ' <a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>';
		}
		?>
	</p>

	<p class="site-copyright">
		&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>

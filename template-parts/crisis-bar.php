<?php
/**
 * Slim crisis bar above the header (toggle: Customizer → Crisis & Safety).
 */
?>
<aside class="crisis-bar" aria-label="<?php esc_attr_e( 'Crisis resources', 'annefpugh' ); ?>">
	<div class="container">
		<p>
			<?php
			printf(
				/* translators: 1: 988 phone link, 2: 911 phone link */
				esc_html__( 'In crisis? Call or text %1$s, or call %2$s in an emergency.', 'annefpugh' ),
				'<a href="tel:988">988</a>',
				'<a href="tel:911">911</a>'
			);
			?>
		</p>
	</div>
</aside>

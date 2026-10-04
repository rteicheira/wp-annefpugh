<?php
/**
 * Slim crisis bar above the header (Customizer → Crisis & Safety).
 */

defined( 'ABSPATH' ) || exit;
?>
<aside class="crisis-bar" aria-label="<?php esc_attr_e( 'Crisis resources', 'annefpugh' ); ?>">
	<div class="container">
		<p><?php echo annefpugh_linkify_phone_numbers( annefpugh_required_text( 'crisis_bar_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></p>
	</div>
</aside>

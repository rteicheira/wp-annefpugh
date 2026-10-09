<?php
/**
 * Footer crisis notice — always shown. Wording is editable under
 * Customizer → Crisis & Safety; blank fields fall back to the defaults.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_crisis_items = array_filter( array_map( 'trim', explode( "\n", annefpugh_required_text( 'crisis_items' ) ) ) );
?>
<section class="crisis-notice" aria-labelledby="crisis-notice-heading">
	<h2 id="crisis-notice-heading" class="crisis-notice__heading"><?php echo esc_html( annefpugh_required_text( 'crisis_heading' ) ); ?></h2>
	<ul class="crisis-notice__list">
		<?php foreach ( $annefpugh_crisis_items as $annefpugh_item ) : ?>
			<li><?php echo annefpugh_crisis_html( $annefpugh_item ); // phpcs:ignore WordPress.Security.EscapeOutput -- filtered by wp_kses() inside. ?></li>
		<?php endforeach; ?>
	</ul>
	<p class="crisis-notice__disclaimer"><?php echo annefpugh_linkify_phone_numbers( annefpugh_required_text( 'crisis_disclaimer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?></p>
</section>

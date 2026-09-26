<?php
/**
 * Full crisis notice — always shown in the footer, not configurable.
 */
?>
<section class="crisis-notice" aria-labelledby="crisis-notice-heading">
	<h2 id="crisis-notice-heading" class="crisis-notice__heading"><?php esc_html_e( 'If you are in crisis', 'annefpugh' ); ?></h2>
	<ul class="crisis-notice__list">
		<li>
			<?php
			printf(
				/* translators: %s: 988 phone link */
				esc_html__( 'Call or text %s — the Suicide & Crisis Lifeline, free and available 24/7.', 'annefpugh' ),
				'<a href="tel:988">988</a>'
			);
			?>
		</li>
		<li>
			<?php
			printf(
				/* translators: %s: 911 phone link */
				esc_html__( 'If you or someone else is in immediate danger, call %s or go to the nearest emergency room.', 'annefpugh' ),
				'<a href="tel:911">911</a>'
			);
			?>
		</li>
		<li>
			<?php
			printf(
				/* translators: %s: SMS link to 741741 */
				esc_html__( 'Prefer texting? Text HOME to %s (Crisis Text Line).', 'annefpugh' ),
				'<a href="sms:741741?&amp;body=HOME">741741</a>'
			);
			?>
		</li>
	</ul>
	<p class="crisis-notice__disclaimer"><?php esc_html_e( 'This website, email, and contact form are not monitored for emergencies.', 'annefpugh' ); ?></p>
</section>

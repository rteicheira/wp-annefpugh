<?php
/**
 * Homepage hero section.
 */
?>
<section class="hero">
	<div class="hero-inner">
		<h1 class="hero-title"><?php bloginfo( 'name' ); ?></h1>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="hero-tagline"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
	</div>
</section>

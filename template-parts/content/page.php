<?php
/**
 * Page header (title + optional featured image banner) and content.
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
	<header class="page-header<?php echo has_post_thumbnail() ? ' page-header--has-image' : ''; ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'annefpugh-hero', array( 'class' => 'page-header__image', 'alt' => '' ) ); ?>
		<?php endif; ?>
		<div class="container">
			<h1 class="page-header__title"><?php the_title(); ?></h1>
		</div>
	</header>

	<div class="container entry-content">
		<?php the_content(); ?>
	</div>
</article>

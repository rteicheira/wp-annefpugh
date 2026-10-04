<?php
/**
 * Site header.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_phone       = annefpugh_mod( 'phone' );
$annefpugh_contact_url = annefpugh_page_url( 'page_contact' );
$annefpugh_has_nav     = has_nav_menu( 'primary' ) || $annefpugh_contact_url || $annefpugh_phone;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'annefpugh' ); ?></a>

<?php
if ( annefpugh_mod( 'show_crisis_bar' ) ) {
	get_template_part( 'template-parts/crisis-bar' );
}
?>

<header class="site-header">
	<div class="container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				<?php if ( annefpugh_mod( 'therapist_credentials' ) ) : ?>
					<span class="site-credentials"><?php echo esc_html( annefpugh_mod( 'therapist_credentials' ) ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<?php if ( $annefpugh_has_nav ) : ?>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
			<span class="nav-toggle__bars" aria-hidden="true"></span>
			<span class="nav-toggle__label"><?php esc_html_e( 'Menu', 'annefpugh' ); ?></span>
		</button>

		<nav id="site-navigation" class="site-nav" aria-label="<?php esc_attr_e( 'Main', 'annefpugh' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
			<?php if ( $annefpugh_contact_url ) : ?>
				<a class="button site-nav__cta" href="<?php echo esc_url( $annefpugh_contact_url ); ?>"><?php esc_html_e( 'Get in touch', 'annefpugh' ); ?></a>
			<?php elseif ( $annefpugh_phone ) : ?>
				<a class="button site-nav__cta" href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>"><?php echo esc_html( $annefpugh_phone ); ?></a>
			<?php endif; ?>
		</nav>
		<?php endif; ?>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">

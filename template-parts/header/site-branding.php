<?php
/**
 * Displays header site branding
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 * @version 1.0
 */

?>
<div class="site-branding">
	<div class="wrap">

		<!-- Mobile menu toggle - left column -->
		<div class="menu-toggle-wrapper">
			<button class="menu-toggle mobile-header-menu" aria-controls="top-menu" aria-expanded="false">
				<?php
				echo wuchaiwp_get_svg( array( 'icon' => 'bars' ) );
				echo wuchaiwp_get_svg( array( 'icon' => 'close' ) );
				_e( 'Menu', 'wuchaiwp' );
				?>
			</button>
		</div>

		<!-- Logo - middle column -->
		<div class="logo-column">
			<?php the_custom_logo(); ?>
		</div>

		<!-- Site title - right column -->
		<div class="title-column">
		<div class="site-branding-text">
			<?php if ( is_front_page() ) : ?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<?php else : ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
			<?php endif; ?>

			<?php
			$description = get_bloginfo( 'description', 'display' );

			if ( $description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $description; ?></p>
			<?php endif; ?>
		</div><!-- .site-branding-text -->
		</div><!-- .title-column -->

		<?php if ( ( wuchaiwp_is_frontpage() || ( is_home() && is_front_page() ) ) && ! has_nav_menu( 'top' ) ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo wuchaiwp_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'wuchaiwp' ); ?></span></a>
	<?php endif; ?>

	</div><!-- .wrap -->
</div><!-- .site-branding -->
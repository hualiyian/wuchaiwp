<?php
/**
 * The front page template file
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 * @version 1.0
 */

// 获取用户选择的首页模板
$selected_template = get_option('wuchaiwp_frontpage_template', 'default');

// 尝试加载对应的模板文件
$template_path = locate_template('template-parts/frontpage/frontpage-' . $selected_template . '.php');

if (!empty($template_path)) {
    // 如果找到对应的模板文件，直接加载
    get_header();
    get_template_part('template-parts/frontpage/frontpage', $selected_template);
    get_footer();
    exit;
}

// 默认首页（带区块）
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php
		// Show the selected front page content.
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/page/content', 'front-page' );
			endwhile;
		else :
			get_template_part( 'template-parts/post/content', 'none' );
		endif;
		?>

		<?php
		// Get each of our panels and show the post data.
		if ( 0 !== wuchaiwp_panel_count() || is_customize_preview() ) : // If we have pages to show.

			/**
			 * Filters the number of front page sections in Twenty Seventeen.
			 *
			 * @since Twenty Seventeen 1.0
			 *
			 * @param int $num_sections Number of front page sections.
			 */
			$num_sections = apply_filters( 'wuchaiwp_front_page_sections', 4 );
			global $wuchaiwpcounter;

			// Create a setting and control for each of the sections available in the theme.
			for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {
				$wuchaiwpcounter = $i;
				wuchaiwp_front_page_section( null, $i );
			}

		endif; // The if ( 0 !== wuchaiwp_panel_count() ) ends here.
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer();
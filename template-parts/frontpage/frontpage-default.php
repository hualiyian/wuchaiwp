<?php
/**
 * 默认首页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php
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
        if ( 0 !== wuchaiwp_panel_count() || is_customize_preview() ) :
            $num_sections = apply_filters( 'wuchaiwp_front_page_sections', 4 );
            global $wuchaiwpcounter;

            for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {
                $wuchaiwpcounter = $i;
                wuchaiwp_front_page_section( null, $i );
            }
        endif;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
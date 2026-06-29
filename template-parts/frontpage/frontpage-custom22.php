<?php
/**
 * 着陆页222首页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main landing-page" role="main">

        <div class="landing-hero">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    the_content();
                endwhile;
            endif;
            ?>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
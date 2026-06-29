<?php
/**
 * 文章列表首页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="page-header">
            <h1 class="page-title"><?php bloginfo( 'name' ); ?></h1>
            <p class="page-description"><?php bloginfo( 'description' ); ?></p>
        </header><!-- .page-header -->

        <?php
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 10,
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) :
                $query->the_post();
                get_template_part( 'template-parts/post/content', get_post_format() );
            endwhile;
            the_posts_navigation();
        else :
            get_template_part( 'template-parts/post/content', 'none' );
        endif;
        wp_reset_postdata();
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
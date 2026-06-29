<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php
        while (have_posts()) : the_post();
            $custom_url = get_post_meta(get_the_ID(), 'wuchai_custom_url', true);
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    <div class="entry-meta">
                        <span class="posted-on"><?php the_date(); ?></span>
                        <span class="byline">作者：<?php the_author(); ?></span>
                    </div><!-- .entry-meta -->
                </header><!-- .entry-header -->

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div><!-- .entry-content -->

                <footer class="entry-footer">
                    <div class="entry-stats">
                        <span class="views">👁️ <?php echo get_post_meta(get_the_ID(), 'wuchai_views', true) ?: '0'; ?></span>
                        <span class="downloads">⬇️ <?php echo get_post_meta(get_the_ID(), 'wuchai_downloads', true) ?: '0'; ?></span>
                    </div>
                    <?php
                    // 显示自定义分类
                    $custom_taxonomies = get_object_taxonomies(get_post_type(), 'objects');
                    foreach ($custom_taxonomies as $taxonomy) {
                        if ($terms = get_the_terms(get_the_ID(), $taxonomy->name)) {
                            echo '<div class="entry-terms">';
                            echo '<span class="taxonomy-label">' . $taxonomy->labels->singular_name . '：</span>';
                            echo the_terms(get_the_ID(), $taxonomy->name, '', ', ', '');
                            echo '</div>';
                        }
                    }
                    ?>
                </footer><!-- .entry-footer -->
            </article><!-- #post-## -->

            <?php
            // 如果不是外部链接，显示评论
            if (empty($custom_url)) {
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
            }

        endwhile; // End of the loop.
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
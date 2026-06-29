<?php
/**
 * 默认归档页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="archive-header">
            <h1 class="archive-title"><?php post_type_archive_title(); ?></h1>
        </header>

        <div class="posts-list">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('post-item'); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                        <div class="post-meta">
                            <?php the_date(); ?> | <?php the_author(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php the_posts_navigation(); ?>
            <?php else : ?>
                <p>暂无内容</p>
            <?php endif; ?>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
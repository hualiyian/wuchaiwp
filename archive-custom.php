<?php
/**
 * 默认自定义文章类型归档模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="archive-header">
            <?php
            $post_type = get_query_var('post_type');
            $post_type_obj = get_post_type_object($post_type);
            ?>
            <h1 class="archive-title"><?php echo $post_type_obj ? $post_type_obj->labels->name : '文章列表'; ?></h1>
            <?php if (term_description()) : ?>
                <div class="archive-description"><?php echo term_description(); ?></div>
            <?php endif; ?>
        </header>

        <div class="posts-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <p class="post-excerpt"><?php the_excerpt(); ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php the_posts_navigation(); ?>
            <?php else : ?>
                <div class="no-posts">
                    <p>暂无内容</p>
                </div>
            <?php endif; ?>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
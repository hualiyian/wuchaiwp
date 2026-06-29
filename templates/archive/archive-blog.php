<?php
/**
 * Template Name: 博客归档页
 * Description: 简约完整的博客文章归档页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <div class="blog-archive-container">
            
             <!-- 面包屑导航 -->
                                    <div class="breadcrumb">
                                 
                                <a href="<?php echo home_url(); ?>">🏠 首页</a>
                                <span class="separator">›</span>
                                
                                <?php 
                                $post_type = get_post_type();
                                $post_type_obj = get_post_type_object($post_type);
                                if ($post_type_obj) {
                                    $archive_link = get_post_type_archive_link($post_type);
                                    echo '<a href="' . esc_url($archive_link) . '">' . esc_html($post_type_obj->labels->name) . '</a>';
                                    echo '<span class="separator">›</span>';
                                }
                                
                                $categories = get_the_terms(get_the_ID(), 'category');
                                if ($categories && !is_wp_error($categories)) {
                                    $category = reset($categories);
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                    echo '<span class="separator">›</span>';
                                }
                                ?>
                                
                                <span class="current"><a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></span>
                            </div>

       
            
            <!-- 页面标题 -->
            <header class="archive-header">
                <h1 class="archive-title">📝 博客文章</h1>
                <p class="archive-description">记录生活点滴，分享知识感悟</p>
            </header>

            <!-- 筛选区域 -->
            <div class="filter-section">
                <!-- 分类筛选 -->
                <div class="filter-categories">
                    <span class="filter-label">📁 分类：</span>
                    <div class="filter-tags">
                        <a href="<?php echo get_post_type_archive_link(get_post_type()); ?>" class="filter-tag <?php echo !isset($_GET['cat']) ? 'active' : ''; ?>">全部</a>
                        <?php
                        $categories = get_categories();
                        if ($categories) {
                            foreach ($categories as $category) {
                                $active = isset($_GET['cat']) && $_GET['cat'] == $category->slug ? 'active' : '';
                                echo '<a href="' . add_query_arg('cat', $category->slug, get_post_type_archive_link(get_post_type())) . '" class="filter-tag ' . $active . '">' . esc_html($category->name) . ' (' . $category->count . ')</a>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- 标签云 -->
                <div class="filter-tags-cloud">
                    <span class="filter-label">🏷️ 标签：</span>
                    <?php
                    $tags = get_tags(array('number' => 15));
                    if ($tags) {
                        foreach ($tags as $tag) {
                            echo '<a href="' . get_tag_link($tag->term_id) . '" class="tag-cloud-item" style="font-size: ' . (12 + $tag->count * 2) . 'px;">' . esc_html($tag->name) . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- 搜索框 -->
            <div class="search-box">
                <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="text" name="s" placeholder="搜索文章..." value="<?php echo get_search_query(); ?>">
                    <button type="submit">🔍</button>
                    <input type="hidden" name="post_type" value="<?php echo get_post_type(); ?>">
                </form>
            </div>

            <!-- 统计信息 -->
            <div class="archive-stats">
                <span class="stat-item">📊 共 <strong><?php echo wp_count_posts(get_post_type())->publish; ?></strong> 篇文章</span>
                <?php
                $month_count = 0;
                $current_month = date('Y-m');
                $args = array(
                    'post_type' => get_post_type(),
                    'date_query' => array(
                        array(
                            'year'  => date('Y'),
                            'month' => date('m'),
                        ),
                    ),
                    'posts_per_page' => -1,
                );
                $query = new WP_Query($args);
                $month_count = $query->post_count;
                ?>
                <span class="stat-item">🔥 本月更新 <strong><?php echo $month_count; ?></strong> 篇</span>
            </div>

            <!-- 文章列表 -->
            <div class="posts-grid">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('class' => 'post-image', 'alt' => get_the_title())); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <!-- 分类标签 -->
                                <?php
                                $categories = get_the_category();
                                if ($categories) {
                                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="post-category">' . esc_html($categories[0]->name) . '</a>';
                                }
                                ?>
                                
                                <!-- 标题 -->
                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <!-- 摘要 -->
                                <p class="post-excerpt">
                                    <?php
                                    $custom_excerpt = get_post_meta(get_the_ID(), 'wuchaiwp_blog_excerpt', true);
                                    echo !empty($custom_excerpt) ? wp_trim_words($custom_excerpt, 40) : get_the_excerpt();
                                    ?>
                                </p>
                                
                                <!-- 元信息 -->
                                <div class="post-meta">
                                    <span class="meta-author">
                                        <?php
                                        $author_name = get_post_meta(get_the_ID(), 'wuchaiwp_blog_author_name', true);
                                        $display_name = !empty($author_name) ? $author_name : get_the_author();
                                        ?>
                                        👤 <?php echo esc_html($display_name); ?>
                                    </span>
                                    <span class="meta-date">📅 <?php the_date('Y-m-d'); ?></span>
                                    <?php
                                    $views = get_post_meta(get_the_ID(), 'wuchaiwp_views', true);
                                    $views = empty($views) ? 0 : (int)$views;
                                    ?>
                                    <span class="meta-views">👁️ <?php echo $views; ?></span>
                                    <span class="meta-comments">💬 <?php comments_number('0', '1', '%'); ?></span>
                                </div>
                                
                                <!-- 标签 -->
                                <?php
                                $post_tags = get_the_tags();
                                if ($post_tags && !is_wp_error($post_tags)) {
                                    echo '<div class="post-tags">';
                                    foreach (array_slice($post_tags, 0, 3) as $tag) {
                                        echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="mini-tag">' . esc_html($tag->name) . '</a>';
                                    }
                                    if (count($post_tags) > 3) {
                                        echo '<span class="more-tags">+' . (count($post_tags) - 3) . '</span>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    
                    <!-- 分页导航 -->
                    <div class="pagination">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '←',
                            'next_text' => '→',
                            'screen_reader_text' => ' '
                        ));
                        ?>
                    </div>
                    
                <?php else : ?>
                    <div class="no-posts">
                        <div class="no-posts-icon">📭</div>
                        <p>暂无博客文章</p>
                        <a href="<?php echo admin_url('post-new.php'); ?>">📝 写一篇新文章</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<style>
/* 强制全屏显示 - 覆盖父容器限制 */
#primary.content-area,
.site-main {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

.blog-archive-container {
    max-width: 100% !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 20px 40px;
    box-sizing: border-box;
}

.archive-header {
    text-align: center;
    margin-bottom: 40px;
}

.archive-title {
    font-size: 36px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.archive-description {
    font-size: 16px;
    color: #666;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.filter-categories,
.filter-tags-cloud {
    margin-bottom: 15px;
}

.filter-categories:last-child,
.filter-tags-cloud:last-child {
    margin-bottom: 0;
}

.filter-label {
    font-weight: 600;
    color: #333;
    margin-right: 10px;
}

.filter-tags {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-tag {
    padding: 6px 14px;
    background: #fff;
    color: #333;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    border: 1px solid #ddd;
}

.filter-tag.active {
    background: #3498db;
    color: #fff;
    border-color: #3498db;
}

.tag-cloud-item {
    display: inline-block;
    padding: 4px 10px;
    background: #fff;
    color: #666;
    border-radius: 4px;
    text-decoration: none;
    margin: 4px;
}

.tag-cloud-item:hover {
    background: #3498db;
    color: #fff;
}

.search-box {
    margin-bottom: 20px;
}

.search-box form {
    display: flex;
    gap: 10px;
}

.search-box input[type="text"] {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}

.search-box button {
    padding: 12px 20px;
    background: #3498db;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
}

.archive-stats {
    display: flex;
    gap: 30px;
    margin-bottom: 20px;
    padding: 15px 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.stat-item {
    font-size: 14px;
    color: #666;
}

.stat-item strong {
    color: #3498db;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 30px;
    width: 100%;
}

.post-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
}

.post-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s;
}

.post-card:hover .post-image {
    transform: scale(1.05);
}

.post-content {
    padding: 20px;
}

.post-category {
    display: inline-block;
    padding: 4px 12px;
    background: #e8f4fd;
    color: #3498db;
    border-radius: 4px;
    font-size: 12px;
    text-decoration: none;
    margin-bottom: 10px;
}

.post-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.post-title a {
    color: #333;
    text-decoration: none;
}

.post-title a:hover {
    color: #3498db;
}

.post-excerpt {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin: 0 0 15px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 13px;
    color: #888;
    margin-bottom: 12px;
}

.post-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.mini-tag {
    padding: 3px 8px;
    background: #f0f0f0;
    color: #666;
    border-radius: 3px;
    font-size: 12px;
    text-decoration: none;
}

.mini-tag:hover {
    background: #3498db;
    color: #fff;
}

.more-tags {
    font-size: 12px;
    color: #999;
    padding: 3px 8px;
}

.pagination {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: 30px;
}

.pagination .page-numbers {
    display: inline-block;
    padding: 8px 16px;
    margin: 0 4px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    color: #333;
    text-decoration: none;
}

.pagination .page-numbers.current {
    background: #3498db;
    color: #fff;
    border-color: #3498db;
}

.pagination .page-numbers:hover:not(.current) {
    background: #f0f0f0;
}

.no-posts {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.no-posts-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.no-posts p {
    font-size: 16px;
    color: #666;
    margin-bottom: 15px;
}

.no-posts a {
    display: inline-block;
    padding: 10px 25px;
    background: #3498db;
    color: #fff;
    border-radius: 25px;
    text-decoration: none;
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-section {
        padding: 15px;
    }
    
    .archive-stats {
        flex-direction: column;
        gap: 10px;
    }
    
    .post-meta {
        gap: 10px;
    }
}
</style>

<?php get_footer(); ?>
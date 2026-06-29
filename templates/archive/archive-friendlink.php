<?php
/**
 * Template Name: 友情链接归档页
 * Description: 简约完整的友情链接归档页模板
 */

get_header(); ?>

<?php
// 获取图标API设置
$friendlink_icon_api = get_option('wuchaiwp_friendlink_icon_api', 'https://t3.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&size=128&url=');

// 生成图标URL的函数
function wuchaiwp_archive_get_icon($logo, $url, $api) {
    if (!empty($logo)) return $logo;
    if (empty($url) || empty($api)) return '';
    
    if (strpos($api, '%s') !== false) {
        $parsed = parse_url($url);
        $domain = isset($parsed['host']) ? $parsed['host'] : $url;
        return sprintf($api, urlencode($domain));
    } else {
        return $api . urlencode($url);
    }
}
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <div class="friendlink-archive-container">
            
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
                <h1 class="archive-title">🔗 友情链接</h1>
                <p class="archive-description">认识更多志同道合的朋友</p>
            </header>

            <!-- 搜索框 -->
            <div class="search-box">
                <form method="get" action="<?php echo esc_url(get_post_type_archive_link('friendlink')); ?>">
                    <input type="text" name="s" placeholder="搜索友情链接..." value="<?php echo get_search_query(); ?>">
                    <button type="submit">🔍</button>
                    <input type="hidden" name="post_type" value="friendlink">
                </form>
            </div>

            <!-- 统计信息 -->
            <div class="archive-stats">
                <span class="stat-item">📊 共 <strong><?php echo wp_count_posts('friendlink')->publish; ?></strong> 个友情链接</span>
            </div>

            <!-- 链接列表 - 按分类分组 -->
            <div class="friendlinks-groups">
                <?php
                $links_by_category = array();
                $uncategorized_links = array();
                
                // 定义可能的分类法
                $possible_taxonomies = array('category', 'post_tag', 'friendlink_category');
                
                // 获取所有友情链接
                $args = array(
                    'post_type' => 'friendlink',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                );
                $friendlinks = new WP_Query($args);
                
                if ($friendlinks->have_posts()) :
                    while ($friendlinks->have_posts()) : $friendlinks->the_post();
                        $friendlink_url = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_url', true);
                        $friendlink_logo = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_logo', true);
                        $icon_url = wuchaiwp_archive_get_icon($friendlink_logo, $friendlink_url, $friendlink_icon_api);
                        
                        $link_data = array(
                            'title' => get_the_title(),
                            'url' => $friendlink_url,
                            'icon_url' => $icon_url,
                            'id' => get_the_ID()
                        );
                        
                        // 获取链接所属的分类（使用全局 $post 对象，不受权限限制）
                        $has_category = false;
                        foreach ($possible_taxonomies as $taxonomy) {
                            if (taxonomy_exists($taxonomy)) {
                                // 使用 get_terms 直接查询该文章的分类，不受权限限制
                                $terms = get_terms(array(
                                    'taxonomy' => $taxonomy,
                                    'object_ids' => get_the_ID(),
                                    'hide_empty' => false,
                                    'fields' => 'all'
                                ));
                                if (!is_wp_error($terms) && !empty($terms)) {
                                    foreach ($terms as $term) {
                                        if (!isset($links_by_category[$term->term_id])) {
                                            $links_by_category[$term->term_id] = array(
                                                'name' => $term->name,
                                                'slug' => $term->slug,
                                                'links' => array()
                                            );
                                        }
                                        $links_by_category[$term->term_id]['links'][] = $link_data;
                                        $has_category = true;
                                    }
                                }
                            }
                        }
                        
                        // 没有分类的链接
                        if (!$has_category) {
                            $uncategorized_links[] = $link_data;
                        }
                    endwhile;
                    wp_reset_postdata();
                    
                    // 显示各分类的链接
                    foreach ($links_by_category as $category) :
                ?>
                        <div class="friendlink-group">
                            <h3 class="friendlink-group-title">📁 <?php echo esc_html($category['name']); ?> <span class="group-count">(<?php echo count($category['links']); ?>)</span></h3>
                            <div class="friendlinks-list">
                                <?php foreach ($category['links'] as $link) : ?>
                                    <a href="<?php echo !empty($link['url']) ? esc_url($link['url']) : '#'; ?>" class="friendlink-item" target="_blank" rel="noopener noreferrer">
                                        <span class="friendlink-icon">
                                            <?php if ($link['icon_url']) : ?>
                                                <img src="<?php echo esc_url($link['icon_url']); ?>" alt="<?php echo esc_attr($link['title']); ?>" loading="lazy">
                                            <?php else : ?>
                                                🌐
                                            <?php endif; ?>
                                        </span>
                                        <span class="friendlink-name"><?php echo esc_html($link['title']); ?><br><?php echo esc_html($link['url']); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                   
                    <?php 
                    
                     // 显示未分类的链接
                    if (!empty($uncategorized_links)) : ?>
                        <div class="friendlink-group">
                            <h3 class="friendlink-group-title">📁 未分类 <span class="group-count">(<?php echo count($uncategorized_links); ?>)</span></h3>
                            <div class="friendlinks-list">
                                <?php foreach ($uncategorized_links as $link) : ?>
                                    <a href="<?php echo !empty($link['url']) ? esc_url($link['url']) : '#'; ?>" class="friendlink-item" target="_blank" rel="noopener noreferrer">
                                        <span class="friendlink-icon">
                                            <?php if ($link['icon_url']) : ?>
                                                <img src="<?php echo esc_url($link['icon_url']); ?>" alt="<?php echo esc_attr($link['title']); ?>" loading="lazy">
                                            <?php else : ?>
                                                🌐
                                            <?php endif; ?>
                                        </span>
                                        <span class="friendlink-name"><?php echo esc_html($link['title']); ?><br><?php echo esc_html($link['url']); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php else : ?>
                    <div class="no-posts">
                        <div class="no-posts-icon">🔗</div>
                        <p>暂无友情链接</p>
                        <a href="<?php echo admin_url('post-new.php?post_type=friendlink'); ?>">➕ 添加友情链接</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<style>
/* 强制全屏显示 */
#primary.content-area,
.site-main {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

.friendlink-archive-container {
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

.search-box {
    max-width: 500px;
    margin: 0 auto 20px auto;
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
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.stat-item {
    font-size: 14px;
    color: #666;
}

.stat-item strong {
    color: #3498db;
}

.friendlinks-groups {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
}

.friendlink-group {
    margin-bottom: 30px;
    padding: 20px;
    background: #fafafa;
    border-radius: 12px;
}

.friendlink-group-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
}

.group-count {
    font-size: 14px;
    font-weight: normal;
    color: #666;
}

.friendlink-archive-container .friendlinks-list {
    display: grid !important;
    grid-template-columns: repeat(5, 1fr) !important;
    gap: 8px;
    width: 100%;
    max-width: 100%;
    max-height: none !important;
    margin: 0 auto;
}

.friendlink-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #fff;
    border-radius: 6px;
    text-decoration: none;
    color: #2d3748;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.friendlink-item:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

.friendlink-icon {
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.friendlink-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.friendlink-name {
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
    max-width: 20ch;!important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.pagination {
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

.no-posts {
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

/* 中屏幕适配 - 保持5列 */
@media (max-width: 1199px) {
    .friendlink-archive-container .friendlinks-list {
        grid-template-columns: repeat(5, 1fr) !important;
    }
}

/* 小屏幕适配 */
@media (max-width: 767px) {
    .friendlink-archive-container .friendlinks-list {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 6px;
    }
    
    .friendlink-item {
        padding: 10px 8px;
        gap: 6px;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }
    
    .friendlink-icon {
        width: 18px;
        height: 18px;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .friendlink-name {
        font-size: 12px;
        flex-shrink: 1;
        min-width: 0;
        max-width: 14ch;!important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

/* 超小屏幕优化 */
@media (max-width: 480px) {
    .friendlink-archive-container .friendlinks-list {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 4px;
    }
    
    .friendlink-item {
        padding: 8px 6px;
        gap: 4px;
    }
    
    .friendlink-icon {
        width: 16px;
        height: 16px;
        font-size: 12px;
    }
    
    .friendlink-name {
        font-size: 12px;
        max-width: 14ch;!important;
    }
}
</style>

<?php get_footer(); ?>
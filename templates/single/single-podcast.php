<?php
/**
 * Template Name: 播客详情页
 * Description: 适配 PowerPress 播客插件的音频内容详情页模板
 */

// 在模板开始时就获取当前文章ID和显示设置
global $wp_query;
$current_post_id = 0;

if (is_singular('podcast')) {
    $current_post_id = get_the_ID();
} elseif (!empty($wp_query->posts)) {
    $current_post_id = $wp_query->posts[0]->ID;
}

// 获取显示设置
$hide_site_header = $current_post_id ? get_post_meta($current_post_id, 'wuchaiwp_podcast_hide_site_header', true) : false;
$hide_site_footer = $current_post_id ? get_post_meta($current_post_id, 'wuchaiwp_podcast_hide_site_footer', true) : false;

// 如果需要隐藏页头，不调用 get_header()
if (!$hide_site_header) {
    get_header(); 
}

// 获取其他显示设置
$hide_title = get_post_meta(get_the_ID(), 'wuchaiwp_podcast_hide_title', true);
$hide_meta = get_post_meta(get_the_ID(), 'wuchaiwp_podcast_hide_meta', true);
$hide_comments = get_post_meta(get_the_ID(), 'wuchaiwp_podcast_hide_comments', true);
$hide_sidebar = get_post_meta(get_the_ID(), 'wuchaiwp_podcast_hide_sidebar', true);

?>

<?php if ($hide_site_header) : ?>
<!-- 自定义页面框架（隐藏默认页头时使用） -->
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if (has_site_icon()) : ?>
        <link rel="icon" href="<?php echo esc_url(get_site_icon_url()); ?>" sizes="32x32" />
    <?php endif; ?>
    <?php wp_head(); ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
        }
    </style>
</head>
<body <?php body_class(); ?>>
<?php endif; ?>

<!-- 页面主要内容 -->
<div class="podcast-single-container">
    <div class="podcast-main">
        <?php while (have_posts()) : the_post(); ?>
        
            <!-- 播客封面 -->
            <div class="podcast-cover">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('large', array('class' => 'cover-image')); ?>
                <?php else : ?>
                    <div class="default-cover">
                        <span class="cover-icon">🎙️</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 播客信息 -->
            <header class="podcast-header">
                <?php if (!$hide_title) : ?>
                    <div class="podcast-badge">🎧 播客</div>
                    <h1 class="podcast-title"><?php the_title(); ?></h1>
                <?php endif; ?>
                
                <?php if (!$hide_meta) : ?>
                    <div class="podcast-meta">
                        <span class="meta-author">👤 <?php the_author(); ?></span>
                        <span class="meta-date">📅 <?php the_date('Y年m月d日'); ?></span>
                        <span class="meta-duration">⏱️ <?php echo get_post_meta(get_the_ID(), 'powerpress_duration', true) ?: '未知时长'; ?></span>
                        <span class="meta-views">👁️ <?php echo get_post_meta(get_the_ID(), 'wuchaiwp_views', true) ?: '0'; ?></span>
                    </div>
                <?php endif; ?>
            </header>

            <!-- PowerPress 播放器 -->
            <div class="podcast-player">
                <?php 
                // 输出 PowerPress 播放器
                if (function_exists('powerpress_get_enclosure')) {
                    powerpress_content();
                } else {
                    echo '<div class="no-player">暂无音频播放器</div>';
                }
                ?>
            </div>

            <!-- 播客简介 -->
            <div class="podcast-description">
                <h3>📝 节目简介</h3>
                <?php the_content(); ?>
            </div>

            <!-- 播客信息面板 -->
            <?php if (!$hide_meta) : ?>
            <div class="podcast-info-panel">
                <div class="info-item">
                    <span class="info-label">🎙️ 主播</span>
                    <span class="info-value"><a href="<?php echo get_author_posts_url(get_post_field('post_author', get_the_ID())); ?>"><?php the_author(); ?></a></span>
                </div>
                <div class="info-item">
                    <span class="info-label">📁 分类</span>
                    <?php 
                    $categories = get_the_category();
                    if ($categories) {
                        foreach ($categories as $cat) {
                            echo '<a href="' . get_category_link($cat->term_id) . '" class="info-value">' . $cat->name . '</a>';
                        }
                    }
                    ?>
                </div>
                <div class="info-item">
                    <span class="info-label">🏷️ 标签</span>
                    <?php 
                    $tags = get_the_tags();
                    if ($tags) {
                        foreach ($tags as $tag) {
                            echo '<a href="' . get_tag_link($tag->term_id) . '" class="info-value tag">' . $tag->name . '</a>';
                        }
                    }
                    ?>
                </div>
                <div class="info-item">
                    <span class="info-label">📊 文件大小</span>
                    <span class="info-value"><?php echo get_post_meta(get_the_ID(), 'powerpress_filesize', true) ?: '未知'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">🔗 RSS链接</span>
                    <a href="<?php echo get_post_meta(get_the_ID(), 'powerpress_feed', true) ?: get_feed_link(); ?>" class="info-value" target="_blank">订阅本播客</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- 分享按钮 -->
            <div class="share-buttons">
                <span class="share-label">📤 分享：</span>
                <button class="share-btn" onclick="wuchaiwp_share('wechat')">💬 微信</button>
                <button class="share-btn" onclick="wuchaiwp_share('weibo')">📱 微博</button>
                <button class="share-btn" onclick="wuchaiwp_share('link')">🔗 复制链接</button>
            </div>

            <!-- 评论区域 -->
            <?php if (!$hide_comments && comments_open()) : ?>
            <div class="comments-section">
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>

        <?php endwhile; ?>
    </div>

    <!-- 侧边栏 -->
    <?php if (!$hide_sidebar) : ?>
    <aside class="podcast-sidebar">
        <!-- 最新播客 -->
        <div class="sidebar-widget">
            <h3>🎧 最新播客</h3>
            <ul class="recent-podcasts">
                <?php
                $recent_podcasts = get_posts(array(
                    'post_type' => 'podcast',
                    'posts_per_page' => 5,
                    'post__not_in' => array(get_the_ID())
                ));
                foreach ($recent_podcasts as $podcast) :
                ?>
                <li>
                    <a href="<?php echo get_permalink($podcast->ID); ?>">
                        <?php if (has_post_thumbnail($podcast->ID)) : ?>
                            <?php echo get_the_post_thumbnail($podcast->ID, 'thumbnail'); ?>
                        <?php endif; ?>
                        <span class="podcast-title"><?php echo get_the_title($podcast->ID); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- 订阅按钮 -->
        <div class="sidebar-widget subscribe-widget">
            <h3>📡 订阅播客</h3>
            <p>订阅后第一时间获取最新节目</p>
            <a href="<?php echo get_feed_link(); ?>" class="subscribe-btn">
                <span class="btn-icon">🎧</span>
                <span class="btn-text">通过 RSS 订阅</span>
            </a>
        </div>
    </aside>
    <?php endif; ?>
</div>

<!-- 样式代码 -->
<style>
.podcast-single-container {
    display: flex;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
    min-height: calc(100vh - 60px);
}

.podcast-main {
    flex: 1;
}

.podcast-sidebar {
    width: 320px;
    flex-shrink: 0;
}

.podcast-cover {
    margin-bottom: 25px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.cover-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
}

.default-cover {
    width: 100%;
    height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cover-icon {
    font-size: 80px;
}

.podcast-header {
    margin-bottom: 25px;
}

.podcast-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    font-size: 12px;
    margin-bottom: 15px;
}

.podcast-title {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
    line-height: 1.4;
}

.podcast-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 14px;
    color: #666;
}

.podcast-player {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.no-player {
    text-align: center;
    padding: 30px;
    color: #999;
}

.podcast-description {
    margin-bottom: 25px;
}

.podcast-description h3 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #2c3e50;
}

.podcast-description p {
    line-height: 1.8;
    color: #444;
}

.podcast-info-panel {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #ddd;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: #666;
    font-weight: 500;
}

.info-value {
    color: #333;
}

.info-value.tag {
    display: inline-block;
    padding: 3px 8px;
    background: #fff;
    border-radius: 4px;
    margin-left: 5px;
    font-size: 12px;
}

.share-buttons {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.share-label {
    font-weight: 500;
    color: #333;
}

.share-btn {
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
}

.share-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.comments-section {
    padding-top: 25px;
    border-top: 1px solid #eee;
}

.sidebar-widget {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.sidebar-widget h3 {
    margin: 0 0 15px 0;
    font-size: 16px;
    color: #2c3e50;
}

.recent-podcasts {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-podcasts li {
    padding: 10px 0;
    border-bottom: 1px dashed #eee;
}

.recent-podcasts li:last-child {
    border-bottom: none;
}

.recent-podcasts a {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #333;
}

.recent-podcasts img {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
}

.subscribe-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white;
}

.subscribe-widget h3 {
    color: white;
}

.subscribe-widget p {
    margin: 0 0 15px 0;
    font-size: 14px;
}

.subscribe-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px;
    background: white;
    color: #667eea;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}

/* 响应式布局 */
@media (max-width: 992px) {
    .podcast-single-container {
        flex-direction: column;
    }
    
    .podcast-sidebar {
        width: 100%;
    }
    
    .cover-image {
        height: 250px;
    }
    
    .podcast-title {
        font-size: 24px;
    }
}

@media (max-width: 768px) {
    .podcast-single-container {
        padding: 15px;
    }
    
    .cover-image {
        height: 200px;
    }
    
    .podcast-title {
        font-size: 20px;
    }
    
    .podcast-meta {
        gap: 12px;
    }
    
    .share-buttons {
        flex-wrap: wrap;
    }
}
</style>

<!-- 页面结束 -->
<?php if ($hide_site_header) : ?>
    <?php wp_footer(); ?>
    </body>
    </html>
<?php else : ?>
    <?php 
    // 根据设置决定是否调用 get_footer()
    if (!$hide_site_footer) {
        get_footer(); 
    } else {
        // 只调用 wp_footer() 输出脚本，但不输出页脚内容
        wp_footer();
        // 直接关闭 body 和 html 标签
        echo '</body></html>';
    }
    ?>
<?php endif; ?>
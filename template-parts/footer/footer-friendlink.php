<?php
/**
 * Template part for displaying footer friendlink section
 */

// 获取设置
$enabled = get_option('wuchaiwp_footer_friendlink_enabled', '1');
$post_type = get_option('wuchaiwp_footer_friendlink_post_type', '');
$icon_api = get_option('wuchaiwp_friendlink_icon_api', 'https://www.google.com/s2/favicons?domain=%s&sz=64');

// 从URL提取域名的函数
function wuchaiwp_extract_domain($url) {
    if (empty($url)) return '';
    $parsed = parse_url($url);
    if (isset($parsed['host'])) {
        return $parsed['host'];
    }
    return $url;
}

// 获取图标URL
function wuchaiwp_get_friendlink_icon($logo, $url, $api) {
    if (!empty($logo)) {
        return $logo;
    }
    if (!empty($url) && !empty($api)) {
        // 检查API是否包含占位符%s
        if (strpos($api, '%s') !== false) {
            // 老格式：使用域名占位符
            $domain = wuchaiwp_extract_domain($url);
            if (!empty($domain)) {
                return sprintf($api, urlencode($domain));
            }
        } else {
            // 新格式：直接拼接URL（如Google新API）
            return $api . urlencode($url);
        }
    }
    return '';
}

// 如果未启用或未选择自定义文章类型，不显示
if ($enabled != '1' || empty($post_type)) {
    return;
}

// 获取该自定义文章类型的文章列表
$args = array(
    'post_type' => $post_type,
    'posts_per_page' => 8, // 最多显示8个
    'post_status' => 'publish',
    'orderby' => 'menu_order title',
    'order' => 'ASC'
);

$friendlinks = new WP_Query($args);

if (!$friendlinks->have_posts()) {
    return;
}
?>

<div class="footer-friendlinks">
    <div class="friendlinks-container">
        <div class="friendlinks-header">
            <h3 class="friendlinks-title">🔗 友情链接</h3>
            <a href="<?php echo get_post_type_archive_link($post_type); ?>" class="friendlinks-more">更多</a>
        </div>
        
        <div class="friendlinks-list">
            <?php while ($friendlinks->have_posts()) : $friendlinks->the_post(); ?>
                <?php
                $friendlink_url = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_url', true);
                $friendlink_logo = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_logo', true);
                ?>
                
                <?php $icon_url = wuchaiwp_get_friendlink_icon($friendlink_logo, $friendlink_url, $icon_api); ?>
                <a href="<?php echo !empty($friendlink_url) ? esc_url($friendlink_url) : '#'; ?>" class="friendlink-item" target="_blank" rel="noopener noreferrer">
                    <span class="friendlink-icon">
                        <?php if ($icon_url) : ?>
                            <img src="<?php echo esc_url($icon_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                        <?php else : ?>
                            🌐
                        <?php endif; ?>
                    </span>
                    <span class="friendlink-name"><?php the_title(); ?></span>
                </a>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>

<style>
.footer-friendlinks {
    padding: 40px 0;
    background: #f8f9fa;
}

.friendlinks-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
}

.friendlinks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}

.friendlinks-title {
    font-size: 20px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.friendlinks-more {
    font-size: 14px;
    color: #63b3ed;
    text-decoration: none;
    transition: color 0.2s;
}

.friendlinks-more:hover {
    color: #4299e1;
    text-decoration: underline;
}

.friendlinks-list {
    display: grid;
    grid-template-columns: repeat(6, 1fr); /* 一行6个，显示两行 */
    gap: 8px;
    max-height: 140px; /* 限制高度，强制显示两行 */
    overflow: hidden;
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
    width: 20px;
    height: 20px;
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
    max-width: 14ch; /* 限制最多显示4个汉字 */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* 中屏幕适配 */
@media (max-width: 1199px) {
    .friendlinks-list {
        grid-template-columns: repeat(6, 1fr); /* 中屏幕一行6个，显示两行 */
        max-height: 140px;
    }
}

/* 小屏幕适配 */
@media (max-width: 767px) {
    .friendlinks-list {
        grid-template-columns: repeat(4, 1fr); /* 小屏幕一行4个，显示两行 */
        gap: 6px;
        max-height: 120px;
    }
    
    .friendlinks-header {
        margin-bottom: 20px;
    }
    
    .friendlinks-title {
        font-size: 18px;
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
        font-size: 13px;
        flex-shrink: 1;
        min-width: 0;
        max-width: 14ch; /* 限制最多显示4个汉字 */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

/* 超小屏幕优化 */
@media (max-width: 480px) {
    .friendlinks-container {
        padding: 0 10px;
    }
    
    .friendlinks-list {
        grid-template-columns: repeat(4, 1fr);
        gap: 4px;
    }
    
    .friendlink-item {
        padding: 8px 6px;
        gap: 4px;
        width: 100%;
        box-sizing: border-box;
    }
    
    .friendlink-icon {
        width: 16px;
        height: 16px;
        font-size: 12px;
    }
    
    .friendlink-name {
        font-size: 10px;
        max-width: 10ch; /* 限制最多显示4个汉字 */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}
</style>
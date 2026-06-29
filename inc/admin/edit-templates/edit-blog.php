<?php
/**
 * 博客编辑页模板
 * 包含简约完整的图文内容字段
 */

function wuchaiwp_register_blog_fields($post_type) {
    // 确保博客文章类型支持所有默认WordPress文章字段
    global $_wp_post_type_features;
    
    // 添加所有默认文章字段支持
    $supports = array(
        'title',           // 标题
        'editor',          // 编辑器
        'author',          // 作者
        'thumbnail',       // 特色图片
        'excerpt',         // 摘要
        'trackbacks',      // Trackbacks
        // 'custom-fields',   // 自定义字段（已禁用）
        'comments',        // 评论
        'revisions',       // 修订版本
        'post-formats'     // 文章格式
    );
    
    foreach ($supports as $feature) {
        if (!isset($_wp_post_type_features[$post_type][$feature])) {
            $_wp_post_type_features[$post_type][$feature] = true;
        }
    }
    
    // 添加文章信息元框
    add_action('add_meta_boxes_' . $post_type, function() use ($post_type) {
        // 文章信息 - 放在编辑器下方
        add_meta_box(
            'wuchaiwp_blog_post_info',
            '📝 文章信息',
            'wuchaiwp_blog_post_info_callback',
            $post_type,
            'normal',
            'default'
        );
        
        // 作者信息 - 保持在侧边栏
        add_meta_box(
            'wuchaiwp_blog_author_info',
            '👤 作者信息',
            'wuchaiwp_blog_author_info_callback',
            $post_type,
            'side',
            'default'
        );
        
        // 文章推荐 - 放在编辑器下方
        add_meta_box(
            'wuchaiwp_blog_recommend',
            '⭐ 文章推荐',
            'wuchaiwp_blog_recommend_callback',
            $post_type,
            'normal',
            'default'
        );
    });
    
    // 保存字段
    add_action('save_post_' . $post_type, 'wuchaiwp_save_blog_fields');
    
    // 移除原生的自定义字段元框
    add_action('admin_menu', function() use ($post_type) {
        remove_meta_box('postcustom', $post_type, 'normal');
    });
}

// 文章信息字段回调
function wuchaiwp_blog_post_info_callback($post) {
    wp_nonce_field('wuchaiwp_blog_meta_nonce', 'wuchaiwp_blog_meta_nonce');
    
    $blog_source = get_post_meta($post->ID, 'wuchaiwp_blog_source', true);
    $blog_excerpt = get_post_meta($post->ID, 'wuchaiwp_blog_excerpt', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔗 原文链接</label>';
    echo '<input type="url" name="wuchaiwp_blog_source" value="' . esc_attr($blog_source) . '" placeholder="文章来源链接（可选）" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📋 自定义摘要</label>';
    echo '<textarea name="wuchaiwp_blog_excerpt" rows="3" placeholder="自定义文章摘要..." style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($blog_excerpt) . '</textarea>';
    echo '</div>';
}

// 作者信息字段回调
function wuchaiwp_blog_author_info_callback($post) {
    $blog_author_name = get_post_meta($post->ID, 'wuchaiwp_blog_author_name', true);
    $blog_author_avatar = get_post_meta($post->ID, 'wuchaiwp_blog_author_avatar', true);
    $blog_author_bio = get_post_meta($post->ID, 'wuchaiwp_blog_author_bio', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">👤 作者名称</label>';
    echo '<input type="text" name="wuchaiwp_blog_author_name" value="' . esc_attr($blog_author_name) . '" placeholder="自定义作者名称（留空使用默认）" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🖼️ 作者头像</label>';
    echo '<input type="text" name="wuchaiwp_blog_author_avatar" value="' . esc_attr($blog_author_avatar) . '" placeholder="头像图片URL（留空使用默认）" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📝 作者简介</label>';
    echo '<textarea name="wuchaiwp_blog_author_bio" rows="3" placeholder="作者的简短介绍..." style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($blog_author_bio) . '</textarea>';
    echo '</div>';
}

// 文章推荐字段回调
function wuchaiwp_blog_recommend_callback($post) {
    $blog_recommend = get_post_meta($post->ID, 'wuchaiwp_blog_recommend', true);
    $blog_recommend_posts = get_post_meta($post->ID, 'wuchaiwp_blog_recommend_posts', true);
    $blog_donate_enable = get_post_meta($post->ID, 'wuchaiwp_blog_donate_enable', true);
    
    // 默认勾选打赏功能
    if ($blog_donate_enable === '') {
        $blog_donate_enable = 1;
    }
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">⭐ 推荐文章</label>';
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">';
    echo '<input type="checkbox" name="wuchaiwp_blog_recommend" value="1" ' . checked(1, $blog_recommend, false) . '>';
    echo '<span style="font-weight:normal;">设为推荐文章</span>';
    echo '</label>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">💰 打赏功能</label>';
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">';
    echo '<input type="checkbox" name="wuchaiwp_blog_donate_enable" value="1" ' . checked(1, $blog_donate_enable, false) . '>';
    echo '<span style="font-weight:normal;">显示打赏按钮</span>';
    echo '</label>';
    echo '<p style="font-size:12px;color:#666;margin-top:4px;">勾选后，文章详情页将显示打赏按钮</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔗 相关推荐文章</label>';
    echo '<select name="wuchaiwp_blog_recommend_posts[]" multiple="multiple" style="width:100%;height:120px;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    // 限制查询数量，避免性能问题
    $posts = get_posts(array(
        'post_type' => get_post_type($post->ID), 
        'posts_per_page' => 50,  // 限制为50篇
        'exclude' => array($post->ID),
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    foreach ($posts as $p) {
        $selected = is_array($blog_recommend_posts) && in_array($p->ID, $blog_recommend_posts) ? 'selected' : '';
        echo '<option value="' . esc_attr($p->ID) . '" ' . $selected . '>' . esc_html($p->post_title) . '</option>';
    }
    echo '</select>';
    echo '<p style="font-size:12px;color:#666;margin-top:4px;">按住 Ctrl 可多选（仅显示最近50篇文章）</p>';
    echo '</div>';
}

// 保存字段
function wuchaiwp_save_blog_fields($post_id) {
    if (!isset($_POST['wuchaiwp_blog_meta_nonce']) || !wp_verify_nonce($_POST['wuchaiwp_blog_meta_nonce'], 'wuchaiwp_blog_meta_nonce')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 保存文章信息
    if (isset($_POST['wuchaiwp_blog_source'])) {
        update_post_meta($post_id, 'wuchaiwp_blog_source', esc_url_raw($_POST['wuchaiwp_blog_source']));
    }
    if (isset($_POST['wuchaiwp_blog_excerpt'])) {
        update_post_meta($post_id, 'wuchaiwp_blog_excerpt', wp_kses_post($_POST['wuchaiwp_blog_excerpt']));
    }
    
    // 保存作者信息
    if (isset($_POST['wuchaiwp_blog_author_name'])) {
        update_post_meta($post_id, 'wuchaiwp_blog_author_name', sanitize_text_field($_POST['wuchaiwp_blog_author_name']));
    }
    if (isset($_POST['wuchaiwp_blog_author_avatar'])) {
        update_post_meta($post_id, 'wuchaiwp_blog_author_avatar', esc_url_raw($_POST['wuchaiwp_blog_author_avatar']));
    }
    if (isset($_POST['wuchaiwp_blog_author_bio'])) {
        update_post_meta($post_id, 'wuchaiwp_blog_author_bio', wp_kses_post($_POST['wuchaiwp_blog_author_bio']));
    }
    
    // 保存推荐信息
    update_post_meta($post_id, 'wuchaiwp_blog_recommend', isset($_POST['wuchaiwp_blog_recommend']) ? 1 : 0);
    if (isset($_POST['wuchaiwp_blog_recommend_posts'])) {
        update_post_meta($post_id, 'wuchaiwp_blog_recommend_posts', array_map('intval', $_POST['wuchaiwp_blog_recommend_posts']));
    }
    
    // 保存打赏设置
    update_post_meta($post_id, 'wuchaiwp_blog_donate_enable', isset($_POST['wuchaiwp_blog_donate_enable']) ? 1 : 0);
    
}
?>
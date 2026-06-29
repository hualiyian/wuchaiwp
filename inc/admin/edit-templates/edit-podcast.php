<?php
/**
 * 播客编辑页模板
 * 包含适配 PowerPress 插件的音频内容字段
 */

function wuchaiwp_register_podcast_fields($post_type) {
    // 确保播客文章类型支持所有默认WordPress文章字段
    global $_wp_post_type_features;
    
    // 添加所有默认文章字段支持
    $supports = array(
        'title',           // 标题
        'editor',          // 编辑器
        'author',          // 作者
        'thumbnail',       // 特色图片
        'excerpt',         // 摘要
        'trackbacks',      // Trackbacks
        'comments',        // 评论
        'revisions',       // 修订版本
        'post-formats'     // 文章格式
    );
    
    foreach ($supports as $feature) {
        if (!isset($_wp_post_type_features[$post_type][$feature])) {
            $_wp_post_type_features[$post_type][$feature] = true;
        }
    }
    
    // 添加播客信息元框
    add_action('add_meta_boxes_' . $post_type, function() use ($post_type) {
        // 播客信息 - 放在编辑器下方
        add_meta_box(
            'wuchaiwp_podcast_info',
            '🎧 播客信息',
            'wuchaiwp_podcast_info_callback',
            $post_type,
            'normal',
            'default'
        );
        
        // 节目信息 - 放在编辑器下方
        add_meta_box(
            'wuchaiwp_podcast_episode',
            '📺 节目信息',
            'wuchaiwp_podcast_episode_callback',
            $post_type,
            'normal',
            'default'
        );
        
        // 显示设置 - 放在侧边栏
        add_meta_box(
            'wuchaiwp_podcast_display',
            '🎨 显示设置',
            'wuchaiwp_podcast_display_callback',
            $post_type,
            'side',
            'default'
        );
        
        // 高级设置 - 放在侧边栏
        add_meta_box(
            'wuchaiwp_podcast_advanced',
            '⚙️ 高级设置',
            'wuchaiwp_podcast_advanced_callback',
            $post_type,
            'side',
            'default'
        );
    });
    
    // 保存字段
    add_action('save_post_' . $post_type, 'wuchaiwp_save_podcast_fields');
    
    // 移除原生的自定义字段元框
    add_action('admin_menu', function() use ($post_type) {
        remove_meta_box('postcustom', $post_type, 'normal');
    });
}

// 播客信息字段回调
function wuchaiwp_podcast_info_callback($post) {
    wp_nonce_field('wuchaiwp_podcast_meta_nonce', 'wuchaiwp_podcast_meta_nonce');
    
    $podcast_subtitle = get_post_meta($post->ID, 'wuchaiwp_podcast_subtitle', true);
    $podcast_summary = get_post_meta($post->ID, 'wuchaiwp_podcast_summary', true);
    $podcast_host = get_post_meta($post->ID, 'wuchaiwp_podcast_host', true);
    $podcast_guest = get_post_meta($post->ID, 'wuchaiwp_podcast_guest', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📝 节目副标题</label>';
    echo '<input type="text" name="wuchaiwp_podcast_subtitle" value="' . esc_attr($podcast_subtitle) . '" placeholder="简短描述本期节目主题" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📋 节目摘要</label>';
    echo '<textarea name="wuchaiwp_podcast_summary" rows="3" placeholder="简短摘要，用于播客平台展示..." style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($podcast_summary) . '</textarea>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🎙️ 主播</label>';
    echo '<input type="text" name="wuchaiwp_podcast_host" value="' . esc_attr($podcast_host) . '" placeholder="本期节目主播名称" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">👥 嘉宾（可选）</label>';
    echo '<input type="text" name="wuchaiwp_podcast_guest" value="' . esc_attr($podcast_guest) . '" placeholder="本期节目嘉宾名称，多个嘉宾用逗号分隔" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
}

// 节目信息字段回调
function wuchaiwp_podcast_episode_callback($post) {
    $podcast_series = get_post_meta($post->ID, 'wuchaiwp_podcast_series', true);
    $podcast_season = get_post_meta($post->ID, 'wuchaiwp_podcast_season', true);
    $podcast_episode = get_post_meta($post->ID, 'wuchaiwp_podcast_episode', true);
    $podcast_duration = get_post_meta($post->ID, 'wuchaiwp_podcast_duration', true);
    
    echo '<div style="display:flex;gap:12px;margin-bottom:16px;">';
    
    echo '<div style="flex:1;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📁 系列名称</label>';
    echo '<input type="text" name="wuchaiwp_podcast_series" value="' . esc_attr($podcast_series) . '" placeholder="如：科技前沿" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="width:80px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📺 季</label>';
    echo '<input type="number" name="wuchaiwp_podcast_season" value="' . esc_attr($podcast_season) . '" placeholder="季" min="1" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="width:80px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔢 集</label>';
    echo '<input type="number" name="wuchaiwp_podcast_episode" value="' . esc_attr($podcast_episode) . '" placeholder="集" min="1" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="width:100px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">⏱️ 时长</label>';
    echo '<input type="text" name="wuchaiwp_podcast_duration" value="' . esc_attr($podcast_duration) . '" placeholder="45:32" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '</div>';
    
    echo '<div style="padding:12px;background:#fff3cd;border:1px solid #ffeeba;border-radius:4px;">';
    echo '<p style="margin:0;font-size:13px;color:#856404;">💡 <strong>PowerPress 集成提示</strong>：请在页面下方的「音频播放器」区域上传您的播客音频文件。PowerPress 插件会自动处理 RSS 订阅、iTunes 元数据等功能。</p>';
    echo '</div>';
}

// 显示设置字段回调 - 新增
// 显示设置字段回调
function wuchaiwp_podcast_display_callback($post) {
    // 获取已保存的值
    $hide_site_header = get_post_meta($post->ID, 'wuchaiwp_podcast_hide_site_header', true);
    $hide_site_footer = get_post_meta($post->ID, 'wuchaiwp_podcast_hide_site_footer', true);
    $hide_title = get_post_meta($post->ID, 'wuchaiwp_podcast_hide_title', true);
    $hide_meta = get_post_meta($post->ID, 'wuchaiwp_podcast_hide_meta', true);
    $hide_comments = get_post_meta($post->ID, 'wuchaiwp_podcast_hide_comments', true);
    $hide_sidebar = get_post_meta($post->ID, 'wuchaiwp_podcast_hide_sidebar', true);
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:8px;font-weight:600;">🎨 页面显示设置</label>';
    echo '<p style="font-size:12px;color:#666;margin-bottom:12px;">勾选以下选项可隐藏对应元素</p>';
    
    // 隐藏网站页头（整个网站头部导航）
    echo '<div style="background:#f0f7ff;border:1px solid #cce0ff;border-radius:4px;padding:8px 10px;margin-bottom:10px;">';
    echo '<p style="margin:0;font-size:12px;color:#1e40af;font-weight:500;margin-bottom:8px;">🌐 网站整体元素</p>';
    
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_podcast_hide_site_header" value="1" ' . checked(1, $hide_site_header, false) . '>';
    echo '<span style="font-size:13px;">🏠 隐藏网站页头（导航栏等）</span>';
    echo '</label>';
    
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_podcast_hide_site_footer" value="1" ' . checked(1, $hide_site_footer, false) . '>';
    echo '<span style="font-size:13px;">🔚 隐藏网站页脚（版权信息等）</span>';
    echo '</label>';
    echo '</div>';
    
    // 文章内容元素
    echo '<div style="background:#fffbeb;border:1px solid #fef3c7;border-radius:4px;padding:8px 10px;margin-bottom:10px;">';
    echo '<p style="margin:0;font-size:12px;color:#92400e;font-weight:500;margin-bottom:8px;">📝 文章内容元素</p>';
    
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_podcast_hide_title" value="1" ' . checked(1, $hide_title, false) . '>';
    echo '<span style="font-size:13px;">📝 隐藏文章标题</span>';
    echo '</label>';
    
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_podcast_hide_meta" value="1" ' . checked(1, $hide_meta, false) . '>';
    echo '<span style="font-size:13px;">👤 隐藏作者/日期等元信息</span>';
    echo '</label>';
    
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_podcast_hide_sidebar" value="1" ' . checked(1, $hide_sidebar, false) . '>';
    echo '<span style="font-size:13px;">📌 隐藏侧边栏</span>';
    echo '</label>';
    
    echo '<label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_podcast_hide_comments" value="1" ' . checked(1, $hide_comments, false) . '>';
    echo '<span style="font-size:13px;">💬 隐藏评论区</span>';
    echo '</label>';
    echo '</div>';
    
    echo '</div>';
    
    // 快捷操作按钮
    echo '<div style="border-top:1px solid #eee;padding-top:12px;">';
    echo '<p style="font-size:12px;color:#666;margin-bottom:8px;">快捷操作：</p>';
    echo '<button type="button" onclick="wuchaiwp_podcast_toggle_all_display(true)" style="margin-right:6px;padding:4px 10px;font-size:12px;border:1px solid #ddd;border-radius:3px;cursor:pointer;">全选</button>';
    echo '<button type="button" onclick="wuchaiwp_podcast_toggle_all_display(false)" style="padding:4px 10px;font-size:12px;border:1px solid #ddd;border-radius:3px;cursor:pointer;">取消全选</button>';
    echo '<button type="button" onclick="wuchaiwp_podcast_set_minimal()" style="margin-left:6px;padding:4px 10px;font-size:12px;border:1px solid #667eea;border-radius:3px;cursor:pointer;color:#667eea;">极简模式</button>';
    echo '</div>';
    
    // JavaScript 脚本
    echo '<script>';
    echo 'function wuchaiwp_podcast_toggle_all_display(checked) {';
    echo '    document.querySelectorAll(\'input[name^="wuchaiwp_podcast_hide_"]\').forEach(function(el) {';
    echo '        el.checked = checked;';
    echo '    });';
    echo '}';
    echo 'function wuchaiwp_podcast_set_minimal() {';
    echo '    // 极简模式：只保留播放器和内容';
    echo '    document.querySelectorAll(\'input[name^="wuchaiwp_podcast_hide_"]\').forEach(function(el) {';
    echo '        el.checked = true;';
    echo '    });';
    echo '    // 取消隐藏标题';
    echo '    document.querySelector(\'input[name="wuchaiwp_podcast_hide_title"]\').checked = false;';
    echo '}';
    echo '</script>';
}

// 高级设置字段回调
function wuchaiwp_podcast_advanced_callback($post) {
    $podcast_explicit = get_post_meta($post->ID, 'wuchaiwp_podcast_explicit', true);
    $podcast_guid = get_post_meta($post->ID, 'wuchaiwp_podcast_guid', true);
    $podcast_transcript = get_post_meta($post->ID, 'wuchaiwp_podcast_transcript', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔞 内容分级</label>';
    echo '<select name="wuchaiwp_podcast_explicit" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<option value="no" ' . selected($podcast_explicit, 'no', false) . '>适合所有年龄段</option>';
    echo '<option value="yes" ' . selected($podcast_explicit, 'yes', false) . '>含成人内容</option>';
    echo '<option value="clean" ' . selected($podcast_explicit, 'clean', false) . '>已净化版本</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔗 唯一标识（GUID）</label>';
    echo '<input type="text" name="wuchaiwp_podcast_guid" value="' . esc_attr($podcast_guid) . '" placeholder="自动生成或手动输入" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<p style="font-size:12px;color:#666;margin-top:4px;">建议留空，系统会自动生成</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📄 完整文稿（可选）</label>';
    echo '<textarea name="wuchaiwp_podcast_transcript" rows="4" placeholder="节目完整文稿内容..." style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($podcast_transcript) . '</textarea>';
    echo '</div>';
}

// 保存字段
function wuchaiwp_save_podcast_fields($post_id) {
    if (!isset($_POST['wuchaiwp_podcast_meta_nonce']) || !wp_verify_nonce($_POST['wuchaiwp_podcast_meta_nonce'], 'wuchaiwp_podcast_meta_nonce')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 保存播客基本信息
    if (isset($_POST['wuchaiwp_podcast_subtitle'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_subtitle', sanitize_text_field($_POST['wuchaiwp_podcast_subtitle']));
    }
    if (isset($_POST['wuchaiwp_podcast_summary'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_summary', wp_kses_post($_POST['wuchaiwp_podcast_summary']));
    }
    if (isset($_POST['wuchaiwp_podcast_host'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_host', sanitize_text_field($_POST['wuchaiwp_podcast_host']));
    }
    if (isset($_POST['wuchaiwp_podcast_guest'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_guest', sanitize_text_field($_POST['wuchaiwp_podcast_guest']));
    }
    
    // 保存节目信息
    if (isset($_POST['wuchaiwp_podcast_series'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_series', sanitize_text_field($_POST['wuchaiwp_podcast_series']));
    }
    if (isset($_POST['wuchaiwp_podcast_season'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_season', intval($_POST['wuchaiwp_podcast_season']));
    }
    if (isset($_POST['wuchaiwp_podcast_episode'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_episode', intval($_POST['wuchaiwp_podcast_episode']));
    }
    if (isset($_POST['wuchaiwp_podcast_duration'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_duration', sanitize_text_field($_POST['wuchaiwp_podcast_duration']));
    }
    
    // 保存显示设置
update_post_meta($post_id, 'wuchaiwp_podcast_hide_site_header', isset($_POST['wuchaiwp_podcast_hide_site_header']) ? 1 : 0);
update_post_meta($post_id, 'wuchaiwp_podcast_hide_site_footer', isset($_POST['wuchaiwp_podcast_hide_site_footer']) ? 1 : 0);
update_post_meta($post_id, 'wuchaiwp_podcast_hide_title', isset($_POST['wuchaiwp_podcast_hide_title']) ? 1 : 0);
update_post_meta($post_id, 'wuchaiwp_podcast_hide_meta', isset($_POST['wuchaiwp_podcast_hide_meta']) ? 1 : 0);
update_post_meta($post_id, 'wuchaiwp_podcast_hide_comments', isset($_POST['wuchaiwp_podcast_hide_comments']) ? 1 : 0);
update_post_meta($post_id, 'wuchaiwp_podcast_hide_sidebar', isset($_POST['wuchaiwp_podcast_hide_sidebar']) ? 1 : 0);
    
    
    
    // 保存高级设置
    if (isset($_POST['wuchaiwp_podcast_explicit'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_explicit', sanitize_text_field($_POST['wuchaiwp_podcast_explicit']));
    }
    if (isset($_POST['wuchaiwp_podcast_guid'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_guid', sanitize_text_field($_POST['wuchaiwp_podcast_guid']));
    }
    if (isset($_POST['wuchaiwp_podcast_transcript'])) {
        update_post_meta($post_id, 'wuchaiwp_podcast_transcript', wp_kses_post($_POST['wuchaiwp_podcast_transcript']));
    }
    
    // 如果 GUID 为空，自动生成一个
    if (empty($_POST['wuchaiwp_podcast_guid'])) {
        $guid = get_permalink($post_id);
        update_post_meta($post_id, 'wuchaiwp_podcast_guid', $guid);
    }
}
?>
<?php
/**
 * 友情链接编辑页模板
 * 包含友情链接专属字段
 */

function wuchaiwp_register_friendlink_fields($post_type) {
    // 添加友情链接元框
    add_action('add_meta_boxes_' . $post_type, function() use ($post_type) {
        // 友情链接信息 - 放在编辑器下方
        add_meta_box(
            'wuchaiwp_friendlink_info',
            '🔗 友情链接信息',
            'wuchaiwp_friendlink_info_callback',
            $post_type,
            'normal',
            'high'
        );
        
        // 高级设置 - 侧边栏
        add_meta_box(
            'wuchaiwp_friendlink_settings',
            '⚙️ 高级设置',
            'wuchaiwp_friendlink_settings_callback',
            $post_type,
            'side',
            'default'
        );
    });
    
    // 保存字段
    add_action('save_post_' . $post_type, 'wuchaiwp_save_friendlink_fields');
    
    // 移除原生的自定义字段元框
    add_action('admin_menu', function() use ($post_type) {
        remove_meta_box('postcustom', $post_type, 'normal');
    });
}

// 友情链接信息字段回调
function wuchaiwp_friendlink_info_callback($post) {
    wp_nonce_field('wuchaiwp_friendlink_meta_nonce', 'wuchaiwp_friendlink_meta_nonce');
    
    $friendlink_url = get_post_meta($post->ID, 'wuchaiwp_friendlink_url', true);
    $friendlink_desc = get_post_meta($post->ID, 'wuchaiwp_friendlink_desc', true);
    $friendlink_logo = get_post_meta($post->ID, 'wuchaiwp_friendlink_logo', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔗 网站链接 <span style="color:red;">*</span></label>';
    echo '<input type="url" name="wuchaiwp_friendlink_url" value="' . esc_attr($friendlink_url) . '" placeholder="如：https://example.com" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:14px;" required>';
    echo '<p style="font-size:12px;color:#666;margin-top:4px;">请输入友情链接的完整URL地址</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📝 网站描述</label>';
    echo '<textarea name="wuchaiwp_friendlink_desc" rows="3" placeholder="简单描述一下这个网站..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:14px;">' . esc_textarea($friendlink_desc) . '</textarea>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🖼️ 网站Logo</label>';
    echo '<input type="text" name="wuchaiwp_friendlink_logo" id="friendlink_logo_input" value="' . esc_attr($friendlink_logo) . '" placeholder="Logo图片URL（可选）" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:14px;">';
    
    // 图片预览和上传按钮
    echo '<div style="margin-top:8px;">';
    if ($friendlink_logo) {
        echo '<img src="' . esc_url($friendlink_logo) . '" alt="Logo预览" style="max-width:120px;height:auto;border-radius:4px;margin-bottom:8px;display:block;">';
    }
    echo '<button type="button" class="button button-secondary" onclick="wuchaiwp_upload_friendlink_logo()">📷 上传图片</button>';
    echo '</div>';
    echo '</div>';
}

// 高级设置字段回调
function wuchaiwp_friendlink_settings_callback($post) {
    $friendlink_status = get_post_meta($post->ID, 'wuchaiwp_friendlink_status', true);
    $friendlink_sort = get_post_meta($post->ID, 'wuchaiwp_friendlink_sort', true);
    
    // 默认状态为待审核
    if ($friendlink_status === '') {
        $friendlink_status = 'pending';
    }
    
    // 默认排序值为100
    if ($friendlink_sort === '') {
        $friendlink_sort = 100;
    }
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📊 链接状态</label>';
    echo '<select name="wuchaiwp_friendlink_status" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:14px;">';
    echo '<option value="pending" ' . selected('pending', $friendlink_status, false) . '>🔴 待审核</option>';
    echo '<option value="active" ' . selected('active', $friendlink_status, false) . '>✅ 正常</option>';
    echo '<option value="disabled" ' . selected('disabled', $friendlink_status, false) . '>❌ 已禁用</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔢 排序值</label>';
    echo '<input type="number" name="wuchaiwp_friendlink_sort" value="' . esc_attr($friendlink_sort) . '" min="0" max="999" placeholder="数字越小越靠前" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:14px;">';
    echo '<p style="font-size:12px;color:#666;margin-top:4px;">设置友情链接的排序顺序，数字越小显示越靠前</p>';
    echo '</div>';
}

// 保存字段
function wuchaiwp_save_friendlink_fields($post_id) {
    if (!isset($_POST['wuchaiwp_friendlink_meta_nonce']) || !wp_verify_nonce($_POST['wuchaiwp_friendlink_meta_nonce'], 'wuchaiwp_friendlink_meta_nonce')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 保存网站链接
    if (isset($_POST['wuchaiwp_friendlink_url'])) {
        update_post_meta($post_id, 'wuchaiwp_friendlink_url', esc_url_raw($_POST['wuchaiwp_friendlink_url']));
    }
    
    // 保存网站描述
    if (isset($_POST['wuchaiwp_friendlink_desc'])) {
        update_post_meta($post_id, 'wuchaiwp_friendlink_desc', sanitize_text_field($_POST['wuchaiwp_friendlink_desc']));
    }
    
    // 保存Logo
    if (isset($_POST['wuchaiwp_friendlink_logo'])) {
        update_post_meta($post_id, 'wuchaiwp_friendlink_logo', esc_url_raw($_POST['wuchaiwp_friendlink_logo']));
    }
    
    // 保存状态
    if (isset($_POST['wuchaiwp_friendlink_status'])) {
        update_post_meta($post_id, 'wuchaiwp_friendlink_status', sanitize_text_field($_POST['wuchaiwp_friendlink_status']));
    }
    
    // 保存排序值
    if (isset($_POST['wuchaiwp_friendlink_sort'])) {
        update_post_meta($post_id, 'wuchaiwp_friendlink_sort', intval($_POST['wuchaiwp_friendlink_sort']));
    }
}

// 添加上传脚本
add_action('admin_footer', function() {
    ?>
    <script>
    function wuchaiwp_upload_friendlink_logo() {
        var uploader = wp.media({
            title: '选择Logo图片',
            button: {
                text: '使用此图片'
            },
            multiple: false
        }).open()
        .on('select', function() {
            var attachment = uploader.state().get('selection').first().toJSON();
            document.getElementById('friendlink_logo_input').value = attachment.url;
        });
    }
    </script>
    <?php
});
?>
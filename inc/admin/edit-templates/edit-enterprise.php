<?php
/**
 * 企业官网编辑页模板
 * 包含企业相关的自定义字段
 */

function wuchaiwp_register_enterprise_fields($post_type) {
    // 添加元框
    add_action('add_meta_boxes_' . $post_type, function() use ($post_type) {
        // 页面设置
        add_meta_box(
            'wuchaiwp_enterprise_settings',
            '🏢 页面设置',
            'wuchaiwp_enterprise_settings_callback',
            $post_type,
            'side',
            'default'
        );
        
        // 内容字段
        add_meta_box(
            'wuchaiwp_enterprise_content_fields',
            '📝 内容字段',
            'wuchaiwp_enterprise_content_fields_callback',
            $post_type,
            'normal',
            'default'
        );
        
        // SEO设置
        add_meta_box(
            'wuchaiwp_enterprise_seo',
            '🔍 SEO 设置',
            'wuchaiwp_enterprise_seo_callback',
            $post_type,
            'normal',
            'default'
        );
    });
    
    // 保存字段
    add_action('save_post_' . $post_type, 'wuchaiwp_save_enterprise_fields');
}

// 额外为 page 类型注册标题链接字段（确保企业页面能使用）
function wuchaiwp_register_page_title_link_field() {
    add_action('add_meta_boxes_page', function() {
        add_meta_box(
            'wuchaiwp_page_title_link',
            '🔗 标题跳转链接',
            'wuchaiwp_page_title_link_callback',
            'page',
            'side',
            'default'
        );
    });
    add_action('save_post_page', 'wuchaiwp_save_page_title_link_field');
}
add_action('admin_init', 'wuchaiwp_register_page_title_link_field');

// 页面标题链接字段回调
function wuchaiwp_page_title_link_callback($post) {
    wp_nonce_field('wuchaiwp_page_title_link_nonce', 'wuchaiwp_page_title_link_nonce');
    $title_link = get_post_meta($post->ID, 'wuchaiwp_enterprise_title_link', true);
    
    echo '<div style="margin-bottom:12px;">';
    echo '<input type="url" name="wuchaiwp_enterprise_title_link" value="' . esc_attr($title_link) . '" placeholder="设置后点击标题将跳转到该链接" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<p style="margin:6px 0 0 0;font-size:12px;color:#666;">留空则标题不跳转，保持默认行为</p>';
    echo '</div>';
}

// 保存页面标题链接字段
function wuchaiwp_save_page_title_link_field($post_id) {
    if (!isset($_POST['wuchaiwp_page_title_link_nonce']) || !wp_verify_nonce($_POST['wuchaiwp_page_title_link_nonce'], 'wuchaiwp_page_title_link_nonce')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['wuchaiwp_enterprise_title_link'])) {
        update_post_meta($post_id, 'wuchaiwp_enterprise_title_link', esc_url_raw($_POST['wuchaiwp_enterprise_title_link']));
    }
}

// 页面设置回调
function wuchaiwp_enterprise_settings_callback($post) {
    wp_nonce_field('wuchaiwp_enterprise_nonce', 'wuchaiwp_enterprise_nonce');
    
    $page_type = get_post_meta($post->ID, 'wuchaiwp_enterprise_page_type', true);
    $hide_header = get_post_meta($post->ID, 'wuchaiwp_enterprise_hide_header', true);
    $hide_footer = get_post_meta($post->ID, 'wuchaiwp_enterprise_hide_footer', true);
    $title_link = get_post_meta($post->ID, 'wuchaiwp_enterprise_title_link', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🔗 标题跳转链接</label>';
    echo '<input type="url" name="wuchaiwp_enterprise_title_link" value="' . esc_attr($title_link) . '" placeholder="设置后点击标题将跳转到该链接" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<p style="margin:6px 0 0 0;font-size:12px;color:#666;">留空则标题不跳转，保持默认行为</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📄 页面类型</label>';
    echo '<select name="wuchaiwp_enterprise_page_type" id="wuchaiwp_page_type_select" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<option value="" ' . selected($page_type, '', false) . '>默认</option>';
    echo '<option value="about" ' . selected($page_type, 'about', false) . '>企业简介</option>';
    echo '<option value="product" ' . selected($page_type, 'product', false) . '>产品介绍</option>';
    echo '<option value="case" ' . selected($page_type, 'case', false) . '>案例展示</option>';
    echo '<option value="news" ' . selected($page_type, 'news', false) . '>新闻动态</option>';
    echo '<option value="devlog" ' . selected($page_type, 'devlog', false) . '>开发日志</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:flex;align-items:center;gap:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_enterprise_hide_header" value="1" ' . checked(1, $hide_header, false) . '>';
    echo '<span style="font-size:13px;">隐藏页头</span>';
    echo '</label>';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:flex;align-items:center;gap:8px;">';
    echo '<input type="checkbox" name="wuchaiwp_enterprise_hide_footer" value="1" ' . checked(1, $hide_footer, false) . '>';
    echo '<span style="font-size:13px;">隐藏页脚</span>';
    echo '</label>';
    echo '</div>';
}

// 内容字段回调
function wuchaiwp_enterprise_content_fields_callback($post) {
    $page_type = get_post_meta($post->ID, 'wuchaiwp_enterprise_page_type', true);
    
    // 案例相关字段
    $case_client = get_post_meta($post->ID, 'enterprise_case_client', true) ?: get_post_meta($post->ID, 'wuchaiwp_case_client', true);
    $case_industry = get_post_meta($post->ID, 'enterprise_case_industry', true) ?: get_post_meta($post->ID, 'wuchaiwp_case_industry', true);
    $case_industry_link = get_post_meta($post->ID, 'enterprise_case_industry_link', true);
    $case_icon = get_post_meta($post->ID, 'enterprise_case_icon', true) ?: '📋';
    $case_tags = get_post_meta($post->ID, 'enterprise_case_tags', true);
    $case_date = get_post_meta($post->ID, 'enterprise_case_date', true);
    $case_author = get_post_meta($post->ID, 'enterprise_case_author', true);
    
    // 产品相关字段
    $product_price = get_post_meta($post->ID, 'enterprise_product_price', true);
    $product_version = get_post_meta($post->ID, 'enterprise_product_version', true);
    $product_category = get_post_meta($post->ID, 'enterprise_product_category', true);
    $product_download = get_post_meta($post->ID, 'enterprise_product_download', true);
    $product_features = get_post_meta($post->ID, 'enterprise_product_features', true);
    
    // 企业简介字段
    $company_address = get_post_meta($post->ID, 'enterprise_company_address', true);
    $company_phone = get_post_meta($post->ID, 'enterprise_company_phone', true);
    $company_email = get_post_meta($post->ID, 'enterprise_company_email', true);
    $company_founded = get_post_meta($post->ID, 'enterprise_company_founded', true);
    $company_size = get_post_meta($post->ID, 'enterprise_company_size', true);
    $company_ceo = get_post_meta($post->ID, 'enterprise_company_ceo', true);
    
    // 新闻相关字段
    $news_author = get_post_meta($post->ID, 'enterprise_news_author', true);
    $news_source = get_post_meta($post->ID, 'enterprise_news_source', true);
    $news_date = get_post_meta($post->ID, 'enterprise_news_date', true);
    $news_category = get_post_meta($post->ID, 'enterprise_news_category', true);
    
    // 开发日志字段
    $devlog_version = get_post_meta($post->ID, 'enterprise_devlog_version', true);
    $devlog_type = get_post_meta($post->ID, 'enterprise_devlog_type', true);
    $devlog_changelog = get_post_meta($post->ID, 'enterprise_devlog_changelog', true);
    $devlog_date = get_post_meta($post->ID, 'enterprise_devlog_date', true);
    
    // 切换按钮
    echo '<div style="margin-bottom:20px;padding:12px;background:#e8f5e9;border:1px solid #c8e6c9;border-radius:6px;">';
    echo '<p style="margin:0;font-size:13px;color:#2e7d32;">💡 <strong>提示：</strong>选择页面类型后，下方字段将自动切换为对应类型的字段</p>';
    echo '</div>';
    
    // 案例展示字段
    echo '<div class="wuchaiwp-field-group" data-type="case" style="display:none;margin-bottom:20px;padding:16px;background:#f8f9fa;border-radius:8px;">';
    echo '<h3 style="margin:0 0 16px 0;font-size:15px;font-weight:600;color:#2c3e50;border-bottom:1px solid #ddd;padding-bottom:10px;">💼 案例信息</h3>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">👥 客户名称</label>';
    echo '<input type="text" name="enterprise_case_client" value="' . esc_attr($case_client) . '" placeholder="例如：华为技术有限公司" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">🏭 所属行业</label>';
    echo '<input type="text" name="enterprise_case_industry" value="' . esc_attr($case_industry) . '" placeholder="例如：通信技术" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<p style="margin:6px 0 0 0;font-size:12px;color:#666;">推荐行业：互联网、金融科技、医疗健康、教育培训、电商零售、智能制造、房地产、物流运输</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">🔗 行业链接</label>';
    echo '<input type="url" name="enterprise_case_industry_link" value="' . esc_attr($case_industry_link) . '" placeholder="例如：https://example.com" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">🎯 案例图标</label>';
    echo '<input type="text" name="enterprise_case_icon" value="' . esc_attr($case_icon) . '" placeholder="点击下方图标选择" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;box-sizing:border-box;">';
    echo '<div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:4px;">';
    $icons = ['📋', '💼', '🚀', '💡', '🎯', '📊', '🔧', '⚡', '🎨', '📱', '🌐', '🛒', '🏢', '💳', '📈', '🔑', '🎁', '⭐'];
    foreach ($icons as $icon) {
        echo '<span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;margin:2px;border:1px solid #ddd;border-radius:4px;cursor:pointer;font-size:18px;" onclick="document.querySelector(\'input[name=enterprise_case_icon]\').value = \'' . $icon . '\';this.style.background=\'#e8f0fe\';setTimeout(function(){this.style.background=\'\'}.bind(this),300);">' . $icon . '</span>';
    }
    echo '</div>';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">🏷️ 案例标签</label>';
    echo '<input type="text" name="enterprise_case_tags" value="' . esc_attr($case_tags) . '" placeholder="多个标签用逗号分隔" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📅 案例日期</label>';
    echo '<input type="date" name="enterprise_case_date" value="' . esc_attr($case_date) . '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">👤 案例作者</label>';
    echo '<input type="text" name="enterprise_case_author" value="' . esc_attr($case_author) . '" placeholder="案例负责人" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    echo '</div>';
    
    // 获取产品字段名称（优先使用文章自定义的，否则使用全局设置，最后使用默认值）
    $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
    $default_price_label = !empty($titles_settings['product_price_label']) ? $titles_settings['product_price_label'] : '💰 产品价格';
    $default_version_label = !empty($titles_settings['product_version_label']) ? $titles_settings['product_version_label'] : '🔖 产品版本';
    $default_category_label = !empty($titles_settings['product_category_label']) ? $titles_settings['product_category_label'] : '📁 产品分类';
    $default_download_label = !empty($titles_settings['product_download_label']) ? $titles_settings['product_download_label'] : '📥 下载链接';
    $default_features_label = !empty($titles_settings['product_features_label']) ? $titles_settings['product_features_label'] : '✨ 产品特性';
    $default_download_text = !empty($titles_settings['product_download_text']) ? $titles_settings['product_download_text'] : '立即下载';
    
    // 获取文章自定义的字段名称
    $product_price_label = get_post_meta($post->ID, 'enterprise_product_price_label', true) ?: $default_price_label;
    $product_version_label = get_post_meta($post->ID, 'enterprise_product_version_label', true) ?: $default_version_label;
    $product_category_label = get_post_meta($post->ID, 'enterprise_product_category_label', true) ?: $default_category_label;
    $product_download_label = get_post_meta($post->ID, 'enterprise_product_download_label', true) ?: $default_download_label;
    $product_features_label = get_post_meta($post->ID, 'enterprise_product_features_label', true) ?: $default_features_label;
    $product_download_text = get_post_meta($post->ID, 'enterprise_product_download_text', true) ?: $default_download_text;
    
    // 产品介绍字段
    echo '<div class="wuchaiwp-field-group" data-type="product" style="display:none;margin-bottom:20px;padding:16px;background:#f3e5f5;border-radius:8px;">';
    echo '<h3 style="margin:0 0 16px 0;font-size:15px;font-weight:600;color:#4a148c;border-bottom:1px solid #ddd;padding-bottom:10px;">📦 产品信息</h3>';
    
    // 字段名称自定义区域
    echo '<div style="margin-bottom:16px;padding:12px;background:#fff;border-radius:6px;border:1px dashed #d1c4e9;">';
    echo '<p style="margin:0 0 10px 0;font-size:13px;font-weight:600;color:#4a148c;">🎨 自定义字段名称（留空则使用默认名称）</p>';
    
    echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:12px;">';
    echo '<div>';
    echo '<label style="display:block;margin-bottom:4px;color:#666;">价格字段名称</label>';
    echo '<input type="text" name="enterprise_product_price_label" value="' . esc_attr(get_post_meta($post->ID, 'enterprise_product_price_label', true)) . '" placeholder="' . esc_attr($default_price_label) . '" style="width:100%;padding:4px;border:1px solid #ddd;border-radius:3px;font-size:12px;">';
    echo '</div>';
    echo '<div>';
    echo '<label style="display:block;margin-bottom:4px;color:#666;">版本字段名称</label>';
    echo '<input type="text" name="enterprise_product_version_label" value="' . esc_attr(get_post_meta($post->ID, 'enterprise_product_version_label', true)) . '" placeholder="' . esc_attr($default_version_label) . '" style="width:100%;padding:4px;border:1px solid #ddd;border-radius:3px;font-size:12px;">';
    echo '</div>';
    echo '<div>';
    echo '<label style="display:block;margin-bottom:4px;color:#666;">分类字段名称</label>';
    echo '<input type="text" name="enterprise_product_category_label" value="' . esc_attr(get_post_meta($post->ID, 'enterprise_product_category_label', true)) . '" placeholder="' . esc_attr($default_category_label) . '" style="width:100%;padding:4px;border:1px solid #ddd;border-radius:3px;font-size:12px;">';
    echo '</div>';
    echo '<div>';
    echo '<label style="display:block;margin-bottom:4px;color:#666;">下载字段名称</label>';
    echo '<input type="text" name="enterprise_product_download_label" value="' . esc_attr(get_post_meta($post->ID, 'enterprise_product_download_label', true)) . '" placeholder="' . esc_attr($default_download_label) . '" style="width:100%;padding:4px;border:1px solid #ddd;border-radius:3px;font-size:12px;">';
    echo '</div>';
    echo '<div>';
    echo '<label style="display:block;margin-bottom:4px;color:#666;">下载按钮文字</label>';
    echo '<input type="text" name="enterprise_product_download_text" value="' . esc_attr(get_post_meta($post->ID, 'enterprise_product_download_text', true)) . '" placeholder="' . esc_attr($default_download_text) . '" style="width:100%;padding:4px;border:1px solid #ddd;border-radius:3px;font-size:12px;">';
    echo '</div>';
    echo '<div style="grid-column:1/-1;">';
    echo '<label style="display:block;margin-bottom:4px;color:#666;">特性字段名称</label>';
    echo '<input type="text" name="enterprise_product_features_label" value="' . esc_attr(get_post_meta($post->ID, 'enterprise_product_features_label', true)) . '" placeholder="' . esc_attr($default_features_label) . '" style="width:100%;padding:4px;border:1px solid #ddd;border-radius:3px;font-size:12px;">';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">' . esc_html($product_price_label) . '</label>';
    echo '<input type="text" name="enterprise_product_price" value="' . esc_attr($product_price) . '" placeholder="例如：¥999" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">' . esc_html($product_version_label) . '</label>';
    echo '<input type="text" name="enterprise_product_version" value="' . esc_attr($product_version) . '" placeholder="例如：v2.0.1" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">' . esc_html($product_category_label) . '</label>';
    echo '<input type="text" name="enterprise_product_category" value="' . esc_attr($product_category) . '" placeholder="例如：SaaS软件" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">' . esc_html($product_download_label) . '</label>';
    echo '<input type="url" name="enterprise_product_download" value="' . esc_attr($product_download) . '" placeholder="产品下载地址" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">' . esc_html($product_features_label) . '</label>';
    echo '<textarea name="enterprise_product_features" rows="4" placeholder="请输入产品特性，每行一个" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($product_features) . '</textarea>';
    echo '</div>';
    echo '</div>';
    
    // 企业简介字段
    echo '<div class="wuchaiwp-field-group" data-type="about" style="display:none;margin-bottom:20px;padding:16px;background:#e3f2fd;border-radius:8px;">';
    echo '<h3 style="margin:0 0 16px 0;font-size:15px;font-weight:600;color:#1565c0;border-bottom:1px solid #ddd;padding-bottom:10px;">🏢 企业信息</h3>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📍 公司地址</label>';
    echo '<input type="text" name="enterprise_company_address" value="' . esc_attr($company_address) . '" placeholder="例如：北京市朝阳区科技园区" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📞 联系电话</label>';
    echo '<input type="text" name="enterprise_company_phone" value="' . esc_attr($company_phone) . '" placeholder="例如：400-888-8888" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📧 电子邮箱</label>';
    echo '<input type="email" name="enterprise_company_email" value="' . esc_attr($company_email) . '" placeholder="例如：contact@example.com" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📅 成立时间</label>';
    echo '<input type="date" name="enterprise_company_founded" value="' . esc_attr($company_founded) . '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">👥 公司规模</label>';
    echo '<input type="text" name="enterprise_company_size" value="' . esc_attr($company_size) . '" placeholder="例如：100-500人" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">👔 法人代表</label>';
    echo '<input type="text" name="enterprise_company_ceo" value="' . esc_attr($company_ceo) . '" placeholder="公司负责人" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    echo '</div>';
    
    // 新闻动态字段
    echo '<div class="wuchaiwp-field-group" data-type="news" style="display:none;margin-bottom:20px;padding:16px;background:#fff3e0;border-radius:8px;">';
    echo '<h3 style="margin:0 0 16px 0;font-size:15px;font-weight:600;color:#e65100;border-bottom:1px solid #ddd;padding-bottom:10px;">📰 新闻信息</h3>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">👤 作者</label>';
    echo '<input type="text" name="enterprise_news_author" value="' . esc_attr($news_author) . '" placeholder="文章作者" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📤 来源</label>';
    echo '<input type="text" name="enterprise_news_source" value="' . esc_attr($news_source) . '" placeholder="新闻来源" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📅 发布日期</label>';
    echo '<input type="date" name="enterprise_news_date" value="' . esc_attr($news_date) . '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📁 新闻分类</label>';
    echo '<input type="text" name="enterprise_news_category" value="' . esc_attr($news_category) . '" placeholder="例如：行业动态" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    echo '</div>';
    
    // 开发日志字段
    echo '<div class="wuchaiwp-field-group" data-type="devlog" style="display:none;margin-bottom:20px;padding:16px;background:#e8f5e9;border-radius:8px;">';
    echo '<h3 style="margin:0 0 16px 0;font-size:15px;font-weight:600;color:#2e7d32;border-bottom:1px solid #ddd;padding-bottom:10px;">🔧 开发日志</h3>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">🔖 版本号</label>';
    echo '<input type="text" name="enterprise_devlog_version" value="' . esc_attr($devlog_version) . '" placeholder="例如：v1.2.0" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📝 更新类型</label>';
    echo '<select name="enterprise_devlog_type" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '<option value="" ' . selected($devlog_type, '', false) . '>请选择</option>';
    echo '<option value="feature" ' . selected($devlog_type, 'feature', false) . '>✨ 新功能</option>';
    echo '<option value="bugfix" ' . selected($devlog_type, 'bugfix', false) . '>🐛 Bug修复</option>';
    echo '<option value="improve" ' . selected($devlog_type, 'improve', false) . '>⚡ 性能优化</option>';
    echo '<option value="security" ' . selected($devlog_type, 'security', false) . '>🔒 安全更新</option>';
    echo '</select>';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📅 更新日期</label>';
    echo '<input type="date" name="enterprise_devlog_date" value="' . esc_attr($devlog_date) . '" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:12px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">📋 更新内容</label>';
    echo '<textarea name="enterprise_devlog_changelog" rows="4" placeholder="请输入更新内容，每行一个" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($devlog_changelog) . '</textarea>';
    echo '</div>';
    echo '</div>';
    
    // JavaScript切换逻辑
    echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var select = document.getElementById("wuchaiwp_page_type_select");
            var fieldGroups = document.querySelectorAll(".wuchaiwp-field-group");
            
            function showFields(pageType) {
                fieldGroups.forEach(function(group) {
                    group.style.display = "none";
                });
                
                if (pageType) {
                    var activeGroup = document.querySelector(".wuchaiwp-field-group[data-type=\"" + pageType + "\"]");
                    if (activeGroup) {
                        activeGroup.style.display = "block";
                    }
                }
            }
            
            // 初始化显示
            showFields(select.value);
            
            // 监听变化
            select.addEventListener("change", function() {
                showFields(this.value);
            });
        });
    </script>';
}

// SEO设置回调
function wuchaiwp_enterprise_seo_callback($post) {
    $seo_title = get_post_meta($post->ID, 'wuchaiwp_enterprise_seo_title', true);
    $seo_description = get_post_meta($post->ID, 'wuchaiwp_enterprise_seo_description', true);
    $seo_keywords = get_post_meta($post->ID, 'wuchaiwp_enterprise_seo_keywords', true);
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📝 SEO 标题</label>';
    echo '<input type="text" name="wuchaiwp_enterprise_seo_title" value="' . esc_attr($seo_title) . '" placeholder="页面SEO标题（建议60字以内）" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">📋 SEO 描述</label>';
    echo '<textarea name="wuchaiwp_enterprise_seo_description" rows="3" placeholder="页面SEO描述（建议160字以内）" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">' . esc_textarea($seo_description) . '</textarea>';
    echo '</div>';
    
    echo '<div style="margin-bottom:16px;">';
    echo '<label style="display:block;margin-bottom:6px;font-weight:600;">🏷️ SEO 关键词</label>';
    echo '<input type="text" name="wuchaiwp_enterprise_seo_keywords" value="' . esc_attr($seo_keywords) . '" placeholder="关键词，用逗号分隔" style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;font-size:13px;">';
    echo '</div>';
}

// 保存字段
function wuchaiwp_save_enterprise_fields($post_id) {
    if (!isset($_POST['wuchaiwp_enterprise_nonce']) || !wp_verify_nonce($_POST['wuchaiwp_enterprise_nonce'], 'wuchaiwp_enterprise_nonce')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 保存页面设置
    if (isset($_POST['wuchaiwp_enterprise_page_type'])) {
        update_post_meta($post_id, 'wuchaiwp_enterprise_page_type', sanitize_text_field($_POST['wuchaiwp_enterprise_page_type']));
    }
    update_post_meta($post_id, 'wuchaiwp_enterprise_hide_header', isset($_POST['wuchaiwp_enterprise_hide_header']) ? 1 : 0);
    update_post_meta($post_id, 'wuchaiwp_enterprise_hide_footer', isset($_POST['wuchaiwp_enterprise_hide_footer']) ? 1 : 0);
    if (isset($_POST['wuchaiwp_enterprise_title_link'])) {
        update_post_meta($post_id, 'wuchaiwp_enterprise_title_link', esc_url_raw($_POST['wuchaiwp_enterprise_title_link']));
    }
    
    // 保存案例信息字段
    if (isset($_POST['enterprise_case_client'])) {
        update_post_meta($post_id, 'enterprise_case_client', sanitize_text_field($_POST['enterprise_case_client']));
        update_post_meta($post_id, 'wuchaiwp_case_client', sanitize_text_field($_POST['enterprise_case_client']));
    }
    if (isset($_POST['enterprise_case_industry'])) {
        update_post_meta($post_id, 'enterprise_case_industry', sanitize_text_field($_POST['enterprise_case_industry']));
        update_post_meta($post_id, 'wuchaiwp_case_industry', sanitize_text_field($_POST['enterprise_case_industry']));
    }
    if (isset($_POST['enterprise_case_industry_link'])) {
        update_post_meta($post_id, 'enterprise_case_industry_link', esc_url_raw($_POST['enterprise_case_industry_link']));
    }
    if (isset($_POST['enterprise_case_icon'])) {
        update_post_meta($post_id, 'enterprise_case_icon', sanitize_text_field($_POST['enterprise_case_icon']));
    }
    if (isset($_POST['enterprise_case_tags'])) {
        update_post_meta($post_id, 'enterprise_case_tags', sanitize_text_field($_POST['enterprise_case_tags']));
    }
    if (isset($_POST['enterprise_case_date'])) {
        update_post_meta($post_id, 'enterprise_case_date', sanitize_text_field($_POST['enterprise_case_date']));
    }
    if (isset($_POST['enterprise_case_author'])) {
        update_post_meta($post_id, 'enterprise_case_author', sanitize_text_field($_POST['enterprise_case_author']));
    }
    
    // 保存产品信息字段（仅当页面类型为product时）
    $page_type = isset($_POST['wuchaiwp_enterprise_page_type']) ? sanitize_text_field($_POST['wuchaiwp_enterprise_page_type']) : '';
    if ($page_type === 'product') {
        if (isset($_POST['enterprise_product_price'])) {
            update_post_meta($post_id, 'enterprise_product_price', sanitize_text_field($_POST['enterprise_product_price']));
        }
        if (isset($_POST['enterprise_product_version'])) {
            update_post_meta($post_id, 'enterprise_product_version', sanitize_text_field($_POST['enterprise_product_version']));
        }
        if (isset($_POST['enterprise_product_category'])) {
            update_post_meta($post_id, 'enterprise_product_category', sanitize_text_field($_POST['enterprise_product_category']));
        }
        if (isset($_POST['enterprise_product_download'])) {
            update_post_meta($post_id, 'enterprise_product_download', esc_url_raw($_POST['enterprise_product_download']));
        }
        if (isset($_POST['enterprise_product_features'])) {
            update_post_meta($post_id, 'enterprise_product_features', wp_kses_post($_POST['enterprise_product_features']));
        }
        // 保存产品字段名称自定义
        if (isset($_POST['enterprise_product_price_label'])) {
            update_post_meta($post_id, 'enterprise_product_price_label', sanitize_text_field($_POST['enterprise_product_price_label']));
        }
        if (isset($_POST['enterprise_product_version_label'])) {
            update_post_meta($post_id, 'enterprise_product_version_label', sanitize_text_field($_POST['enterprise_product_version_label']));
        }
        if (isset($_POST['enterprise_product_category_label'])) {
            update_post_meta($post_id, 'enterprise_product_category_label', sanitize_text_field($_POST['enterprise_product_category_label']));
        }
        if (isset($_POST['enterprise_product_download_label'])) {
            update_post_meta($post_id, 'enterprise_product_download_label', sanitize_text_field($_POST['enterprise_product_download_label']));
        }
        if (isset($_POST['enterprise_product_download_text'])) {
            update_post_meta($post_id, 'enterprise_product_download_text', sanitize_text_field($_POST['enterprise_product_download_text']));
        }
        if (isset($_POST['enterprise_product_features_label'])) {
            update_post_meta($post_id, 'enterprise_product_features_label', sanitize_text_field($_POST['enterprise_product_features_label']));
        }
    } else {
        // 如果不是产品类型，清理产品字段值（避免旧数据干扰）
        delete_post_meta($post_id, 'enterprise_product_price');
        delete_post_meta($post_id, 'enterprise_product_version');
        delete_post_meta($post_id, 'enterprise_product_category');
        delete_post_meta($post_id, 'enterprise_product_download');
        delete_post_meta($post_id, 'enterprise_product_features');
        delete_post_meta($post_id, 'enterprise_product_price_label');
        delete_post_meta($post_id, 'enterprise_product_version_label');
        delete_post_meta($post_id, 'enterprise_product_category_label');
        delete_post_meta($post_id, 'enterprise_product_download_label');
        delete_post_meta($post_id, 'enterprise_product_download_text');
        delete_post_meta($post_id, 'enterprise_product_features_label');
    }
    
    // 保存企业信息字段
    if (isset($_POST['enterprise_company_address'])) {
        update_post_meta($post_id, 'enterprise_company_address', sanitize_text_field($_POST['enterprise_company_address']));
    }
    if (isset($_POST['enterprise_company_phone'])) {
        update_post_meta($post_id, 'enterprise_company_phone', sanitize_text_field($_POST['enterprise_company_phone']));
    }
    if (isset($_POST['enterprise_company_email'])) {
        update_post_meta($post_id, 'enterprise_company_email', sanitize_email($_POST['enterprise_company_email']));
    }
    if (isset($_POST['enterprise_company_founded'])) {
        update_post_meta($post_id, 'enterprise_company_founded', sanitize_text_field($_POST['enterprise_company_founded']));
    }
    if (isset($_POST['enterprise_company_size'])) {
        update_post_meta($post_id, 'enterprise_company_size', sanitize_text_field($_POST['enterprise_company_size']));
    }
    if (isset($_POST['enterprise_company_ceo'])) {
        update_post_meta($post_id, 'enterprise_company_ceo', sanitize_text_field($_POST['enterprise_company_ceo']));
    }
    
    // 保存新闻信息字段
    if (isset($_POST['enterprise_news_author'])) {
        update_post_meta($post_id, 'enterprise_news_author', sanitize_text_field($_POST['enterprise_news_author']));
    }
    if (isset($_POST['enterprise_news_source'])) {
        update_post_meta($post_id, 'enterprise_news_source', sanitize_text_field($_POST['enterprise_news_source']));
    }
    if (isset($_POST['enterprise_news_date'])) {
        update_post_meta($post_id, 'enterprise_news_date', sanitize_text_field($_POST['enterprise_news_date']));
    }
    if (isset($_POST['enterprise_news_category'])) {
        update_post_meta($post_id, 'enterprise_news_category', sanitize_text_field($_POST['enterprise_news_category']));
    }
    
    // 保存开发日志字段
    if (isset($_POST['enterprise_devlog_version'])) {
        update_post_meta($post_id, 'enterprise_devlog_version', sanitize_text_field($_POST['enterprise_devlog_version']));
    }
    if (isset($_POST['enterprise_devlog_type'])) {
        update_post_meta($post_id, 'enterprise_devlog_type', sanitize_text_field($_POST['enterprise_devlog_type']));
    }
    if (isset($_POST['enterprise_devlog_date'])) {
        update_post_meta($post_id, 'enterprise_devlog_date', sanitize_text_field($_POST['enterprise_devlog_date']));
    }
    if (isset($_POST['enterprise_devlog_changelog'])) {
        update_post_meta($post_id, 'enterprise_devlog_changelog', wp_kses_post($_POST['enterprise_devlog_changelog']));
    }
    
    // 保存SEO设置
    if (isset($_POST['wuchaiwp_enterprise_seo_title'])) {
        update_post_meta($post_id, 'wuchaiwp_enterprise_seo_title', sanitize_text_field($_POST['wuchaiwp_enterprise_seo_title']));
    }
    if (isset($_POST['wuchaiwp_enterprise_seo_description'])) {
        update_post_meta($post_id, 'wuchaiwp_enterprise_seo_description', wp_kses_post($_POST['wuchaiwp_enterprise_seo_description']));
    }
    if (isset($_POST['wuchaiwp_enterprise_seo_keywords'])) {
        update_post_meta($post_id, 'wuchaiwp_enterprise_seo_keywords', sanitize_text_field($_POST['wuchaiwp_enterprise_seo_keywords']));
    }
}
?>
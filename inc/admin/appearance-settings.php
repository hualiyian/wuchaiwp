<?php
/**
 * 外观设置
 *
 * @package wuchaiwp
 */

// 验证复选框
function wuchaiwp_sanitize_checkbox( $input ) {
    return ( isset( $input ) && $input === '1' ) ? '1' : '0';
}

class Wuchaiwp_Appearance_Settings {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        // 外观设置
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_container_width', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_content_width', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_content_sidebar_ratio', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_sidebar_margin', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_font_family', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_font_size', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_line_height', array('sanitize_callback' => 'floatval'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_theme_color', array('sanitize_callback' => 'sanitize_hex_color'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_secondary_color', array('sanitize_callback' => 'sanitize_hex_color'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_background_color', array('sanitize_callback' => 'sanitize_hex_color'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_text_color', array('sanitize_callback' => 'sanitize_hex_color'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_link_color', array('sanitize_callback' => 'sanitize_hex_color'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_border_radius', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_shadow_intensity', array('sanitize_callback' => 'absint'));
        
        // 悬浮球设置
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_floating_ball_frontpage', array('sanitize_callback' => 'wuchaiwp_sanitize_checkbox'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_floating_ball_single', array('sanitize_callback' => 'wuchaiwp_sanitize_checkbox'));
        
        // 友情链接展示设置
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_footer_friendlink_enabled', array('sanitize_callback' => 'wuchaiwp_sanitize_checkbox'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_footer_friendlink_post_type', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_appearance_settings', 'wuchaiwp_friendlink_icon_api', array('sanitize_callback' => 'esc_url_raw'));
    }

    public function get_settings() {
        return array(
            'container_width' => get_option('wuchaiwp_container_width', '1200'),
            'content_width' => get_option('wuchaiwp_content_width', '800'),
            'content_sidebar_ratio' => get_option('wuchaiwp_content_sidebar_ratio', '3-1'),
            'sidebar_margin' => get_option('wuchaiwp_sidebar_margin', '30'),
            'font_family' => get_option('wuchaiwp_font_family', 'default'),
            'font_size' => get_option('wuchaiwp_font_size', '16'),
            'line_height' => get_option('wuchaiwp_line_height', '1.6'),
            'theme_color' => get_option('wuchaiwp_theme_color', '#333333'),
            'secondary_color' => get_option('wuchaiwp_secondary_color', '#666666'),
            'background_color' => get_option('wuchaiwp_background_color', '#ffffff'),
            'text_color' => get_option('wuchaiwp_text_color', '#333333'),
            'link_color' => get_option('wuchaiwp_link_color', '#666666'),
            'border_radius' => get_option('wuchaiwp_border_radius', '0'),
            'shadow_intensity' => get_option('wuchaiwp_shadow_intensity', '0'),
            'floating_ball_frontpage' => get_option('wuchaiwp_floating_ball_frontpage', '1'),
            'floating_ball_single' => get_option('wuchaiwp_floating_ball_single', '1'),
            'footer_friendlink_enabled' => get_option('wuchaiwp_footer_friendlink_enabled', '1'),
            'footer_friendlink_post_type' => get_option('wuchaiwp_footer_friendlink_post_type', ''),
            'friendlink_icon_api' => get_option('wuchaiwp_friendlink_icon_api', 'https://t3.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&size=128&url='),
        );
    }

    public function generate_css_code($settings) {
        $font_families = array(
            'default' => 'inherit',
            'pingfang' => '"PingFang SC", "Microsoft YaHei", sans-serif',
            'noto' => '"Noto Sans SC", sans-serif',
            'roboto' => 'Roboto, sans-serif',
            'sans' => '"Helvetica Neue", Arial, sans-serif',
            'serif' => 'Georgia, serif',
        );

        return ":root {\n    --wuchaiwp-theme-color: {$settings['theme_color']};\n    --wuchaiwp-secondary-color: {$settings['secondary_color']};\n    --wuchaiwp-background-color: {$settings['background_color']};\n    --wuchaiwp-text-color: {$settings['text_color']};\n    --wuchaiwp-link-color: {$settings['link_color']};\n    --wuchaiwp-border-radius: {$settings['border_radius']}px;\n    --wuchaiwp-shadow-intensity: {$settings['shadow_intensity']}px;\n}\n\nbody {\n    font-family: {$font_families[$settings['font_family']]};\n    font-size: {$settings['font_size']}px;\n    line-height: {$settings['line_height']};\n    color: {$settings['text_color']};\n    background-color: {$settings['background_color']};\n}\n\n.container {\n    max-width: {$settings['container_width']}px;\n}\n\na {\n    color: {$settings['link_color']};\n}\n\n.btn-primary {\n    background-color: {$settings['theme_color']};\n    border-radius: {$settings['border_radius']}px;\n}\n\n.btn-primary:hover {\n    background-color: {$settings['secondary_color']};\n}";
    }

    public function render_appearance_settings() {
        $settings = $this->get_settings();
        $css_code = $this->generate_css_code($settings);
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>外观设置</h1>
                <p class="description">自定义网站的页面尺寸、字体样式和颜色方案</p>
            </div>

            <div class="wuchaiwp-appearance-container">
                <div class="appearance-form">
                    <form method="post" action="options.php" class="wuchaiwp-form">
                        <?php settings_fields('wuchaiwp_appearance_settings'); ?>
                        
                        <div class="form-section">
                            <h3>📐 页面尺寸设置</h3>
                            
                            <div class="form-row">
                                <label>容器最大宽度 (px)</label>
                                <input type="number" name="wuchaiwp_container_width" value="<?php echo esc_attr($settings['container_width']); ?>" min="900" max="1600">
                                <p class="help">网站主容器的最大宽度</p>
                            </div>

                            <div class="form-row">
                                <label>内容区域宽度 (px)</label>
                                <input type="number" name="wuchaiwp_content_width" value="<?php echo esc_attr($settings['content_width']); ?>" min="600" max="1200">
                                <p class="help">文章/页面内容区域的宽度</p>
                            </div>

                            <div class="form-row">
                                <label>内容/侧边栏比例</label>
                                <select name="wuchaiwp_content_sidebar_ratio">
                                    <option value="3-1" <?php selected($settings['content_sidebar_ratio'], '3-1'); ?>>3:1 (内容占75%)</option>
                                    <option value="4-1" <?php selected($settings['content_sidebar_ratio'], '4-1'); ?>>4:1 (内容占80%)</option>
                                    <option value="2-1" <?php selected($settings['content_sidebar_ratio'], '2-1'); ?>>2:1 (内容占66%)</option>
                                    <option value="5-2" <?php selected($settings['content_sidebar_ratio'], '5-2'); ?>>5:2 (内容占71%)</option>
                                    <option value="full" <?php selected($settings['content_sidebar_ratio'], 'full'); ?>>全屏宽度</option>
                                </select>
                                <p class="help">主内容区域和侧边栏的宽度比例</p>
                            </div>

                            <div class="form-row">
                                <label>侧边栏边距 (px)</label>
                                <input type="number" name="wuchaiwp_sidebar_margin" value="<?php echo esc_attr($settings['sidebar_margin']); ?>" min="0" max="100">
                                <p class="help">侧边栏与内容区域之间的距离</p>
                            </div>

                            <div class="form-row">
                                <label>卡片圆角半径 (px)</label>
                                <input type="number" name="wuchaiwp_border_radius" value="<?php echo esc_attr($settings['border_radius']); ?>" min="0" max="30">
                                <p class="help">卡片、按钮等元素的圆角大小</p>
                            </div>

                            <div class="form-row">
                                <label>阴影强度</label>
                                <input type="number" name="wuchaiwp_shadow_intensity" value="<?php echo esc_attr($settings['shadow_intensity']); ?>" min="0" max="30">
                                <p class="help">元素阴影的模糊程度，0为无阴影</p>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>🔤 字体设置</h3>
                            
                            <div class="form-row">
                                <label>字体族</label>
                                <select name="wuchaiwp_font_family">
                                    <option value="default" <?php selected($settings['font_family'], 'default'); ?>>系统默认</option>
                                    <option value="pingfang" <?php selected($settings['font_family'], 'pingfang'); ?>>苹方 & Microsoft YaHei</option>
                                    <option value="noto" <?php selected($settings['font_family'], 'noto'); ?>>Noto Sans SC</option>
                                    <option value="roboto" <?php selected($settings['font_family'], 'roboto'); ?>>Roboto</option>
                                    <option value="sans" <?php selected($settings['font_family'], 'sans'); ?>>Helvetica Neue</option>
                                    <option value="serif" <?php selected($settings['font_family'], 'serif'); ?>>衬线字体</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label>基础字体大小 (px)</label>
                                <input type="number" name="wuchaiwp_font_size" value="<?php echo esc_attr($settings['font_size']); ?>" min="14" max="20">
                                <p class="help">网站正文的基础字体大小</p>
                            </div>

                            <div class="form-row">
                                <label>行高</label>
                                <input type="number" name="wuchaiwp_line_height" value="<?php echo esc_attr($settings['line_height']); ?>" min="1.2" max="2.5" step="0.1">
                                <p class="help">文字行间距倍数</p>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>🎨 颜色设置</h3>
                            
                            <div class="form-row">
                                <label>主题色</label>
                                <input type="color" name="wuchaiwp_theme_color" value="<?php echo esc_attr($settings['theme_color']); ?>">
                                <p class="help">网站主要强调色，用于按钮、链接等</p>
                            </div>

                            <div class="form-row">
                                <label>次要颜色</label>
                                <input type="color" name="wuchaiwp_secondary_color" value="<?php echo esc_attr($settings['secondary_color']); ?>">
                                <p class="help">辅助强调色</p>
                            </div>

                            <div class="form-row">
                                <label>背景色</label>
                                <input type="color" name="wuchaiwp_background_color" value="<?php echo esc_attr($settings['background_color']); ?>">
                                <p class="help">网站页面背景色</p>
                            </div>

                            <div class="form-row">
                                <label>文字颜色</label>
                                <input type="color" name="wuchaiwp_text_color" value="<?php echo esc_attr($settings['text_color']); ?>">
                                <p class="help">主要文字颜色</p>
                            </div>

                            <div class="form-row">
                                <label>链接颜色</label>
                                <input type="color" name="wuchaiwp_link_color" value="<?php echo esc_attr($settings['link_color']); ?>">
                                <p class="help">超链接颜色</p>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>🔵 悬浮球设置</h3>
                            
                            <div class="form-row">
                                <label>
                                    <input type="checkbox" name="wuchaiwp_floating_ball_frontpage" value="1" <?php checked($settings['floating_ball_frontpage'], '1'); ?>>
                                    在首页显示悬浮球
                                </label>
                                <p class="help">控制是否在多区域首页模板中显示悬浮球</p>
                            </div>

                            <div class="form-row">
                                <label>
                                    <input type="checkbox" name="wuchaiwp_floating_ball_single" value="1" <?php checked($settings['floating_ball_single'], '1'); ?>>
                                    在文章详情页显示悬浮球
                                </label>
                                <p class="help">控制是否在博客文章详情页模板中显示悬浮球</p>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>🔗 页脚友情链接展示</h3>
                            
                            <div class="form-row">
                                <label>
                                    <input type="checkbox" name="wuchaiwp_footer_friendlink_enabled" value="1" <?php checked($settings['footer_friendlink_enabled'], '1'); ?>>
                                    启用页脚友情链接展示
                                </label>
                                <p class="help">在首页页脚显示友情链接展示区域</p>
                            </div>

                            <div class="form-row">
                                <label>选择自定义文章类型</label>
                                <select name="wuchaiwp_footer_friendlink_post_type">
                                    <option value="" <?php selected($settings['footer_friendlink_post_type'], ''); ?>>请选择自定义文章类型</option>
                                    <?php
                                    // 获取所有已注册的自定义文章类型（包含没有文章的类型）
                                    $post_types = get_post_types(array(
                                        'public' => true,
                                        '_builtin' => false
                                    ), 'objects');
                                    
                                    if (!empty($post_types)) {
                                        foreach ($post_types as $post_type) {
                                            echo '<option value="' . esc_attr($post_type->name) . '" ' . selected($settings['footer_friendlink_post_type'], $post_type->name, false) . '>' . esc_html($post_type->labels->singular_name . ' (' . $post_type->name . ')') . '</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>暂无自定义文章类型</option>';
                                    }
                                    ?>
                                </select>
                                <p class="help">选择要在页脚展示的自定义文章类型（如友情链接）</p>
                            </div>

                            <div class="form-row">
                                <label>图标API链接</label>
                                <input type="url" name="wuchaiwp_friendlink_icon_api" value="<?php echo esc_attr($settings['friendlink_icon_api']); ?>" placeholder="https://www.google.com/s2/favicons?domain=%s&sz=64">
                                <p class="help">用于自动获取网站图标的API地址，请使用 %s 作为域名占位符。<br>推荐API：Google Favicon（默认）、Icon Horse等<br>
                                https://t3.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&size=128&url=</p>
                            </div>
                        </div>

                        <div class="form-actions">
                            <?php submit_button('保存设置', 'primary', 'submit', false); ?>
                            <button type="button" class="btn btn-secondary" onclick="wuchaiwpResetDefault()">恢复默认</button>
                            <button type="button" class="btn btn-secondary" onclick="wuchaiwpSavePreset()">保存为预设</button>
                        </div>
                    </form>
                </div>

                <div class="appearance-preview">
                    <div class="preview-section">
                        <h3>👀 实时预览</h3>
                        <div class="preview-box">
                            <div class="preview-header" style="background: <?php echo $settings['theme_color']; ?>">
                                <span>导航栏预览</span>
                            </div>
                            <div class="preview-content" style="background: <?php echo $settings['background_color']; ?>; color: <?php echo $settings['text_color']; ?>">
                                <p style="font-size: <?php echo $settings['font_size']; ?>px; line-height: <?php echo $settings['line_height']; ?>">这是一段示例文字，展示当前设置的字体大小和行高。颜色方案也会在此预览中体现。</p>
                                <button style="background: <?php echo $settings['theme_color']; ?>; color: #fff; padding: 10px 20px; border: none; border-radius: <?php echo $settings['border_radius']; ?>px; margin-top: 10px; box-shadow: 0 2px <?php echo $settings['shadow_intensity']; ?>px rgba(0,0,0,0.1);">示例按钮</button>
                            </div>
                        </div>
                    </div>

                    <div class="preview-section">
                        <h3>📋 生成的CSS代码</h3>
                        <div class="code-box">
                            <button class="copy-btn" onclick="wuchaiwpCopyCSS()">📋 复制代码</button>
                            <pre id="wuchaiwp-css-code"><?php echo esc_html($css_code); ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

// 初始化
new Wuchaiwp_Appearance_Settings();

/**
 * 渲染外观设置页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_appearance_settings() {
    $settings = new Wuchaiwp_Appearance_Settings();
    $settings->render_appearance_settings();
}
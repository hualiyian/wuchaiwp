<?php
/**
 * 主题设置页面
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Settings {

    public function __construct() {
        // 移除管理员权限限制，各个子菜单页面已经通过 manage_options 权限保护
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_wuchaiwp_save_preset', array($this, 'ajax_save_preset'));
        add_action('wp_ajax_wuchaiwp_load_preset', array($this, 'ajax_load_preset'));
        add_action('wp_ajax_wuchaiwp_delete_preset', array($this, 'ajax_delete_preset'));
        add_action('wp_ajax_wuchaiwp_reset_default', array($this, 'ajax_reset_default'));
        
        // 加载独立的设置页面文件
        require_once get_template_directory() . '/inc/admin/multi-frontpage-settings.php';
        require_once get_template_directory() . '/inc/admin/appearance-settings.php';
        require_once get_template_directory() . '/inc/admin/style-presets.php';
        require_once get_template_directory() . '/inc/admin/copyright-settings.php';
        require_once get_template_directory() . '/inc/admin/donate-settings.php';
        require_once get_template_directory() . '/inc/admin/usage-settings.php';
        require_once get_template_directory() . '/inc/admin/template-manager-settings.php';
        require_once get_template_directory() . '/inc/admin/template-selection-settings.php';
        
         // 应该在 __construct 中添加
        require_once get_template_directory() . '/inc/admin/cpt-manager-settings.php';
        //require_once get_template_directory() . '/inc/admin/taxonomy-manager.php';
        // 企业官网管理
        require_once get_template_directory() . '/inc/admin/enterprise-manager-settings.php';
        
        
    }

    public function add_admin_menu() {
        // 主菜单
        add_menu_page(
            '主题设置',
            '主题设置',
            'manage_options',
            'wuchaiwp-settings',
            array($this, 'render_dashboard'),
            'dashicons-admin-settings',
            20
        );

        // 子菜单 - 首页设置（主页面）
        add_submenu_page(
            'wuchaiwp-settings',
            '首页设置',
            '首页设置',
            'manage_options',
            'wuchaiwp-settings',
            array($this, 'render_dashboard')
        );

        // 子菜单 - 多区域首页设置
        add_submenu_page(
            'wuchaiwp-settings',
            '多区域首页设置',
            '多区域首页设置',
            'manage_options',
            'wuchaiwp-multi-frontpage-settings',
            'wuchaiwp_render_multi_frontpage_settings'
        );
        
        
      // 子菜单 - 企业官网管理
        add_submenu_page(
            'wuchaiwp-settings',
            '企业官网管理',
            '企业官网管理',
            'manage_options',
            'wuchaiwp-enterprise-manager',
            'wuchaiwp_render_enterprise_manager_page'
        );
      
        

        // 子菜单 - 外观设置
        add_submenu_page(
            'wuchaiwp-settings',
            '外观设置',
            '外观设置',
            'manage_options',
            'wuchaiwp-appearance-settings',
            'wuchaiwp_render_appearance_settings'
        );

        // 子菜单 - 样式预设
        add_submenu_page(
            'wuchaiwp-settings',
            '样式预设',
            '样式预设',
            'manage_options',
            'wuchaiwp-style-presets',
            'wuchaiwp_render_style_presets'
        );
        
        
           
     // 在 existing add_submenu_page 调用后添加
      add_submenu_page(
          'wuchaiwp-settings',
          '自定义文章类型',
          '自定义文章类型',
          'manage_options',
          'wuchaiwp-cpt-manager',
          array($this, 'render_cpt_manager_page')
       );
        
        // 子菜单 - 分类管理
        //add_submenu_page(
           // 'wuchaiwp-settings',
          //  '分类管理',
           // '分类管理',
          //  'manage_options',
           // 'wuchaiwp-taxonomy-manager',
           // array($this, 'render_taxonomy_manager_page')
      //  );
        
     // 子菜单 - 模板管理
        add_submenu_page(
            'wuchaiwp-settings',
            '模板管理',
            '模板管理',
            'manage_options',
            'wuchaiwp-template-manager',
            array($this, 'render_template_manager_page')
        );

        // 子菜单 - 模板选择
        add_submenu_page(
            'wuchaiwp-settings',
            '模板选择',
            '模板选择',
            'manage_options',
            'wuchaiwp-template-selection',
            array($this, 'render_template_selection_page')
        );
        

        // 子菜单 - 版权说明
        add_submenu_page(
            'wuchaiwp-settings',
            '版权说明',
            '版权说明',
            'manage_options',
            'wuchaiwp-copyright-settings',
            'wuchaiwp_render_copyright_settings'
        );

        // 子菜单 - 打赏设置
        add_submenu_page(
            'wuchaiwp-settings',
            '打赏设置',
            '打赏设置',
            'manage_options',
            'wuchaiwp-donate-settings',
            'wuchaiwp_render_donate_settings'
        );

        // 子菜单 - 使用说明
        add_submenu_page(
            'wuchaiwp-settings',
            '使用说明',
            '使用说明',
            'manage_options',
            'wuchaiwp-usage',
            'wuchaiwp_render_usage_page'
        );

       
    }
    
    


// 添加渲染方法
public function render_cpt_manager_page() {
    //require_once get_template_directory() . '/inc/admin/cpt-manager-settings.php';
    $cpt_manager = new Wuchaiwp_Custom_Post_Type_Manager();
    $cpt_manager->render_admin_page();
}

// 渲染分类管理页面
public function render_taxonomy_manager_page() {
    $taxonomy_manager = new Wuchaiwp_Taxonomy_Manager();
    $taxonomy_manager->render_admin_page();
}
    
    

    // 渲染模板管理页面
    public function render_template_manager_page() {
        $template_manager = new Wuchaiwp_Template_Manager();
        $template_manager->render_admin_page();
    }

    // 渲染模板选择页面
    public function render_template_selection_page() {
        $template_selection = new Wuchaiwp_Template_Selection();
        $template_selection->render_admin_page();
    }

    public function register_settings() {
        // 首页设置
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_hero_title', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_hero_description', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_hero_bg_image', array('sanitize_callback' => 'esc_url_raw'));
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_hero_search_placeholder', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_show_categories', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_show_featured', array('sanitize_callback' => 'absint'));
        register_setting('wuchaiwp_home_settings', 'wuchaiwp_frontpage_template', array('sanitize_callback' => array($this, 'wuchaiwp_sanitize_frontpage_template')));

        // 外观设置、样式预设、版权设置（独立文件）
    }

    public function sanitize_presets($value) {
        if (is_array($value)) {
            foreach ($value as $key => $preset) {
                if (!isset($preset['name']) || empty($preset['name'])) {
                    unset($value[$key]);
                }
            }
        }
        return $value;
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'wuchaiwp') !== false) {
            wp_enqueue_style('wuchaiwp-admin', get_template_directory_uri() . '/inc/admin/css/admin.css');
            wp_enqueue_script('wuchaiwp-admin', get_template_directory_uri() . '/inc/admin/js/admin.js', array('jquery'), '1.0', true);
            wp_localize_script('wuchaiwp-admin', 'wuchaiwp_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wuchaiwp_preset_nonce')
            ));
            
            // 在所有wuchaiwp设置页面加载媒体库脚本
            wp_enqueue_media();
        }
    }

// 验证专题来源
function wuchaiwp_sanitize_featured_source( $input ) {
    $valid = array( 'sticky', 'featured', 'selected', 'latest' );
    if ( in_array( $input, $valid ) ) {
        return $input;
    }
    return 'sticky';
}

// 验证文章ID数组
function wuchaiwp_sanitize_post_ids( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }
    return array_map( 'absint', $input );
}

// 验证首页模板
function wuchaiwp_sanitize_frontpage_template( $input ) {
    $valid = array( 'default', 'blog', 'custom', 'landing', 'multi-section' );
    if ( in_array( $input, $valid ) ) {
        return $input;
    }
    return 'default';
}

// 验证样式预设
function wuchaiwp_sanitize_style_preset( $input ) {
    $valid = array( 'default', 'dark', 'light', 'modern', 'elegant' );
    if ( in_array( $input, $valid ) ) {
        return $input;
    }
    return 'default';
}

// 验证复选框
function wuchaiwp_sanitize_checkbox( $input ) {
    return ( isset( $input ) && $input === '1' ) ? '1' : '0';
}

    public function render_dashboard() {
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>主题设置中心</h1>
                <p class="description">欢迎来到 wuchaiwp 主题设置中心，在这里您可以自定义网站的各项功能。</p>
            </div>

            <div class="wuchaiwp-dashboard">
                <div class="dashboard-card">
                    <h3>📊 网站统计</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo wp_count_posts()->publish; ?></span>
                            <span class="stat-label">已发布文章</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo count(get_categories()); ?></span>
                            <span class="stat-label">分类数量</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo count_users()['total_users']; ?></span>
                            <span class="stat-label">用户数量</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>⚙️ 快速设置</h3>
                    <div class="quick-links">
                        <a href="?page=wuchaiwp-settings" class="quick-link">首页设置</a>
                        <a href="?page=wuchaiwp-appearance-settings" class="quick-link">外观设置</a>
                        <a href="?page=wuchaiwp-style-presets" class="quick-link">样式预设</a>
                        <a href="?page=wuchaiwp-copyright-settings" class="quick-link">版权说明</a>
                        <a href="?page=wuchaiwp-donate-settings" class="quick-link">打赏设置</a>
                        <a href="?page=wuchaiwp-usage" class="quick-link">使用说明</a>
                    </div>
                </div>
            </div>

            <!-- 首页模板设置 -->
            <div class="home-content-settings">
                <div class="wuchaiwp-header">
                    <h1>首页模板设置</h1>
                </div>
                
                <form method="post" action="options.php" class="wuchaiwp-form">
                    <?php settings_fields('wuchaiwp_home_settings'); ?>
                

                    <div class="form-section">
                        <h3>🏠 首页模板选择</h3>
                        
                        <div class="form-row">
                            <label>选择首页模板</label>
                            <select name="wuchaiwp_frontpage_template">
                                <?php
                                // 自动扫描 template-parts/frontpage/ 目录中的模板文件
                                $template_dir = get_template_directory() . '/template-parts/frontpage/';
                                $template_files = glob($template_dir . 'frontpage-*.php');
                                $template_icons = array(
                                    'default' => '📄',
                                    'multi-section' => '📚',
                                    'landing' => '🎯',
                                    'blog' => '📝',
                                    'custom' => '⚙️',
                                );
                                $template_names = array(
                                    'default' => '默认首页模板',
                                    'multi-section' => '多区块首页模板',
                                    'landing' => '落地页模板',
                                    'blog' => '博客风格模板',
                                    'custom' => '自定义模板',
                                );
                                
                                foreach ($template_files as $file) {
                                    $template_name = basename($file, '.php');
                                    $template_slug = str_replace('frontpage-', '', $template_name);
                                    $icon = isset($template_icons[$template_slug]) ? $template_icons[$template_slug] : '📄';
                                    $label = isset($template_names[$template_slug]) ? $template_names[$template_slug] : ucwords(str_replace('-', ' ', $template_slug));
                                    ?>
                                    <option value="<?php echo esc_attr($template_slug); ?>" <?php selected(get_option('wuchaiwp_frontpage_template', 'default'), $template_slug); ?>>
                                        <?php echo $icon; ?> <?php echo esc_html($label); ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <p class="help">选择首页显示的模板风格。在 template-parts/frontpage/ 目录中创建 frontpage-{名称}.php 文件即可自动添加新模板。</p>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <?php submit_button('保存设置', 'primary', 'submit', false); ?>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#upload_hero_bg_image').click(function(e) {
                    e.preventDefault();
                    
                    var mediaUploader = wp.media({
                        title: '选择背景图片',
                        button: { text: '选择图片' },
                        multiple: false
                    });
                    
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#wuchaiwp_hero_bg_image').val(attachment.url);
                    });
                    
                    mediaUploader.open();
                });
            });
        </script>
        <?php
    }

    public function render_appearance_settings() {
        $settings = $this->get_appearance_settings();
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

    public function get_appearance_settings() {
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

    public function render_style_presets() {
        $presets = get_option('wuchaiwp_style_presets', array());
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>样式预设管理</h1>
                <p class="description">保存和管理您的样式预设，快速切换网站外观</p>
            </div>

            <div class="presets-container">
                <div class="presets-list">
                    <h3>已保存的预设</h3>
                    
                    <?php if (empty($presets)) : ?>
                        <p class="empty-message">暂无保存的预设，您可以在外观设置中创建预设</p>
                    <?php else : ?>
                        <div class="presets-grid">
                            <?php foreach ($presets as $key => $preset) : ?>
                                <div class="preset-card" data-preset-key="<?php echo esc_attr($key); ?>">
                                    <div class="preset-header" style="background: <?php echo $preset['theme_color']; ?>">
                                        <span class="preset-name"><?php echo esc_html($preset['name']); ?></span>
                                    </div>
                                    <div class="preset-colors">
                                        <div class="color-swatch" style="background: <?php echo $preset['theme_color']; ?>;" title="主题色"></div>
                                        <div class="color-swatch" style="background: <?php echo $preset['secondary_color']; ?>;" title="次要色"></div>
                                        <div class="color-swatch" style="background: <?php echo $preset['background_color']; ?>;" title="背景色"></div>
                                        <div class="color-swatch" style="background: <?php echo $preset['text_color']; ?>;" title="文字色"></div>
                                    </div>
                                    <div class="preset-actions">
                                        <button class="preset-action load-preset" data-preset-key="<?php echo esc_attr($key); ?>">🎯 应用</button>
                                        <button class="preset-action delete-preset" data-preset-key="<?php echo esc_attr($key); ?>">🗑️ 删除</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="presets-info">
                    <h3>💡 使用说明</h3>
                    <ul>
                        <li>在外观设置页面点击"保存为预设"可以保存当前样式</li>
                        <li>点击预设卡片上的"应用"按钮可以快速切换样式</li>
                        <li>预设会保存所有外观设置，包括颜色、字体和尺寸</li>
                        <li>删除预设不会影响当前网站的样式</li>
                    </ul>

                    <h3>🎨 内置预设</h3>
                    <div class="built-in-presets">
                        <div class="preset-card" data-preset-key="default">
                            <div class="preset-header" style="background: #3498db;">
                                <span class="preset-name">默认主题</span>
                            </div>
                            <div class="preset-colors">
                                <div class="color-swatch" style="background: #3498db;"></div>
                                <div class="color-swatch" style="background: #9b59b6;"></div>
                                <div class="color-swatch" style="background: #f5f5f5;"></div>
                                <div class="color-swatch" style="background: #333333;"></div>
                            </div>
                            <div class="preset-actions">
                                <button class="preset-action load-preset" data-preset-key="default">🎯 应用默认</button>
                            </div>
                        </div>
                        
                        <div class="preset-card" data-preset-key="dark">
                            <div class="preset-header" style="background: #2c3e50;">
                                <span class="preset-name">深色主题</span>
                            </div>
                            <div class="preset-colors">
                                <div class="color-swatch" style="background: #34495e;"></div>
                                <div class="color-swatch" style="background: #1abc9c;"></div>
                                <div class="color-swatch" style="background: #1a1a2e;"></div>
                                <div class="color-swatch" style="background: #ecf0f1;"></div>
                            </div>
                            <div class="preset-actions">
                                <button class="preset-action load-preset" data-preset-key="dark">🎯 应用深色</button>
                            </div>
                        </div>

                        <div class="preset-card" data-preset-key="warm">
                            <div class="preset-header" style="background: #e67e22;">
                                <span class="preset-name">暖色主题</span>
                            </div>
                            <div class="preset-colors">
                                <div class="color-swatch" style="background: #e67e22;"></div>
                                <div class="color-swatch" style="background: #d35400;"></div>
                                <div class="color-swatch" style="background: #fdf6e3;"></div>
                                <div class="color-swatch" style="background: #4a4a4a;"></div>
                            </div>
                            <div class="preset-actions">
                                <button class="preset-action load-preset" data-preset-key="warm">🎯 应用暖色</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_copyright_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('您没有权限访问此页面。'));
        }
        
        if (isset($_POST['wuchaiwp_copyright_submit']) && check_admin_referer('wuchaiwp_copyright_nonce')) {
            $copyright_text = isset($_POST['wuchaiwp_copyright_text']) ? wp_kses_post($_POST['wuchaiwp_copyright_text']) : '';
            update_option('wuchaiwp_copyright_text', $copyright_text);
            echo '<div class="updated"><p>设置已保存！</p></div>';
        }
        
        $copyright_text = get_option('wuchaiwp_copyright_text', '');
        ?>
        <div class="wrap">
            <h1>版权说明设置</h1>
            <form method="post" action="">
                <?php wp_nonce_field('wuchaiwp_copyright_nonce'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">网站版权说明</th>
                        <td>
                            <textarea name="wuchaiwp_copyright_text" rows="10" cols="50" class="large-text"><?php echo esc_textarea($copyright_text); ?></textarea>
                            <p class="description">在此输入网站的版权说明内容，将显示在文章详情页的版权区域底部。</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="wuchaiwp_copyright_submit" class="button-primary" value="保存设置">
                </p>
            </form>
        </div>
        <?php
    }

    public function render_usage_page() {
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>使用说明</h1>
                <p class="description">了解如何使用 wuchaiwp 主题的各项功能</p>
            </div>

            <div class="usage-content">
                <div class="usage-section">
                    <h2>📝 快速开始</h2>
                    <ol>
                        <li>在「主题设置」->「首页设置」中配置Hero区域</li>
                        <li>在「外观设置」中自定义网站的颜色和字体</li>
                        <li>使用「样式预设」快速切换网站主题</li>
                        <li>在「版权说明」中设置网站版权信息</li>
                    </ol>
                </div>

                <div class="usage-section">
                    <h2>🏠 首页设置</h2>
                    <p>首页设置允许您配置Hero区域的显示内容：</p>
                    <ul>
                        <li><strong>Hero标题：</strong>首页顶部显示的主标题</li>
                        <li><strong>Hero描述：</strong>标题下方的描述文字</li>
                        <li><strong>背景图片：</strong>Hero区域的背景图片</li>
                        <li><strong>搜索框：</strong>自定义搜索框占位文本</li>
                    </ul>
                </div>

                <div class="usage-section">
                    <h2>🎨 外观设置</h2>
                    <p>外观设置允许您自定义网站的视觉风格：</p>
                    <ul>
                        <li><strong>页面尺寸：</strong>容器宽度、内容宽度、圆角半径、阴影强度</li>
                        <li><strong>字体设置：</strong>字体族、字体大小、行高</li>
                        <li><strong>颜色设置：</strong>主题色、次要色、背景色、文字色、链接色</li>
                    </ul>
                </div>

                <div class="usage-section">
                    <h2>🎭 样式预设</h2>
                    <p>样式预设提供了快速切换网站外观的方式：</p>
                    <ul>
                        <li><strong>保存预设：</strong>在外观设置中点击"保存为预设"</li>
                        <li><strong>应用预设：</strong>点击预设卡片上的"应用"按钮</li>
                        <li><strong>删除预设：</strong>点击预设卡片上的"删除"按钮</li>
                        <li><strong>内置预设：</strong>默认主题、深色主题、暖色主题</li>
                    </ul>
                </div>

                <div class="usage-section">
                    <h2>📄 版权说明</h2>
                    <p>版权说明设置允许您添加网站的版权信息，将显示在文章详情页底部。</p>
                </div>

                <div class="usage-section">
                    <h2>💡 提示</h2>
                    <p>所有设置保存后会立即生效，无需刷新缓存。如果您遇到任何问题，请查看官方文档或联系技术支持。</p>
                </div>
            </div>
        </div>
        <?php
    }

    public function ajax_save_preset() {
        check_ajax_referer('wuchaiwp_preset_nonce', 'nonce');
        
        $preset_name = sanitize_text_field($_POST['preset_name']);
        $settings = $this->get_appearance_settings();
        $settings['name'] = $preset_name;
        
        $presets = get_option('wuchaiwp_style_presets', array());
        $presets[] = $settings;
        
        update_option('wuchaiwp_style_presets', $presets);
        
        wp_send_json_success(array('message' => '预设保存成功'));
    }

    public function ajax_load_preset() {
        check_ajax_referer('wuchaiwp_preset_nonce', 'nonce');
        
        $preset_key = sanitize_text_field($_POST['preset_key']);
        
        $built_in_presets = array(
            'default' => array(
                'name' => '默认主题',
                'container_width' => '1200',
                'content_width' => '800',
                'font_family' => 'pingfang',
                'font_size' => '16',
                'line_height' => '1.6',
                'theme_color' => '#3498db',
                'secondary_color' => '#9b59b6',
                'background_color' => '#f5f5f5',
                'text_color' => '#333333',
                'link_color' => '#3498db',
                'border_radius' => '8',
                'shadow_intensity' => '10',
            ),
            'dark' => array(
                'name' => '深色主题',
                'container_width' => '1200',
                'content_width' => '800',
                'font_family' => 'pingfang',
                'font_size' => '16',
                'line_height' => '1.6',
                'theme_color' => '#34495e',
                'secondary_color' => '#1abc9c',
                'background_color' => '#1a1a2e',
                'text_color' => '#ecf0f1',
                'link_color' => '#3498db',
                'border_radius' => '8',
                'shadow_intensity' => '15',
            ),
            'warm' => array(
                'name' => '暖色主题',
                'container_width' => '1200',
                'content_width' => '800',
                'font_family' => 'pingfang',
                'font_size' => '16',
                'line_height' => '1.6',
                'theme_color' => '#e67e22',
                'secondary_color' => '#d35400',
                'background_color' => '#fdf6e3',
                'text_color' => '#4a4a4a',
                'link_color' => '#e67e22',
                'border_radius' => '8',
                'shadow_intensity' => '10',
            ),
        );
        
        if (isset($built_in_presets[$preset_key])) {
            $preset = $built_in_presets[$preset_key];
        } else {
            $presets = get_option('wuchaiwp_style_presets', array());
            if (!isset($presets[$preset_key])) {
                wp_send_json_error(array('message' => '预设不存在'));
            }
            $preset = $presets[$preset_key];
        }
        
        foreach ($preset as $key => $value) {
            if ($key !== 'name') {
                update_option('wuchaiwp_' . $key, $value);
            }
        }
        
        wp_send_json_success(array('message' => '预设应用成功'));
    }

    public function ajax_delete_preset() {
        check_ajax_referer('wuchaiwp_preset_nonce', 'nonce');
        
        $preset_key = sanitize_text_field($_POST['preset_key']);
        $presets = get_option('wuchaiwp_style_presets', array());
        
        if (!isset($presets[$preset_key])) {
            wp_send_json_error(array('message' => '预设不存在'));
        }
        
        unset($presets[$preset_key]);
        update_option('wuchaiwp_style_presets', $presets);
        
        wp_send_json_success(array('message' => '预设删除成功'));
    }

    public function ajax_reset_default() {
        check_ajax_referer('wuchaiwp_preset_nonce', 'nonce');
        
        // 恢复为简约黑白配色
        update_option('wuchaiwp_container_width', '1200');
        update_option('wuchaiwp_content_width', '800');
        update_option('wuchaiwp_content_sidebar_ratio', '3-1');
        update_option('wuchaiwp_sidebar_margin', '30');
        update_option('wuchaiwp_font_family', 'default');
        update_option('wuchaiwp_font_size', '16');
        update_option('wuchaiwp_line_height', '1.6');
        update_option('wuchaiwp_theme_color', '#333333');
        update_option('wuchaiwp_secondary_color', '#666666');
        update_option('wuchaiwp_background_color', '#ffffff');
        update_option('wuchaiwp_text_color', '#333333');
        update_option('wuchaiwp_link_color', '#666666');
        update_option('wuchaiwp_border_radius', '0');
        update_option('wuchaiwp_shadow_intensity', '0');
        
        wp_send_json_success(array('message' => '已恢复默认设置'));
    }
}

// 初始化设置类
new Wuchaiwp_Settings();
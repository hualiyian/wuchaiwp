<?php
/**
 * 模板选择设置 - 使用 WordPress 设置 API
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Template_Selection {
    
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function register_settings() {
        // 注册设置组和选项
        register_setting(
            'wuchaiwp_template_selection_group', // 设置组名称
            'wuchaiwp_post_type_templates',       // 选项名称
            array(
                'sanitize_callback' => array($this, 'sanitize_settings'),
                'default' => array()
            )
        );
        
        // 添加设置字段
        add_settings_section(
            'wuchaiwp_template_selection_section',
            '', // 标题留空，我们在页面中自己显示
            array($this, 'render_section'),
            'wuchaiwp-template-selection'
        );
    }
    
    public function sanitize_settings($input) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        foreach ($input as $post_type => $templates) {
            $sanitized[$post_type] = array(
                'single' => isset($templates['single']) ? sanitize_text_field($templates['single']) : 'default',
                'archive' => isset($templates['archive']) ? sanitize_text_field($templates['archive']) : 'default',
                'edit' => isset($templates['edit']) ? sanitize_text_field($templates['edit']) : 'default'
            );
        }
        return $sanitized;
    }
    
    public function render_section() {
        // 留空，我们在页面中自己处理
    }
    
    public function render_admin_page() {
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>🎯 模板选择</h1>
                <p class="description">为每种文章类型选择详情页、归档页和编辑页模板</p>
            </div>

            <!-- 使用 WordPress 设置 API 的表单 -->
            <form action="options.php" method="post" class="wuchaiwp-form">
                <?php
                // 自动生成 nonce 和隐藏字段
                settings_fields('wuchaiwp_template_selection_group');
                do_settings_sections('wuchaiwp-template-selection');
                ?>

                <div class="wuchaiwp-form-section">
                    <h3>📋 文章类型模板设置</h3>
                    <p class="description">选择每种文章类型使用的模板</p>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>文章类型</th>
                                <th>详情页模板</th>
                                <th>归档页模板</th>
                                <th>编辑页模板</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // 获取所有公开文章类型
                            $post_types = get_post_types(array('public' => true), 'names');
                            $current_settings = get_option('wuchaiwp_post_type_templates', array());
                            
                            foreach ($post_types as $post_type) :
                                $post_type_obj = get_post_type_object($post_type);
                                // 获取当前设置，默认使用 'default'
                                $current = isset($current_settings[$post_type]) ? $current_settings[$post_type] : array(
                                    'single' => 'default',
                                    'archive' => 'default',
                                    'edit' => 'default'
                                );
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($post_type_obj->labels->name); ?></strong>
                                        <p class="description">标识: <code><?php echo esc_html($post_type); ?></code></p>
                                    </td>
                                    <td>
                                        <select name="wuchaiwp_post_type_templates[<?php echo esc_attr($post_type); ?>][single]">
                                            <?php foreach ($this->get_available_templates('single') as $value => $label) : ?>
                                                <option value="<?php echo esc_attr($value); ?>" <?php selected($current['single'], $value); ?>>
                                                    <?php echo esc_html($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="wuchaiwp_post_type_templates[<?php echo esc_attr($post_type); ?>][archive]">
                                            <?php foreach ($this->get_available_templates('archive') as $value => $label) : ?>
                                                <option value="<?php echo esc_attr($value); ?>" <?php selected($current['archive'], $value); ?>>
                                                    <?php echo esc_html($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="wuchaiwp_post_type_templates[<?php echo esc_attr($post_type); ?>][edit]">
                                            <?php foreach ($this->get_available_templates('edit') as $value => $label) : ?>
                                                <option value="<?php echo esc_attr($value); ?>" <?php selected($current['edit'], $value); ?>>
                                                    <?php echo esc_html($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <?php submit_button('保存模板设置', 'primary'); ?>
                </div>
            </form>

            <div class="wuchaiwp-form-section info-box">
                <h3>💡 使用说明</h3>
                <ul>
                    <li><strong>详情页模板</strong>: 用于显示单篇文章内容的页面</li>
                    <li><strong>归档页模板</strong>: 用于显示文章列表的页面</li>
                    <li><strong>编辑页模板</strong>: 用于后台编辑文章时显示的自定义字段</li>
                    <li>选择「默认模板」将使用 WordPress 默认模板</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * 获取可用模板列表
     */
    private function get_available_templates($type = 'single') {
        $templates = array('default' => '默认模板');
        
        // 从主题自定义模板配置获取
        $custom_templates = get_option('wuchaiwp_custom_templates', array());
        foreach ($custom_templates as $slug => $data) {
            $templates[$slug] = $data['icon'] . ' ' . $data['name'];
        }
        
        // 从自定义文章类型配置获取模板
        $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
        if (empty($custom_post_types)) {
            $custom_post_types = get_option('wuchai_custom_post_types', array());
        }
        foreach ($custom_post_types as $key => $post_type) {
            if (!empty($post_type['template'])) {
                $templates[$post_type['template']] = ucfirst($post_type['template']) . ' 模板';
            }
        }
        
        // 根据类型扫描模板目录
        if ($type === 'edit') {
            // 编辑页模板存储在 inc/admin/edit-templates/
            $template_dir = get_template_directory() . '/inc/admin/edit-templates/';
            $file_pattern = 'edit-*.php';
        } else {
            // 详情页和归档页模板存储在 templates/{type}/
            $template_dir = get_template_directory() . '/templates/' . $type . '/';
            $file_pattern = $type . '-*.php';
        }
        
        if (file_exists($template_dir)) {
            $files = glob($template_dir . $file_pattern);
            foreach ($files as $file) {
                $slug = str_replace(array($type . '-', '.php'), '', basename($file));
                if (!isset($templates[$slug])) {
                    $templates[$slug] = ucwords(str_replace('-', ' ', $slug));
                }
            }
        }
        
        return $templates;
    }
    
    /**
     * 获取指定文章类型的模板设置（静态方法，供模板加载器使用）
     */
    public static function get_post_type_template($post_type, $template_type) {
        $templates = get_option('wuchaiwp_post_type_templates', array());
        if (isset($templates[$post_type][$template_type])) {
            return $templates[$post_type][$template_type];
        }
        return 'default';
    }
}

// 初始化
new Wuchaiwp_Template_Selection();
?>
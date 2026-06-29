<?php
/**
 * 吾侪主题 - 分类法管理器
 * 
 * 为任意自定义文章类型启用或禁用分类和标签功能
 */

class Wuchaiwp_Taxonomy_Manager {

    private $option_name = 'wuchaiwp_taxonomy_settings';

  
   public function __construct() {
        // 不在构造函数中注册子菜单，让主题设置统一管理菜单顺序
        add_action('admin_init', array($this, 'register_settings'));
        add_action('init', array($this, 'register_taxonomies_to_post_types'), 99);
        add_action('admin_menu', array($this, 'add_taxonomy_submenus'), 99);
        add_action('admin_notices', array($this, 'admin_notice'));
        add_action('wp_ajax_wuchaiwp_delete_taxonomy', array($this, 'ajax_delete_taxonomy'));
        add_action('wp_ajax_wuchaiwp_reset_all_taxonomies', array($this, 'ajax_reset_all_taxonomies'));
    }
    
    // 提供公共方法让外部调用注册菜单
    public function add_admin_menu() {
        // 添加为主题设置的子菜单
        add_submenu_page(
            'wuchaiwp-settings',
            '文章类型分类标签管理',
            '分类管理',
            'manage_options',
            'wuchaiwp-taxonomy-manager',
            array($this, 'render_admin_page')
        );
    }
    
  
    public static function activate() {
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

 
    public function register_settings() {
        register_setting('wuchaiwp_taxonomy_settings_group', $this->option_name, array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));
    }

    public function sanitize_settings($settings) {
        global $wpdb;
        
        $sanitized = array();
        
        $old_settings = $this->get_settings();
        
        if (!empty($old_settings['post_types'])) {
            foreach ($old_settings['post_types'] as $post_type => $config) {
                $sanitized['post_types'][$post_type] = array(
                    'has_category' => false,
                    'has_tag' => false
                );
            }
        }
        
        if (!empty($settings['post_types'])) {
            foreach ($settings['post_types'] as $post_type => $config) {
                $sanitized['post_types'][$post_type] = array(
                    'has_category' => isset($config['has_category']) ? true : false,
                    'has_tag' => isset($config['has_tag']) ? true : false
                );
            }
        }
        
        if (!empty($old_settings['post_types'])) {
            foreach ($old_settings['post_types'] as $post_type => $old_config) {
                $new_config = !empty($sanitized['post_types'][$post_type]) ? $sanitized['post_types'][$post_type] : array();
                
                $old_has_category = !empty($old_config['has_category']);
                $new_has_category = !empty($new_config['has_category']);
                if ($old_has_category && !$new_has_category) {
                    $this->delete_taxonomy_from_db($post_type . '_category');
                }
                
                $old_has_tag = !empty($old_config['has_tag']);
                $new_has_tag = !empty($new_config['has_tag']);
                if ($old_has_tag && !$new_has_tag) {
                    $this->delete_taxonomy_from_db($post_type . '_tag');
                }
            }
        }
        
        if (get_option('wuchaiwp_taxonomy_needs_flush') !== '1') {
            update_option('wuchaiwp_taxonomy_needs_flush', '1');
        }
        
        return $sanitized;
    }

    private function delete_taxonomy_from_db($taxonomy_key) {
        global $wpdb;
        
        if (!taxonomy_exists($taxonomy_key)) {
            return;
        }
        
        $terms = get_terms(array(
            'taxonomy' => $taxonomy_key,
            'hide_empty' => false,
            'number' => 0
        ));
        
        foreach ($terms as $term) {
            wp_delete_term($term->term_id, $taxonomy_key);
        }
        
        $wpdb->delete($wpdb->term_taxonomy, array('taxonomy' => $taxonomy_key));
        
        wp_cache_delete('taxonomy_' . $taxonomy_key, 'taxonomy');
        wp_cache_delete('all_taxonomies', 'taxonomy');
    }

    public function admin_notice() {
        if (get_option('wuchaiwp_taxonomy_needs_flush') === '1') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>分类标签设置已更新，请 <a href="<?php echo admin_url('options-permalink.php'); ?>">点击这里</a> 刷新重写规则。</p>
            </div>
            <?php
        }
        
        if (isset($_GET['wuchaiwp_taxonomy_deleted']) && $_GET['wuchaiwp_taxonomy_deleted'] === '1') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>分类法已成功删除！</p>
            </div>
            <?php
        }
    }

    public function ajax_delete_taxonomy() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('权限不足');
        }
        
        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';
        
        if (empty($taxonomy) || empty($post_type)) {
            wp_send_json_error('参数错误');
        }
        
        $protected_taxonomies = array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format');
        if (in_array($taxonomy, $protected_taxonomies)) {
            wp_send_json_error('不能删除WordPress内置分类法');
        }
        
        if (!taxonomy_exists($taxonomy)) {
            wp_send_json_error('分类法不存在');
        }
        
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'number' => 0
        ));
        
        foreach ($terms as $term) {
            wp_delete_term($term->term_id, $taxonomy);
        }
        
        global $wpdb;
        $wpdb->delete($wpdb->term_taxonomy, array('taxonomy' => $taxonomy));
        
        wp_cache_delete('taxonomy_' . $taxonomy, 'taxonomy');
        wp_cache_delete('all_taxonomies', 'taxonomy');
        
        $settings = $this->get_settings();
        if (!empty($settings['post_types'][$post_type])) {
            if ($taxonomy === $post_type . '_category') {
                $settings['post_types'][$post_type]['has_category'] = false;
            } elseif ($taxonomy === $post_type . '_tag') {
                $settings['post_types'][$post_type]['has_tag'] = false;
            }
            update_option($this->option_name, $settings);
        }
        
        update_option('wuchaiwp_taxonomy_needs_flush', '1');
        
        wp_send_json_success('分类法已删除');
    }

    public function ajax_reset_all_taxonomies() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('权限不足');
        }
        
        $settings = $this->get_settings();
        $deleted_count = 0;
        
        if (!empty($settings['post_types'])) {
            foreach ($settings['post_types'] as $post_type => $config) {
                $category_tax = $post_type . '_category';
                if (taxonomy_exists($category_tax)) {
                    $terms = get_terms(array(
                        'taxonomy' => $category_tax,
                        'hide_empty' => false,
                        'number' => 0
                    ));
                    foreach ($terms as $term) {
                        wp_delete_term($term->term_id, $category_tax);
                    }
                    $deleted_count++;
                }
                
                $tag_tax = $post_type . '_tag';
                if (taxonomy_exists($tag_tax)) {
                    $terms = get_terms(array(
                        'taxonomy' => $tag_tax,
                        'hide_empty' => false,
                        'number' => 0
                    ));
                    foreach ($terms as $term) {
                        wp_delete_term($term->term_id, $tag_tax);
                    }
                    $deleted_count++;
                }
                
                $settings['post_types'][$post_type]['has_category'] = false;
                $settings['post_types'][$post_type]['has_tag'] = false;
            }
        }
        
        update_option($this->option_name, $settings);
        update_option('wuchaiwp_taxonomy_needs_flush', '1');
        
        wp_send_json_success('已删除 ' . $deleted_count . ' 个分类法');
    }

    private function get_post_type_taxonomies($post_type) {
        $taxonomies = get_object_taxonomies($post_type, 'objects');
        $result = array();
        
        foreach ($taxonomies as $tax_key => $tax_obj) {
            if (($tax_key === 'category' || $tax_key === 'post_tag') && $post_type !== 'post') {
                continue;
            }
            
            $is_wuchaiwp = ($tax_key === $post_type . '_category' || $tax_key === $post_type . '_tag');
            
            $result[$tax_key] = array(
                'name' => $tax_obj->labels->name,
                'hierarchical' => $tax_obj->hierarchical,
                'is_wuchaiwp' => $is_wuchaiwp,
                'count' => wp_count_terms($tax_key)
            );
        }
        
        return $result;
    }

    public function get_settings() {
        $defaults = array(
            'post_types' => array(
                'post' => array(
                    'has_category' => true,
                    'has_tag' => true
                )
            )
        );
        
        $settings = get_option($this->option_name, array());
        return array_replace_recursive($defaults, $settings);
    }

    public function register_taxonomies_to_post_types() {
        $settings = $this->get_settings();
        
        if (empty($settings['post_types'])) {
            return;
        }

        foreach ($settings['post_types'] as $post_type => $config) {
            if (!post_type_exists($post_type)) {
                continue;
            }

            if (!empty($config['has_category'])) {
                $this->register_category_taxonomy($post_type);
            }

            if (!empty($config['has_tag'])) {
                $this->register_tag_taxonomy($post_type);
            }
        }
    }

    private function register_category_taxonomy($post_type) {
        $taxonomy_key = $post_type . '_category';
        
        if (taxonomy_exists($taxonomy_key)) {
            return;
        }

        $post_type_obj = get_post_type_object($post_type);
        $plural_name = $post_type_obj ? $post_type_obj->labels->name : ucfirst($post_type);
        $singular_name = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst($post_type);

        $labels = array(
            'name'              => __('独立' . $plural_name . '分类', 'wuchaiwp'),
            'singular_name'     => __('独立' . $singular_name . '分类', 'wuchaiwp'),
            'search_items'      => __('搜索独立分类', 'wuchaiwp'),
            'all_items'         => __('所有独立分类', 'wuchaiwp'),
            'parent_item'       => __('父独立分类', 'wuchaiwp'),
            'parent_item_colon' => __('父独立分类:', 'wuchaiwp'),
            'edit_item'         => __('编辑独立分类', 'wuchaiwp'),
            'update_item'       => __('更新独立分类', 'wuchaiwp'),
            'add_new_item'      => __('添加新独立分类', 'wuchaiwp'),
            'new_item_name'     => __('新独立分类名称', 'wuchaiwp'),
            'menu_name'         => __('独立分类', 'wuchaiwp'),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array('slug' => $post_type . '-category'),
            'show_in_rest'      => true,
        );

        register_taxonomy($taxonomy_key, $post_type, $args);
    }

    private function register_tag_taxonomy($post_type) {
        $taxonomy_key = $post_type . '_tag';
        
        if (taxonomy_exists($taxonomy_key)) {
            return;
        }

        $post_type_obj = get_post_type_object($post_type);
        $plural_name = $post_type_obj ? $post_type_obj->labels->name : ucfirst($post_type);
        $singular_name = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst($post_type);

        $labels = array(
            'name'              => __('独立' . $plural_name . '标签', 'wuchaiwp'),
            'singular_name'     => __('独立' . $singular_name . '标签', 'wuchaiwp'),
            'search_items'      => __('搜索独立标签', 'wuchaiwp'),
            'all_items'         => __('所有独立标签', 'wuchaiwp'),
            'parent_item'       => __('父独立标签', 'wuchaiwp'),
            'parent_item_colon' => __('父独立标签:', 'wuchaiwp'),
            'edit_item'         => __('编辑独立标签', 'wuchaiwp'),
            'update_item'       => __('更新独立标签', 'wuchaiwp'),
            'add_new_item'      => __('添加新独立标签', 'wuchaiwp'),
            'new_item_name'     => __('新独立标签名称', 'wuchaiwp'),
            'menu_name'         => __('独立标签', 'wuchaiwp'),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array('slug' => $post_type . '-tag'),
            'show_in_rest'      => true,
        );

        register_taxonomy($taxonomy_key, $post_type, $args);
    }

    public function add_taxonomy_submenus() {
        global $submenu;
        
        $settings = $this->get_settings();
        
        if (empty($settings['post_types'])) {
            return;
        }

        foreach ($settings['post_types'] as $post_type => $config) {
            if (!post_type_exists($post_type)) {
                continue;
            }

            $post_type_obj = get_post_type_object($post_type);
            $plural_name = $post_type_obj ? $post_type_obj->labels->name : ucfirst($post_type);
            $parent_slug = 'edit.php?post_type=' . $post_type;

            if (!empty($config['has_category'])) {
                $taxonomy_key = $post_type . '_category';
                $menu_slug = 'edit-tags.php?taxonomy=' . $taxonomy_key . '&post_type=' . $post_type;
                
                if (taxonomy_exists($taxonomy_key) && !$this->submenu_exists($parent_slug, $menu_slug)) {
                    add_submenu_page(
                        $parent_slug,
                        '独立' . $plural_name . '分类',
                        '独立分类',
                        'manage_categories',
                        $menu_slug,
                        ''
                    );
                }
            }

            if (!empty($config['has_tag'])) {
                $taxonomy_key = $post_type . '_tag';
                $menu_slug = 'edit-tags.php?taxonomy=' . $taxonomy_key . '&post_type=' . $post_type;
                
                if (taxonomy_exists($taxonomy_key) && !$this->submenu_exists($parent_slug, $menu_slug)) {
                    add_submenu_page(
                        $parent_slug,
                        '独立' . $plural_name . '标签',
                        '独立标签',
                        'manage_categories',
                        $menu_slug,
                        ''
                    );
                }
            }
        }
    }

    private function submenu_exists($parent_slug, $menu_slug) {
        global $submenu;
        
        if (!isset($submenu[$parent_slug])) {
            return false;
        }
        
        foreach ($submenu[$parent_slug] as $item) {
            if (isset($item[2]) && $item[2] === $menu_slug) {
                return true;
            }
        }
        
        return false;
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('您没有权限访问此页面。', 'wuchaiwp'));
        }

        $post_types = get_post_types(array(
            'show_ui' => true,
            'public' => true
        ), 'objects');

        $settings = $this->get_settings();
        ?>
        <div class="wrap">
            <h1>文章类型分类标签管理</h1>
            <p class="description">为各个文章类型启用或禁用独立分类和独立标签功能</p>

            <form method="post" action="options.php">
                <?php settings_fields('wuchaiwp_taxonomy_settings_group'); ?>
                <?php do_settings_sections('wuchaiwp-taxonomy-manager'); ?>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>文章类型</th>
                            <th>启用独立分类</th>
                            <th>启用独立标签</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($post_types as $post_type_key => $post_type_obj) : ?>
                            <?php
                            $has_category = !empty($settings['post_types'][$post_type_key]['has_category']);
                            $has_tag = !empty($settings['post_types'][$post_type_key]['has_tag']);
                            
                            $category_exists = taxonomy_exists($post_type_key . '_category');
                            $tag_exists = taxonomy_exists($post_type_key . '_tag');
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($post_type_obj->labels->name); ?></strong>
                                    <p class="description"><?php echo esc_html($post_type_key); ?></p>
                                </td>
                                <td>
                                    <label>
                                        <input type="checkbox" 
                                               name="<?php echo $this->option_name; ?>[post_types][<?php echo esc_attr($post_type_key); ?>][has_category]"
                                               value="1" <?php checked($has_category); ?>>
                                        启用
                                    </label>
                                    <?php if ($category_exists) : ?>
                                        <p class="description" style="color:#2271b1;">✓ 已注册: <code><?php echo $post_type_key; ?>_category</code></p>
                                    <?php elseif ($has_category) : ?>
                                        <p class="description" style="color:#f59e0b;">需要刷新规则</p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <label>
                                        <input type="checkbox" 
                                               name="<?php echo $this->option_name; ?>[post_types][<?php echo esc_attr($post_type_key); ?>][has_tag]"
                                               value="1" <?php checked($has_tag); ?>>
                                        启用
                                    </label>
                                    <?php if ($tag_exists) : ?>
                                        <p class="description" style="color:#2271b1;">✓ 已注册: <code><?php echo $post_type_key; ?>_tag</code></p>
                                    <?php elseif ($has_tag) : ?>
                                        <p class="description" style="color:#f59e0b;">需要刷新规则</p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($category_exists || $tag_exists) : ?>
                                        <span style="color:#22c55e;">✓ 已生效</span>
                                    <?php else : ?>
                                        <span style="color:#6b7280;">未配置</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php submit_button('保存设置'); ?>
            </form>

            <div class="notice notice-info">
                <h3>使用说明</h3>
                <ul>
                    <li><strong>独立分类</strong>: 启用后将为该文章类型创建独立的层级分类系统</li>
                    <li><strong>独立标签</strong>: 启用后将为该文章类型创建独立的非层级标签系统</li>
                    <li><strong>刷新规则</strong>: 修改设置后，请前往 <a href="<?php echo admin_url('options-permalink.php'); ?>">固定链接设置</a> 页面刷新重写规则</li>
                    <li>分类法标识符格式: <code>{post_type}_category</code></li>
                    <li>标签法标识符格式: <code>{post_type}_tag</code></li>
                    <li>启用后，"独立分类"和"独立标签"菜单将显示在对应文章类型的子菜单中</li>
                </ul>
            </div>

            <div class="notice notice-warning">
                <h3>注意事项</h3>
                <ul>
                    <li>修改设置后需要刷新页面才能看到新菜单</li>
                    <li>如果菜单仍未显示，请尝试清除浏览器缓存</li>
                    <li>确保文章类型本身已正确注册并显示在后台菜单中</li>
                </ul>
            </div>

            <div class="notice notice-danger" style="border-left-color: #dc3232;">
                <h3>重置分类法（危险操作）</h3>
                <p>如果菜单出现重复或其他异常问题，可以点击下方按钮删除所有本插件创建的分类法及其数据，然后重新配置。</p>
                <button class="button button-danger wuchaiwp-reset-all-taxonomies" style="background: #dc3232; border-color: #dc3232;">
                    重置所有分类法
                </button>
            </div>

            <h2>现有分类法管理</h2>
            <p class="description">查看所有文章类型已注册的分类法，并可删除自定义分类法（WordPress内置分类法不可删除）</p>

            <?php foreach ($post_types as $post_type_key => $post_type_obj) : ?>
                <?php $taxonomies = $this->get_post_type_taxonomies($post_type_key); ?>
                <?php if (!empty($taxonomies)) : ?>
                    <h3><?php echo esc_html($post_type_obj->labels->name); ?> (<code><?php echo $post_type_key; ?></code>)</h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>分类法名称</th>
                                <th>分类法标识</th>
                                <th>类型</th>
                                <th>术语数量</th>
                                <th>来源</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($taxonomies as $tax_key => $tax_info) : ?>
                                <?php 
                                $protected_taxonomies = array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format');
                                $is_protected = in_array($tax_key, $protected_taxonomies);
                                ?>
                                <tr>
                                    <td><?php echo esc_html($tax_info['name']); ?></td>
                                    <td><code><?php echo $tax_key; ?></code></td>
                                    <td><?php echo $tax_info['hierarchical'] ? '分类（层级）' : '标签（非层级）'; ?></td>
                                    <td><?php echo $tax_info['count']; ?></td>
                                    <td>
                                        <?php if ($is_protected) : ?>
                                            <span style="color:#dc3232;">WordPress内置</span>
                                        <?php elseif ($tax_info['is_wuchaiwp']) : ?>
                                            <span style="color:#2271b1;">本主题</span>
                                        <?php else : ?>
                                            <span style="color:#6b7280;">其他来源</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($is_protected) : ?>
                                            <span style="color:#9ca3af;">内置保护</span>
                                        <?php else : ?>
                                            <button class="button button-danger wuchaiwp-delete-taxonomy" 
                                                    data-taxonomy="<?php echo esc_attr($tax_key); ?>" 
                                                    data-post-type="<?php echo esc_attr($post_type_key); ?>">
                                                删除
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h3><?php echo esc_html($post_type_obj->labels->name); ?> (<code><?php echo $post_type_key; ?></code>)</h3>
                    <p style="color:#6b7280;">该文章类型暂无已注册的分类法</p>
                <?php endif; ?>
            <?php endforeach; ?>

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.wuchaiwp-delete-taxonomy').on('click', function() {
                        var taxonomy = $(this).data('taxonomy');
                        var post_type = $(this).data('post-type');
                        
                        if (confirm('确定要删除分类法 "' + taxonomy + '" 吗？这将删除该分类法下的所有术语，此操作不可撤销！')) {
                            var button = $(this);
                            button.prop('disabled', true).text('处理中...');
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'wuchaiwp_delete_taxonomy',
                                    taxonomy: taxonomy,
                                    post_type: post_type
                                },
                                success: function(response) {
                                    if (response.success) {
                                        location.reload();
                                    } else {
                                        alert('删除失败: ' + response.data);
                                        button.prop('disabled', false).text('删除');
                                    }
                                },
                                error: function() {
                                    alert('删除失败，请重试');
                                    button.prop('disabled', false).text('删除');
                                }
                            });
                        }
                    });
                    
                    $('.wuchaiwp-reset-all-taxonomies').on('click', function() {
                        if (confirm('警告！这将删除所有本主题创建的分类法及其下的所有术语，此操作不可撤销！\n\n确定要继续吗？')) {
                            var button = $(this);
                            button.prop('disabled', true).text('处理中...');
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'wuchaiwp_reset_all_taxonomies'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert(response.data);
                                        location.reload();
                                    } else {
                                        alert('重置失败: ' + response.data);
                                        button.prop('disabled', false).text('重置所有分类法');
                                    }
                                },
                                error: function() {
                                    alert('重置失败，请重试');
                                    button.prop('disabled', false).text('重置所有分类法');
                                }
                            });
                        }
                    });
                });
            </script>
        </div>
        <?php
    }
}

// 初始化分类法管理器
new Wuchaiwp_Taxonomy_Manager();
<?php
/**
 * 自定义文章类型管理器 - 支持独立分类法的编辑和回收站
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Custom_Post_Type_Manager {
    
    private $supports_options = array(
        'title' => '标题',
        'editor' => '编辑器',
        'author' => '作者',
        'thumbnail' => '特色图片',
        'excerpt' => '摘要',
        'trackbacks' => '引用',
        'custom-fields' => '自定义字段',
        'comments' => '评论',
        'revisions' => '修订版',
        'page-attributes' => '页面属性',
        'post-formats' => '文章格式'
    );
    
    public function __construct() {
        add_action('admin_init', array($this, 'handle_form_submit'));
        add_action('init', array($this, 'register_custom_post_types'));
    }
    

    public function render_admin_page() {
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'post_types';
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';
        $type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : ''; // cpt 或 taxonomy
        
        // 编辑模式
        if ($action === 'edit' && !empty($slug)) {
            if ($type === 'taxonomy') {
                $this->render_edit_taxonomy_form($slug);
            } else {
                $this->render_edit_cpt_form($slug);
            }
            return;
        }
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>📦 自定义文章类型管理</h1>
                <p class="description">创建、编辑和管理自定义文章类型</p>
            </div>
            
            <h2 class="nav-tab-wrapper">
                <a href="?page=wuchaiwp-cpt-manager&tab=post_types" class="nav-tab <?php echo $tab === 'post_types' ? 'nav-tab-active' : ''; ?>">
                    📄 文章类型列表
                </a>
                <a href="?page=wuchaiwp-cpt-manager&tab=trash" class="nav-tab <?php echo $tab === 'trash' ? 'nav-tab-active' : ''; ?>">
                    🗑️ 回收站
                    <?php $trash_count = $this->get_trash_count(); ?>
                    <?php if ($trash_count > 0) : ?>
                        <span class="update-plugins count-1"><span class="plugin-count"><?php echo $trash_count; ?></span></span>
                    <?php endif; ?>
                </a>
            </h2>
            
            <?php if ($tab === 'post_types') : ?>
                <!-- 创建文章类型表单 -->
                <div class="wuchaiwp-form-section">
                    <h3>➕ 创建新文章类型</h3>
                    <form method="post" action="" class="wuchaiwp-form">
                        <?php wp_nonce_field('wuchaiwp_create_cpt', 'wuchaiwp_cpt_nonce'); ?>
                        
                        <div class="form-row">
                            <label>名称 <span style="color:red;">*</span></label>
                            <input type="text" name="cpt_name" required placeholder="如：美食菜谱" class="regular-text">
                        </div>
                        
                        <div class="form-row">
                            <label>标识 <span style="color:red;">*</span></label>
                            <input type="text" name="cpt_slug" required placeholder="如：food-recipe" class="regular-text">
                        </div>
                        
                        <div class="form-row">
                            <label>描述</label>
                            <textarea name="cpt_description" rows="3" class="regular-text"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <label>图标</label>
                            <div>
                                <span id="cpt-icon-preview" style="font-size:32px;">📄</span>
                                <input type="hidden" id="cpt_icon" name="cpt_icon" value="📄">
                            </div>
                            <div style="padding:15px;background:#f8f9fa;border-radius:6px;">
                                <?php $icons = array('📄', '📁', '📂', '📋', '📝', '📖', '📚', '🎯', '🎨', '🎬', '🎵', '🌍', '🍎', '🚀', '⚙️', '💎'); ?>
                                <?php foreach ($icons as $icon) : ?>
                                    <button type="button" class="cpt-icon-btn" data-icon="<?php echo esc_attr($icon); ?>"
                                            style="width:36px;height:36px;border:2px solid #ddd;background:#fff;font-size:20px;border-radius:6px;margin:2px;"
                                            onclick="wuchaiwp_select_cpt_icon(this)">
                                        <?php echo esc_html($icon); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <label>功能支持</label>
                            <div class="checkbox-group">
                                <?php foreach ($this->supports_options as $key => $label) : ?>
                                    <label><input type="checkbox" name="cpt_supports[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, array('title', 'editor'))); ?>> <?php echo esc_html($label); ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <label>关联默认分类法（可选）</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="cpt_taxonomies[]" value="category"> 默认分类</label>
                                <label><input type="checkbox" name="cpt_taxonomies[]" value="post_tag"> 默认标签</label>
                            </div>
                            <p class="help">关联WordPress默认的分类和标签（建议仅在需要时勾选）</p>
                        </div>
                        
                        <div class="form-row">
                            <label>启用独立分类法</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="cpt_has_category"> 启用分类功能</label>
                                <label><input type="checkbox" name="cpt_has_tag"> 启用标签功能</label>
                            </div>
                            <p class="help">为该文章类型创建独立的分类和标签分类法，可公开访问</p>
                        </div>
                        
                        <div class="form-row">
                            <label>设置选项</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="cpt_public" checked> 公开</label>
                                <label><input type="checkbox" name="cpt_has_archive" checked> 启用归档</label>
                                <label><input type="checkbox" name="cpt_show_ui" checked> 显示管理界面</label>
                                <label><input type="checkbox" name="cpt_show_in_menu" checked> 在菜单中显示</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <label>菜单显示位置</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <select name="cpt_menu_position_preset" id="cpt_menu_position_preset">
                                    <option value="">选择预设位置</option>
                                    <option value="1">1 - 最顶部</option>
                                    <option value="2">2 - 仪表盘下方</option>
                                    <option value="5" selected>5 - 媒体库上方</option>
                                    <option value="10">10 - 链接上方</option>
                                    <option value="15">15 - 页面上方</option>
                                    <option value="20">20 - 评论上方</option>
                                    <option value="25">25 - 外观上方</option>
                                    <option value="60">60 - 工具上方</option>
                                    <option value="65">65 - 设置上方</option>
                                </select>
                                <span style="color:#999;">或</span>
                                <input type="number" name="cpt_menu_position" value="5" min="0" max="100" placeholder="自定义数值" style="width: 120px;">
                            </div>
                            <p class="help">设置文章类型在后台菜单中的显示位置，数值越小越靠前。可以选择预设位置或输入自定义数值</p>
                        </div>
                        
                        <div class="form-actions">
                            <?php submit_button('创建文章类型', 'primary', 'submit_create_cpt'); ?>
                        </div>
                    </form>
                </div>
                
                <!-- 文章类型列表 -->
                <div class="wuchaiwp-form-section">
                    <h3>📋 已注册的文章类型</h3>
                    <?php $post_types = $this->get_custom_post_types(); ?>
                    <?php if (empty($post_types)) : ?>
                        <p class="empty-message">暂无自定义文章类型</p>
                    <?php else : ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>图标</th>
                                    <th>名称</th>
                                    <th>标识</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($post_types as $post_slug => $data) : ?>
                                    <tr>
                                        <td><?php echo esc_html($data['icon']); ?></td>
                                        <td><strong><?php echo esc_html($data['labels']['name']); ?></strong></td>
                                        <td><code><?php echo esc_html($post_slug); ?></code></td>
                                        <td>
                                            <a href="?page=wuchaiwp-cpt-manager&action=edit&slug=<?php echo esc_attr($post_slug); ?>" class="button button-link" style="color:#3498db;">编辑</a>
                                            |
                                            <form method="post" action="" style="display:inline;">
                                                <?php wp_nonce_field('wuchaiwp_trash_cpt', 'wuchaiwp_trash_nonce'); ?>
                                                <input type="hidden" name="cpt_slug_trash" value="<?php echo esc_attr($post_slug); ?>">
                                                <button type="submit" name="submit_trash_cpt" class="button button-link delete">移到回收站</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <!-- 回收站页面 -->
                <div class="wuchaiwp-form-section">
                    <h3>🗑️ 回收站</h3>
                    
                    <!-- 文章类型回收站 -->
                    <div style="margin-bottom:30px;">
                        <h4>📄 文章类型</h4>
                        <?php $trash_cpts = $this->get_trash_cpts(); ?>
                    <?php if (empty($trash_cpts)) : ?>
                        <p style="color:#666;">暂无已删除的文章类型</p>
                    <?php else : ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>图标</th>
                                    <th>名称</th>
                                    <th>标识</th>
                                    <th>删除时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trash_cpts as $post_slug => $data) : ?>
                                    <tr>
                                        <td><?php echo esc_html($data['icon']); ?></td>
                                        <td><strong><?php echo esc_html($data['labels']['name']); ?></strong></td>
                                        <td><code><?php echo esc_html($post_slug); ?></code></td>
                                        <td><?php echo esc_html($data['deleted']); ?></td>
                                        <td>
                                            <form method="post" action="" style="display:inline;">
                                                <?php wp_nonce_field('wuchaiwp_restore_cpt', 'wuchaiwp_restore_nonce'); ?>
                                                <input type="hidden" name="cpt_slug_restore" value="<?php echo esc_attr($post_slug); ?>">
                                                <button type="submit" name="submit_restore_cpt" class="button button-link" style="color:#27ae60;">恢复</button>
                                            </form>
                                            |
                                            <form method="post" action="" style="display:inline;">
                                                <?php wp_nonce_field('wuchaiwp_delete_cpt', 'wuchaiwp_delete_nonce'); ?>
                                                <input type="hidden" name="cpt_slug_delete" value="<?php echo esc_attr($post_slug); ?>">
                                                <button type="submit" name="submit_delete_cpt" class="button button-link delete">永久删除</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- 清空回收站按钮 -->
                <?php if (!empty($trash_cpts)) : ?>
                        <div style="margin-top:20px;">
                            <form method="post" action="">
                                <?php wp_nonce_field('wuchaiwp_empty_trash', 'wuchaiwp_empty_trash_nonce'); ?>
                                <button type="submit" name="submit_empty_trash" class="button button-secondary" onclick="return confirm('确定要清空回收站吗？所有内容将被永久删除！')">清空回收站</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        function wuchaiwp_select_cpt_icon(btn) {
            document.querySelectorAll('.cpt-icon-btn').forEach(function(b) { b.style.borderColor = '#ddd'; });
            btn.style.borderColor = '#27ae60';
            document.getElementById('cpt-icon-preview').textContent = btn.getAttribute('data-icon');
            document.getElementById('cpt_icon').value = btn.getAttribute('data-icon');
        }
        
        // 菜单位置预设选择同步
        document.addEventListener('DOMContentLoaded', function() {
            var presetSelect = document.getElementById('cpt_menu_position_preset');
            var customInput = document.querySelector('input[name="cpt_menu_position"]');
            
            if (presetSelect && customInput) {
                presetSelect.addEventListener('change', function() {
                    if (this.value !== '') {
                        customInput.value = this.value;
                    }
                });
                
                customInput.addEventListener('input', function() {
                    if (this.value !== '' && this.value !== presetSelect.value) {
                        presetSelect.value = '';
                    }
                });
            }
        });
        </script>
        <?php
    }
    
    // 渲染文章类型编辑表单
    private function render_edit_cpt_form($slug) {
        $cpts = $this->get_custom_post_types();
        if (!isset($cpts[$slug])) {
            echo '<div class="wrap"><h1>文章类型不存在</h1></div>';
            return;
        }
        
        $data = $cpts[$slug];
        $supports = isset($data['supports']) ? $data['supports'] : array();
        $taxonomies = isset($data['taxonomies']) ? $data['taxonomies'] : array();
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>✏️ 编辑文章类型</h1>
                <a href="?page=wuchaiwp-cpt-manager" class="button button-secondary">← 返回列表</a>
            </div>
            
            <div class="wuchaiwp-form-section">
                <form method="post" action="" class="wuchaiwp-form">
                    <?php wp_nonce_field('wuchaiwp_edit_cpt', 'wuchaiwp_edit_nonce'); ?>
                    <input type="hidden" name="cpt_slug_edit" value="<?php echo esc_attr($slug); ?>">
                    
                    <div class="form-row">
                        <label>名称 <span style="color:red;">*</span></label>
                        <input type="text" name="cpt_name" required value="<?php echo esc_attr($data['labels']['name']); ?>" class="regular-text">
                    </div>
                    
                    <div class="form-row">
                        <label>标识 <span style="color:gray;">(不可修改)</span></label>
                        <input type="text" value="<?php echo esc_attr($slug); ?>" class="regular-text" disabled>
                    </div>
                    
                    <div class="form-row">
                        <label>描述</label>
                        <textarea name="cpt_description" rows="3" class="regular-text"><?php echo esc_textarea($data['description']); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <label>图标</label>
                        <div>
                            <span id="edit-icon-preview" style="font-size:32px;"><?php echo esc_html($data['icon']); ?></span>
                            <input type="hidden" id="edit_cpt_icon" name="cpt_icon" value="<?php echo esc_attr($data['icon']); ?>">
                        </div>
                        <div style="padding:15px;background:#f8f9fa;border-radius:6px;">
                            <?php $icons = array('📄', '📁', '📂', '📋', '📝', '📖', '📚', '🎯', '🎨', '🎬', '🎵', '🌍', '🍎', '🚀', '⚙️', '💎'); ?>
                            <?php foreach ($icons as $icon) : ?>
                                <button type="button" class="edit-cpt-icon-btn" data-icon="<?php echo esc_attr($icon); ?>"
                                        style="width:36px;height:36px;border:2px solid <?php echo $icon === $data['icon'] ? '#27ae60' : '#ddd'; ?>;font-size:20px;border-radius:6px;margin:2px;"
                                        onclick="wuchaiwp_select_edit_cpt_icon(this)">
                                    <?php echo esc_html($icon); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>功能支持</label>
                        <div class="checkbox-group">
                            <?php foreach ($this->supports_options as $key => $label) : ?>
                                <label><input type="checkbox" name="cpt_supports[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $supports)); ?>> <?php echo esc_html($label); ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>关联默认分类法</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="cpt_taxonomies[]" value="category" <?php checked(in_array('category', $taxonomies)); ?>> 默认分类</label>
                            <label><input type="checkbox" name="cpt_taxonomies[]" value="post_tag" <?php checked(in_array('post_tag', $taxonomies)); ?>> 默认标签</label>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>启用独立分类法</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="cpt_has_category" <?php checked(isset($data['has_category']) && $data['has_category']); ?>> 启用分类功能</label>
                            <label><input type="checkbox" name="cpt_has_tag" <?php checked(isset($data['has_tag']) && $data['has_tag']); ?>> 启用标签功能</label>
                        </div>
                        <p class="help">为该文章类型创建独立的分类和标签分类法，可公开访问</p>
                    </div>
                    
                    <div class="form-row">
                        <label>设置选项</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="cpt_public" <?php checked($data['public']); ?>> 公开</label>
                            <label><input type="checkbox" name="cpt_has_archive" <?php checked($data['has_archive']); ?>> 启用归档</label>
                            <label><input type="checkbox" name="cpt_show_ui" <?php checked($data['show_ui']); ?>> 显示管理界面</label>
                            <label><input type="checkbox" name="cpt_show_in_menu" <?php checked($data['show_in_menu']); ?>> 在菜单中显示</label>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>菜单显示位置</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <select name="cpt_menu_position_preset" id="cpt_menu_position_preset_edit">
                                <option value="">选择预设位置</option>
                                <option value="1" <?php selected($data['menu_position'], 1); ?>>1 - 最顶部</option>
                                <option value="2" <?php selected($data['menu_position'], 2); ?>>2 - 仪表盘下方</option>
                                <option value="5" <?php selected($data['menu_position'], 5); ?>>5 - 媒体库上方</option>
                                <option value="10" <?php selected($data['menu_position'], 10); ?>>10 - 链接上方</option>
                                <option value="15" <?php selected($data['menu_position'], 15); ?>>15 - 页面上方</option>
                                <option value="20" <?php selected($data['menu_position'], 20); ?>>20 - 评论上方</option>
                                <option value="25" <?php selected($data['menu_position'], 25); ?>>25 - 外观上方</option>
                                <option value="60" <?php selected($data['menu_position'], 60); ?>>60 - 工具上方</option>
                                <option value="65" <?php selected($data['menu_position'], 65); ?>>65 - 设置上方</option>
                            </select>
                            <span style="color:#999;">或</span>
                            <input type="number" name="cpt_menu_position" value="<?php echo esc_attr($data['menu_position'] ?? 5); ?>" min="0" max="100" placeholder="自定义数值" style="width: 120px;">
                        </div>
                        <p class="help">设置文章类型在后台菜单中的显示位置，数值越小越靠前。可以选择预设位置或输入自定义数值</p>
                    </div>
                    
                    <div class="form-actions">
                        <a href="?page=wuchaiwp-cpt-manager" class="button button-secondary">取消</a>
                        <?php submit_button('更新文章类型', 'primary', 'submit_edit_cpt'); ?>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        function wuchaiwp_select_edit_cpt_icon(btn) {
            document.querySelectorAll('.edit-cpt-icon-btn').forEach(function(b) { b.style.borderColor = '#ddd'; });
            btn.style.borderColor = '#27ae60';
            document.getElementById('edit-icon-preview').textContent = btn.getAttribute('data-icon');
            document.getElementById('edit_cpt_icon').value = btn.getAttribute('data-icon');
        }
        
        // 菜单位置预设选择同步（编辑页面）
        document.addEventListener('DOMContentLoaded', function() {
            var presetSelect = document.getElementById('cpt_menu_position_preset_edit');
            var customInput = document.querySelector('input[name="cpt_menu_position"]');
            
            if (presetSelect && customInput) {
                presetSelect.addEventListener('change', function() {
                    if (this.value !== '') {
                        customInput.value = this.value;
                    }
                });
                
                customInput.addEventListener('input', function() {
                    if (this.value !== '' && this.value !== presetSelect.value) {
                        presetSelect.value = '';
                    }
                });
            }
        });
        </script>
        <?php
    }
    
    public function handle_form_submit() {
        // 创建文章类型
        if (isset($_POST['submit_create_cpt']) && check_admin_referer('wuchaiwp_create_cpt', 'wuchaiwp_cpt_nonce')) {
            $name = sanitize_text_field($_POST['cpt_name']);
            $slug = sanitize_text_field($_POST['cpt_slug']);
            $description = sanitize_text_field($_POST['cpt_description']);
            $icon = sanitize_text_field($_POST['cpt_icon']);
            
            $supports = isset($_POST['cpt_supports']) ? array_map('sanitize_text_field', $_POST['cpt_supports']) : array('title', 'editor');
            $taxonomies = isset($_POST['cpt_taxonomies']) ? array_map('sanitize_text_field', $_POST['cpt_taxonomies']) : array();
            
            // 添加独立分类法
            $has_category = isset($_POST['cpt_has_category']);
            $has_tag = isset($_POST['cpt_has_tag']);
            
            if ($has_category) {
                $taxonomies[] = $slug . '_category';
            }
            if ($has_tag) {
                $taxonomies[] = $slug . '_tag';
            }
            
            $args = array(
                'labels' => array(
                    'name' => $name,
                    'singular_name' => $name,
                    'add_new' => '添加' . $name,
                    'add_new_item' => '添加新' . $name,
                    'edit_item' => '编辑' . $name,
                    'new_item' => '新' . $name,
                    'view_item' => '查看' . $name,
                    'search_items' => '搜索' . $name,
                    'not_found' => '未找到' . $name,
                    'not_found_in_trash' => '回收站中未找到' . $name,
                    'parent_item_colon' => '',
                    'menu_name' => $name
                ),
                'description' => $description,
                'public' => isset($_POST['cpt_public']),
                'has_archive' => isset($_POST['cpt_has_archive']),
                'show_ui' => isset($_POST['cpt_show_ui']),
                'show_in_menu' => isset($_POST['cpt_show_in_menu']),
                'menu_position' => !empty($_POST['cpt_menu_position']) ? intval($_POST['cpt_menu_position']) : null,
                'supports' => $supports,
                'taxonomies' => $taxonomies,
                'icon' => $icon,
                'has_category' => $has_category,
                'has_tag' => $has_tag,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_in_rest' => true,
                'rewrite' => array('slug' => $slug)
            );
            
            $this->save_cpt_config($slug, $args);
            
            // 刷新重写规则
            $this->flush_rewrite_rules();
            wp_redirect(add_query_arg('message', 'cpt_created', admin_url('admin.php?page=wuchaiwp-cpt-manager')));
            exit;
        }
        
        // 更新文章类型
        if (isset($_POST['submit_edit_cpt']) && check_admin_referer('wuchaiwp_edit_cpt', 'wuchaiwp_edit_nonce')) {
            $slug = sanitize_text_field($_POST['cpt_slug_edit']);
            $name = sanitize_text_field($_POST['cpt_name']);
            $description = sanitize_text_field($_POST['cpt_description']);
            $icon = sanitize_text_field($_POST['cpt_icon']);
            
            $supports = isset($_POST['cpt_supports']) ? array_map('sanitize_text_field', $_POST['cpt_supports']) : array('title', 'editor');
            $taxonomies = isset($_POST['cpt_taxonomies']) ? array_map('sanitize_text_field', $_POST['cpt_taxonomies']) : array();
            
            // 添加独立分类法
            $has_category = isset($_POST['cpt_has_category']);
            $has_tag = isset($_POST['cpt_has_tag']);
            
            if ($has_category) {
                $taxonomies[] = $slug . '_category';
            }
            if ($has_tag) {
                $taxonomies[] = $slug . '_tag';
            }
            
            $args = array(
                'labels' => array(
                    'name' => $name,
                    'singular_name' => $name,
                    'add_new' => '添加' . $name,
                    'add_new_item' => '添加新' . $name,
                    'edit_item' => '编辑' . $name,
                    'new_item' => '新' . $name,
                    'view_item' => '查看' . $name,
                    'search_items' => '搜索' . $name,
                    'not_found' => '未找到' . $name,
                    'not_found_in_trash' => '回收站中未找到' . $name,
                    'parent_item_colon' => '',
                    'menu_name' => $name
                ),
                'description' => $description,
                'public' => isset($_POST['cpt_public']),
                'has_archive' => isset($_POST['cpt_has_archive']),
                'show_ui' => isset($_POST['cpt_show_ui']),
                'show_in_menu' => isset($_POST['cpt_show_in_menu']),
                'menu_position' => !empty($_POST['cpt_menu_position']) ? intval($_POST['cpt_menu_position']) : null,
                'supports' => $supports,
                'taxonomies' => $taxonomies,
                'icon' => $icon,
                'has_category' => $has_category,
                'has_tag' => $has_tag,
                'publicly_queryable' => true,
                'exclude_from_search' => false,
                'show_in_rest' => true,
                'rewrite' => array('slug' => $slug)
            );
            
            $this->save_cpt_config($slug, $args);
            
            // 刷新重写规则
            $this->flush_rewrite_rules();
            wp_redirect(add_query_arg('message', 'cpt_updated', admin_url('admin.php?page=wuchaiwp-cpt-manager')));
            exit;
        }
        
        // 文章类型移到回收站
        if (isset($_POST['submit_trash_cpt']) && check_admin_referer('wuchaiwp_trash_cpt', 'wuchaiwp_trash_nonce')) {
            $slug = sanitize_text_field($_POST['cpt_slug_trash']);
            $this->move_cpt_to_trash($slug);
            wp_redirect(add_query_arg('message', 'cpt_trashed', admin_url('admin.php?page=wuchaiwp-cpt-manager')));
            exit;
        }
        
        // 文章类型恢复
        if (isset($_POST['submit_restore_cpt']) && check_admin_referer('wuchaiwp_restore_cpt', 'wuchaiwp_restore_nonce')) {
            $slug = sanitize_text_field($_POST['cpt_slug_restore']);
            $this->restore_cpt_from_trash($slug);
            wp_redirect(add_query_arg('message', 'cpt_restored', admin_url('admin.php?page=wuchaiwp-cpt-manager&tab=trash')));
            exit;
        }
        
        // 文章类型永久删除
        if (isset($_POST['submit_delete_cpt']) && check_admin_referer('wuchaiwp_delete_cpt', 'wuchaiwp_delete_nonce')) {
            $slug = sanitize_text_field($_POST['cpt_slug_delete']);
            $this->delete_cpt_permanently($slug);
            wp_redirect(add_query_arg('message', 'cpt_deleted', admin_url('admin.php?page=wuchaiwp-cpt-manager&tab=trash')));
            exit;
        }
        
        // 清空回收站
        if (isset($_POST['submit_empty_trash']) && check_admin_referer('wuchaiwp_empty_trash', 'wuchaiwp_empty_trash_nonce')) {
            $this->empty_trash();
            wp_redirect(add_query_arg('message', 'trash_emptied', admin_url('admin.php?page=wuchaiwp-cpt-manager&tab=trash')));
            exit;
        }
    }
    
    public function register_custom_post_types() {
        $cpts = get_option('wuchaiwp_custom_post_types', array());
        foreach ($cpts as $slug => $args) {
            register_post_type($slug, $args);
            
            // 注册独立分类法
            if (isset($args['has_category']) && $args['has_category']) {
                $this->register_taxonomy($slug, 'category');
            }
            if (isset($args['has_tag']) && $args['has_tag']) {
                $this->register_taxonomy($slug, 'tag');
            }
        }
    }
    
    private function register_taxonomy($cpt_slug, $type) {
        $cpts = get_option('wuchaiwp_custom_post_types', array());
        if (!isset($cpts[$cpt_slug])) {
            return;
        }
        
        $cpt_data = $cpts[$cpt_slug];
        $cpt_name = $cpt_data['labels']['name'];
        
        if ($type === 'category') {
            $tax_slug = $cpt_slug . '_category';
            $plural_name = $cpt_name . '分类';
            $singular_name = $cpt_name . '分类';
        } else {
            $tax_slug = $cpt_slug . '_tag';
            $plural_name = $cpt_name . '标签';
            $singular_name = $cpt_name . '标签';
        }
        
        $args = array(
            'hierarchical' => ($type === 'category'),
            'labels' => array(
                'name' => $plural_name,
                'singular_name' => $singular_name,
                'search_items' => '搜索' . $plural_name,
                'all_items' => '所有' . $plural_name,
                'parent_item' => '父' . $singular_name,
                'parent_item_colon' => '父' . $singular_name . ':',
                'edit_item' => '编辑' . $singular_name,
                'update_item' => '更新' . $singular_name,
                'add_new_item' => '添加新' . $singular_name,
                'new_item_name' => '新' . $singular_name . '名称',
                'menu_name' => $plural_name,
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'public' => true,
            'publicly_queryable' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => ($type === 'tag'),
            'query_var' => $tax_slug,
            'show_in_rest' => true,
            'rest_base' => $tax_slug,
            'rewrite' => array(
                'slug' => $type === 'category' ? $cpt_slug . '-category' : $cpt_slug . '-tag',
                'with_front' => false,
                'hierarchical' => ($type === 'category'),
            ),
        );
        
        register_taxonomy($tax_slug, $cpt_slug, $args);
    }
    
    private function save_cpt_config($slug, $args) {
        $cpts = get_option('wuchaiwp_custom_post_types', array());
        $cpts[$slug] = $args;
        update_option('wuchaiwp_custom_post_types', $cpts);
    }
    
    /**
     * 刷新重写规则
     */
    private function flush_rewrite_rules() {
        // 先注册所有自定义文章类型
        $this->register_custom_post_types();
        // 刷新重写规则
        flush_rewrite_rules(false);
    }
    
    private function move_cpt_to_trash($slug) {
        $cpts = get_option('wuchaiwp_custom_post_types', array());
        $trash_cpts = get_option('wuchaiwp_trash_cpts', array());
        
        if (isset($cpts[$slug])) {
            $trash_cpts[$slug] = $cpts[$slug];
            $trash_cpts[$slug]['deleted'] = date('Y-m-d H:i:s');
            unset($cpts[$slug]);
            
            update_option('wuchaiwp_custom_post_types', $cpts);
            update_option('wuchaiwp_trash_cpts', $trash_cpts);
        }
    }
    
    private function restore_cpt_from_trash($slug) {
        $cpts = get_option('wuchaiwp_custom_post_types', array());
        $trash_cpts = get_option('wuchaiwp_trash_cpts', array());
        
        if (isset($trash_cpts[$slug])) {
            $cpts[$slug] = $trash_cpts[$slug];
            unset($cpts[$slug]['deleted']);
            unset($trash_cpts[$slug]);
            
            update_option('wuchaiwp_custom_post_types', $cpts);
            update_option('wuchaiwp_trash_cpts', $trash_cpts);
        }
    }
    
    private function delete_cpt_permanently($slug) {
        $trash_cpts = get_option('wuchaiwp_trash_cpts', array());
        unset($trash_cpts[$slug]);
        update_option('wuchaiwp_trash_cpts', $trash_cpts);
    }
    
    private function empty_trash() {
        delete_option('wuchaiwp_trash_cpts');
    }
    
    private function get_custom_post_types() {
        return get_option('wuchaiwp_custom_post_types', array());
    }
    
    private function get_custom_taxonomies() {
        return get_option('wuchaiwp_custom_taxonomies', array());
    }
    
    private function get_trash_count() {
        $trash_cpts = get_option('wuchaiwp_trash_cpts', array());
        return count($trash_cpts);
    }
    
    private function get_trash_cpts() {
        return get_option('wuchaiwp_trash_cpts', array());
    }
    
    }

new Wuchaiwp_Custom_Post_Type_Manager();
?>
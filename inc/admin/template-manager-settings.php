<?php
/**
 * 模板管理器设置 - 集成到主题设置菜单
 * 支持回收站功能
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Template_Manager {
    
    // 常用图标列表（分类）
    private $icons = array(
        '文件文档' => array('📄', '📁', '📂', '📋', '📝', '📖', '📚', '📛', '📜', '📊'),
        '娱乐艺术' => array('🎯', '🎨', '🎭', '🎬', '🎵', '🎶', '🎹', '🎸', '🎤', '🎧'),
        '建筑场所' => array('🏠', '🏡', '🏢', '🏬', '🏭', '🏰', '🏞️', '🏜️', '🏠', '🏩'),
        '自然风景' => array('🌍', '🌎', '🌏', '🌴', '🌺', '🌸', '🌻', '🌷', '🍀', '🌿'),
        '食物饮品' => array('🍎', '🍊', '🍋', '🍇', '🍓', '🍒', '🍑', '🍕', '🍔', '🍟'),
        '交通工具' => array('🚀', '✈️', '🚗', '🚂', '🚢', '🚲', '🏍️', '🚁', '🚀', '🛸'),
        '工具物品' => array('⚙️', '🔧', '🔨', '🛠️', '🔌', '💡', '🔦', '🔮', '🕯️', '🔥'),
        '装饰符号' => array('💎', '👑', '🎁', '🎈', '🎉', '🎊', '⭐', '🌟', '✨', '💫')
    );
    
    // 回收站目录
    private $trash_dir = '/templates/trash/';
    private $edit_trash_dir = '/inc/admin/edit-templates/trash/';
    
    public function __construct() {
        add_action('admin_init', array($this, 'handle_form_submit'));
    }
    
    // 渲染管理页面
    public function render_admin_page() {
        // 获取当前标签页
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'templates';
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>📁 模板管理</h1>
                <p class="description">创建和管理详情页、归档页、编辑页模板</p>
            </div>
            
            <!-- 标签页导航 -->
            <h2 class="nav-tab-wrapper">
                <a href="?page=wuchaiwp-template-manager&tab=templates" class="nav-tab <?php echo $tab === 'templates' ? 'nav-tab-active' : ''; ?>">
                    📄 模板列表
                </a>
                <a href="?page=wuchaiwp-template-manager&tab=trash" class="nav-tab <?php echo $tab === 'trash' ? 'nav-tab-active' : ''; ?>">
                    🗑️ 回收站
                    <?php $trash_count = $this->get_trash_count(); ?>
                    <?php if ($trash_count > 0) : ?>
                        <span class="update-plugins count-1"><span class="plugin-count"><?php echo $trash_count; ?></span></span>
                    <?php endif; ?>
                </a>
            </h2>
            
            <?php if ($tab === 'templates') : ?>
                <!-- 创建新模板表单 -->
                <div class="wuchaiwp-form-section">
                    <h3>📄 创建新模板</h3>
                    <form method="post" action="" id="wuchai-template-form" class="wuchaiwp-form">
                        <?php wp_nonce_field('wuchaiwp_create_template', 'wuchaiwp_template_nonce'); ?>
                        
                        <div class="form-row">
                            <label>模板名称 <span style="color:red;">*</span></label>
                            <input type="text" id="template_name" name="template_name" required 
                                   placeholder="如：美食菜谱" class="regular-text">
                            <p class="help">模板的显示名称</p>
                        </div>
                        
                        <div class="form-row">
                            <label>模板标识 <span style="color:red;">*</span></label>
                            <input type="text" id="template_slug" name="template_slug" required 
                                   placeholder="如：food-recipe（小写字母和连字符）" class="regular-text">
                            <p class="help">用于生成文件名，只能包含小写字母、数字和连字符</p>
                        </div>
                        
                        <div class="form-row">
                            <label>图标 <span style="color:gray;">(点击选择)</span></label>
                            <div style="margin-bottom:10px;">
                                <span style="font-weight:600;">当前选中：</span>
                                <span id="selected-icon-preview" style="font-size:32px;">📄</span>
                                <input type="hidden" id="template_icon" name="template_icon" value="📄">
                            </div>
                            
                            <!-- 图标选择面板 -->
                            <div style="padding:15px;background:#f8f9fa;border-radius:6px;">
                                <?php foreach ($this->icons as $category => $icon_list) : ?>
                                    <div style="margin-bottom:12px;">
                                        <span style="font-size:14px;color:#666;font-weight:600;margin-bottom:6px;display:block;"><?php echo esc_html($category); ?></span>
                                        <div style="display:flex;flex-wrap:gap:5px;">
                                            <?php foreach ($icon_list as $icon) : ?>
                                                <button type="button" 
                                                        class="wuchaiwp-icon-btn"
                                                        data-icon="<?php echo esc_attr($icon); ?>"
                                                        style="width:36px;height:36px;border:2px solid #ddd;background:#fff;font-size:20px;cursor:pointer;border-radius:6px;transition:all 0.2s;margin:2px;"
                                                        onmouseover="this.style.borderColor='#3498db';this.style.background='#f0f8ff';"
                                                        onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='#ddd';this.style.background='#fff';}"
                                                        onclick="wuchaiwp_select_icon(this)">
                                                    <?php echo esc_html($icon); ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <label>创建文件</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="create_single" checked> 📄 详情页模板</label>
                                <label><input type="checkbox" name="create_archive" checked> 📋 归档页模板</label>
                                <label><input type="checkbox" name="create_edit" checked> ✏️ 编辑页模板</label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <?php submit_button('创建模板', 'primary', 'submit_create_template'); ?>
                        </div>
                    </form>
                </div>
                
                <!-- 已有模板列表 -->
                <div class="wuchaiwp-form-section">
                    <h3>📋 已有模板列表</h3>
                    <?php $templates = $this->get_existing_templates(); ?>
                    <?php if (empty($templates)) : ?>
                        <p class="empty-message">暂无自定义模板，点击上方创建第一个模板</p>
                    <?php else : ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>图标</th>
                                    <th>模板名称</th>
                                    <th>标识</th>
                                    <th>创建时间</th>
                                    <th>文件状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($templates as $template) : ?>
                                    <tr>
                                        <td><?php echo esc_html($template['icon']); ?></td>
                                        <td><?php echo esc_html($template['name']); ?></td>
                                        <td><code><?php echo esc_html($template['slug']); ?></code></td>
                                        <td><?php echo esc_html($template['created']); ?></td>
                                        <td>
                                            <?php if ($template['has_single']) : ?><span style="color:green">✓ 详情页</span><?php else : ?><span style="color:red">✗ 详情页</span><?php endif; ?>
                                            <?php if ($template['has_archive']) : ?><span style="color:green">✓ 归档页</span><?php else : ?><span style="color:red">✗ 归档页</span><?php endif; ?>
                                            <?php if ($template['has_edit']) : ?><span style="color:green">✓ 编辑页</span><?php else : ?><span style="color:red">✗ 编辑页</span><?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="post" action="" style="display:inline;">
                                                <?php wp_nonce_field('wuchaiwp_trash_template', 'wuchaiwp_trash_nonce'); ?>
                                                <input type="hidden" name="template_slug_trash" value="<?php echo esc_attr($template['slug']); ?>">
                                                <button type="submit" name="submit_trash_template" class="button button-link delete" onclick="return confirm('确定要将此模板移到回收站吗？')">移到回收站</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- 使用说明 -->
                <div class="wuchaiwp-form-section info-box">
                    <h3>💡 使用说明</h3>
                    <div class="info-grid">
                        <div>
                            <h4>🔧 第一步：创建模板</h4>
                            <ol>
                                <li>输入模板名称（如：美食菜谱）</li>
                                <li>设置模板标识（小写字母+连字符）</li>
                                <li>点击图标选择（默认📄）</li>
                                <li>勾选需要创建的文件类型</li>
                                <li>点击「创建模板」按钮</li>
                            </ol>
                        </div>
                        <div>
                            <h4>📝 第二步：应用模板</h4>
                            <ol>
                                <li>进入「模板选择」设置</li>
                                <li>选择文章类型</li>
                                <li>在对应模板下拉框中选择新建的模板</li>
                                <li>保存设置</li>
                            </ol>
                        </div>
                        <div>
                            <h4>🎯 第三步：自定义模板</h4>
                            <ol>
                                <li>在 <code>templates/single/</code> 或 <code>templates/archive/</code> 中修改模板文件</li>
                                <li>编辑页模板位于 <code>inc/admin/edit-templates/</code></li>
                                <li>刷新页面即可看到效果</li>
                            </ol>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <!-- 回收站页面 -->
                <div class="wuchaiwp-form-section">
                    <h3>🗑️ 回收站</h3>
                    <p class="description">删除的模板会暂时存放在这里，可以恢复或永久删除</p>
                    
                    <?php $trash_templates = $this->get_trash_templates(); ?>
                    <?php if (empty($trash_templates)) : ?>
                        <div class="notice notice-info">
                            <p>回收站是空的</p>
                        </div>
                    <?php else : ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>图标</th>
                                    <th>模板名称</th>
                                    <th>标识</th>
                                    <th>删除时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trash_templates as $template) : ?>
                                    <tr>
                                        <td><?php echo esc_html($template['icon']); ?></td>
                                        <td><?php echo esc_html($template['name']); ?></td>
                                        <td><code><?php echo esc_html($template['slug']); ?></code></td>
                                        <td><?php echo esc_html($template['deleted']); ?></td>
                                        <td>
                                            <!-- 恢复按钮 -->
                                            <form method="post" action="" style="display:inline;">
                                                <?php wp_nonce_field('wuchaiwp_restore_template', 'wuchaiwp_restore_nonce'); ?>
                                                <input type="hidden" name="template_slug_restore" value="<?php echo esc_attr($template['slug']); ?>">
                                                <button type="submit" name="submit_restore_template" class="button button-link" style="color:#27ae60;">恢复</button>
                                            </form>
                                            |
                                            <!-- 永久删除按钮 -->
                                            <form method="post" action="" style="display:inline;">
                                                <?php wp_nonce_field('wuchaiwp_delete_template', 'wuchaiwp_delete_nonce'); ?>
                                                <input type="hidden" name="template_slug_delete" value="<?php echo esc_attr($template['slug']); ?>">
                                                <button type="submit" name="submit_delete_template" class="button button-link delete" onclick="return confirm('确定要永久删除此模板吗？此操作不可撤销！')">永久删除</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- 清空回收站按钮 -->
                        <div style="margin-top:20px;">
                            <form method="post" action="">
                                <?php wp_nonce_field('wuchaiwp_empty_trash', 'wuchaiwp_empty_trash_nonce'); ?>
                                <button type="submit" name="submit_empty_trash" class="button button-secondary" onclick="return confirm('确定要清空回收站吗？所有模板将被永久删除！')">清空回收站</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        // 图标选择函数
        function wuchaiwp_select_icon(btn) {
            var allBtns = document.querySelectorAll('.wuchaiwp-icon-btn');
            allBtns.forEach(function(b) {
                b.classList.remove('selected');
                b.style.borderColor = '#ddd';
                b.style.background = '#fff';
            });
            
            btn.classList.add('selected');
            btn.style.borderColor = '#27ae60';
            btn.style.background = '#e8f5e9';
            
            var icon = btn.getAttribute('data-icon');
            document.getElementById('selected-icon-preview').textContent = icon;
            document.getElementById('template_icon').value = icon;
        }
        </script>
        <?php
    }
    
    // 处理表单提交
    public function handle_form_submit() {
        // 创建模板
        if (isset($_POST['submit_create_template']) && check_admin_referer('wuchaiwp_create_template', 'wuchaiwp_template_nonce')) {
            $name = sanitize_text_field($_POST['template_name']);
            $slug = sanitize_text_field($_POST['template_slug']);
            $icon = sanitize_text_field($_POST['template_icon']);
            
            if (empty($icon)) {
                $icon = '📄';
            }
            
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                wp_die('模板标识只能包含小写字母、数字和连字符');
            }
            
            $created = false;
            
            if (isset($_POST['create_single'])) {
                $this->create_single_template($slug, $name, $icon);
                $created = true;
            }
            
            if (isset($_POST['create_archive'])) {
                $this->create_archive_template($slug, $name, $icon);
                $created = true;
            }
            
            if (isset($_POST['create_edit'])) {
                $this->create_edit_template($slug, $name, $icon);
                $created = true;
            }
            
            if ($created) {
                $this->update_template_config($slug, $name, $icon);
                
                wp_redirect(add_query_arg('message', 'template_created', admin_url('admin.php?page=wuchaiwp-template-manager')));
                exit;
            }
        }
        
        // 移到回收站
        if (isset($_POST['submit_trash_template']) && check_admin_referer('wuchaiwp_trash_template', 'wuchaiwp_trash_nonce')) {
            $slug = sanitize_text_field($_POST['template_slug_trash']);
            $this->move_to_trash($slug);
            wp_redirect(add_query_arg('message', 'template_trashed', admin_url('admin.php?page=wuchaiwp-template-manager')));
            exit;
        }
        
        // 恢复模板
        if (isset($_POST['submit_restore_template']) && check_admin_referer('wuchaiwp_restore_template', 'wuchaiwp_restore_nonce')) {
            $slug = sanitize_text_field($_POST['template_slug_restore']);
            $this->restore_from_trash($slug);
            wp_redirect(add_query_arg('message', 'template_restored', admin_url('admin.php?page=wuchaiwp-template-manager&tab=trash')));
            exit;
        }
        
        // 永久删除
        if (isset($_POST['submit_delete_template']) && check_admin_referer('wuchaiwp_delete_template', 'wuchaiwp_delete_nonce')) {
            $slug = sanitize_text_field($_POST['template_slug_delete']);
            $this->delete_permanently($slug);
            wp_redirect(add_query_arg('message', 'template_deleted', admin_url('admin.php?page=wuchaiwp-template-manager&tab=trash')));
            exit;
        }
        
        // 清空回收站
        if (isset($_POST['submit_empty_trash']) && check_admin_referer('wuchaiwp_empty_trash', 'wuchaiwp_empty_trash_nonce')) {
            $this->empty_trash();
            wp_redirect(add_query_arg('message', 'trash_emptied', admin_url('admin.php?page=wuchaiwp-template-manager&tab=trash')));
            exit;
        }
    }
    
    // 创建详情页模板
    private function create_single_template($slug, $name, $icon) {
        $template_dir = get_template_directory() . '/templates/single/';
        if (!file_exists($template_dir)) {
            mkdir($template_dir, 0755, true);
        }
        
        $content = $this->get_single_template_content($slug, $name, $icon);
        file_put_contents($template_dir . 'single-' . $slug . '.php', $content);
    }
    
    // 创建归档页模板
    private function create_archive_template($slug, $name, $icon) {
        $template_dir = get_template_directory() . '/templates/archive/';
        if (!file_exists($template_dir)) {
            mkdir($template_dir, 0755, true);
        }
        
        $content = $this->get_archive_template_content($slug, $name, $icon);
        file_put_contents($template_dir . 'archive-' . $slug . '.php', $content);
    }
    
    // 创建编辑页模板
    private function create_edit_template($slug, $name, $icon) {
        $template_dir = get_template_directory() . '/inc/admin/edit-templates/';
        if (!file_exists($template_dir)) {
            mkdir($template_dir, 0755, true);
        }
        
        $content = $this->get_edit_template_content($slug, $name, $icon);
        file_put_contents($template_dir . 'edit-' . $slug . '.php', $content);
    }
    
    // 获取详情页模板内容
    private function get_single_template_content($slug, $name, $icon) {
        return <<<PHP
<?php
/**
 * Template Name: {$name}详情页
 * Description: {$icon} {$name}类内容的详情页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="entry-content-wrapper">
                <div class="container">
                    <!-- 面包屑导航 -->
                    <div class="breadcrumb">
                        <a href="<?php echo home_url(); ?>">🏠 首页</a>
                        <span class="separator">›</span>
                        <a href="<?php echo get_post_type_archive_link('{$slug}'); ?>">{$icon} {$name}</a>
                        <span class="separator">›</span>
                        <span class="current"><?php the_title(); ?></span>
                    </div>

                    <!-- 标题区域 -->
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-meta">
                            <span>👤 <?php the_author(); ?></span>
                            <span>📅 <?php the_date('Y年m月d日'); ?></span>
                            <span>👁️<?php echo get_post_meta(get_the_ID(), 'wuchaiwp_views', true) ?: '0'; ?></span>
                        </div>
                    </header>

                    <!-- 特色图片 -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- 文章内容 -->
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- 标签 -->
                    <?php if (has_tag()) : ?>
                        <div class="tags-section">
                            <?php the_tags('🏷️ ', ', ', ''); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </article>

    <?php endwhile; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<style>
.breadcrumb { margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 6px; }
.breadcrumb a { color: #3498db; text-decoration: none; }
.separator { margin: 0 8px; color: #999; }
.entry-header { margin-bottom: 25px; }
.entry-title { font-size: 28px; margin: 0 0 10px 0; }
.entry-meta { display: flex; gap: 15px; color: #666; font-size: 14px; }
.featured-image { margin-bottom: 25px; }
.featured-image img { width: 100%; height: auto; border-radius: 8px; }
.entry-content { line-height: 1.8; margin-bottom: 30px; }
.tags-section { margin-bottom: 25px; }
</style>

<?php get_footer(); ?>
PHP;
    }
    
    // 获取归档页模板内容
    private function get_archive_template_content($slug, $name, $icon) {
        return <<<PHP
<?php
/**
 * Template Name: {$name}归档页
 * Description: {$icon} {$name}类内容的归档页模板
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="archive-header">
            <h1 class="archive-title">{$icon} {$name}</h1>
            <p class="archive-description">精选{$name}内容，持续更新中</p>
        </header>

        <div class="posts-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('class' => 'post-image')); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <p class="post-excerpt"><?php the_excerpt(); ?></p>
                            <div class="post-meta">
                                <span>📅 <?php the_date(); ?></span>
                                <span>👁️<?php echo get_post_meta(get_the_ID(), 'wuchaiwp_views', true) ?: '0'; ?></span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php the_posts_navigation(); ?>
            <?php else : ?>
                <div class="no-posts">
                    <p>暂无{$name}内容</p>
                </div>
            <?php endif; ?>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<style>
.archive-header { margin-bottom: 30px; text-align: center; }
.archive-title { font-size: 32px; margin: 0 0 10px 0; }
.archive-description { font-size: 16px; color: #666; }
.posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
.post-card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.post-thumbnail { overflow: hidden; }
.post-image { width: 100%; height: 180px; object-fit: cover; }
.post-content { padding: 16px; }
.post-title { font-size: 18px; margin: 0 0 10px 0; }
.post-title a { color: #333; text-decoration: none; }
.post-excerpt { font-size: 14px; color: #666; line-height: 1.5; margin: 0 0 12px 0; }
.post-meta { display: flex; gap: 12px; font-size: 13px; color: #999; }
.no-posts { text-align: center; padding: 60px; color: #666; }
@media (max-width: 768px) { .posts-grid { grid-template-columns: 1fr; } }
</style>

<?php get_footer(); ?>
PHP;
    }
    
    // 获取编辑页模板内容
    private function get_edit_template_content($slug, $name, $icon) {
        $func_prefix = str_replace('-', '_', $slug);
        return <<<PHP
<?php
/**
 * {$name}编辑页模板
 */

// {$name}信息字段
function wuchaiwp_{$func_prefix}_info_field(\$post) {
    wp_nonce_field('wuchaiwp_{$func_prefix}_save', 'wuchaiwp_{$func_prefix}_nonce');
    
    // 自定义字段示例
    \$custom_field = get_post_meta(\$post->ID, 'wuchaiwp_{$slug}_custom', true);
    
    echo '<div style="padding:8px;">';
    echo '<div style="margin-bottom:15px;">';
    echo '<label style="display:block;margin-bottom:4px;font-weight:600;">📝 自定义字段</label>';
    echo '<input type="text" name="wuchaiwp_{$slug}_custom" value="' . esc_attr(\$custom_field) . '" placeholder="输入自定义内容..." style="width:100%;padding:6px;border:1px solid #ddd;border-radius:4px;">';
    echo '</div>';
    echo '</div>';
}

// 注册字段
function wuchaiwp_register_{$func_prefix}_fields(\$post_type) {
    add_action('add_meta_boxes_' . \$post_type, function() use (\$post_type) {
        add_meta_box(
            'wuchaiwp_{$slug}_info',
            '{$icon} {$name}信息',
            'wuchaiwp_{$func_prefix}_info_field',
            \$post_type,
            'side',
            'default'
        );
    });
}

// 保存字段
function wuchaiwp_save_{$func_prefix}_fields(\$post_id) {
    if (!isset(\$_POST['wuchaiwp_{$func_prefix}_nonce']) || !wp_verify_nonce(\$_POST['wuchaiwp_{$func_prefix}_nonce'], 'wuchaiwp_{$func_prefix}_save')) return;
    if (!current_user_can('edit_post', \$post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    if (isset(\$_POST['wuchaiwp_{$slug}_custom'])) {
        update_post_meta(\$post_id, 'wuchaiwp_{$slug}_custom', sanitize_text_field(\$_POST['wuchaiwp_{$slug}_custom']));
    }
}

add_action('save_post', 'wuchaiwp_save_{$func_prefix}_fields');
?>
PHP;
    }
    
    // 更新模板配置
    private function update_template_config($slug, $name, $icon) {
        $templates = get_option('wuchaiwp_custom_templates', array());
        $templates[$slug] = array(
            'name' => $name,
            'slug' => $slug,
            'icon' => $icon,
            'created' => date('Y-m-d H:i:s')
        );
        update_option('wuchaiwp_custom_templates', $templates);
    }
    
    // 移到回收站
    private function move_to_trash($slug) {
        // 创建回收站目录
        $single_trash_dir = get_template_directory() . $this->trash_dir . 'single/';
        $archive_trash_dir = get_template_directory() . $this->trash_dir . 'archive/';
        $edit_trash_dir = get_template_directory() . $this->edit_trash_dir;
        
        if (!file_exists($single_trash_dir)) mkdir($single_trash_dir, 0755, true);
        if (!file_exists($archive_trash_dir)) mkdir($archive_trash_dir, 0755, true);
        if (!file_exists($edit_trash_dir)) mkdir($edit_trash_dir, 0755, true);
        
        // 移动模板文件
        $single_file = get_template_directory() . '/templates/single/single-' . $slug . '.php';
        $archive_file = get_template_directory() . '/templates/archive/archive-' . $slug . '.php';
        $edit_file = get_template_directory() . '/inc/admin/edit-templates/edit-' . $slug . '.php';
        
        if (file_exists($single_file)) rename($single_file, $single_trash_dir . 'single-' . $slug . '.php');
        if (file_exists($archive_file)) rename($archive_file, $archive_trash_dir . 'archive-' . $slug . '.php');
        if (file_exists($edit_file)) rename($edit_file, $edit_trash_dir . 'edit-' . $slug . '.php');
        
        // 更新配置（移到回收站）
        $templates = get_option('wuchaiwp_custom_templates', array());
        $trash_templates = get_option('wuchaiwp_trash_templates', array());
        
        if (isset($templates[$slug])) {
            $trash_templates[$slug] = $templates[$slug];
            $trash_templates[$slug]['deleted'] = date('Y-m-d H:i:s');
            unset($templates[$slug]);
            
            update_option('wuchaiwp_custom_templates', $templates);
            update_option('wuchaiwp_trash_templates', $trash_templates);
        }
    }
    
    // 从回收站恢复
    private function restore_from_trash($slug) {
        // 恢复模板文件
        $single_trash_dir = get_template_directory() . $this->trash_dir . 'single/';
        $archive_trash_dir = get_template_directory() . $this->trash_dir . 'archive/';
        $edit_trash_dir = get_template_directory() . $this->edit_trash_dir;
        
        $single_file = $single_trash_dir . 'single-' . $slug . '.php';
        $archive_file = $archive_trash_dir . 'archive-' . $slug . '.php';
        $edit_file = $edit_trash_dir . 'edit-' . $slug . '.php';
        
        if (file_exists($single_file)) rename($single_file, get_template_directory() . '/templates/single/single-' . $slug . '.php');
        if (file_exists($archive_file)) rename($archive_file, get_template_directory() . '/templates/archive/archive-' . $slug . '.php');
        if (file_exists($edit_file)) rename($edit_file, get_template_directory() . '/inc/admin/edit-templates/edit-' . $slug . '.php');
        
        // 更新配置（恢复）
        $templates = get_option('wuchaiwp_custom_templates', array());
        $trash_templates = get_option('wuchaiwp_trash_templates', array());
        
        if (isset($trash_templates[$slug])) {
            $templates[$slug] = $trash_templates[$slug];
            unset($templates[$slug]['deleted']);
            unset($trash_templates[$slug]);
            
            update_option('wuchaiwp_custom_templates', $templates);
            update_option('wuchaiwp_trash_templates', $trash_templates);
        }
    }
    
    // 永久删除
    private function delete_permanently($slug) {
        // 删除回收站中的文件
        $single_trash_dir = get_template_directory() . $this->trash_dir . 'single/';
        $archive_trash_dir = get_template_directory() . $this->trash_dir . 'archive/';
        $edit_trash_dir = get_template_directory() . $this->edit_trash_dir;
        
        @unlink($single_trash_dir . 'single-' . $slug . '.php');
        @unlink($archive_trash_dir . 'archive-' . $slug . '.php');
        @unlink($edit_trash_dir . 'edit-' . $slug . '.php');
        
        // 从回收站配置中移除
        $trash_templates = get_option('wuchaiwp_trash_templates', array());
        unset($trash_templates[$slug]);
        update_option('wuchaiwp_trash_templates', $trash_templates);
    }
    
    // 清空回收站
    private function empty_trash() {
        $single_trash_dir = get_template_directory() . $this->trash_dir . 'single/';
        $archive_trash_dir = get_template_directory() . $this->trash_dir . 'archive/';
        $edit_trash_dir = get_template_directory() . $this->edit_trash_dir;
        
        // 删除所有回收站文件
        if (file_exists($single_trash_dir)) {
            $files = glob($single_trash_dir . '*.php');
            foreach ($files as $file) @unlink($file);
        }
        if (file_exists($archive_trash_dir)) {
            $files = glob($archive_trash_dir . '*.php');
            foreach ($files as $file) @unlink($file);
        }
        if (file_exists($edit_trash_dir)) {
            $files = glob($edit_trash_dir . '*.php');
            foreach ($files as $file) @unlink($file);
        }
        
        // 清空回收站配置
        delete_option('wuchaiwp_trash_templates');
    }
    
    // 获取回收站模板数量
    private function get_trash_count() {
        $trash_templates = get_option('wuchaiwp_trash_templates', array());
        return count($trash_templates);
    }
    
    // 获取回收站模板列表
    private function get_trash_templates() {
        return get_option('wuchaiwp_trash_templates', array());
    }
    
    // 获取已有模板列表
    private function get_existing_templates() {
        $templates = get_option('wuchaiwp_custom_templates', array());
        $result = array();
        
        foreach ($templates as $slug => $data) {
            $result[] = array(
                'slug' => $slug,
                'name' => $data['name'],
                'icon' => $data['icon'],
                'created' => $data['created'],
                'has_single' => file_exists(get_template_directory() . '/templates/single/single-' . $slug . '.php'),
                'has_archive' => file_exists(get_template_directory() . '/templates/archive/archive-' . $slug . '.php'),
                'has_edit' => file_exists(get_template_directory() . '/inc/admin/edit-templates/edit-' . $slug . '.php')
            );
        }
        
        return $result;
    }
}

// 初始化管理器
new Wuchaiwp_Template_Manager();
?>
<?php
/**
 * 企业官网管理设置
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Enterprise_Manager_Settings {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
    }

    public function render_enterprise_manager_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'sections';
        
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>🏢 企业官网管理</h1>
                <p class="description">管理企业官网归档页各区域的内容和设置</p>
            </div>
            
            <!-- 标签页导航 -->
            <h2 class="nav-tab-wrapper">
                <a href="?page=wuchaiwp-enterprise-manager&tab=sections" class="nav-tab <?php echo $active_tab === 'sections' ? 'nav-tab-active' : ''; ?>">
                    📋 区域管理
                </a>
                
                <a href="?page=wuchaiwp-enterprise-manager&tab=hero" class="nav-tab <?php echo $active_tab === 'hero' ? 'nav-tab-active' : ''; ?>">
                    🚀 Hero区域
                </a>
                
                <?php 
                // 动态生成区域设置菜单 - 显示用户设置的区域名称
                $sections = $this->get_sections();
                $special_tabs = array('sections', 'hero', 'categories', 'titles', 'trash'); // 特殊标签
                $displayed_ids = array(); // 用于去重
                
                foreach ($sections as $section) {
                    // 跳过特殊标签和状态不为active的区域
                    if (in_array($section['id'], $special_tabs) || $section['status'] !== 'active') {
                        continue;
                    }
                    
                    // 跳过重复的区域ID
                    if (in_array($section['id'], $displayed_ids)) {
                        continue;
                    }
                    
                    // 使用用户设置的区域名称（而不是区域类型）
                    $icon = !empty($section['icon']) ? $section['icon'] : '📄';
                    $name = !empty($section['name']) ? $section['name'] : '未命名区域';
                    $label = $icon . ' ' . $name;
                    
                    echo '<a href="?page=wuchaiwp-enterprise-manager&tab=' . esc_attr($section['id']) . '" class="nav-tab ' . ($active_tab === $section['id'] ? 'nav-tab-active' : '') . '" title="类型: ' . ($section['type'] ?? 'unknown') . '">';
                    echo $label;
                    echo '</a>';
                    
                    // 记录已显示的区域ID
                    $displayed_ids[] = $section['id'];
                }
                ?>
                
                <a href="?page=wuchaiwp-enterprise-manager&tab=announcement" class="nav-tab <?php echo $active_tab === 'announcement' ? 'nav-tab-active' : ''; ?>">
                    📢 最新公告
                </a>
                <a href="?page=wuchaiwp-enterprise-manager&tab=categories" class="nav-tab <?php echo $active_tab === 'categories' ? 'nav-tab-active' : ''; ?>">
                    📁 分类管理
                </a>
                <a href="?page=wuchaiwp-enterprise-manager&tab=titles" class="nav-tab <?php echo $active_tab === 'titles' ? 'nav-tab-active' : ''; ?>">
                    📝 归档页标题
                </a>
                <a href="?page=wuchaiwp-enterprise-manager&tab=trash" class="nav-tab <?php echo $active_tab === 'trash' ? 'nav-tab-active' : ''; ?>">
                    🗑️ 回收站
                </a>
            </h2>
            
            <?php
            switch ($active_tab) {
                case 'sections':
                    $this->render_sections_management();
                    break;
                    
                case 'hero':
                    $this->render_hero_section_settings();
                    break;    
                    
                case 'products':
                    $this->render_products_section_settings();
                    break;
                case 'news':
                    $this->render_section('news', '开发日志', '选择要显示在开发日志区域的内容');
                    break;
                case 'news_dynamic':
                    $this->render_section('news_dynamic', '新闻动态', '选择要显示在新闻动态区域的内容');
                    break;
                case 'announcement':
                    $this->render_section('announcement', '最新公告', '选择要显示在公告区域的内容（最多1条）');
                    break;
                case 'cases':
                    $this->render_section('cases', '案例参考', '选择要显示在案例参考区域的内容');
                    break;
                case 'about':
                    $this->render_about_section();
                    break;
                case 'contact':
                    $this->render_contact_section();
                    break;
                case 'team':
                    $this->render_section('team', '团队介绍', '选择要显示在团队介绍区域的内容');
                    break;
                case 'partners':
                    $this->render_section('partners', '合作伙伴', '选择要显示在合作伙伴区域的内容');
                    break;
                case 'services':
                    $this->render_section('services', '服务项目', '选择要显示在服务项目区域的内容');
                    break;
                case 'faq':
                    $this->render_section('faq', '常见问题', '选择要显示在常见问题区域的内容');
                    break;
                case 'banner':
                    $this->render_section('banner', '横幅广告', '选择要显示在横幅广告区域的内容');
                    break;
                case 'stats':
                    $this->render_section('stats', '数据统计', '选择要显示在数据统计区域的内容');
                    break;
                case 'custom':
                    $this->render_section('custom', '自定义内容', '自定义内容区域设置');
                    break;
                case 'categories':
                    $this->render_categories_section();
                    break;
                case 'titles':
                    $this->render_titles_section();
                    break;
                case 'trash':
                    $this->render_trash_section();
                    break;
                default:
                    // 处理自定义添加的区域
                    $this->render_custom_section_settings($active_tab);
                    break;
            }
            ?>
        </div>
        <?php
    }
    
    // 区域管理页面
    private function render_sections_management() {
        $sections = $this->get_sections();
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>📋 区域管理</h3>
            <p class="description">管理企业官网首页的各个区域，可以添加、排序、隐藏或删除区域</p>
            
            <!-- 操作按钮 -->
            <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                <button class="button button-primary" onclick="showAddSectionModal()">+ 添加新区域</button>
                <button class="button button-secondary" onclick="restoreDefaultSections()">🔄 恢复默认区域</button>
            </div>
            
            <!-- 区域列表 -->
            <div class="sections-list">
                <?php if (empty($sections)) : ?>
                    <div class="empty-state">
                        <p>暂无区域，请添加新区域</p>
                    </div>
                <?php else : ?>
                    <div id="sections-sortable" class="sections-sortable">
                        <?php foreach ($sections as $section) : ?>
                            <div class="section-item" data-section-id="<?php echo $section['id']; ?>">
                                <div class="section-handle">☰</div>
                                <div class="section-info">
                                    <span class="section-icon"><?php echo $section['icon']; ?></span>
                                    <span class="section-name"><?php echo $section['name']; ?></span>
                                    <span class="section-type"><?php echo $section['type']; ?></span>
                                </div>
                                <div class="section-status">
                                    <?php echo $section['status'] === 'active' ? '<span class="status-active">显示</span>' : '<span class="status-hidden">隐藏</span>'; ?>
                                </div>
                                <div class="section-actions">
                                    <button class="action-btn edit-btn" onclick="editSection('<?php echo $section['id']; ?>')">编辑</button>
                                    <button class="action-btn toggle-btn" onclick="toggleSection('<?php echo $section['id']; ?>')">
                                        <?php echo $section['status'] === 'active' ? '隐藏' : '显示'; ?>
                                    </button>
                                    <button class="action-btn delete-btn" onclick="moveToTrash('<?php echo $section['id']; ?>')">删除</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 保存顺序按钮 -->
            <?php if (!empty($sections)) : ?>
            <div style="margin-top: 20px;">
                <button class="button button-primary" onclick="saveSectionOrder()">💾 保存排序</button>
            </div>
            <?php endif; ?>
            
            <p class="description" style="font-size: 12px; color: #666; margin-top: 10px;">
                <strong>注意：</strong>点击"恢复默认区域"将重置所有区域设置为初始状态，此操作不可撤销。
            </p>
        </div>
        
        <!-- 添加/编辑区域弹窗 -->
        <div id="section-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h3 id="modal-title">添加新区域</h3>
                <form id="section-form" method="post">
                    <?php wp_nonce_field('wuchaiwp_enterprise_section', 'wuchaiwp_section_nonce'); ?>
                    <input type="hidden" name="section_id" id="section_id" value="">
                    
                    <div class="form-row">
                        <label>区域名称</label>
                        <input type="text" name="section_name" id="section_name" required>
                    </div>
                    
                    <div class="form-row">
                        <label>区域图标</label>
                        <select name="section_icon" id="section_icon">
                            <option value="📦">📦 箱子</option>
                            <option value="📝">📝 文件</option>
                            <option value="📢">📢 公告</option>
                            <option value="💼">💼 公文包</option>
                            <option value="🏢">🏢 建筑</option>
                            <option value="📞">📞 电话</option>
                            <option value="📁">📁 文件夹</option>
                            <option value="🌟">🌟 星星</option>
                            <option value="🎯">🎯 目标</option>
                            <option value="💡">💡 灯泡</option>
                            <option value="📊">📊 图表</option>
                            <option value="👥">👥 用户</option>
                            <option value="🌐">🌐 地球</option>
                            <option value="⚡">⚡ 闪电</option>
                            <option value="🎨">🎨 调色板</option>
                            <option value="🚀">🚀 火箭</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label>区域类型</label>
                        <select name="section_type" id="section_type">
                            <option value="products">📦 产品介绍</option>
                            <option value="news">📝 开发日志</option>
                            <option value="cases">💼 案例参考</option>
                            <option value="about">🏢 企业简介</option>
                            <option value="news_dynamic">📰 新闻动态</option>
                            <option value="contact">📞 联系表单</option>
                            <option value="custom">📝 自定义HTML</option>
                            <option value="banner">🚩 横幅广告</option>
                            <option value="stats">📊 数据统计</option>
                            <option value="team">👥 团队介绍</option>
                            <option value="partners">🤝 合作伙伴</option>
                            <option value="services">⚙️ 服务项目</option>
                            <option value="faq">❓ 常见问题</option>
                        </select>
                    </div>
                    
                    <div class="form-row" id="custom_html_container" style="display: none;">
                        <label>自定义内容（HTML）</label>
                        <textarea name="section_content" id="section_content" rows="6"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="button button-secondary" onclick="closeModal()">取消</button>
                        <button type="submit" class="button button-primary">保存</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- 使用说明区域 -->
<div class="custom-section">
    <div class="custom-content">
        <h3>使用说明</h3>
        <p><strong>企业归档页地址：</strong></p>
         <ul>
            <li>网址/自定义文章类型标识（选择企业系列模板后）。</li>
        </ul>
        <p><strong>静态页面调用方法：</strong></p>
        <ul>
            <li>在后台页面功能中新建页面，标题根据需要写，链接后缀改成自定义文章类型标识（选择企业系列模板后），网站首页若要显示这个页面，在自定义模式中选择这个静态页面即可。</li>
        </ul>
        <p>更多使用方式，请参考主题文档或联系管理员。</p>
    </div>
</div>
        
        <style>
        /* 导航菜单自适应样式 */
        .nav-tab-wrapper {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 8px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
        }
        
        .nav-tab-wrapper::-webkit-scrollbar {
            height: 6px;
        }
        
        .nav-tab-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .nav-tab-wrapper::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        .nav-tab-wrapper::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
        
        .nav-tab {
            flex-shrink: 0;
            margin-bottom: 0 !important;
            margin-right: 4px;
        }
        
        .sections-list {
            margin-top: 20px;
        }
        
        .sections-sortable {
            border: 1px solid #ddd;
            border-radius: 6px;
            min-height: 100px;
        }
        
        .section-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            background: #fff;
            transition: all 0.2s ease;
        }
        
        .section-item:last-child {
            border-bottom: none;
        }
        
        .section-item:hover {
            background: #f9f9f9;
        }
        
        .section-item.ui-sortable-helper {
            background: #fff;
            border: 1px solid #007cba;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: scale(1.02);
        }
        
        .section-item.section-changed {
            background: #fff3cd;
            border-color: #ffeeba;
        }
        
        .section-placeholder {
            border: 2px dashed #007cba;
            border-radius: 6px;
            margin: 5px;
            height: 40px;
            background: rgba(0, 124, 186, 0.05);
        }
        
        .section-handle {
            margin-right: 10px;
            color: #999;
            cursor: move;
            padding: 5px;
            font-size: 16px;
        }
        
        .section-handle:hover {
            color: #007cba;
        }
        
        .section-info {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-icon {
            font-size: 18px;
        }
        
        .section-name {
            font-weight: 600;
        }
        
        .section-type {
            font-size: 12px;
            color: #666;
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
        }
        
        .section-status {
            margin-right: 15px;
        }
        
        .status-active {
            color: #46b450;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-hidden {
            color: #dc3232;
            font-size: 12px;
            font-weight: 500;
        }
        
        .section-actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 4px 10px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .edit-btn {
            background: #007cba;
            color: white;
        }
        
        .toggle-btn {
            background: #f56e28;
            color: white;
        }
        
        .delete-btn {
            background: #dc3232;
            color: white;
        }
        
        /* 弹窗样式 */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 500px;
            border-radius: 8px;
            position: relative;
        }
        
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: black;
        }
        
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #999;
        }
        </style>
        
        <script>
        // 显示添加区域弹窗
        function showAddSectionModal() {
            document.getElementById('modal-title').textContent = '添加新区域';
            document.getElementById('section_id').value = '';
            document.getElementById('section_name').value = '';
            document.getElementById('section_icon').value = '📦';
            document.getElementById('section_type').value = 'products';
            document.getElementById('custom_html_container').style.display = 'none';
            document.getElementById('section-modal').style.display = 'block';
        }
        
        // 编辑区域
        function editSection(sectionId) {
            // 通过AJAX获取区域信息
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    document.getElementById('modal-title').textContent = '编辑区域';
                    document.getElementById('section_id').value = data.id;
                    document.getElementById('section_name').value = data.name;
                    document.getElementById('section_icon').value = data.icon;
                    document.getElementById('section_type').value = data.type;
                    document.getElementById('section_content').value = data.content || '';
                    
                    // 显示/隐藏自定义内容区域
                    if (data.type === 'custom') {
                        document.getElementById('custom_html_container').style.display = 'block';
                    } else {
                        document.getElementById('custom_html_container').style.display = 'none';
                    }
                    
                    document.getElementById('section-modal').style.display = 'block';
                }
            };
            xhr.send('action=wuchaiwp_get_section&section_id=' + sectionId);
        }
        
        // 关闭弹窗
        function closeModal() {
            document.getElementById('section-modal').style.display = 'none';
        }
        
        // 切换区域状态
        function toggleSection(sectionId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send('action=wuchaiwp_toggle_section&section_id=' + sectionId);
        }
        
        // 移动到回收站
        function moveToTrash(sectionId) {
            if (confirm('确定要将此区域移到回收站吗？')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send('action=wuchaiwp_move_to_trash&section_id=' + sectionId);
            }
        }
        
        // 初始化拖拽排序
        jQuery(document).ready(function($) {
            $('#sections-sortable').sortable({
                handle: '.section-handle',
                cursor: 'move',
                opacity: 0.7,
                placeholder: 'section-placeholder',
                update: function(event, ui) {
                    // 排序改变后高亮提示
                    ui.item.addClass('section-changed');
                    setTimeout(function() {
                        ui.item.removeClass('section-changed');
                    }, 1000);
                }
            });
            $('#sections-sortable').disableSelection();
        });
        
        // 保存排序
        function saveSectionOrder() {
            var order = [];
            document.querySelectorAll('.section-item').forEach(function(item) {
                order.push(item.getAttribute('data-section-id'));
            });
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('排序已保存');
                }
            };
            xhr.send('action=wuchaiwp_save_section_order&order=' + JSON.stringify(order));
        }
        
        // 监听类型变化
        document.getElementById('section_type').addEventListener('change', function() {
            if (this.value === 'custom') {
                document.getElementById('custom_html_container').style.display = 'block';
            } else {
                document.getElementById('custom_html_container').style.display = 'none';
            }
        });
        
        // 恢复默认区域
        function restoreDefaultSections() {
            if (confirm('确定要恢复默认区域吗？此操作将重置所有区域设置为初始状态，包括已添加的自定义区域和排序设置。此操作不可撤销！')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send('action=wuchaiwp_restore_default_sections');
            }
        }
        
        // 点击弹窗外部关闭
        window.onclick = function(event) {
            var modal = document.getElementById('section-modal');
            if (event.target == modal) {
                closeModal();
            }
        }
        </script>
        <?php
    }
    
    // 获取区域列表
    private function get_sections() {
        $sections = get_option('wuchaiwp_enterprise_sections', array(
            array(
                'id' => 'hero',
                'name' => 'Hero区域',
                'icon' => '🚀',
                'type' => 'hero',
                'status' => 'active',
                'order' => 1
            ),
            array(
                'id' => 'about',
                'name' => '企业简介',
                'icon' => '🏢',
                'type' => 'about',
                'status' => 'active',
                'order' => 2
            ),
            array(
                'id' => 'products',
                'name' => '产品介绍',
                'icon' => '📦',
                'type' => 'products',
                'status' => 'active',
                'order' => 3
            ),
            array(
                'id' => 'news',
                'name' => '开发日志',
                'icon' => '📝',
                'type' => 'news',
                'status' => 'active',
                'order' => 4
            ),
            array(
                'id' => 'cases',
                'name' => '案例参考',
                'icon' => '💼',
                'type' => 'cases',
                'status' => 'active',
                'order' => 5
            ),
            array(
                'id' => 'contact',
                'name' => '联系我们',
                'icon' => '📞',
                'type' => 'contact',
                'status' => 'active',
                'order' => 6
            )
        ));
        
        // 按order排序
        usort($sections, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        
        return $sections;
    }
    
    // 回收站页面
    private function render_trash_section() {
        $trash = get_option('wuchaiwp_enterprise_sections_trash', array());
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>🗑️ 回收站</h3>
            <p class="description">已删除的区域会保留在此处，可以恢复或永久删除</p>
            
            <?php if (empty($trash)) : ?>
                <div class="empty-state">
                    <p>回收站为空</p>
                </div>
            <?php else : ?>
                <div class="trash-list">
                    <?php foreach ($trash as $section) : ?>
                        <div class="trash-item">
                            <span class="trash-icon"><?php echo $section['icon']; ?></span>
                            <span class="trash-name"><?php echo $section['name']; ?></span>
                            <span class="trash-date">删除于: <?php echo $section['deleted_at']; ?></span>
                            <div class="trash-actions">
                                <button class="button button-primary" onclick="restoreSection('<?php echo $section['id']; ?>')">恢复</button>
                                <button class="button button-secondary" onclick="deletePermanently('<?php echo $section['id']; ?>')">永久删除</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top: 20px;">
                    <button class="button button-secondary" onclick="emptyTrash()">🗑️ 清空回收站</button>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
        .trash-list {
            margin-top: 20px;
        }
        
        .trash-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #fff;
        }
        
        .trash-icon {
            font-size: 18px;
            margin-right: 10px;
        }
        
        .trash-name {
            flex: 1;
            font-weight: 600;
        }
        
        .trash-date {
            font-size: 12px;
            color: #999;
            margin-right: 15px;
        }
        
        .trash-actions {
            display: flex;
            gap: 8px;
        }
        
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #999;
        }
        </style>
        
        <script>
        function restoreSection(sectionId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send('action=wuchaiwp_restore_section&section_id=' + sectionId);
        }
        
        function deletePermanently(sectionId) {
            if (confirm('确定要永久删除此区域吗？此操作不可恢复！')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send('action=wuchaiwp_delete_permanently&section_id=' + sectionId);
            }
        }
        
        function emptyTrash() {
            if (confirm('确定要清空回收站吗？所有内容将被永久删除！')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    }
                };
                xhr.send('action=wuchaiwp_empty_trash');
            }
        }
        </script>
        <?php
    }
    
    private function render_section($section, $title, $description) {
        $recommended_ids = $this->get_recommended_posts($section);
        $max_posts = $section === 'announcement' ? 1 : ($section === 'cases' ? 999 : 6);
        
        // 获取显示设置
        $display_settings = get_option('wuchaiwp_enterprise_display_settings', array());
        $selected_post_types = !empty($display_settings[$section . '_post_types']) ? $display_settings[$section . '_post_types'] : array();
        $selected_categories = !empty($display_settings[$section . '_categories']) ? $display_settings[$section . '_categories'] : array();
        
        // 获取所有公开的文章类型
        $all_post_types = get_post_types(array('public' => true), 'objects');
        unset($all_post_types['attachment']);
        
        // 开发日志（news）只使用筛选机制，不显示推荐机制
        $show_recommend = !($section === 'news');
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3><?php echo $title; ?></h3>
            <p class="description"><?php echo $description; ?>（最多显示 <?php echo $max_posts; ?> 条）</p>
            
            <!-- 显示模式设置 -->
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #fffbeb;">
                <form method="post" action="" class="display-settings-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_display', 'wuchaiwp_display_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section; ?>">
                    
                    <h4>📋 显示设置</h4>
                    
                    <div class="form-row">
                        <label>选择文章类型（可多选）</label>
                        <select name="auto_post_types[]" id="auto_post_types_<?php echo $section; ?>" class="auto-post-types-select" multiple size="4">
                            <?php foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php echo in_array($slug, $selected_post_types) ? 'selected' : ''; ?>><?php echo $obj->labels->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>选择分类（可多选，不选则显示全部）</label>
                        <select name="auto_categories[]" id="auto_categories_<?php echo $section; ?>" class="auto-categories-select" multiple size="5" <?php echo empty($selected_post_types) ? 'disabled' : ''; ?>>
                            <?php if (!empty($selected_post_types)) : ?>
                                <?php foreach ($selected_post_types as $post_type) : ?>
                                    <?php $taxonomies = get_object_taxonomies($post_type, 'objects'); ?>
                                    <?php foreach ($taxonomies as $taxonomy) : ?>
                                        <?php if ($taxonomy->hierarchical) : ?>
                                            <?php $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => false)); ?>
                                            <?php if (!empty($terms)) : ?>
                                                <optgroup label="<?php echo esc_attr($taxonomy->labels->name); ?>">
                                                    <?php foreach ($terms as $term) : ?>
                                                        <option value="<?php echo $term->term_id; ?>" <?php echo in_array($term->term_id, $selected_categories) ? 'selected' : ''; ?>><?php echo esc_html($term->name . ' (' . $term->slug . ')'); ?></option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">请先选择文章类型</option>
                            <?php endif; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <!-- 案例布局设置 -->
                    <?php if ($section === 'cases') : ?>
                    <div class="form-row">
                        <label>选择展示布局</label>
                        <select name="cases_layout" style="min-width: 200px;">
                            <option value="grid" <?php selected(get_option('wuchaiwp_enterprise_cases_settings')['layout'] ?? 'grid', 'grid'); ?>>网格布局（多列卡片）</option>
                            <option value="list" <?php selected(get_option('wuchaiwp_enterprise_cases_settings')['layout'] ?? 'grid', 'list'); ?>>列表布局（垂直排列）</option>
                            <option value="card" <?php selected(get_option('wuchaiwp_enterprise_cases_settings')['layout'] ?? 'grid', 'card'); ?>>卡片布局（大图展示）</option>
                            <option value="simple" <?php selected(get_option('wuchaiwp_enterprise_cases_settings')['layout'] ?? 'grid', 'simple'); ?>>简约布局（无封面）</option>
                            <option value="minimal" <?php selected(get_option('wuchaiwp_enterprise_cases_settings')['layout'] ?? 'grid', 'minimal'); ?>>极简布局（仅标题）</option>
                            <option value="lightbox" <?php selected(get_option('wuchaiwp_enterprise_cases_settings')['layout'] ?? 'grid', 'lightbox'); ?>>灯箱网格（点击放大）</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>每页显示数量</label>
                        <input type="number" name="cases_posts_per_page" value="<?php echo esc_attr(get_option('wuchaiwp_enterprise_cases_settings')['posts_per_page'] ?? 18); ?>" min="6" max="50" step="6">
                    </div>
                    <div class="form-row">
                        <label>摘要截取长度（字数）</label>
                        <input type="number" name="cases_excerpt_length" value="<?php echo esc_attr(get_option('wuchaiwp_enterprise_cases_settings')['excerpt_length'] ?? 15); ?>" min="5" max="50" step="1">
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存显示设置', 'primary', 'submit_display_settings'); ?>
                    </div>
                </form>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var section = '<?php echo $section; ?>';
                
                // 文章类型变化时更新分类选项
                document.getElementById('auto_post_types_' + section).addEventListener('change', function() {
                    var postTypes = Array.from(this.selectedOptions).map(opt => opt.value);
                    var categoriesSelect = document.getElementById('auto_categories_' + section);
                    
                    if (postTypes.length > 0) {
                        categoriesSelect.disabled = false;
                        
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                categoriesSelect.innerHTML = xhr.responseText;
                            }
                        };
                        xhr.send('action=wuchaiwp_get_taxonomy_terms&post_types=' + encodeURIComponent(JSON.stringify(postTypes)));
                    } else {
                        categoriesSelect.disabled = true;
                        categoriesSelect.innerHTML = '<option value="">请先选择文章类型</option>';
                    }
                });
            });
            </script>
            
            <?php if ($show_recommend) : ?>
            <!-- 已推荐内容列表 -->
            <div class="recommended-list">
                <h4>已推荐内容</h4>
                <div id="recommended-items-<?php echo $section; ?>" class="recommended-items">
                    <?php if (empty($recommended_ids)) : ?>
                        <div class="empty-state">
                            <p>暂无推荐内容，请从下方添加</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($recommended_ids as $post_id) : ?>
                            <?php $post = get_post($post_id); ?>
                            <?php if ($post) : ?>
                                <div class="recommended-item" data-post-id="<?php echo $post_id; ?>">
                                    <span class="post-title"><?php echo get_the_title($post_id); ?></span>
                                    <span class="post-type"><?php echo $this->get_post_type_label($post->post_type); ?></span>
                                    <button class="remove-btn" data-post-id="<?php echo $post_id; ?>" data-section="<?php echo $section; ?>">移除</button>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 添加推荐内容表单 -->
            <div class="add-recommend">
                <h4>添加推荐内容</h4>
                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="recommend-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_recommend', 'wuchaiwp_enterprise_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section; ?>">
                    
                    <div class="form-row">
                        <label>选择文章类型（可多选）</label>
                        <select name="post_types[]" id="post_types_<?php echo $section; ?>" class="post-types-select" multiple size="5">
                            <?php foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>"><?php echo $obj->labels->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>选择分类（可多选，不选则显示全部）</label>
                        <select name="categories[]" id="categories_<?php echo $section; ?>" class="categories-select" multiple size="6" disabled>
                            <option value="">请先选择文章类型</option>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>搜索文章</label>
                        <input type="text" name="search_term" id="search_<?php echo $section; ?>" placeholder="输入文章标题搜索..." class="search-input">
                        <button type="button" class="search-btn" data-section="<?php echo $section; ?>">搜索</button>
                    </div>
                    
                    <div class="form-row">
                        <label>选择文章</label>
                        <select name="post_id[]" id="post_select_<?php echo $section; ?>" class="post-select" multiple size="8">
                            <option value="">请先选择文章类型并搜索</option>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-actions">
                        <?php submit_button('添加到推荐', 'primary', 'submit_recommend'); ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .recommended-items {
            border: 1px solid #ddd;
            border-radius: 6px;
            min-height: 100px;
            margin-bottom: 20px;
        }
        .recommended-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            background: #fff;
        }
        .recommended-item:last-child {
            border-bottom: none;
        }
        .post-title {
            flex: 1;
            font-size: 14px;
        }
        .post-type {
            font-size: 12px;
            color: #666;
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
            margin-right: 10px;
        }
        .remove-btn {
            background: #dc3232;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }
        .empty-state {
            padding: 20px;
            text-align: center;
            color: #999;
        }
        .post-select {
            width: 100%;
        }
        </style>
        
        <?php if ($show_recommend) : ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var section = '<?php echo $section; ?>';
            
            // 文章类型变化时更新分类选项（推荐机制）
            document.getElementById('post_types_' + section).addEventListener('change', function() {
                var postTypes = Array.from(this.selectedOptions).map(opt => opt.value);
                var categoriesSelect = document.getElementById('categories_' + section);
                
                if (postTypes.length > 0) {
                    categoriesSelect.disabled = false;
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            categoriesSelect.innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send('action=wuchaiwp_get_taxonomy_terms&post_types=' + encodeURIComponent(JSON.stringify(postTypes)));
                } else {
                    categoriesSelect.disabled = true;
                    categoriesSelect.innerHTML = '<option value="">请先选择文章类型</option>';
                }
            });
            
            document.querySelector('.search-btn[data-section="' + section + '"]').addEventListener('click', function() {
                var postTypes = Array.from(document.getElementById('post_types_' + section).selectedOptions).map(opt => opt.value);
                var categories = Array.from(document.getElementById('categories_' + section).selectedOptions).map(opt => opt.value);
                var searchTerm = document.getElementById('search_' + section).value;
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('post_select_' + section).innerHTML = xhr.responseText;
                    }
                };
                xhr.send('action=wuchaiwp_search_posts_multi&post_types=' + encodeURIComponent(JSON.stringify(postTypes)) + '&categories=' + encodeURIComponent(JSON.stringify(categories)) + '&search=' + encodeURIComponent(searchTerm));
            });
            
            document.querySelectorAll('.remove-btn[data-section="' + section + '"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var postId = this.getAttribute('data-post-id');
                    var section = this.getAttribute('data-section');
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            location.reload();
                        }
                    };
                    xhr.send('action=wuchaiwp_remove_recommend&post_id=' + postId + '&section=' + section);
                });
            });
        });
        </script>
        <?php endif; ?>
        <?php
    }
    
    
    
    // Hero区域设置页面
private function render_hero_section_settings() {
    $hero_settings = get_option('wuchaiwp_enterprise_hero_settings', array(
        'title' => '',
        'description' => '',
        'button1_text' => '查看产品',
        'button1_link' => '#products',
        'button2_text' => '了解我们',
        'button2_link' => '#about',
        'background_image' => '',
        'background_color' => '#667eea',
        'background_gradient' => '#764ba2',
        'illustration' => '🚀',
        'autoplay' => '1',
        'interval' => '5000',
        'slides' => array()
    ));
    ?>
    <div class="wuchaiwp-form-section">
        <h3>🚀 Hero区域设置</h3>
        <p class="description">设置网站首页Hero区域的内容和样式</p>
        
        <form method="post" action="" class="wuchaiwp-form" enctype="multipart/form-data">
            <?php wp_nonce_field('wuchaiwp_enterprise_hero', 'wuchaiwp_hero_nonce'); ?>
            
            <div class="form-row">
                <label>主标题</label>
                <input type="text" name="hero_title" value="<?php echo esc_attr($hero_settings['title']); ?>" class="regular-text" placeholder="留空则使用网站名称">
            </div>
            
            <div class="form-row">
                <label>副标题/描述</label>
                <textarea name="hero_description" rows="3" class="regular-text" placeholder="留空则使用网站描述"><?php echo esc_textarea($hero_settings['description']); ?></textarea>
            </div>
            
            <div class="form-row">
                <label>主按钮文字</label>
                <input type="text" name="hero_button1_text" value="<?php echo esc_attr($hero_settings['button1_text']); ?>" class="regular-text">
            </div>
            
            <div class="form-row">
                <label>主按钮链接</label>
                <input type="text" name="hero_button1_link" value="<?php echo esc_attr($hero_settings['button1_link']); ?>" class="regular-text" placeholder="#products 或 http://...">
            </div>
            
            <div class="form-row">
                <label>次按钮文字</label>
                <input type="text" name="hero_button2_text" value="<?php echo esc_attr($hero_settings['button2_text']); ?>" class="regular-text">
            </div>
            
            <div class="form-row">
                <label>次按钮链接</label>
                <input type="text" name="hero_button2_link" value="<?php echo esc_attr($hero_settings['button2_link']); ?>" class="regular-text" placeholder="#about 或 http://...">
            </div>
            
            
<div class="form-row">
    <label>轮播图片（支持多张，每张可设置独立内容）</label>
    <div id="hero_images_container">
        <?php 
        $slides = isset($hero_settings['slides']) ? $hero_settings['slides'] : array();
        // 兼容旧的background_images数组
        if (empty($slides) && isset($hero_settings['background_images']) && !empty($hero_settings['background_images'])) {
            foreach ($hero_settings['background_images'] as $image_url) {
                $slides[] = array(
                    'image' => $image_url,
                    'title' => '',
                    'description' => '',
                    'button_text' => '',
                    'button_link' => ''
                );
            }
        }
        if (!empty($slides)) {
            foreach ($slides as $index => $slide) {
                ?>
                <div class="hero-slide-item">
                    <h4 style="margin: 15px 0 10px; padding-bottom: 10px; border-bottom: 1px dashed #ddd;">轮播图 #<?php echo $index + 1; ?></h4>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px;">图片</label>
                        <input type="text" name="slide_images[]" value="<?php echo esc_attr($slide['image'] ?? ''); ?>" class="regular-text">
                        <button type="button" class="button button-secondary upload-btn">选择图片</button>
                        <?php if (!empty($slide['image'])) : ?>
                            <img src="<?php echo esc_url($slide['image']); ?>" style="max-width: 100px; margin-top: 5px; display: block;">
                        <?php endif; ?>
                    </div>
                    
                     <!-- 新增：右侧产品叠加图 -->
    <div style="margin-bottom: 10px;">
        <label style="display: block; margin-bottom: 5px;">右侧产品叠加图（支持PNG透明）</label>
        <input type="text" name="slide_product_images[]" value="<?php echo esc_attr($slide['product_image'] ?? ''); ?>" class="regular-text">
        <button type="button" class="button button-secondary upload-btn">选择图片</button>
        <?php if (!empty($slide['product_image'])) : ?>
            <img src="<?php echo esc_url($slide['product_image']); ?>" style="max-width: 100px; margin-top: 5px; display: block;">
        <?php endif; ?>
    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px;">标题（留空使用全局标题）</label>
                        <input type="text" name="slide_titles[]" value="<?php echo esc_attr($slide['title'] ?? ''); ?>" class="regular-text">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px;">描述（留空使用全局描述）</label>
                        <textarea name="slide_descriptions[]" rows="2" class="regular-text"><?php echo esc_textarea($slide['description'] ?? ''); ?></textarea>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px;">按钮文字（留空不显示按钮）</label>
                        <input type="text" name="slide_button_texts[]" value="<?php echo esc_attr($slide['button_text'] ?? ''); ?>" class="regular-text">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px;">按钮链接</label>
                        <input type="text" name="slide_button_links[]" value="<?php echo esc_attr($slide['button_link'] ?? ''); ?>" class="regular-text">
                    </div>
                    <button type="button" class="button button-danger" onclick="removeHeroSlide(this)">删除此轮播图</button>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <button type="button" class="button button-primary" onclick="addHeroSlide()">+ 添加轮播图</button>
</div>

<div class="form-row">
    <label>
        <input type="checkbox" name="hero_autoplay" value="1" <?php checked($hero_settings['autoplay'], '1'); ?>>
        自动切换
    </label>
</div>

<div class="form-row">
    <label>自动切换间隔（毫秒）</label>
    <input type="number" name="hero_interval" value="<?php echo esc_attr($hero_settings['interval']); ?>" min="2000" max="10000" step="1000">
    <p class="help">默认5000毫秒（5秒）</p>
</div>


<script>
function addHeroSlide() {
    var container = document.getElementById('hero_images_container');
    var index = container.children.length;
    var html = `
        <div class="hero-slide-item">
            <h4 style="margin: 15px 0 10px; padding-bottom: 10px; border-bottom: 1px dashed #ddd;">轮播图 #${index + 1}</h4>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">图片</label>
                <input type="text" name="slide_images[]" class="regular-text">
                <button type="button" class="button button-secondary upload-btn">选择图片</button>
            </div>
            
            <!-- 右侧产品叠加图 -->
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">右侧产品叠加图（支持PNG透明）</label>
                <input type="text" name="slide_product_images[]" class="regular-text">
                <button type="button" class="button button-secondary upload-btn">选择图片</button>
            </div>
            
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">标题（留空使用全局标题）</label>
                <input type="text" name="slide_titles[]" class="regular-text">
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">描述（留空使用全局描述）</label>
                <textarea name="slide_descriptions[]" rows="2" class="regular-text"></textarea>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">按钮文字（留空不显示按钮）</label>
                <input type="text" name="slide_button_texts[]" class="regular-text">
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">按钮链接</label>
                <input type="text" name="slide_button_links[]" class="regular-text">
            </div>
            <button type="button" class="button button-danger" onclick="removeHeroSlide(this)">删除此轮播图</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    
    // 更新序号
    updateSlideNumbers();
    
    // 为新添加的按钮绑定点击事件
    var buttons = container.querySelectorAll('.upload-btn');
    buttons[buttons.length - 1].addEventListener('click', function() {
        openMediaUploader(this);
    });
}

function removeHeroSlide(el) {
    el.parentElement.remove();
    updateSlideNumbers();
}

function updateSlideNumbers() {
    var container = document.getElementById('hero_images_container');
    var items = container.querySelectorAll('.hero-slide-item');
    items.forEach(function(item, index) {
        var h4 = item.querySelector('h4');
        if (h4) {
            h4.textContent = '轮播图 #' + (index + 1);
        }
    });
}

function openMediaUploader(button) {
    var input = button.previousElementSibling;
    
    var customUploader = wp.media({
        title: '选择图片',
        button: { text: '使用此图片' },
        multiple: false
    }).on('select', function() {
        var attachment = customUploader.state().get('selection').first().toJSON();
        input.value = attachment.url;
        
        // 显示预览图片
        var existingImg = button.parentElement.querySelector('img');
        if (existingImg) {
            existingImg.src = attachment.url;
        } else {
            var img = document.createElement('img');
            img.src = attachment.url;
            img.style.maxWidth = '100px';
            img.style.marginTop = '5px';
            img.style.display = 'block';
            button.parentElement.appendChild(img);
        }
    }).open();
}

// 页面加载时为已有的按钮绑定事件
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.upload-btn');
    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            openMediaUploader(this);
        });
    });
});
</script>

<style>
.hero-slide-item {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #eee;
}
</style>
            
            
            
            <div class="form-row">
                <label>背景渐变色（起始）</label>
                <input type="color" name="hero_background_color" value="<?php echo esc_attr($hero_settings['background_color']); ?>">
            </div>
            
            <div class="form-row">
                <label>背景渐变色（结束）</label>
                <input type="color" name="hero_background_gradient" value="<?php echo esc_attr($hero_settings['background_gradient']); ?>">
            </div>
            
            <div class="form-row">
                <label>装饰图标/表情符号</label>
                <input type="text" name="hero_illustration" value="<?php echo esc_attr($hero_settings['illustration']); ?>" class="regular-text" placeholder="例如: 🚀">
            </div>
            
            <p class="submit">
                <input type="submit" name="submit_hero" class="button button-primary" value="保存设置">
            </p>
        </form>
    </div>
    
    <?php
}
    
    
    
    // ... 保持其他原有方法不变 ...
    
    private function render_about_section() {
        $about_settings = get_option('wuchaiwp_enterprise_about_settings', array(
            'about_page' => '',
            'excerpt_length' => '500',
            'show_read_more' => '1',
            'read_more_text' => '查看完整内容',
            'stats' => array(
                array('value' => '10+', 'label' => '年行业经验'),
                array('value' => '500+', 'label' => '服务客户'),
                array('value' => '100+', 'label' => '团队成员'),
                array('value' => '99%', 'label' => '客户满意度')
            ),
            'show_stats' => '1'
        ));
        
        // 获取标题设置
        $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array(
            'about_title' => '🏢 企业简介',
            'about_subtitle' => '关于我们的故事'
        ));
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>🏢 企业简介设置</h3>
            
            <form method="post" action="" class="recommend-form">
                <?php wp_nonce_field('wuchaiwp_enterprise_about', 'wuchaiwp_about_nonce'); ?>
                
                <!-- 区域标题设置 -->
                <div class="title-settings" style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">区域标题设置</h4>
                    <div class="form-row">
                        <label>区域标题</label>
                        <input type="text" name="about_title" value="<?php echo esc_attr($titles_settings['about_title']); ?>" class="regular-text">
                    </div>
                    <div class="form-row">
                        <label>区域副标题</label>
                        <input type="text" name="about_subtitle" value="<?php echo esc_attr($titles_settings['about_subtitle']); ?>" class="regular-text">
                    </div>
                </div>
                
                <!-- 已选择内容显示区域 -->
                <?php if (!empty($about_settings['about_page'])) : ?>
                <div class="selected-content-box" style="border: 1px solid #667eea; padding: 15px; border-radius: 8px; margin-bottom: 20px; background: #f8f9ff;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #667eea;">✓ 已选择内容：</strong>
                            <?php
                            $selected_post = get_post($about_settings['about_page']);
                            if ($selected_post) {
                                $post_type_obj = get_post_type_object($selected_post->post_type);
                                echo '<span style="margin-left: 10px;">';
                                echo $selected_post->post_type === 'page' ? '📄' : '📝';
                                echo ' ' . get_the_title($selected_post->ID);
                                echo ' (' . esc_html($post_type_obj->labels->singular_name) . ')';
                                echo '</span>';
                            }
                            ?>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?php echo get_edit_post_link($about_settings['about_page']); ?>" target="_blank" style="padding: 6px 15px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background 0.3s; text-decoration: none; font-size: 13px;">
                                ✏️ 编辑
                            </a>
                            <button type="button" onclick="clearAboutPage()" style="padding: 6px 15px; background: #dc3232; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background 0.3s;">
                                🗑️ 清除
                            </button>
                        </div>
                    </div>
                    <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">提示：点击清除后，请务必点击下方的「保存设置」按钮以生效</p>
                </div>
                <input type="hidden" name="clear_about_page" id="clear_about_page" value="0">
                <?php endif; ?>
                
                <div class="form-row">
                    <label>关联企业简介内容</label>
                    <select name="about_page" class="post-select" style="width: 100%;">
                        <option value="">选择页面或文章</option>
                        <?php
                        // 获取所有公开的页面
                        $pages = get_pages(array('post_status' => 'publish'));
                        if (!empty($pages)) {
                            echo '<optgroup label="页面">';
                            foreach ($pages as $page) {
                                echo '<option value="' . $page->ID . '" ' . selected($about_settings['about_page'], $page->ID, false) . '>📄 ' . get_the_title($page->ID) . '</option>';
                            }
                            echo '</optgroup>';
                        }
                        
                        // 获取所有自定义文章类型
                        $custom_post_types = get_post_types(array(
                            'public' => true,
                            '_builtin' => false
                        ), 'objects');
                        
                        foreach ($custom_post_types as $cpt) {
                            $posts = get_posts(array(
                                'post_type' => $cpt->name,
                                'post_status' => 'publish',
                                'posts_per_page' => -1
                            ));
                            if (!empty($posts)) {
                                echo '<optgroup label="' . esc_attr($cpt->labels->name) . '">';
                                foreach ($posts as $post) {
                                    echo '<option value="' . $post->ID . '" ' . selected($about_settings['about_page'], $post->ID, false) . '>📝 ' . get_the_title($post->ID) . '</option>';
                                }
                                echo '</optgroup>';
                            }
                        }
                        ?>
                    </select>
                    <p class="help">选择一个页面或自定义文章作为企业简介内容来源，将显示其前500字内容</p>
                </div>
                
                <script>
                function clearAboutPage() {
                    if (confirm('确定要清除已选择的企业简介内容吗？此操作需要点击保存设置才能生效。')) {
                        document.getElementById('clear_about_page').value = '1';
                        document.querySelector('select[name="about_page"]').value = '';
                        document.querySelector('.selected-content-box').style.display = 'none';
                        alert('已标记清除，请点击下方的「保存设置」按钮完成操作');
                    }
                }
                </script>
                
                <!-- 内容截取设置 -->
                <div class="content-settings" style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">内容显示设置</h4>
                    
                    <div class="form-row">
                        <label>简介截取字数</label>
                        <input type="number" name="excerpt_length" value="<?php echo esc_attr($about_settings['excerpt_length']); ?>" min="100" max="2000" step="50" class="regular-text">
                        <p class="help">设置企业简介显示的字数，超过此字数将显示"查看更多"链接</p>
                    </div>
                    
                    <div class="form-row">
                        <label>
                            <input type="checkbox" name="show_read_more" value="1" <?php checked($about_settings['show_read_more'], '1'); ?>>
                            显示"查看更多"按钮
                        </label>
                    </div>
                    
                    <div class="form-row">
                        <label>"查看更多"按钮文字</label>
                        <input type="text" name="read_more_text" value="<?php echo esc_attr($about_settings['read_more_text']); ?>" class="regular-text">
                    </div>
                </div>
                
                <div class="form-row">
                    <label>
                        <input type="checkbox" name="show_stats" value="1" <?php checked($about_settings['show_stats'], '1'); ?>>
                        显示统计数据
                    </label>
                </div>
                
                <div class="form-row">
                    <label>统计数据设置</label>
                    <div class="stats-grid">
                        <?php for ($i = 0; $i < 4; $i++) : ?>
                            <div class="stat-input">
                                <input type="text" name="stats_value[]" value="<?php echo esc_attr($about_settings['stats'][$i]['value'] ?? ''); ?>" placeholder="数值" style="width: 80px;">
                                <input type="text" name="stats_label[]" value="<?php echo esc_attr($about_settings['stats'][$i]['label'] ?? ''); ?>" placeholder="标签">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php submit_button('保存设置', 'primary', 'submit_about'); ?>
                </div>
            </form>
        </div>
        
        <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .stat-input {
            display: flex;
            gap: 10px;
        }
        </style>
        <?php
    }
    
    // 产品介绍设置页面
    private function render_products_section_settings() {
        $products_settings = get_option('wuchaiwp_enterprise_products_settings', array(
            'excerpt_length' => '500',
            'show_read_more' => '1',
            'read_more_text' => '查看更多',
            'image_autoplay' => '1',
            'image_interval' => '5000'
        ));
        
        // 获取标题设置
        $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array(
            'products_title' => '📦 产品介绍',
            'products_subtitle' => '我们的核心产品'
        ));
        
        // 获取已推荐的产品
        $recommended_ids = get_option('wuchaiwp_enterprise_products_posts', array());
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>📦 产品介绍设置</h3>
            
            <form method="post" action="" class="recommend-form">
                <?php wp_nonce_field('wuchaiwp_enterprise_products', 'wuchaiwp_products_nonce'); ?>
                
                <!-- 区域标题设置 -->
                <div class="title-settings" style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">区域标题设置</h4>
                    <div class="form-row">
                        <label>区域标题</label>
                        <input type="text" name="products_title" value="<?php echo esc_attr($titles_settings['products_title']); ?>" class="regular-text">
                    </div>
                    <div class="form-row">
                        <label>区域副标题</label>
                        <input type="text" name="products_subtitle" value="<?php echo esc_attr($titles_settings['products_subtitle']); ?>" class="regular-text">
                    </div>
                </div>
                
                <!-- 内容设置 -->
                <div class="content-settings" style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">内容显示设置</h4>
                    <div class="form-row">
                        <label>内容截取长度（字符）</label>
                        <input type="number" name="products_excerpt_length" value="<?php echo esc_attr($products_settings['excerpt_length']); ?>" min="100" max="2000" step="50">
                    </div>
                    <div class="form-row">
                        <label>
                            <input type="checkbox" name="products_show_read_more" value="1" <?php checked($products_settings['show_read_more'], '1'); ?>>
                            显示"查看更多"按钮
                        </label>
                    </div>
                    <div class="form-row">
                        <label>"查看更多"按钮文字</label>
                        <input type="text" name="products_read_more_text" value="<?php echo esc_attr($products_settings['read_more_text']); ?>" class="regular-text">
                    </div>
                </div>
                
                <!-- 图片轮播设置 -->
                <div class="slider-settings" style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">图片轮播设置</h4>
                    <div class="form-row">
                        <label>
                            <input type="checkbox" name="products_image_autoplay" value="1" <?php checked($products_settings['image_autoplay'], '1'); ?>>
                            图片自动播放
                        </label>
                    </div>
                    <div class="form-row">
                        <label>自动播放间隔（毫秒）</label>
                        <input type="number" name="products_image_interval" value="<?php echo esc_attr($products_settings['image_interval']); ?>" min="2000" max="10000" step="500">
                    </div>
                </div>
                
                <!-- 已推荐内容列表 -->
                <div class="recommended-list">
                    <h4>已推荐产品</h4>
                    <div id="recommended-items-products" class="recommended-items">
                        <?php if (empty($recommended_ids)) : ?>
                            <div class="empty-state">
                                <p>暂无推荐产品，请从下方添加</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($recommended_ids as $post_id) : ?>
                                <?php $post = get_post($post_id); ?>
                                <?php if ($post) : ?>
                                    <div class="recommended-item" data-post-id="<?php echo $post_id; ?>">
                                        <span class="post-title"><?php echo get_the_title($post_id); ?></span>
                                        <span class="post-type"><?php echo $this->get_post_type_label($post->post_type); ?></span>
                                        <button class="remove-btn" data-post-id="<?php echo $post_id; ?>" data-section="products">移除</button>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
            </form>
            
            <!-- 添加推荐内容表单 -->
            <div class="add-recommend">
                <h4>添加推荐产品</h4>
                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="recommend-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_recommend', 'wuchaiwp_enterprise_nonce'); ?>
                    <input type="hidden" name="section" value="products">
                    
                    <?php $all_post_types = get_post_types(array('public' => true), 'objects'); ?>
                    
                    <div class="form-row">
                        <label>选择文章类型（可多选）</label>
                        <select name="post_types[]" id="post_types_products" class="post-types-select" multiple size="5">
                            <?php foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>"><?php echo $obj->labels->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>选择分类（可多选，不选则显示全部）</label>
                        <select name="categories[]" id="categories_products" class="categories-select" multiple size="6" disabled>
                            <option value="">请先选择文章类型</option>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>搜索文章</label>
                        <input type="text" name="search_term" id="search_products" placeholder="输入文章标题搜索..." class="search-input">
                        <button type="button" class="search-btn" data-section="products">搜索</button>
                    </div>
                    
                    <div class="form-row">
                        <label>选择文章</label>
                        <select name="post_id[]" id="post_select_products" class="post-select" multiple size="8">
                            <option value="">请先选择文章类型并搜索</option>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-actions">
                        <?php submit_button('添加到推荐', 'primary', 'submit_recommend'); ?>
                    </div>
                </form>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // 使用 data-section 属性动态获取当前区域
                    var sectionContainer = document.querySelector('.add-recommend');
                    var section = sectionContainer.getAttribute('data-section') || 'products';
                    
                    // 文章类型变化时更新分类选项
                    var postTypesSelect = document.getElementById('post_types_' + section);
                    var categoriesSelect = document.getElementById('categories_' + section);
                    var searchBtn = document.querySelector('.search-btn[data-section="' + section + '"]');
                    var postSelect = document.getElementById('post_select_' + section);
                    var searchInput = document.getElementById('search_' + section);
                    
                    if (postTypesSelect) {
                        postTypesSelect.addEventListener('change', function() {
                            var postTypes = Array.from(this.selectedOptions).map(opt => opt.value);
                            
                            if (postTypes.length > 0) {
                                var xhr = new XMLHttpRequest();
                                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                xhr.onload = function() {
                                    if (xhr.status === 200) {
                                        if (categoriesSelect) {
                                            categoriesSelect.innerHTML = xhr.responseText;
                                            categoriesSelect.disabled = false;
                                        }
                                    }
                                };
                                xhr.send('action=wuchaiwp_get_taxonomy_terms&post_types=' + encodeURIComponent(JSON.stringify(postTypes)));
                            } else {
                                if (categoriesSelect) {
                                    categoriesSelect.disabled = true;
                                    categoriesSelect.innerHTML = '<option value="">请先选择文章类型</option>';
                                }
                            }
                        });
                    }
                    
                    if (searchBtn) {
                        searchBtn.addEventListener('click', function() {
                            var postTypes = postTypesSelect ? Array.from(postTypesSelect.selectedOptions).map(opt => opt.value) : [];
                            var categories = categoriesSelect ? Array.from(categoriesSelect.selectedOptions).map(opt => opt.value) : [];
                            var searchTerm = searchInput ? searchInput.value : '';
                            
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200 && postSelect) {
                                    postSelect.innerHTML = xhr.responseText;
                                }
                            };
                            xhr.send('action=wuchaiwp_search_posts_multi&post_types=' + encodeURIComponent(JSON.stringify(postTypes)) + '&categories=' + encodeURIComponent(JSON.stringify(categories)) + '&search=' + encodeURIComponent(searchTerm));
                        });
                    }
                });
                </script>
            </div>
        </div>
        
        <style>
        .recommended-items {
            border: 1px solid #ddd;
            border-radius: 6px;
            min-height: 100px;
            margin-bottom: 20px;
        }
        .recommended-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            background: #fff;
        }
        .recommended-item:last-child {
            border-bottom: none;
        }
        .post-title {
            flex: 1;
            font-size: 14px;
        }
        .post-type {
            font-size: 12px;
            color: #666;
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 4px;
            margin-right: 10px;
        }
        .remove-btn {
            background: #dc3232;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }
        .empty-state {
            padding: 20px;
            text-align: center;
            color: #999;
        }
        .post-select {
            width: 100%;
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var section = 'products';
            
            document.querySelector('.search-btn[data-section="' + section + '"]').addEventListener('click', function() {
                var postType = document.getElementById('post_type_' + section).value;
                var searchTerm = document.getElementById('search_' + section).value;
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('post_select_' + section).innerHTML = xhr.responseText;
                    }
                };
                xhr.send('action=wuchaiwp_search_posts&post_type=' + encodeURIComponent(postType) + '&search=' + encodeURIComponent(searchTerm));
            });
            
            document.querySelectorAll('.remove-btn[data-section="' + section + '"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var postId = this.getAttribute('data-post-id');
                    var section = this.getAttribute('data-section');
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            location.reload();
                        }
                    };
                    xhr.send('action=wuchaiwp_remove_recommend&post_id=' + postId + '&section=' + section);
                });
            });
        });
        </script>
        <?php
    }
    
    private function render_contact_section() {
        $contact_settings = get_option('wuchaiwp_enterprise_contact_settings', array(
            'address' => '北京市朝阳区科技园区A座18层',
            'phone' => '400-888-8888',
            'email' => 'contact@example.com',
            'work_time' => '周一至周五 9:00-18:00',
            'contact_form_shortcode' => '[contact-form-7 id="123" title="联系表单"]',
            'show_contact_form' => '1',
            'custom_fields' => array()
        ));
        
        // 获取标题设置
        $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array(
            'contact_title' => '📞 联系我们',
            'contact_subtitle' => '期待与您合作'
        ));
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>📞 联系我们设置</h3>
            
            <form method="post" action="" class="recommend-form">
                <?php wp_nonce_field('wuchaiwp_enterprise_contact', 'wuchaiwp_contact_nonce'); ?>
                
                <!-- 区域标题设置 -->
                <div class="title-settings" style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">区域标题设置</h4>
                    <div class="form-row">
                        <label>区域标题</label>
                        <input type="text" name="contact_title" value="<?php echo esc_attr($titles_settings['contact_title']); ?>" class="regular-text">
                    </div>
                    <div class="form-row">
                        <label>区域副标题</label>
                        <input type="text" name="contact_subtitle" value="<?php echo esc_attr($titles_settings['contact_subtitle']); ?>" class="regular-text">
                    </div>
                </div>
                
                <div class="form-row">
                    <label>公司地址</label>
                    <input type="text" name="address" value="<?php echo esc_attr($contact_settings['address']); ?>" style="width: 100%;">
                </div>
                
                <div class="form-row">
                    <label>联系电话</label>
                    <input type="text" name="phone" value="<?php echo esc_attr($contact_settings['phone']); ?>" style="width: 100%;">
                </div>
                
                <div class="form-row">
                    <label>电子邮箱</label>
                    <input type="text" name="email" value="<?php echo esc_attr($contact_settings['email']); ?>" style="width: 100%;">
                </div>
                
                <div class="form-row">
                    <label>工作时间</label>
                    <input type="text" name="work_time" value="<?php echo esc_attr($contact_settings['work_time']); ?>" style="width: 100%;">
                </div>
                
                <div class="form-row">
                    <label>
                        <input type="checkbox" name="show_contact_form" value="1" <?php checked($contact_settings['show_contact_form'], '1'); ?>>
                        显示联系表单
                    </label>
                </div>
                
                <div class="form-row">
                    <label>联系表单短代码</label>
                    <input type="text" name="contact_form_shortcode" value="<?php echo esc_attr($contact_settings['contact_form_shortcode']); ?>" style="width: 100%;">
                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #666;">提示：使用 Contact Form 7 等表单插件生成短代码后粘贴到此</p>
                </div>
                
                <!-- 使用说明 -->
                <div style="border: 1px solid #667eea; padding: 15px; border-radius: 6px; margin-bottom: 15px; background: #f0f4ff;">
                    <h4 style="margin: 0 0 10px 0; color: #667eea;">📝 联系表单使用说明</h4>
                    <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #555;">
                        <li style="margin-bottom: 5px;">首先安装并启用 <strong>Contact Form 7</strong> 表单插件</li>
                        <li style="margin-bottom: 5px;">进入后台「联系」菜单，点击「添加新」创建表单</li>
                        <li style="margin-bottom: 5px;">表单创建后复制短代码（如 [contact-form-7 id="123" title="联系表单"]）</li>
                        <li style="margin-bottom: 5px;">将短代码粘贴到上方输入框，勾选「显示联系表单」选项</li>
                        <li style="margin-bottom: 5px;">保存设置后，联系表单将显示在联系我们区域右侧</li>
                    </ol>
                    <p style="margin: 10px 0 0 0; font-size: 12px; color: #888;">💡 也可使用 Gravity Forms、WPForms 等其他表单插件的短代码</p>
                </div>
                
                <!-- 自定义字段 -->
                <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4 style="margin: 0 0 15px 0;">➕ 自定义字段</h4>
                    <div id="custom_contact_fields">
                        <?php 
                        $custom_fields = isset($contact_settings['custom_fields']) ? $contact_settings['custom_fields'] : array();
                        if (!empty($custom_fields)) :
                            foreach ($custom_fields as $index => $field) :
                        ?>
                        <div class="custom-field-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="custom_field_labels[]" value="<?php echo esc_attr($field['label']); ?>" placeholder="字段标签（如：技术支持）" style="flex: 1; padding: 6px;">
                            <input type="text" name="custom_field_icons[]" value="<?php echo esc_attr($field['icon']); ?>" placeholder="图标（如：🛠️）" style="width: 80px; padding: 6px;">
                            <input type="text" name="custom_field_values[]" value="<?php echo esc_attr($field['value']); ?>" placeholder="字段值" style="flex: 2; padding: 6px;">
                            <button type="button" class="button button-secondary remove-custom-field" onclick="removeCustomField(this)">删除</button>
                        </div>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </div>
                    <button type="button" class="button button-primary" onclick="addCustomField()">+ 添加新字段</button>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">提示：图标可使用emoji表情，如 📞 📧 📍 🕐 🛠️ 💬 等</p>
                </div>
                
                <div class="form-actions">
                    <?php submit_button('保存设置', 'primary', 'submit_contact'); ?>
                </div>
            </form>
        </div>
        
        <script>
        function addCustomField() {
            var container = document.getElementById('custom_contact_fields');
            var html = `
                <div class="custom-field-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="custom_field_labels[]" placeholder="字段标签（如：技术支持）" style="flex: 1; padding: 6px;">
                    <input type="text" name="custom_field_icons[]" placeholder="图标（如：🛠️）" style="width: 80px; padding: 6px;">
                    <input type="text" name="custom_field_values[]" placeholder="字段值" style="flex: 2; padding: 6px;">
                    <button type="button" class="button button-secondary remove-custom-field" onclick="removeCustomField(this)">删除</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
        
        function removeCustomField(el) {
            el.parentElement.remove();
        }
        </script>
        <?php
    }
    
    private function render_categories_section() {
        $category_settings = get_option('wuchaiwp_enterprise_category_settings', array(
            'news_categories' => array(),
            'product_categories' => array(),
            'case_categories' => array(),
            'news_post_type' => 'news',
            'product_post_type' => 'product',
            'case_post_type' => 'case'
        ));
        
        // 获取所有公开的自定义文章类型
        $post_types = get_post_types(array('public' => true), 'objects');
        unset($post_types['attachment']); // 排除附件
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>📁 分类显示设置</h3>
            
            <form method="post" action="" class="recommend-form">
                <?php wp_nonce_field('wuchaiwp_enterprise_categories', 'wuchaiwp_categories_nonce'); ?>
                
                <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4>开发日志分类设置</h4>
                    <div class="form-row">
                        <label>选择文章类型</label>
                        <select name="news_post_type" id="news_post_type" class="post-type-select" style="width: 100%;">
                            <?php foreach ($post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php selected($category_settings['news_post_type'], $slug); ?>>
                                    <?php echo $obj->labels->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>选择要显示的分类</label>
                        <select name="news_categories[]" multiple size="8" style="width: 100%;">
                            <?php $this->render_taxonomy_options($category_settings['news_post_type'], $category_settings['news_categories']); ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选，不选择则显示所有分类</p>
                    </div>
                </div>
                
                <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4>产品分类设置</h4>
                    <div class="form-row">
                        <label>选择文章类型</label>
                        <select name="product_post_type" id="product_post_type" class="post-type-select" style="width: 100%;">
                            <?php foreach ($post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php selected($category_settings['product_post_type'], $slug); ?>>
                                    <?php echo $obj->labels->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>选择要显示的分类</label>
                        <select name="product_categories[]" multiple size="8" style="width: 100%;">
                            <?php $this->render_taxonomy_options($category_settings['product_post_type'], $category_settings['product_categories']); ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选，不选择则显示所有分类</p>
                    </div>
                </div>
                
                <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                    <h4>案例分类设置</h4>
                    <div class="form-row">
                        <label>选择文章类型</label>
                        <select name="case_post_type" id="case_post_type" class="post-type-select" style="width: 100%;">
                            <?php foreach ($post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php selected($category_settings['case_post_type'], $slug); ?>>
                                    <?php echo $obj->labels->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>选择要显示的分类</label>
                        <select name="case_categories[]" multiple size="8" style="width: 100%;">
                            <?php $this->render_taxonomy_options($category_settings['case_post_type'], $category_settings['case_categories']); ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选，不选择则显示所有分类</p>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php submit_button('保存设置', 'primary', 'submit_categories'); ?>
                </div>
            </form>
        </div>
        <?php
    }
    
    // 渲染分类选项辅助函数
    private function render_taxonomy_options($post_type, $selected_ids) {
        $taxonomies = get_object_taxonomies($post_type, 'objects');
        $has_hierarchical = false;
        
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy->hierarchical) {
                $has_hierarchical = true;
                $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => false));
                if (!empty($terms)) {
                    echo '<optgroup label="' . esc_attr($taxonomy->labels->name) . '">';
                    foreach ($terms as $term) {
                        $selected = in_array($term->term_id, $selected_ids) ? 'selected' : '';
                        echo '<option value="' . $term->term_id . '" ' . $selected . '>' . esc_html($term->name . ' (' . $term->slug . ')') . '</option>';
                    }
                    echo '</optgroup>';
                }
            }
        }
        
        // 如果没有层级分类，显示默认分类
        if (!$has_hierarchical && $post_type === 'post') {
            $categories = get_categories();
            if (!empty($categories)) {
                echo '<optgroup label="文章分类">';
                foreach ($categories as $cat) {
                    $selected = in_array($cat->term_id, $selected_ids) ? 'selected' : '';
                    echo '<option value="' . $cat->term_id . '" ' . $selected . '>' . esc_html($cat->name . ' (' . $cat->slug . ')') . '</option>';
                }
                echo '</optgroup>';
            }
        }
    }
    
    public function handle_form_submit() {
        // 处理显示设置提交
        if (isset($_POST['submit_display_settings']) && check_admin_referer('wuchaiwp_enterprise_display', 'wuchaiwp_display_nonce')) {
            $active_tab = isset($_POST['section']) ? sanitize_text_field($_POST['section']) : (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'news');
            
            $display_settings = get_option('wuchaiwp_enterprise_display_settings', array());
            
            // 更新跳过推荐设置
            $display_settings[$active_tab . '_skip_recommend'] = isset($_POST['skip_recommend']) ? '1' : '0';
            
            // 更新文章类型设置
            $display_settings[$active_tab . '_post_types'] = isset($_POST['auto_post_types']) ? array_map('sanitize_text_field', (array)$_POST['auto_post_types']) : array();
            
            // 更新分类设置
            $display_settings[$active_tab . '_categories'] = isset($_POST['auto_categories']) ? array_map('intval', (array)$_POST['auto_categories']) : array();
            
            update_option('wuchaiwp_enterprise_display_settings', $display_settings);
            
            // 保存案例布局设置
            if ($active_tab === 'cases') {
                $cases_settings = get_option('wuchaiwp_enterprise_cases_settings', array());
                $cases_settings['layout'] = sanitize_text_field($_POST['cases_layout'] ?? 'grid');
                $cases_settings['posts_per_page'] = intval($_POST['cases_posts_per_page'] ?? 18);
                $cases_settings['excerpt_length'] = intval($_POST['cases_excerpt_length'] ?? 15);
                update_option('wuchaiwp_enterprise_cases_settings', $cases_settings);
            }
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $active_tab), admin_url('admin.php')));
            exit;
        }
        
        // 处理区域表单提交
        if (isset($_POST['section_name']) && check_admin_referer('wuchaiwp_enterprise_section', 'wuchaiwp_section_nonce')) {
            $sections = $this->get_sections();
            $section_id = sanitize_text_field($_POST['section_id']);
            
            $new_section = array(
                'id' => empty($section_id) ? 'section_' . uniqid() : $section_id,
                'name' => sanitize_text_field($_POST['section_name']),
                'icon' => sanitize_text_field($_POST['section_icon']),
                'type' => sanitize_text_field($_POST['section_type']),
                'content' => isset($_POST['section_content']) ? wp_kses_post($_POST['section_content']) : '',
                'status' => 'active',
                'order' => empty($section_id) ? count($sections) + 1 : $this->get_section_order($section_id, $sections)
            );
            
            if (!empty($section_id)) {
                // 编辑现有区域
                foreach ($sections as &$section) {
                    if ($section['id'] === $section_id) {
                        $section = $new_section;
                        break;
                    }
                }
            } else {
                // 添加新区域
                $sections[] = $new_section;
            }
            
            update_option('wuchaiwp_enterprise_sections', $sections);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'sections'), admin_url('admin.php')));
            exit;
        }
        
        
        
        // 处理Hero区域设置提交
if (isset($_POST['submit_hero']) && check_admin_referer('wuchaiwp_enterprise_hero', 'wuchaiwp_hero_nonce')) {
    $hero_settings = array(
        'title' => sanitize_text_field($_POST['hero_title']),
        'description' => sanitize_textarea_field($_POST['hero_description']),
        'button1_text' => sanitize_text_field($_POST['hero_button1_text']),
        'button1_link' => sanitize_text_field($_POST['hero_button1_link']),
        'button2_text' => sanitize_text_field($_POST['hero_button2_text']),
        'button2_link' => sanitize_text_field($_POST['hero_button2_link']),
        'background_color' => sanitize_text_field($_POST['hero_background_color']),
        'background_gradient' => sanitize_text_field($_POST['hero_background_gradient']),
        'illustration' => sanitize_text_field($_POST['hero_illustration']),
        'autoplay' => isset($_POST['hero_autoplay']) ? '1' : '0',
        'interval' => intval($_POST['hero_interval']),
        'slides' => array()
    );
    
   
   // 处理轮播图数据
$slide_images = isset($_POST['slide_images']) ? $_POST['slide_images'] : array();
$slide_product_images = isset($_POST['slide_product_images']) ? $_POST['slide_product_images'] : array(); // 新增
$slide_titles = isset($_POST['slide_titles']) ? $_POST['slide_titles'] : array();
$slide_descriptions = isset($_POST['slide_descriptions']) ? $_POST['slide_descriptions'] : array();
$slide_button_texts = isset($_POST['slide_button_texts']) ? $_POST['slide_button_texts'] : array();
$slide_button_links = isset($_POST['slide_button_links']) ? $_POST['slide_button_links'] : array();

foreach ($slide_images as $index => $image_url) {
    if (!empty($image_url)) {
        $hero_settings['slides'][] = array(
            'image' => esc_url_raw($image_url),
            'product_image' => esc_url_raw($slide_product_images[$index] ?? ''), // 新增
            'title' => sanitize_text_field($slide_titles[$index] ?? ''),
            'description' => sanitize_textarea_field($slide_descriptions[$index] ?? ''),
            'button_text' => sanitize_text_field($slide_button_texts[$index] ?? ''),
            'button_link' => sanitize_text_field($slide_button_links[$index] ?? '')
        );
    }
}
    
    // 兼容旧的background_images格式
    if (empty($hero_settings['slides']) && isset($_POST['hero_background_images'])) {
        foreach ($_POST['hero_background_images'] as $image_url) {
            if (!empty($image_url)) {
                $hero_settings['slides'][] = array(
                    'image' => esc_url_raw($image_url),
                    'title' => '',
                    'description' => '',
                    'button_text' => '',
                    'button_link' => ''
                );
            }
        }
    }
    
    update_option('wuchaiwp_enterprise_hero_settings', $hero_settings);
    
    wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'hero'), admin_url('admin.php')));
    exit;
}
        
        
        
        // ... 保持其他表单处理代码不变 ...
        
        // 处理推荐内容提交
        if (isset($_POST['submit_recommend']) && check_admin_referer('wuchaiwp_enterprise_recommend', 'wuchaiwp_enterprise_nonce')) {
            $section = sanitize_text_field($_POST['section']);
            $post_ids = isset($_POST['post_id']) ? (array)$_POST['post_id'] : array();
            
            $current_ids = $this->get_recommended_posts($section);
            $new_ids = array_unique(array_merge($current_ids, $post_ids));
            
            $max_posts = $section === 'announcement' ? 1 : ($section === 'cases' ? 999 : 6);
            $new_ids = array_slice($new_ids, 0, $max_posts);
            
            update_option('wuchaiwp_enterprise_' . $section . '_posts', $new_ids);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $section), admin_url('admin.php')));
            exit;
        }
        
        // 产品介绍设置保存
        if (isset($_POST['submit_products']) && check_admin_referer('wuchaiwp_enterprise_products', 'wuchaiwp_products_nonce')) {
            $products_settings = array(
                'excerpt_length' => intval($_POST['products_excerpt_length']),
                'show_read_more' => isset($_POST['products_show_read_more']) ? '1' : '0',
                'read_more_text' => sanitize_text_field($_POST['products_read_more_text']),
                'image_autoplay' => isset($_POST['products_image_autoplay']) ? '1' : '0',
                'image_interval' => intval($_POST['products_image_interval'])
            );
            
            update_option('wuchaiwp_enterprise_products_settings', $products_settings);
            
            // 保存标题设置
            $this->update_titles_settings('products', $_POST);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'products'), admin_url('admin.php')));
            exit;
        }
        
        if (isset($_POST['submit_about']) && check_admin_referer('wuchaiwp_enterprise_about', 'wuchaiwp_about_nonce')) {
            // 处理清除选择操作
            $about_page = 0;
            if (isset($_POST['clear_about_page']) && $_POST['clear_about_page'] == '1') {
                $about_page = 0;
            } else {
                $about_page = intval($_POST['about_page']);
            }
            
            $about_settings = array(
                'about_page' => $about_page,
                'excerpt_length' => intval($_POST['excerpt_length']),
                'show_read_more' => isset($_POST['show_read_more']) ? '1' : '0',
                'read_more_text' => sanitize_text_field($_POST['read_more_text']),
                'show_stats' => isset($_POST['show_stats']) ? '1' : '0',
                'stats' => array()
            );
            
            $stats_values = isset($_POST['stats_value']) ? $_POST['stats_value'] : array();
            $stats_labels = isset($_POST['stats_label']) ? $_POST['stats_label'] : array();
            
            for ($i = 0; $i < 4; $i++) {
                $about_settings['stats'][] = array(
                    'value' => sanitize_text_field($stats_values[$i] ?? ''),
                    'label' => sanitize_text_field($stats_labels[$i] ?? '')
                );
            }
            
            update_option('wuchaiwp_enterprise_about_settings', $about_settings);
            
            // 保存标题设置
            $this->update_titles_settings('about', $_POST);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'about'), admin_url('admin.php')));
            exit;
        }
        
        if (isset($_POST['submit_contact']) && check_admin_referer('wuchaiwp_enterprise_contact', 'wuchaiwp_contact_nonce')) {
            // 处理自定义字段
            $custom_fields = array();
            if (isset($_POST['custom_field_labels']) && is_array($_POST['custom_field_labels'])) {
                foreach ($_POST['custom_field_labels'] as $index => $label) {
                    $label = sanitize_text_field($label);
                    $icon = sanitize_text_field($_POST['custom_field_icons'][$index] ?? '');
                    $value = sanitize_text_field($_POST['custom_field_values'][$index] ?? '');
                    if (!empty($label) && !empty($value)) {
                        $custom_fields[] = array(
                            'label' => $label,
                            'icon' => $icon,
                            'value' => $value
                        );
                    }
                }
            }
            
            $contact_settings = array(
                'address' => sanitize_text_field($_POST['address']),
                'phone' => sanitize_text_field($_POST['phone']),
                'email' => sanitize_email($_POST['email']),
                'work_time' => sanitize_text_field($_POST['work_time']),
                'contact_form_shortcode' => sanitize_text_field($_POST['contact_form_shortcode']),
                'show_contact_form' => isset($_POST['show_contact_form']) ? '1' : '0',
                'custom_fields' => $custom_fields
            );
            
            update_option('wuchaiwp_enterprise_contact_settings', $contact_settings);
            
            // 保存标题设置
            $this->update_titles_settings('contact', $_POST);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'contact'), admin_url('admin.php')));
            exit;
        }
        
        if (isset($_POST['submit_categories']) && check_admin_referer('wuchaiwp_enterprise_categories', 'wuchaiwp_categories_nonce')) {
            $category_settings = array(
                'news_categories' => isset($_POST['news_categories']) ? array_map('intval', $_POST['news_categories']) : array(),
                'product_categories' => isset($_POST['product_categories']) ? array_map('intval', $_POST['product_categories']) : array(),
                'case_categories' => isset($_POST['case_categories']) ? array_map('intval', $_POST['case_categories']) : array(),
                'news_post_type' => sanitize_text_field($_POST['news_post_type']),
                'product_post_type' => sanitize_text_field($_POST['product_post_type']),
                'case_post_type' => sanitize_text_field($_POST['case_post_type'])
            );
            
            update_option('wuchaiwp_enterprise_category_settings', $category_settings);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'categories'), admin_url('admin.php')));
            exit;
        }
        
        // 处理归档页标题设置提交
        if (isset($_POST['submit_titles']) && check_admin_referer('wuchaiwp_enterprise_titles', 'wuchaiwp_titles_nonce')) {
            $titles_settings = array(
                'about_title' => sanitize_text_field($_POST['about_title']),
                'about_subtitle' => sanitize_text_field($_POST['about_subtitle']),
                'products_title' => sanitize_text_field($_POST['products_title']),
                'products_subtitle' => sanitize_text_field($_POST['products_subtitle']),
                'news_title' => sanitize_text_field($_POST['news_title']),
                'news_subtitle' => sanitize_text_field($_POST['news_subtitle']),
                'cases_title' => sanitize_text_field($_POST['cases_title']),
                'cases_subtitle' => sanitize_text_field($_POST['cases_subtitle']),
                'contact_title' => sanitize_text_field($_POST['contact_title']),
                'contact_subtitle' => sanitize_text_field($_POST['contact_subtitle']),
                'announcement_title' => sanitize_text_field($_POST['announcement_title']),
                'categories_title' => sanitize_text_field($_POST['categories_title'])
            );
            
            update_option('wuchaiwp_enterprise_titles_settings', $titles_settings);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => 'titles'), admin_url('admin.php')));
            exit;
        }
        
        // 处理通用区域标题设置提交
        if (isset($_POST['submit_section_title']) && check_admin_referer('wuchaiwp_enterprise_section_title', 'wuchaiwp_section_title_nonce')) {
            $section = sanitize_text_field($_POST['section']);
            $this->update_titles_settings($section, $_POST);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $section), admin_url('admin.php')));
            exit;
        }
        
        // 处理自定义产品介绍设置提交
        if (isset($_POST['submit_products_settings']) && check_admin_referer('wuchaiwp_enterprise_custom_products', 'wuchaiwp_custom_products_nonce')) {
            $section = sanitize_text_field($_POST['section']);
            
            $products_settings = array(
                'excerpt_length' => intval($_POST['excerpt_length']),
                'show_read_more' => isset($_POST['show_read_more']) ? '1' : '0',
                'read_more_text' => sanitize_text_field($_POST['read_more_text']),
                'image_autoplay' => isset($_POST['image_autoplay']) ? '1' : '0',
                'image_interval' => intval($_POST['image_interval'])
            );
            
            update_option('wuchaiwp_enterprise_' . $section . '_products_settings', $products_settings);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $section), admin_url('admin.php')));
            exit;
        }
        
        // 处理自定义案例参考设置提交
        if (isset($_POST['submit_cases_settings']) && check_admin_referer('wuchaiwp_enterprise_custom_cases', 'wuchaiwp_custom_cases_nonce')) {
            $section = sanitize_text_field($_POST['section']);
            
            $cases_settings = array(
                'layout' => sanitize_text_field($_POST['cases_layout']),
                'posts_per_page' => intval($_POST['cases_posts_per_page']),
                'excerpt_length' => intval($_POST['cases_excerpt_length'])
            );
            
            update_option('wuchaiwp_enterprise_' . $section . '_cases_settings', $cases_settings);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $section), admin_url('admin.php')));
            exit;
        }
        
        // 处理自定义产品分类筛选设置提交
        if (isset($_POST['submit_product_categories']) && check_admin_referer('wuchaiwp_enterprise_custom_product_categories', 'wuchaiwp_custom_product_categories_nonce')) {
            $section = sanitize_text_field($_POST['section']);
            
            $category_settings = array(
                'post_type' => sanitize_text_field($_POST['post_type']),
                'categories' => isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array()
            );
            
            update_option('wuchaiwp_enterprise_' . $section . '_category_settings', $category_settings);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $section), admin_url('admin.php')));
            exit;
        }
        
        // 处理自定义案例分类筛选设置提交
        if (isset($_POST['submit_case_categories']) && check_admin_referer('wuchaiwp_enterprise_custom_case_categories', 'wuchaiwp_custom_case_categories_nonce')) {
            $section = sanitize_text_field($_POST['section']);
            
            $category_settings = array(
                'post_type' => sanitize_text_field($_POST['post_type']),
                'categories' => isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array()
            );
            
            update_option('wuchaiwp_enterprise_' . $section . '_category_settings', $category_settings);
            
            wp_redirect(add_query_arg(array('page' => 'wuchaiwp-enterprise-manager', 'tab' => $section), admin_url('admin.php')));
            exit;
        }
    }
    
    // 自定义区域设置页面（处理用户动态添加的区域）
    private function render_custom_section_settings($section_id) {
        $sections = $this->get_sections();
        $section_info = null;
        
        foreach ($sections as $section) {
            if ($section['id'] === $section_id) {
                $section_info = $section;
                break;
            }
        }
        
        if (!$section_info) {
            echo '<div class="wuchaiwp-form-section"><p>区域不存在或已被删除</p></div>';
            return;
        }
        
        $section_type = $section_info['type'];
        $section_name = $section_info['name'];
        $section_icon = $section_info['icon'];
        
        // 根据区域类型设置不同的配置
        $type_labels = array(
            'products' => '产品展示',
            'news' => '新闻列表',
            'cases' => '案例展示',
            'about' => '企业简介',
            'news_dynamic' => '新闻动态',
            'contact' => '联系表单',
            'custom' => '自定义HTML',
            'banner' => '横幅广告',
            'stats' => '数据统计',
            'team' => '团队介绍',
            'partners' => '合作伙伴',
            'services' => '服务项目',
            'faq' => '常见问题'
        );
        
        $type_label = isset($type_labels[$section_type]) ? $type_labels[$section_type] : $section_type;
        
        // 获取该区域的推荐文章
        $recommended_ids = $this->get_recommended_posts($section_id);
        $max_posts = $section_type === 'announcement' ? 1 : ($section_type === 'cases' ? 999 : 6);
        
        // 获取产品设置（用于产品介绍类型）
        $products_settings = get_option('wuchaiwp_enterprise_' . $section_id . '_settings', array(
            'excerpt_length' => '500',
            'show_read_more' => '1',
            'read_more_text' => '查看更多',
            'image_autoplay' => '1',
            'image_interval' => '5000'
        ));
        
        // 获取案例设置
       // 获取案例设置
$cases_settings = get_option('wuchaiwp_enterprise_' . $section_id . '_cases_settings', array(
    'layout' => 'grid',
    'posts_per_page' => '18',
    'excerpt_length' => '15'
));
        
        ?> 
        <div class="wuchaiwp-form-section">
            <h3><?php echo $section_icon; ?> <?php echo $section_name; ?></h3>
            <p class="description">配置「<?php echo $section_name; ?>」区域的显示内容（类型：<?php echo $type_label; ?>）</p>
            
            <!-- 标题设置 -->
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                <form method="post" action="" class="wuchaiwp-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_section_title', 'wuchaiwp_section_title_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                    <h4>📝 区域标题设置</h4>
                    
                    <div class="form-row">
                        <label>区域标题</label>
                        <input type="text" name="section_title" value="<?php 
                            $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
                            echo esc_attr($titles_settings[$section_id . '_title'] ?? ''); 
                        ?>" class="regular-text">
                    </div>
                    
                    <div class="form-row">
                        <label>区域副标题</label>
                        <input type="text" name="section_subtitle" value="<?php 
                            $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
                            echo esc_attr($titles_settings[$section_id . '_subtitle'] ?? ''); 
                        ?>" class="regular-text">
                    </div>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存标题设置', 'primary', 'submit_section_title'); ?>
                    </div>
                </form>
            </div>
            
            <!-- 产品介绍特有设置 -->
            <?php if ($section_type === 'products') : ?>
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                <form method="post" action="" class="wuchaiwp-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_custom_products', 'wuchaiwp_custom_products_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                    <h4>📦 内容显示设置</h4>
                    
                    <div class="form-row">
                        <label>内容截取长度（字符）</label>
                        <input type="number" name="excerpt_length" value="<?php echo esc_attr($products_settings['excerpt_length']); ?>" min="100" max="2000" step="50">
                    </div>
                    <div class="form-row">
                        <label>
                            <input type="checkbox" name="show_read_more" value="1" <?php checked($products_settings['show_read_more'], '1'); ?>>
                            显示"查看更多"按钮
                        </label>
                    </div>
                    <div class="form-row">
                        <label>"查看更多"按钮文字</label>
                        <input type="text" name="read_more_text" value="<?php echo esc_attr($products_settings['read_more_text']); ?>" class="regular-text">
                    </div>
                    
                    <h4 style="margin: 20px 0 15px 0;">🖼️ 图片轮播设置</h4>
                    <div class="form-row">
                        <label>
                            <input type="checkbox" name="image_autoplay" value="1" <?php checked($products_settings['image_autoplay'], '1'); ?>>
                            图片自动播放
                        </label>
                    </div>
                    <div class="form-row">
                        <label>自动播放间隔（毫秒）</label>
                        <input type="number" name="image_interval" value="<?php echo esc_attr($products_settings['image_interval']); ?>" min="2000" max="10000" step="500">
                    </div>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存产品设置', 'primary', 'submit_products_settings'); ?>
                    </div>
                </form>
            </div>
            
            <!-- 产品分类筛选设置 -->
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                <form method="post" action="" class="wuchaiwp-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_custom_product_categories', 'wuchaiwp_custom_product_categories_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                    <h4>📁 分类筛选设置</h4>
                    
                    <div class="form-row">
                        <label>选择文章类型</label>
                        <select name="post_type" id="post_type_<?php echo $section_id; ?>_cat" class="post-type-select" style="width: 100%;">
                            <?php 
                            $all_post_types = get_post_types(array('public' => true), 'objects');
                            unset($all_post_types['attachment']);
                            $cat_settings = get_option('wuchaiwp_enterprise_' . $section_id . '_category_settings', array());
                            foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php selected($cat_settings['post_type'] ?? 'product', $slug); ?>>
                                    <?php echo $obj->labels->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>选择要显示的分类</label>
                        <select name="categories[]" id="categories_<?php echo $section_id; ?>_cat" multiple size="8" style="width: 100%;">
                            <?php $this->render_taxonomy_options($cat_settings['post_type'] ?? 'product', $cat_settings['categories'] ?? array()); ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选，不选择则显示所有分类</p>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存分类设置', 'primary', 'submit_product_categories'); ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- 案例参考特有设置 -->
            <?php if ($section_type === 'cases') : ?>
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                <form method="post" action="" class="wuchaiwp-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_custom_cases', 'wuchaiwp_custom_cases_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                    <h4>🎨 展示布局设置</h4>
                    
                    <div class="form-row">
                        <label>选择展示布局</label>
                        <select name="cases_layout" style="min-width: 200px;">
                            <option value="grid" <?php selected($cases_settings['layout'] ?? 'grid', 'grid'); ?>>网格布局（多列卡片）</option>
                            <option value="list" <?php selected($cases_settings['layout'] ?? 'grid', 'list'); ?>>列表布局（垂直排列）</option>
                            <option value="card" <?php selected($cases_settings['layout'] ?? 'grid', 'card'); ?>>卡片布局（大图展示）</option>
                            <option value="simple" <?php selected($cases_settings['layout'] ?? 'grid', 'simple'); ?>>简约布局（无封面）</option>
                            <option value="minimal" <?php selected($cases_settings['layout'] ?? 'grid', 'minimal'); ?>>极简布局（仅标题）</option>
                            <option value="lightbox" <?php selected($cases_settings['layout'] ?? 'grid', 'lightbox'); ?>>灯箱网格（点击放大）</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>每页显示数量</label>
                        <input type="number" name="cases_posts_per_page" value="<?php echo esc_attr($cases_settings['posts_per_page'] ?? 18); ?>" min="6" max="50" step="6">
                    </div>
                    <div class="form-row">
                        <label>摘要截取长度（字数）</label>
                        <input type="number" name="cases_excerpt_length" value="<?php echo esc_attr($cases_settings['excerpt_length'] ?? 15); ?>" min="5" max="50" step="1">
                    </div>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存案例设置', 'primary', 'submit_cases_settings'); ?>
                    </div>
                </form>
            </div>
            
            <!-- 案例分类筛选设置 -->
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #f9f9f9;">
                <form method="post" action="" class="wuchaiwp-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_custom_case_categories', 'wuchaiwp_custom_case_categories_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                    <h4>📁 分类筛选设置</h4>
                    
                    <div class="form-row">
                        <label>选择文章类型</label>
                        <select name="post_type" id="post_type_<?php echo $section_id; ?>_cat" class="post-type-select" style="width: 100%;">
                            <?php 
                            $all_post_types = get_post_types(array('public' => true), 'objects');
                            unset($all_post_types['attachment']);
                            $cat_settings = get_option('wuchaiwp_enterprise_' . $section_id . '_category_settings', array());
                            foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php selected($cat_settings['post_type'] ?? 'case', $slug); ?>>
                                    <?php echo $obj->labels->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>选择要显示的分类</label>
                        <select name="categories[]" id="categories_<?php echo $section_id; ?>_cat" multiple size="8" style="width: 100%;">
                            <?php $this->render_taxonomy_options($cat_settings['post_type'] ?? 'case', $cat_settings['categories'] ?? array()); ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选，不选择则显示所有分类</p>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存分类设置', 'primary', 'submit_case_categories'); ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- 显示模式设置 -->
            <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; background: #fffbeb;">
                <form method="post" action="" class="display-settings-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_display', 'wuchaiwp_display_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                    <h4>📋 显示设置</h4>
                    
                    <div class="form-row">
                        <label>选择文章类型（可多选）</label>
                        <select name="auto_post_types[]" id="auto_post_types_<?php echo $section_id; ?>" class="auto-post-types-select" multiple size="4">
                            <?php 
                            $all_post_types = get_post_types(array('public' => true), 'objects');
                            unset($all_post_types['attachment']);
                            $display_settings = get_option('wuchaiwp_enterprise_display_settings', array());
                            $selected_post_types = !empty($display_settings[$section_id . '_post_types']) ? $display_settings[$section_id . '_post_types'] : array();
                            
                            foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>" <?php echo in_array($slug, $selected_post_types) ? 'selected' : ''; ?>><?php echo $obj->labels->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>选择分类（可多选，不选则显示全部）</label>
                        <select name="auto_categories[]" id="auto_categories_<?php echo $section_id; ?>" class="auto-categories-select" multiple size="5">
                            <?php 
                            $selected_categories = !empty($display_settings[$section_id . '_categories']) ? $display_settings[$section_id . '_categories'] : array();
                            
                            if (!empty($selected_post_types)) : 
                                foreach ($selected_post_types as $post_type) : 
                                    $taxonomies = get_object_taxonomies($post_type, 'objects');
                                    foreach ($taxonomies as $taxonomy) : 
                                        if ($taxonomy->hierarchical) : 
                                            $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => false));
                                            if (!empty($terms)) : ?>
                                                <optgroup label="<?php echo esc_attr($taxonomy->labels->name); ?>">
                                                    <?php foreach ($terms as $term) : ?>
                                                        <option value="<?php echo $term->term_id; ?>" <?php echo in_array($term->term_id, $selected_categories) ? 'selected' : ''; ?>><?php echo esc_html($term->name . ' (' . $term->slug . ')'); ?></option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endif; 
                                        endif; 
                                    endforeach; 
                                endforeach; 
                            else : ?>
                                <option value="">请先选择文章类型</option>
                            <?php endif; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 15px;">
                        <?php submit_button('保存显示设置', 'primary', 'submit_display_settings'); ?>
                    </div>
                </form>
            </div>
            
            <!-- 推荐内容管理 -->
            <div class="recommend-section">
                <h4>⭐ 推荐内容（手动选择展示的文章）</h4>
                <p class="description">在此选择要推荐展示的文章，最多显示 <?php echo $max_posts; ?> 条</p>
                
                <?php if (!empty($recommended_ids)) : ?>
                <div class="recommended-list">
                    <?php foreach ($recommended_ids as $post_id) : 
                        $post = get_post($post_id);
                        if ($post) : ?>
                            <div class="recommended-item">
                                <span class="post-title"><?php echo get_the_title($post_id); ?></span>
                                <span class="post-type"><?php echo get_post_type_object($post->post_type)->labels->singular_name; ?></span>
                                <button class="remove-btn" data-post-id="<?php echo $post_id; ?>" data-section="<?php echo $section_id; ?>">移除</button>
                            </div>
                        <?php endif; 
                    endforeach; ?>
                </div>
                
                <!-- 添加推荐内容表单 -->
<div class="add-recommend">
    <h4>添加推荐内容</h4>
    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="recommend-form">
        <?php wp_nonce_field('wuchaiwp_enterprise_recommend', 'wuchaiwp_enterprise_nonce'); ?>
        <input type="hidden" name="section" value="<?php echo $section_id; ?>">
        
        <!-- 添加移除按钮的JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-btn[data-section="<?php echo $section_id; ?>"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var postId = this.getAttribute('data-post-id');
                    var section = this.getAttribute('data-section');
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            location.reload();
                        }
                    };
                    xhr.send('action=wuchaiwp_remove_recommend&post_id=' + postId + '&section=' + section);
                });
            });
        });
        </script>
                
                <?php endif; ?>
            </div>
            
            <!-- 添加推荐内容表单 -->
            <div class="add-recommend">
                <h4>添加推荐内容</h4>
                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="recommend-form">
                    <?php wp_nonce_field('wuchaiwp_enterprise_recommend', 'wuchaiwp_enterprise_nonce'); ?>
                    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
                    
                   
                                       <?php $all_post_types = get_post_types(array('public' => true), 'objects'); ?>
                    
                    <div class="form-row">
                        <label>选择文章类型（可多选）</label>
                        <select name="post_types[]" id="post_types_<?php echo $section_id; ?>" class="post-types-select" multiple size="5">
                            <?php foreach ($all_post_types as $slug => $obj) : ?>
                                <option value="<?php echo $slug; ?>"><?php echo $obj->labels->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>选择分类（可多选，不选则显示全部）</label>
                        <select name="categories[]" id="categories_<?php echo $section_id; ?>" class="categories-select" multiple size="6" disabled>
                            <option value="">请先选择文章类型</option>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-row">
                        <label>搜索文章</label>
                   
                        <input type="text" name="search_term" id="search_<?php echo $section_id; ?>" placeholder="输入文章标题搜索..." class="search-input">
                        <button type="button" class="search-btn" data-section="<?php echo $section_id; ?>">搜索</button>
                    </div>
                    
                    <div class="form-row">
                        <label>选择文章</label>
                        <select name="post_id[]" id="post_select_<?php echo $section_id; ?>" class="post-select" multiple size="8">
                            <option value="">请先选择文章类型并搜索</option>
                        </select>
                        <p class="help">按住 Ctrl 键可多选</p>
                    </div>
                    
                    <div class="form-actions">
                        <?php submit_button('添加到推荐', 'primary', 'submit_recommend'); ?>
                    </div>
                </form>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var section = '<?php echo $section_id; ?>';
                
                // 显示模式设置：文章类型变化时更新分类选项
                document.getElementById('auto_post_types_' + section).addEventListener('change', function() {
                    var postTypes = Array.from(this.selectedOptions).map(opt => opt.value);
                    var categoriesSelect = document.getElementById('auto_categories_' + section);
                    
                    if (postTypes.length > 0) {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                categoriesSelect.innerHTML = xhr.responseText;
                                categoriesSelect.disabled = false;
                            }
                        };
                        xhr.send('action=wuchaiwp_get_taxonomy_terms&post_types=' + encodeURIComponent(JSON.stringify(postTypes)));
                    } else {
                        categoriesSelect.disabled = true;
                        categoriesSelect.innerHTML = '<option value="">请先选择文章类型</option>';
                    }
                });
                
                // 推荐机制：文章类型变化时更新分类选项（修复：添加这部分代码）
                var recommendPostTypesSelect = document.getElementById('post_types_' + section);
                var recommendCategoriesSelect = document.getElementById('categories_' + section);
                
                if (recommendPostTypesSelect && recommendCategoriesSelect) {
                    recommendPostTypesSelect.addEventListener('change', function() {
                        var postTypes = Array.from(this.selectedOptions).map(opt => opt.value);
                        
                        if (postTypes.length > 0) {
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    recommendCategoriesSelect.innerHTML = xhr.responseText;
                                    recommendCategoriesSelect.disabled = false;
                                }
                            };
                            xhr.send('action=wuchaiwp_get_taxonomy_terms&post_types=' + encodeURIComponent(JSON.stringify(postTypes)));
                        } else {
                            recommendCategoriesSelect.disabled = true;
                            recommendCategoriesSelect.innerHTML = '<option value="">请先选择文章类型</option>';
                        }
                    });
                }
                
                // 分类筛选设置中的文章类型变化时更新分类选项
                var catPostTypeSelect = document.getElementById('post_type_' + section + '_cat');
                var catCategoriesSelect = document.getElementById('categories_' + section + '_cat');
                
                if (catPostTypeSelect && catCategoriesSelect) {
                    catPostTypeSelect.addEventListener('change', function() {
                        var postType = this.value;
                        
                        if (postType) {
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    catCategoriesSelect.innerHTML = xhr.responseText;
                                    catCategoriesSelect.disabled = false;
                                }
                            };
                            xhr.send('action=wuchaiwp_get_taxonomy_terms&post_types=' + encodeURIComponent(JSON.stringify([postType])));
                        } else {
                            catCategoriesSelect.disabled = true;
                            catCategoriesSelect.innerHTML = '<option value="">请先选择文章类型</option>';
                        }
                    });
                }
                
                // 搜索按钮点击事件（推荐机制）
                document.querySelector('.search-btn[data-section="' + section + '"]').addEventListener('click', function() {
                    var postTypes = recommendPostTypesSelect ? Array.from(recommendPostTypesSelect.selectedOptions).map(opt => opt.value) : [];
                    var categories = recommendCategoriesSelect ? Array.from(recommendCategoriesSelect.selectedOptions).map(opt => opt.value) : [];
                    var searchTerm = document.getElementById('search_' + section).value;
                    
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('post_select_' + section).innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send('action=wuchaiwp_search_posts_multi&post_types=' + encodeURIComponent(JSON.stringify(postTypes)) + '&categories=' + encodeURIComponent(JSON.stringify(categories)) + '&search=' + encodeURIComponent(searchTerm));
                });
                
                document.querySelectorAll('.remove-btn[data-section="' + section + '"]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var postId = this.getAttribute('data-post-id');
                        var section = this.getAttribute('data-section');
                        
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                location.reload();
                            }
                        };
                        xhr.send('action=wuchaiwp_remove_recommend&post_id=' + postId + '&section=' + section);
                    });
                });
            });
            </script>
        </div>
        <?php
    }
    
    // 更新标题设置的辅助方法
    private function update_titles_settings($section, $post_data) {
        $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
        
        if (isset($post_data['section_title'])) {
            $titles_settings[$section . '_title'] = sanitize_text_field($post_data['section_title']);
        }
        if (isset($post_data['section_subtitle'])) {
            $titles_settings[$section . '_subtitle'] = sanitize_text_field($post_data['section_subtitle']);
        }
        if (isset($post_data[$section . '_title'])) {
            $titles_settings[$section . '_title'] = sanitize_text_field($post_data[$section . '_title']);
        }
        if (isset($post_data[$section . '_subtitle'])) {
            $titles_settings[$section . '_subtitle'] = sanitize_text_field($post_data[$section . '_subtitle']);
        }
        
        update_option('wuchaiwp_enterprise_titles_settings', $titles_settings);
    }
    
    // 归档页标题设置页面
    private function render_titles_section() {
        $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array(
            'about_title' => '🏢 企业简介',
            'about_subtitle' => '关于我们的故事',
            'products_title' => '📦 产品介绍',
            'products_subtitle' => '我们的核心产品',
            'news_title' => '📝 开发日志',
            'news_subtitle' => '最新动态与更新',
            'cases_title' => '💼 案例参考',
            'cases_subtitle' => '成功案例展示',
            'contact_title' => '📞 联系我们',
            'contact_subtitle' => '期待与您合作',
            'announcement_title' => '🌟 最新公告',
            'categories_title' => '📁 分类'
        ));
        
        ?>
        <div class="wuchaiwp-form-section">
            <h3>📝 归档页标题设置</h3>
            <p class="description">管理企业官网归档页各区域的标题和副标题</p>
            
            <form method="post" action="" class="wuchaiwp-form">
                <?php wp_nonce_field('wuchaiwp_enterprise_titles', 'wuchaiwp_titles_nonce'); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">企业简介区域</th>
                            <td>
                                <input type="text" name="about_title" value="<?php echo esc_attr($titles_settings['about_title']); ?>" class="regular-text" placeholder="标题">
                                <input type="text" name="about_subtitle" value="<?php echo esc_attr($titles_settings['about_subtitle']); ?>" class="regular-text" placeholder="副标题">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">产品介绍区域</th>
                            <td>
                                <input type="text" name="products_title" value="<?php echo esc_attr($titles_settings['products_title']); ?>" class="regular-text" placeholder="标题">
                                <input type="text" name="products_subtitle" value="<?php echo esc_attr($titles_settings['products_subtitle']); ?>" class="regular-text" placeholder="副标题">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">开发日志区域</th>
                            <td>
                                <input type="text" name="news_title" value="<?php echo esc_attr($titles_settings['news_title']); ?>" class="regular-text" placeholder="标题">
                                <input type="text" name="news_subtitle" value="<?php echo esc_attr($titles_settings['news_subtitle']); ?>" class="regular-text" placeholder="副标题">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">案例参考区域</th>
                            <td>
                                <input type="text" name="cases_title" value="<?php echo esc_attr($titles_settings['cases_title']); ?>" class="regular-text" placeholder="标题">
                                <input type="text" name="cases_subtitle" value="<?php echo esc_attr($titles_settings['cases_subtitle']); ?>" class="regular-text" placeholder="副标题">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">联系我们区域</th>
                            <td>
                                <input type="text" name="contact_title" value="<?php echo esc_attr($titles_settings['contact_title']); ?>" class="regular-text" placeholder="标题">
                                <input type="text" name="contact_subtitle" value="<?php echo esc_attr($titles_settings['contact_subtitle']); ?>" class="regular-text" placeholder="副标题">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">最新公告区块</th>
                            <td>
                                <input type="text" name="announcement_title" value="<?php echo esc_attr($titles_settings['announcement_title']); ?>" class="regular-text" placeholder="标题">
                            </td>
                        </tr>
                        <tr>
                            <!--<th scope="row">分类区块</th>
                            <td>
                                <input type="text" name="categories_title" value="<?php echo esc_attr($titles_settings['categories_title']); ?>" class="regular-text" placeholder="标题">
                            </td>-->
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit_titles" class="button button-primary" value="保存设置">
                </p>
            </form>
        </div>
        <?php
    }
    
    private function get_section_order($section_id, $sections) {
        foreach ($sections as $section) {
            if ($section['id'] === $section_id) {
                return $section['order'];
            }
        }
        return count($sections) + 1;
    }
    
    private function get_recommended_posts($section) {
        return get_option('wuchaiwp_enterprise_' . $section . '_posts', array());
    }
    
    private function get_post_types() {
        $post_types = array();
        $args = array(
            'public' => true,
            '_builtin' => false
        );
        
        $custom_post_types = get_post_types($args, 'objects');
        foreach ($custom_post_types as $post_type) {
            $post_types[$post_type->name] = $post_type->labels->name . ' (自定义)';
        }
        
        $post_types['post'] = '文章 (默认)';
        $post_types['page'] = '页面 (默认)';
        
        return $post_types;
    }
    
    private function get_post_type_label($post_type) {
        $post_type_obj = get_post_type_object($post_type);
        return $post_type_obj ? $post_type_obj->labels->singular_name : $post_type;
    }
}

// AJAX 搜索文章
function wuchaiwp_search_posts_ajax() {
    $post_type = sanitize_text_field($_POST['post_type']);
    $search = sanitize_text_field($_POST['search']);
    
    $args = array(
        'post_type' => $post_type,
        's' => $search,
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    
    $posts = get_posts($args);
    
    foreach ($posts as $post) {
        echo '<option value="' . $post->ID . '">' . get_the_title($post->ID) . '</option>';
    }
    
    wp_die();
}
add_action('wp_ajax_wuchaiwp_search_posts', 'wuchaiwp_search_posts_ajax');

// AJAX 获取分类选项（支持多选文章类型）
function wuchaiwp_get_taxonomy_terms_ajax() {
    $post_types = json_decode(stripslashes($_POST['post_types']), true);
    
    if (!is_array($post_types) || empty($post_types)) {
        echo '<option value="">请选择文章类型</option>';
        wp_die();
    }
    
    // 获取所有层级分类
    $all_terms = array();
    foreach ($post_types as $post_type) {
        $taxonomies = get_object_taxonomies($post_type, 'objects');
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy->hierarchical) {
                $terms = get_terms(array(
                    'taxonomy' => $taxonomy->name,
                    'hide_empty' => false
                ));
                foreach ($terms as $term) {
                    $all_terms[$term->term_id] = array(
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'term_id' => $term->term_id,
                        'taxonomy' => $taxonomy->name,
                        'taxonomy_label' => $taxonomy->labels->name
                    );
                }
            }
        }
    }
    
    // 按分类法分组显示
    $grouped_terms = array();
    foreach ($all_terms as $term) {
        if (!isset($grouped_terms[$term['taxonomy_label']])) {
            $grouped_terms[$term['taxonomy_label']] = array();
        }
        $grouped_terms[$term['taxonomy_label']][] = $term;
    }
    
    echo '<option value="">全部分类</option>';
    foreach ($grouped_terms as $tax_label => $terms) {
        echo '<optgroup label="' . esc_attr($tax_label) . '">';
        foreach ($terms as $term) {
            echo '<option value="' . $term['term_id'] . '">' . esc_html($term['name'] . ' (' . $term['slug'] . ')') . '</option>';
        }
        echo '</optgroup>';
    }
    
    wp_die();
}
add_action('wp_ajax_wuchaiwp_get_taxonomy_terms', 'wuchaiwp_get_taxonomy_terms_ajax');

// AJAX 搜索文章（支持多选文章类型和分类）
function wuchaiwp_search_posts_multi_ajax() {
    $post_types = json_decode(stripslashes($_POST['post_types']), true);
    $categories = json_decode(stripslashes($_POST['categories']), true);
    $search = sanitize_text_field($_POST['search']);
    
    if (!is_array($post_types) || empty($post_types)) {
        echo '<option value="">请选择文章类型</option>';
        wp_die();
    }
    
    $args = array(
        'post_type' => $post_types,
        's' => $search,
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    
    // 添加分类筛选
    if (is_array($categories) && !empty($categories) && !in_array('', $categories)) {
        $args['tax_query'] = array('relation' => 'OR');
        
        foreach ($post_types as $post_type) {
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->hierarchical) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $taxonomy->name,
                        'field' => 'term_id',
                        'terms' => $categories
                    );
                }
            }
        }
    }
    
    $posts = get_posts($args);
    
    if (empty($posts)) {
        echo '<option value="">没有找到匹配的文章</option>';
    } else {
        foreach ($posts as $post) {
            echo '<option value="' . $post->ID . '">' . get_the_title($post->ID) . ' (' . get_post_type_object($post->post_type)->labels->singular_name . ')</option>';
        }
    }
    
    wp_die();
}
add_action('wp_ajax_wuchaiwp_search_posts_multi', 'wuchaiwp_search_posts_multi_ajax');

// AJAX 移除推荐
function wuchaiwp_remove_recommend_ajax() {
    $post_id = intval($_POST['post_id']);
    $section = sanitize_text_field($_POST['section']);
    
    $current_ids = get_option('wuchaiwp_enterprise_' . $section . '_posts', array());
    $current_ids = array_diff($current_ids, array($post_id));
    
    update_option('wuchaiwp_enterprise_' . $section . '_posts', $current_ids);
    
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_remove_recommend', 'wuchaiwp_remove_recommend_ajax');

// AJAX 获取区域信息
function wuchaiwp_get_section_ajax() {
    $section_id = sanitize_text_field($_POST['section_id']);
    $sections = get_option('wuchaiwp_enterprise_sections', array());
    
    foreach ($sections as $section) {
        if ($section['id'] === $section_id) {
            echo json_encode($section);
            wp_die();
        }
    }
    
    echo json_encode(array());
    wp_die();
}
add_action('wp_ajax_wuchaiwp_get_section', 'wuchaiwp_get_section_ajax');

// AJAX 切换区域状态
function wuchaiwp_toggle_section_ajax() {
    $section_id = sanitize_text_field($_POST['section_id']);
    $sections = get_option('wuchaiwp_enterprise_sections', array());
    
    foreach ($sections as &$section) {
        if ($section['id'] === $section_id) {
            $section['status'] = $section['status'] === 'active' ? 'hidden' : 'active';
            break;
        }
    }
    
    update_option('wuchaiwp_enterprise_sections', $sections);
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_toggle_section', 'wuchaiwp_toggle_section_ajax');

// AJAX 移动到回收站
function wuchaiwp_move_to_trash_ajax() {
    $section_id = sanitize_text_field($_POST['section_id']);
    $sections = get_option('wuchaiwp_enterprise_sections', array());
    $trash = get_option('wuchaiwp_enterprise_sections_trash', array());
    
    foreach ($sections as $key => $section) {
        if ($section['id'] === $section_id) {
            $section['deleted_at'] = date('Y-m-d H:i:s');
            $trash[] = $section;
            unset($sections[$key]);
            break;
        }
    }
    
    update_option('wuchaiwp_enterprise_sections', array_values($sections));
    update_option('wuchaiwp_enterprise_sections_trash', $trash);
    
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_move_to_trash', 'wuchaiwp_move_to_trash_ajax');

// AJAX 保存排序
function wuchaiwp_save_section_order_ajax() {
    $order = json_decode(stripslashes($_POST['order']), true);
    $sections = get_option('wuchaiwp_enterprise_sections', array());
    
    $ordered_sections = array();
    foreach ($order as $section_id) {
        foreach ($sections as $section) {
            if ($section['id'] === $section_id) {
                $section['order'] = count($ordered_sections) + 1;
                $ordered_sections[] = $section;
                break;
            }
        }
    }
    
    update_option('wuchaiwp_enterprise_sections', $ordered_sections);
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_save_section_order', 'wuchaiwp_save_section_order_ajax');

// AJAX 恢复区域
function wuchaiwp_restore_section_ajax() {
    $section_id = sanitize_text_field($_POST['section_id']);
    $sections = get_option('wuchaiwp_enterprise_sections', array());
    $trash = get_option('wuchaiwp_enterprise_sections_trash', array());
    
    foreach ($trash as $key => $section) {
        if ($section['id'] === $section_id) {
            $section['status'] = 'active';
            $section['order'] = count($sections) + 1;
            unset($section['deleted_at']);
            $sections[] = $section;
            unset($trash[$key]);
            break;
        }
    }
    
    update_option('wuchaiwp_enterprise_sections', $sections);
    update_option('wuchaiwp_enterprise_sections_trash', array_values($trash));
    
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_restore_section', 'wuchaiwp_restore_section_ajax');

// AJAX 恢复默认区域
function wuchaiwp_restore_default_sections_ajax() {
    // 定义默认区域配置
    $default_sections = array(
        array(
            'id' => 'hero',
            'name' => 'Hero区域',
            'icon' => '🚀',
            'type' => 'hero',
            'status' => 'active',
            'order' => 1
        ),
        array(
            'id' => 'about',
            'name' => '企业简介',
            'icon' => '🏢',
            'type' => 'about',
            'status' => 'active',
            'order' => 2
        ),
        array(
            'id' => 'products',
            'name' => '产品介绍',
            'icon' => '📦',
            'type' => 'products',
            'status' => 'active',
            'order' => 3
        ),
        array(
            'id' => 'news',
            'name' => '开发日志',
            'icon' => '📝',
            'type' => 'news',
            'status' => 'active',
            'order' => 4
        ),
        array(
            'id' => 'cases',
            'name' => '案例参考',
            'icon' => '💼',
            'type' => 'cases',
            'status' => 'active',
            'order' => 5
        ),
        array(
            'id' => 'contact',
            'name' => '联系我们',
            'icon' => '📞',
            'type' => 'contact',
            'status' => 'active',
            'order' => 6
        )
    );
    
    // 更新区域配置为默认值
    update_option('wuchaiwp_enterprise_sections', $default_sections);
    // 清空回收站
    update_option('wuchaiwp_enterprise_sections_trash', array());
    
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_restore_default_sections', 'wuchaiwp_restore_default_sections_ajax');

// AJAX 永久删除
function wuchaiwp_delete_permanently_ajax() {
    $section_id = sanitize_text_field($_POST['section_id']);
    $trash = get_option('wuchaiwp_enterprise_sections_trash', array());
    
    foreach ($trash as $key => $section) {
        if ($section['id'] === $section_id) {
            unset($trash[$key]);
            break;
        }
    }
    
    update_option('wuchaiwp_enterprise_sections_trash', array_values($trash));
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_delete_permanently', 'wuchaiwp_delete_permanently_ajax');

// AJAX 清空回收站
function wuchaiwp_empty_trash_ajax() {
    update_option('wuchaiwp_enterprise_sections_trash', array());
    echo 'success';
    wp_die();
}
add_action('wp_ajax_wuchaiwp_empty_trash', 'wuchaiwp_empty_trash_ajax');

// 初始化
new Wuchaiwp_Enterprise_Manager_Settings();

// 注册表单提交处理
function wuchaiwp_enterprise_handle_form() {
    $manager = new Wuchaiwp_Enterprise_Manager_Settings();
    $manager->handle_form_submit();
}
add_action('admin_init', 'wuchaiwp_enterprise_handle_form');

/**
 * 渲染企业官网管理页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_enterprise_manager_page() {
    $settings = new Wuchaiwp_Enterprise_Manager_Settings();
    $settings->render_enterprise_manager_page();
}
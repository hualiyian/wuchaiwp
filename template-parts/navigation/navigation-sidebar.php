<?php
/**
 * 独立模块化的移动端侧边栏菜单
 * 
 * 使用方法：
 * 1. 在文章或页面中使用短代码: [wuchaiwp_sidebar_menu]
 * 2. 在模板文件中调用: <?php get_template_part('template-parts/navigation/navigation', 'sidebar'); ?>
 * 
 * 功能特点：
 * - 移动端点击汉堡菜单图标，从左侧滑出菜单
 * - 点击右上角 × 按钮或遮罩层关闭菜单
 * - 深色主题设计，紫色头部
 * - 可设置菜单按钮位置（左/中/右）
 */

// 获取菜单按钮位置设置
$menu_position = get_theme_mod('mobile_menu_position', 'center');

// 注册短代码
function wuchaiwp_sidebar_menu_shortcode() {
    global $menu_position;
    ob_start();
    ?>

<style type="text/css">
/* 移动端菜单按钮位置和样式 */
@media screen and (max-width: 767px) {
    body .main-navigation {
        text-align: <?php echo esc_js($menu_position); ?> !important;
    }
    body .main-navigation .menu-toggle {
        float: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        font-size: 0 !important;
        width: 44px !important;
        height: 44px !important;
        padding: 0 !important;
        line-height: 1 !important;
    }
    body .main-navigation .menu-toggle svg {
        font-size: 20px !important;
        display: block !important;
        width: 20px !important;
        height: 20px !important;
        color: inherit !important;
    }
    /* 默认隐藏close图标，只显示bars图标 */
    body .main-navigation .menu-toggle .icon-close {
        display: none !important;
    }
    /* 侧边栏打开时显示close图标，隐藏bars图标 */
    body.sidebar-open .main-navigation .menu-toggle .icon-bars {
        display: none !important;
    }
    body.sidebar-open .main-navigation .menu-toggle .icon-close {
        display: block !important;
    }
}
</style>

<style type="text/css">
/* 侧边栏菜单样式 */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9998;
    display: none;
}
.sidebar-overlay.active {
    display: block;
}

.sidebar-navigation {
    position: fixed;
    top: 0;
    left: -280px;
    width: 280px;
    height: 100%;
    background: #ffffff;
    z-index: 9999;
    transition: left 0.3s ease;
    overflow-y: auto;
}
.sidebar-navigation.active {
    left: 0;
}

.sidebar-header {
    padding: 15px 20px;
    background: #f5f5f5;
    text-align: right;
}

.sidebar-close {
    background: none;
    border: none;
    color: #333;
    font-size: 36px;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.sidebar-content {
    padding: 0;
}

#sidebar-menu,
#sidebar-menu ul,
#sidebar-menu li {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
}

#sidebar-menu li {
    border-bottom: 1px solid #eee !important;
}

#sidebar-menu a {
    display: block !important;
    padding: 15px 20px !important;
    text-decoration: none !important;
    color: #333 !important;
    background: none !important;
}

#sidebar-menu a:hover {
    background: #f5f5f5 !important;
}

#sidebar-menu ul {
    background: #fafafa !important;
    display: none !important; /* 默认隐藏子菜单 */
}
#sidebar-menu ul.toggled-on {
    display: block !important; /* 激活时显示 */
}
#sidebar-menu ul li {
    border-bottom: 1px solid #eee !important;
}
#sidebar-menu ul a {
    padding-left: 40px !important;
}
/* 子菜单箭头按钮 */
#sidebar-menu .menu-item-has-children .submenu-toggle,
#sidebar-menu .page_item_has_children .submenu-toggle {
    display: inline-block;
    float: right;
    background: none;
    border: none;
    color: #999;
    font-size: 12px;
    cursor: pointer;
    padding: 0 5px;
    line-height: 1;
}
#sidebar-menu .menu-item-has-children.toggled-on .submenu-toggle,
#sidebar-menu .page_item_has_children.toggled-on .submenu-toggle {
    transform: rotate(180deg);
}

/* 移动端隐藏默认菜单 */
@media screen and (max-width: 767px) {
    .main-navigation > div > ul {
        display: none !important;
    }
}

/* 桌面端隐藏侧边栏菜单 */
@media screen and (min-width: 768px) {
    .sidebar-overlay,
    .sidebar-navigation {
        display: none !important;
    }
    .main-navigation > div > ul {
        display: block !important;
    }
}
</style>

<!-- 侧边栏菜单遮罩层 -->
<div id="sidebar-overlay" class="sidebar-overlay"></div>

<!-- 侧边栏菜单 -->
<nav id="sidebar-navigation" class="sidebar-navigation">
    <div class="sidebar-header">
        <button id="sidebar-close" class="sidebar-close">
            <span class="screen-reader-text">Close Menu</span>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="sidebar-content">
        <?php
        wp_nav_menu(
            array(
                'theme_location' => 'top',
                'menu_id'        => 'sidebar-menu',
                'menu_class'     => '',
            )
        );
        ?>
    </div>
</nav>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    var menuToggle = document.querySelector('.menu-toggle');
    var sidebarNav = document.getElementById('sidebar-navigation');
    var sidebarOverlay = document.getElementById('sidebar-overlay');
    var sidebarClose = document.getElementById('sidebar-close');

    // 移除菜单按钮中的"Menu"文字，只保留图标
    if (menuToggle) {
        // 遍历按钮内的所有子节点
        var children = menuToggle.childNodes;
        for (var i = children.length - 1; i >= 0; i--) {
            var child = children[i];
            // 如果是文本节点（包含"Menu"文字），移除它
            if (child.nodeType === 3 && child.textContent.trim() !== '') {
                menuToggle.removeChild(child);
            }
        }
    }

    // 侧边栏打开函数
    function openSidebar() {
        sidebarNav.classList.add('active');
        sidebarOverlay.classList.add('active');
        document.body.classList.add('sidebar-open');
        if (menuToggle) {
            menuToggle.setAttribute('aria-expanded', 'true');
        }
    }

    // 侧边栏关闭函数
    function closeSidebar() {
        sidebarNav.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
        if (menuToggle) {
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    }

    // 点击汉堡菜单按钮
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebarNav.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    // 点击关闭按钮
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
    }

    // 点击遮罩层关闭
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }

    // 添加子菜单箭头按钮
    var parentItems = document.querySelectorAll('#sidebar-menu .menu-item-has-children, #sidebar-menu .page_item_has_children');
    parentItems.forEach(function(item) {
        var link = item.querySelector('a');
        if (link && !item.querySelector('.submenu-toggle')) {
            // 创建箭头按钮
            var toggleBtn = document.createElement('button');
            toggleBtn.className = 'submenu-toggle';
            toggleBtn.innerHTML = '▼';
            toggleBtn.setAttribute('aria-label', 'Toggle submenu');
            
            // 在链接后添加按钮
            link.parentNode.insertBefore(toggleBtn, link.nextSibling);
            
            // 点击箭头按钮展开/收起子菜单
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                item.classList.toggle('toggled-on');
                var submenu = item.querySelector('ul');
                if (submenu) {
                    submenu.classList.toggle('toggled-on');
                }
            });
        }
    });
});
</script>

    <?php
    return ob_get_clean();
}
add_shortcode('wuchaiwp_sidebar_menu', 'wuchaiwp_sidebar_menu_shortcode');

// 如果是直接调用模板，输出菜单
if (!defined('DOING_AJAX') && !defined('REST_REQUEST')) {
    echo wuchaiwp_sidebar_menu_shortcode();
}
?>
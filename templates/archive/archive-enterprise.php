<?php
/**
 * Template Name: 企业官网首页
 * Description: 企业官网归档页模板，包含企业简介、产品介绍、开发日志、案例参考等内容
 * 支持自定义文章类型的调用和后台设置
 */

// 获取自定义文章类型配置
function wuchaiwp_get_custom_post_types() {
    $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
    if (empty($custom_post_types)) {
        $custom_post_types = get_option('wuchai_custom_post_types', array());
    }
    return $custom_post_types;
}

// 获取指定文章类型是否存在
function wuchaiwp_post_type_exists($post_type) {
    return post_type_exists($post_type);
}

// 获取文章类型的归档链接
function wuchaiwp_get_post_type_archive_url($post_type) {
    if (post_type_exists($post_type)) {
        return get_post_type_archive_link($post_type);
    }
    return '#';
}

// 获取后台推荐的文章
function wuchaiwp_get_recommended_posts($section) {
    $post_ids = get_option('wuchaiwp_enterprise_' . $section . '_posts', array());
    if (!empty($post_ids)) {
        return get_posts(array(
            'post__in' => $post_ids,
            'post_type' => 'any',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'post__in'
        ));
    }
    return array();
}

// 获取企业简介设置
function wuchaiwp_get_about_settings() {
    return get_option('wuchaiwp_enterprise_about_settings', array(
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
}

// 获取产品介绍设置
function wuchaiwp_get_products_settings() {
    return get_option('wuchaiwp_enterprise_products_settings', array(
        'excerpt_length' => '500',
        'show_read_more' => '1',
        'read_more_text' => '查看更多',
        'image_autoplay' => '1',
        'image_interval' => '5000'
    ));
}

// 获取案例参考设置
function wuchaiwp_get_cases_settings() {
    return get_option('wuchaiwp_enterprise_cases_settings', array(
        'excerpt_length' => '15',
        'posts_per_page' => '18',
        'layout' => 'lightbox' // grid: 网格布局, list: 列表布局, card: 卡片布局, simple: 简约网格布局, minimal: 极简布局, lightbox: 灯箱网格布局
    ));
}

// 安全截断HTML内容（保留标签结构）
function wuchaiwp_truncate_html($html, $length = 500) {
    // 移除脚本和样式标签
    $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
    $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
    
    $text = '';
    $current_length = 0;
    $open_tags = array();
    
    // 使用DOM解析HTML
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $dom->preserveWhiteSpace = false;
    
    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//text()');
    
    foreach ($nodes as $node) {
        $node_value = $node->nodeValue;
        $remaining = $length - $current_length;
        
        if ($remaining <= 0) {
            break;
        }
        
        if (mb_strlen($node_value, 'UTF-8') <= $remaining) {
            $text .= $node_value;
            $current_length += mb_strlen($node_value, 'UTF-8');
        } else {
            $text .= mb_substr($node_value, 0, $remaining, 'UTF-8') . '...';
            $current_length = $length;
            break;
        }
    }
    
    return $text;
}

// 获取联系我们设置
function wuchaiwp_get_contact_settings() {
    return get_option('wuchaiwp_enterprise_contact_settings', array(
        'address' => '北京市朝阳区科技园区A座18层',
        'phone' => '400-888-8888',
        'email' => 'contact@example.com',
        'work_time' => '周一至周五 9:00-18:00',
        'contact_form_shortcode' => '[contact-form-7 id="123" title="联系表单"]',
        'show_contact_form' => '1'
    ));
}

// 获取分类设置
function wuchaiwp_get_category_settings() {
    return get_option('wuchaiwp_enterprise_category_settings', array(
        'news_categories' => array(),
        'product_categories' => array(),
        'case_categories' => array()
    ));
}

// 获取归档页标题设置
function wuchaiwp_get_titles_settings() {
    return get_option('wuchaiwp_enterprise_titles_settings', array(
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
}

// 获取区域配置
function wuchaiwp_get_sections() {
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



// 获取Hero区域设置
function wuchaiwp_get_hero_settings() {
    return get_option('wuchaiwp_enterprise_hero_settings', array(
        'title' => '',
        'description' => '',
        'button1_text' => '查看产品',
        'button1_link' => '#products',
        'button2_text' => '了解我们',
        'button2_link' => '#about',
        'background_images' => array(),
        'background_color' => '#667eea',
        'background_gradient' => '#764ba2',
        'illustration' => '🚀',
        'autoplay' => '1',
        'interval' => '5000',
        'slides' => array()
    ));
}


// 渲染Hero区域
// 渲染Hero区域
function wuchaiwp_render_hero_section() {
    $hero_settings = wuchaiwp_get_hero_settings();
    
    $global_title = !empty($hero_settings['title']) ? $hero_settings['title'] : get_bloginfo('name');
    $global_description = !empty($hero_settings['description']) ? $hero_settings['description'] : get_bloginfo('description');
    
    // 获取轮播图数据，兼容旧格式
    $slides = isset($hero_settings['slides']) && !empty($hero_settings['slides']) ? $hero_settings['slides'] : array();
    
    // 兼容旧的background_images格式
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
    
    $has_slider = count($slides) > 1;
    ?>
    <section id="hero" class="hero-section">
        <?php if ($has_slider && !empty($slides)) : ?>
            <!-- 多图轮播 -->
            <div class="hero-slider" data-autoplay="<?php echo $hero_settings['autoplay']; ?>" data-interval="<?php echo $hero_settings['interval']; ?>">
                <?php foreach ($slides as $index => $slide) : ?>
                    <div class="hero-slide" style="background-image: url('<?php echo esc_url($slide['image']); ?>');">
                        <?php if (!empty($slide['title']) || !empty($slide['description']) || !empty($slide['button_text'])) : ?>
                            <div class="hero-slide-content">
                                <?php if (!empty($slide['title'])) : ?>
                                    <h2 class="hero-slide-title"><?php echo esc_html($slide['title']); ?></h2>
                                <?php endif; ?>
                                <?php if (!empty($slide['description'])) : ?>
                                    <p class="hero-slide-description"><?php echo esc_html($slide['description']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])) : ?>
                                    <?php 
                                    $link_value = do_shortcode($slide['button_link']);
                                    // 判断是否是URL还是短代码输出（如联系表单）
                                    if (filter_var($link_value, FILTER_VALIDATE_URL) || strpos($link_value, '#') === 0) : ?>
                                        <a href="<?php echo esc_url($link_value); ?>" class="btn-slide"><?php echo esc_html($slide['button_text']); ?></a>
                                    <?php else : ?>
                                        <button class="btn-slide" onclick="openShortcodeModal('hero-slide-modal-<?php echo $index; ?>')"><?php echo esc_html($slide['button_text']); ?></button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 右侧产品叠加图 -->
                <?php if (!empty($slide['product_image'])) : ?>
                    <div class="hero-product-overlay">
                        <img src="<?php echo esc_url($slide['product_image']); ?>" alt="产品叠加图">
                    </div>
                <?php endif; ?>
                        
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="hero-overlay"></div>
        <?php else : ?>
            <!-- 单图或渐变背景 -->
            <?php
            $background_style = '';
            if (!empty($slides[0]['image'])) {
                $background_style = 'background-image: url(' . esc_url($slides[0]['image']) . '); background-size: cover; background-position: center;';
            } else {
                $background_style = 'background: linear-gradient(135deg, ' . esc_attr($hero_settings['background_color']) . ' 0%, ' . esc_attr($hero_settings['background_gradient']) . ' 100%);';
            }
            ?>
            <div class="hero-background" style="<?php echo $background_style; ?>"></div>
        <?php endif; ?>
        
        <!-- 全局内容（单图模式或轮播的默认内容） -->
        <div class="hero-content">
            <h1 class="hero-title"><?php echo esc_html($global_title); ?></h1>
            <p class="hero-description"><?php echo esc_html($global_description); ?></p>
            <div class="hero-buttons">
                <a href="<?php echo esc_url(do_shortcode($hero_settings['button1_link'])); ?>" class="btn-primary"><?php echo esc_html($hero_settings['button1_text']); ?></a>
                <a href="<?php echo esc_url(do_shortcode($hero_settings['button2_link'])); ?>" class="btn-secondary"><?php echo esc_html($hero_settings['button2_text']); ?></a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-illustration"><?php echo esc_html($hero_settings['illustration']); ?></div>
        </div>
        
        <?php if ($has_slider) : ?>
            <!-- 轮播指示器 -->
            <div class="hero-indicators">
                <?php foreach ($slides as $index => $slide) : ?>
                    <button class="hero-indicator" data-index="<?php echo $index; ?>"></button>
                <?php endforeach; ?>
            </div>
            
            <!-- 轮播控制按钮 -->
            <button class="hero-prev">‹</button>
            <button class="hero-next">›</button>
        <?php endif; ?>
    </section>
    
    <!-- 轮播图短代码弹窗（放在Hero区域外部） -->
    <?php if ($has_slider && !empty($slides)) : ?>
        <?php foreach ($slides as $index => $slide) : ?>
            <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])) : ?>
                <?php 
                $link_value = do_shortcode($slide['button_link']);
                // 只有当不是URL时才显示弹窗
                if (!filter_var($link_value, FILTER_VALIDATE_URL) && strpos($link_value, '#') !== 0) : ?>
                    <div id="hero-slide-modal-<?php echo $index; ?>" class="shortcode-modal">
                        <div class="shortcode-modal-content">
                            <span class="shortcode-modal-close" onclick="closeShortcodeModal('hero-slide-modal-<?php echo $index; ?>')">&times;</span>
                            <?php echo do_shortcode($slide['button_link']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
    
    /*-- 右侧产品叠加图 -*/
   /* 修改产品叠加图样式 */
.hero-product-overlay {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 15;
    height: 100%;
    display: flex;
    align-items: center;
}

.hero-product-overlay img {
    max-height: calc(100% - 60px); /* 减去上下边距，与左侧内容高度一致 */
    max-width: 250px;
    object-fit: contain;
}

    
    .hero-section {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        margin-bottom: 60px;
    }
    
    .hero-slider {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .hero-slide.active {
        opacity: 1;
    }
    
    .hero-slide-content {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        z-index: 15;
        padding: 10px;
        text-align: center;
    }
    
    .hero-slide-title {
        font-size: 24px;
        margin: 0 0 10px 0;
    }
    
    .hero-slide-description {
        font-size: 14px;
        margin: 0 0 15px 0;
        opacity: 0.9;
    }
    
    .btn-slide {
        display: inline-block;
        padding: 8px 20px;
        background: #fff;
        color: #667eea;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }
    
    .btn-slide:hover {
        background: #f0f0f0;
    }
    
    /* 弹窗样式 */
    .shortcode-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.7);
    }
    
    .shortcode-modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 600px;
        border-radius: 12px;
        position: relative;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .shortcode-modal-close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .shortcode-modal-close:hover,
    .shortcode-modal-close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    
    
    /* 修改成更亮的效果 */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2); /* 降低透明度，让背景更亮 */
}
    
    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        padding: 80px 0;
        color: #fff;
    }
    
   
   /* 容器 - 确保没有高度限制 */
.hero-indicators {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 20;
    display: flex;
    gap: 10px;
    height: auto !important; /* 强制高度自适应 */
}

/* 指示器 - 强制改成细短线 */
.hero-indicator {
    width: 15px !important;
    height: 1px !important;
    border-radius: 1px !important;
    border: none !important;
    background: rgba(255,255,255,0.4) !important;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0 !important;
    margin: 0 !important;
}

.hero-indicator.active {
    width: 25px !important;
    height: 1px !important;
    background: rgba(255,255,255,0.9) !important;
}
   
    
   /* 简约箭头样式 */
/* 强制覆盖所有样式 */
.hero-prev, .hero-next {
    position: absolute !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 40px !important;
    height: 40px !important;
    border: none !important;
    background: transparent !important;
    color: rgba(255,255,255,0.7) !important;
    font-size: 20px !important;
    cursor: pointer !important;
    z-index: 20 !important;
    border-radius: 0 !important;
    transition: all 0.3s ease !important;
    line-height: 40px !important;
    text-align: center !important;
    padding: 0 !important;
    margin: 0 !important;
}

.hero-prev:hover, .hero-next:hover {
    background: radial-gradient(ellipse at center, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 60%, transparent 80%) !important;
    color: rgba(255,255,255,1) !important;
}

.hero-prev {
    left: 6px !important;
}

.hero-next {
    right: 6px !important;
}

   
   
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var slider = document.querySelector('.hero-slider');
        if (!slider) return;
        
        var slides = slider.querySelectorAll('.hero-slide');
        var indicators = document.querySelectorAll('.hero-indicator');
        var prevBtn = document.querySelector('.hero-prev');
        var nextBtn = document.querySelector('.hero-next');
        var currentIndex = 0;
        var autoplay = slider.dataset.autoplay === '1';
        var interval = parseInt(slider.dataset.interval) || 5000;
        var timer;
        
        function goToSlide(index) {
            slides.forEach(function(slide) {
                slide.classList.remove('active');
            });
            indicators.forEach(function(indicator) {
                indicator.classList.remove('active');
            });
            slides[index].classList.add('active');
            indicators[index].classList.add('active');
            currentIndex = index;
        }
        
        function nextSlide() {
            var newIndex = (currentIndex + 1) % slides.length;
            goToSlide(newIndex);
        }
        
        function prevSlide() {
            var newIndex = (currentIndex - 1 + slides.length) % slides.length;
            goToSlide(newIndex);
        }
        
        // 初始化
        goToSlide(0);
        
        // 自动播放
        if (autoplay) {
            timer = setInterval(nextSlide, interval);
        }
        
        // 点击指示器
        indicators.forEach(function(indicator, index) {
            indicator.addEventListener('click', function() {
                goToSlide(index);
                if (autoplay) {
                    clearInterval(timer);
                    timer = setInterval(nextSlide, interval);
                }
            });
        });
        
        // 点击控制按钮
        prevBtn.addEventListener('click', function() {
            prevSlide();
            if (autoplay) {
                clearInterval(timer);
                timer = setInterval(nextSlide, interval);
            }
        });
        
        nextBtn.addEventListener('click', function() {
            nextSlide();
            if (autoplay) {
                clearInterval(timer);
                timer = setInterval(nextSlide, interval);
            }
        });
        
        // 鼠标悬停暂停
        slider.parentElement.addEventListener('mouseenter', function() {
            if (autoplay) {
                clearInterval(timer);
            }
        });
        
        slider.parentElement.addEventListener('mouseleave', function() {
            if (autoplay) {
                timer = setInterval(nextSlide, interval);
            }
        });
    });
    
    // 弹窗函数
    function openShortcodeModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeShortcodeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // 点击弹窗外部关闭
    document.addEventListener('click', function(event) {
        var modals = document.querySelectorAll('.shortcode-modal');
        modals.forEach(function(modal) {
            if (event.target == modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });
    </script>
    <?php
}


// 渲染企业简介区域

function wuchaiwp_render_about_section() {
    $about_settings = wuchaiwp_get_about_settings();
    $titles_settings = wuchaiwp_get_titles_settings();
    
    // 获取页面内容（支持自定义文章类型）
    $page_content = '';
    $page_link = '';
    $page_title = '';
    $page_featured_image = '';
    $post_type = '';
    
    if (!empty($about_settings['about_page'])) {
        $about_page = get_post($about_settings['about_page']);
        if ($about_page && $about_page->post_status === 'publish') {
            // 获取原始内容（不应用任何过滤器）
            $raw_content = $about_page->post_content;
            
            // 从内容中提取图片
            $images = array();
            preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $raw_content, $matches);
            if (!empty($matches[1])) {
                $images = $matches[1];
            }
            
            // 获取特色图片
            $page_featured_image = '';
            if (has_post_thumbnail($about_page->ID)) {
                $page_featured_image = get_the_post_thumbnail_url($about_page->ID, 'large');
                // 如果有特色图片，优先使用特色图片
                if (!in_array($page_featured_image, $images)) {
                    array_unshift($images, $page_featured_image);
                }
            }
            
            // 移除内容中的图片标签（图文分离）
            $content_without_images = preg_replace('/<img[^>]+>/i', '', $raw_content);
            
            // 清理图片移除后留下的空白和空段落
            $content_without_images = preg_replace('/<p>\s*<\/p>/i', '', $content_without_images);
            $content_without_images = preg_replace('/<p>\s*&nbsp;\s*<\/p>/i', '', $content_without_images);
            $content_without_images = preg_replace('/\s{2,}/', ' ', $content_without_images);
            $content_without_images = trim($content_without_images);
            
            // 获取截取字数设置
            $excerpt_length = intval($about_settings['excerpt_length'] ?? 500);
            
            // 先截取内容（保留HTML标签结构，但不含图片）
            $excerpt_raw = wuchaiwp_truncate_html($content_without_images, $excerpt_length);
            
            // 只应用必要的格式化，跳过the_content过滤器（避免插件干扰）
            // 转换短代码（如果需要）
            $page_content = do_shortcode($excerpt_raw);
            // 转换换行符为<br>
            $page_content = wpautop($page_content);
            // 转换特殊字符
            $page_content = htmlspecialchars_decode($page_content);
            
            // 移除可能的read more链接和省略标记
            $page_content = preg_replace('/<a[^>]*>[\s\S]*?(read\s*more|更多|查看全文|阅读全文|了解更多)[\s\S]*?<\/a>/i', '', $page_content);
            $page_content = preg_replace('/\s*\[\.{2,}\]\s*/', '', $page_content);
            
            // 再次清理空段落
            $page_content = preg_replace('/<p>\s*<\/p>/i', '', $page_content);
            $page_content = trim($page_content);
            
            $page_link = get_permalink($about_page->ID);
            $page_title = $about_page->post_title;
            $post_type = $about_page->post_type;
        }
    }
    
    // 提取纯文本内容（用于判断是否需要显示"查看更多"）
    $text_content = strip_tags($raw_content ?? '');
    $text_content = trim($text_content);
    $has_content = !empty($text_content);
    
    // 判断是否需要显示"查看更多"
    $excerpt_length = intval($about_settings['excerpt_length'] ?? 500);
    $has_more = mb_strlen($text_content, 'UTF-8') > $excerpt_length;
    
    // 截取后的内容已经在上面处理好了
    $excerpt_content = $page_content;
    ?>
    <section id="about" class="about-section">
        <div class="section-header">
            <h2 class="section-title"><?php echo esc_html($titles_settings['about_title']); ?></h2>
            <p class="section-subtitle"><?php echo esc_html($titles_settings['about_subtitle']); ?></p>
        </div>
        <div class="about-content">
            <!-- 页面标题和类型 -->
            <?php if ($page_title && $has_content) : ?>
            <div class="about-page-header">
                <h3 class="about-page-title"><?php echo esc_html($page_title); ?></h3>
                <?php if ($post_type && $post_type !== 'page') : ?>
                    <span class="about-post-type"><?php echo esc_html(get_post_type_object($post_type)->labels->singular_name); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- 图文并排展示 -->
            <div class="about-content-wrapper">
                
                <!-- 移除数量限制，显示所有图片 -->
<!--<div class="about-images-grid">
    <?php //foreach ($images as $index => $img_src) : ?>
        <div class="about-image-item" onclick="openLightbox(<?php //echo $index; ?>)">
            <img src="<?php //echo esc_url($img_src); ?>" alt="企业图片" />
        </div>
    <?php //endforeach; ?>
</div>-->
<!-- 移除"还有X张图片"提示 -->
                
                
                <!-- 图片区域 -->
                <div class="about-images-section">
                    <?php if (!empty($images)) : ?>
                        <div class="about-images-grid">
                            <?php foreach ($images as $index => $img_src) : ?>
                                <?php if ($index < 6) : ?><!-- 最多显示3张图片 -->
                                    <div class="about-image-item" onclick="openLightbox(<?php echo $index; ?>)">
                                        <img src="<?php echo esc_url($img_src); ?>" alt="企业图片" />
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($images) > 3) : ?>
                            <!--<p class="more-images">还有 <?php //echo count($images) - 3; ?> 张图片...</p>-->
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- 文字内容区域（前500字） -->
                <div class="about-excerpt-content">
                <?php if ($has_content) : ?>
                    <?php echo $excerpt_content; ?>
                    <?php if ($has_more && $page_link && $about_settings['show_read_more'] === '1') : ?>
                        <div class="about-read-more">
                            <a href="<?php echo esc_url($page_link); ?>" class="read-more-btn">
                                <span><?php echo esc_html($about_settings['read_more_text'] ?? '查看完整内容'); ?></span>
                                <span class="arrow">→</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div><!-- .about-excerpt-content -->
            </div><!-- .about-content-wrapper -->
            
            <?php else : ?>
                    <div class="about-empty-state">
                        <div class="empty-icon">📝</div>
                        <h4>暂无企业简介内容</h4>
                        <p>请在后台管理页面中选择一个页面或自定义文章作为企业简介内容来源</p>
                        <?php if (current_user_can('manage_options')) : ?>
                        <a href="<?php echo admin_url('admin.php?page=wuchaiwp-enterprise-manager&tab=about'); ?>" class="button button-primary">
                            前往设置
                        </a>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 统计数据区域 -->
            <?php if ($about_settings['show_stats'] === '1') : ?>
            <div class="about-stats">
                <?php foreach ($about_settings['stats'] as $stat) : ?>
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($stat['value'] ?? ''); ?></span>
                    <span class="stat-label"><?php echo esc_html($stat['label'] ?? ''); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- 图片灯箱 -->
    <div id="lightbox" class="lightbox" style="display: none;" onclick="closeLightbox()">
        <span class="lightbox-close">&times;</span>
        <img class="lightbox-content" id="lightbox-img">
        <a class="lightbox-prev" onclick="prevImage()">&#10094;</a>
        <a class="lightbox-next" onclick="nextImage()">&#10095;</a>
        <div class="lightbox-caption" id="lightbox-caption"></div>
    </div>
    
    <script>
        var lightboxImages = <?php echo json_encode($images); ?>;
        var currentIndex = 0;
        
        function openLightbox(index) {
            currentIndex = index;
            document.getElementById('lightbox').style.display = 'flex';
            document.getElementById('lightbox-img').src = lightboxImages[currentIndex];
        }
        
        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
        }
        
        function prevImage() {
            event.stopPropagation();
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : lightboxImages.length - 1;
            document.getElementById('lightbox-img').src = lightboxImages[currentIndex];
        }
        
        function nextImage() {
            event.stopPropagation();
            currentIndex = (currentIndex < lightboxImages.length - 1) ? currentIndex + 1 : 0;
            document.getElementById('lightbox-img').src = lightboxImages[currentIndex];
        }
        
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('lightbox').style.display === 'flex') {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') prevImage();
                if (e.key === 'ArrowRight') nextImage();
            }
        });
    </script>
    <?php
}

// 渲染产品介绍区域
function wuchaiwp_render_products_section() {
    $titles_settings = wuchaiwp_get_titles_settings();
    $products_settings = wuchaiwp_get_products_settings();
    
    // 获取后台分类设置
    $category_settings = wuchaiwp_get_category_settings();
    $product_post_type = !empty($category_settings['product_post_type']) ? $category_settings['product_post_type'] : 'product';
    if (!wuchaiwp_post_type_exists($product_post_type)) {
        $product_post_type = 'post';
    }
    
    // 获取产品分类
    $product_taxonomies = get_object_taxonomies($product_post_type, 'objects');
    $selected_product_cat_ids = !empty($category_settings['product_categories']) ? $category_settings['product_categories'] : array();
    
    // 获取产品数据
    $products = wuchaiwp_get_recommended_posts('products');
    if (empty($products)) {
        if (wuchaiwp_post_type_exists($product_post_type)) {
            $products = get_posts(array(
                'post_type' => $product_post_type,
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ));
        } else {
            $products = get_posts(array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                'meta_key' => '_wp_page_template',
                'meta_value' => 'templates/single/single-product.php'
            ));
        }
    }
    
    // 处理产品数据
    $product_data = array();
    foreach ($products as $product) {
        // 获取分类ID
        $term_ids = array();
        foreach ($product_taxonomies as $tax) {
            if ($tax->hierarchical) {
                $terms = get_the_terms($product->ID, $tax->name);
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $term_ids[] = $term->term_id;
                    }
                }
            }
        }
        $term_ids_str = implode(' ', $term_ids);
        
        // 获取文章中的所有图片（特色图片 + 内容中的图片）
        $images = array();
        
        // 1. 添加特色图片
        if (has_post_thumbnail($product->ID)) {
            $images[] = get_the_post_thumbnail_url($product->ID, 'large');
        }
        
        // 2. 从文章内容中提取图片
        $content = get_post_field('post_content', $product->ID);
        preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $img_src) {
                // 确保是完整URL
                if (!preg_match('/^https?:/', $img_src)) {
                    $img_src = home_url($img_src);
                }
                // 避免重复
                if (!in_array($img_src, $images)) {
                    $images[] = $img_src;
                }
            }
        }
        
        // 获取产品自定义字段
        $product_price = get_post_meta($product->ID, 'enterprise_product_price', true);
        $product_version = get_post_meta($product->ID, 'enterprise_product_version', true);
        $product_category = get_post_meta($product->ID, 'enterprise_product_category', true);
        $product_download = get_post_meta($product->ID, 'enterprise_product_download', true);
        $product_features = get_post_meta($product->ID, 'enterprise_product_features', true);
        
        // 获取字段名称自定义
        $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
        $default_price_label = !empty($titles_settings['product_price_label']) ? $titles_settings['product_price_label'] : '💰 产品价格';
        $default_version_label = !empty($titles_settings['product_version_label']) ? $titles_settings['product_version_label'] : '🔖 产品版本';
        $default_category_label = !empty($titles_settings['product_category_label']) ? $titles_settings['product_category_label'] : '📁 产品分类';
        $default_download_label = !empty($titles_settings['product_download_label']) ? $titles_settings['product_download_label'] : '📥 下载链接';
        $default_features_label = !empty($titles_settings['product_features_label']) ? $titles_settings['product_features_label'] : '✨ 产品特性';
        
        // 优先使用文章自定义的字段名称
        $price_label = get_post_meta($product->ID, 'enterprise_product_price_label', true) ?: $default_price_label;
        $version_label = get_post_meta($product->ID, 'enterprise_product_version_label', true) ?: $default_version_label;
        $category_label = get_post_meta($product->ID, 'enterprise_product_category_label', true) ?: $default_category_label;
        $download_label = get_post_meta($product->ID, 'enterprise_product_download_label', true) ?: $default_download_label;
        $features_label = get_post_meta($product->ID, 'enterprise_product_features_label', true) ?: $default_features_label;
        
        $product_data[] = array(
            'id' => $product->ID,
            'title' => get_the_title($product->ID),
            'permalink' => get_permalink($product->ID),
            'images' => $images,
            'image' => !empty($images) ? $images[0] : '',
            'excerpt' => wp_trim_words(get_post_field('post_excerpt', $product->ID) ?: get_post_field('post_content', $product->ID), 30),
            'term_ids' => $term_ids_str,
            'price' => $product_price,
            'version' => $product_version,
            'category' => $product_category,
            'download' => $product_download,
            'features' => $product_features,
            'price_label' => $price_label,
            'version_label' => $version_label,
            'category_label' => $category_label,
            'download_label' => $download_label,
            'download_text' => get_post_meta($product->ID, 'enterprise_product_download_text', true) ?: (!empty($titles_settings['product_download_text']) ? $titles_settings['product_download_text'] : '立即下载'),
            'features_label' => $features_label
        );
    }
    
    // 获取可用分类
    $available_categories = array();
    foreach ($product_taxonomies as $tax) {
        if ($tax->hierarchical) {
            $terms = get_terms(array('taxonomy' => $tax->name, 'hide_empty' => true));
            foreach ($terms as $term) {
                if (empty($selected_product_cat_ids) || in_array($term->term_id, $selected_product_cat_ids)) {
                    $available_categories[] = $term;
                }
            }
        }
    }
    
    ?>
    <section id="products" class="products-section">
        <div class="section-header">
            <h2 class="section-title"><?php echo esc_html($titles_settings['products_title']); ?></h2>
            <p class="section-subtitle"><?php echo esc_html($titles_settings['products_subtitle']); ?></p>
        </div>
        
        <?php if (empty($product_data)) : ?>
        <div class="no-posts">
            <p>暂无产品内容</p>
        </div>
        <?php else : ?>
        
        <!-- 产品分类导航 -->
        <?php if (!empty($available_categories)) : ?>
        <div class="product-categories-nav">
            <div class="category-tabs">
                <button class="category-tab active" data-filter="all">全部</button>
                <?php foreach ($available_categories as $term) : ?>
                    <button class="category-tab" data-filter="<?php echo $term->term_id; ?>">
                        <?php echo esc_html($term->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Tab栏 -->
        <div class="product-tabs-wrapper">
            <div class="product-tabs" id="productTabs">
                <?php foreach ($product_data as $index => $product) : ?>
                    <button class="product-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-category-ids="<?php echo $product['term_ids']; ?>">
                        <?php echo esc_html($product['title']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- 内容区域 -->
        <div class="product-content-wrapper" id="productContentWrapper">
            <?php foreach ($product_data as $index => $product) : ?>
            <div class="product-content" data-index="<?php echo $index; ?>" data-category-ids="<?php echo $product['term_ids']; ?>" <?php echo $index > 0 ? 'style="display: none;"' : ''; ?>>
                <div class="product-card">
                    <!-- 左侧图片 -->
                    <div class="product-image-col">
                        <?php if (!empty($product['images'])) : ?>
                            <div class="product-image-slider" data-images="<?php echo esc_attr(json_encode($product['images'])); ?>">
                                <?php foreach ($product['images'] as $img_index => $img_src) : ?>
                                    <div class="product-slide <?php echo $img_index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($product['title']); ?>" />
                                    </div>
                                <?php endforeach; ?>
                                
                                <!-- 轮播控制按钮 -->
                                <?php if (count($product['images']) > 1) : ?>
                                    <button class="slider-prev" onclick="prevSlide(this)">‹</button>
                                    <button class="slider-next" onclick="nextSlide(this)">›</button>
                                    <div class="slider-indicators">
                                        <?php foreach ($product['images'] as $img_index => $img_src) : ?>
                                            <button class="slider-indicator <?php echo $img_index === 0 ? 'active' : ''; ?>" onclick="goToSlide(this, <?php echo $img_index; ?>)"></button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="no-image-placeholder">
                                <span class="no-image-icon">📷</span>
                                <p>暂无图片</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- 右侧内容 -->
                    <div class="product-info-col">
                        <h3 class="product-title"><?php echo esc_html($product['title']); ?></h3>
                        <div class="product-excerpt"><?php echo $product['excerpt']; ?></div>
                        
                        <!-- 产品信息字段 -->
                        <?php if (!empty($product['price']) || !empty($product['version']) || !empty($product['category']) || !empty($product['download']) || !empty($product['features'])) : ?>
                        <div class="product-meta">
                            <?php if (!empty($product['price'])) : ?>
                            <div class="product-meta-item">
                                <span class="meta-label"><?php echo esc_html($product['price_label'] ?? '💰 产品价格'); ?></span>
                                <span class="meta-value"><?php echo esc_html($product['price']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['version'])) : ?>
                            <div class="product-meta-item">
                                <span class="meta-label"><?php echo esc_html($product['version_label'] ?? '🔖 产品版本'); ?></span>
                                <span class="meta-value"><?php echo esc_html($product['version']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['category'])) : ?>
                            <div class="product-meta-item">
                                <span class="meta-label"><?php echo esc_html($product['category_label'] ?? '📁 产品分类'); ?></span>
                                <span class="meta-value"><?php echo esc_html($product['category']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['download'])) : ?>
                            <div class="product-meta-item">
                                <span class="meta-label"><?php echo esc_html($product['download_label'] ?? '📥 下载链接'); ?></span>
                                <a href="<?php echo esc_url($product['download']); ?>" class="meta-link" target="_blank"><?php echo esc_html($product['download_text'] ?? '立即下载'); ?></a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['features'])) : ?>
                            <div class="product-features">
                                <span class="meta-label"><?php echo esc_html($product['features_label'] ?? '✨ 产品特性'); ?></span>
                                <div class="features-list">
                                    <?php 
                                    $features = explode("\n", trim($product['features']));
                                    foreach ($features as $feature) {
                                        $feature = trim($feature);
                                        if (!empty($feature)) {
                                            echo '<span class="feature-tag">' . esc_html($feature) . '</span>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url($product['permalink']); ?>" class="product-link">查看详情 →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- 查看更多按钮 -->
            <div class="product-load-more" id="productLoadMore" style="display: none;">
                <button class="load-more-btn">查看更多</button>
            </div>
        </div>
        
        <?php endif; ?>
    </section>
    
    <style>
        /* 产品分类导航 */
        .product-categories-nav {
            margin-bottom: 20px;
        }
        
        .product-categories-nav .category-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .product-categories-nav .category-tab {
            padding: 8px 20px;
            background: #f5f5f5;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            transition: all 0.3s ease;
        }
        
        .product-categories-nav .category-tab:hover {
            background: #e8e8e8;
        }
        
        .product-categories-nav .category-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* Tab栏 */
        .product-tabs-wrapper {
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        
        .product-tabs {
            display: flex;
            gap: 15px;
            padding: 10px 0;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .product-tab {
            padding: 8px 20px;
            background: #f5f5f5;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .product-tab:hover {
            background: #e8e8e8;
        }
        
        .product-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* 内容区域 */
        .product-content-wrapper {
            position: relative;
            min-height: 300px;
        }
        
        .product-content {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* 产品卡片 */
        .product-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: flex-start;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        /* 左侧图片区域 */
        .product-image-col {
            position: relative;
        }
        
        /* 图片轮播 */
        .product-image-slider {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 8px;
        }
        
        .product-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .product-slide.active {
            opacity: 1;
        }
        
        .product-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* 轮播控制按钮 - 参考Hero区域样式 */
        .slider-prev, .slider-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.7);
            font-size: 20px;
            cursor: pointer;
            z-index: 20;
            border-radius: 0;
            transition: all 0.3s ease;
            line-height: 40px;
            text-align: center;
            padding: 0;
            margin: 0;
        }
        
        .slider-prev:hover, .slider-next:hover {
            background: radial-gradient(ellipse at center, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 60%, transparent 80%);
            color: rgba(255,255,255,1);
        }
        
        .slider-prev {
            left: 6px;
        }
        
        .slider-next {
            right: 6px;
        }
        
        /* 轮播指示器 - 参考Hero区域样式 */
        .slider-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 20;
            height: auto;
        }
        
        .slider-indicator {
            width: 15px;
            height: 1px;
            border-radius: 1px;
            border: none;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
            margin: 0;
        }
        
        .slider-indicator.active {
            width: 25px;
            height: 1px;
            background: rgba(255,255,255,0.9);
        }
        
        .product-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .no-image-placeholder {
            width: 100%;
            height: 300px;
            background: #f5f5f5;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #999;
        }
        
        .no-image-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        /* 右侧内容区域 */
        .product-info-col {
            padding: 10px;
        }
        
        .product-info-col .product-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .product-info-col .product-excerpt {
            line-height: 1.8;
            color: #555;
            margin-bottom: 15px;
            font-size: 15px;
        }
        
        .product-info-col .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            padding: 12px 16px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .product-info-col .product-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }
        
        .product-info-col .meta-label {
            font-size: 16px;
        }
        
        .product-info-col .meta-value {
            color: #333;
        }
        
        .product-info-col .meta-link {
            color: #667eea;
            text-decoration: none;
        }
        
        .product-info-col .meta-link:hover {
            text-decoration: underline;
        }
        
        .product-info-col .product-features {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 8px;
            width: 100%;
        }
        
        .product-info-col .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        
        .product-info-col .feature-tag {
            padding: 4px 10px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .product-info-col .product-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .product-info-col .product-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        /* 查看更多按钮 */
        .product-load-more {
            text-align: center;
            margin-top: 30px;
        }
        
        .load-more-btn {
            padding: 12px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .load-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        @media (max-width: 768px) {
            .product-card {
                grid-template-columns: 1fr;
            }
            
            .product-image img,
            .no-image-placeholder {
                height: 200px;
            }
            
            .product-info-col .product-title {
                font-size: 22px;
            }
        }
    </style>
    
    <script>
    // 图片轮播全局函数（供内联onclick调用）
    function prevSlide(btn) {
        var slider = btn.parentElement;
        var slides = slider.querySelectorAll('.product-slide');
        var currentIndex = -1;
        slides.forEach(function(slide, index) {
            if (slide.classList.contains('active')) {
                currentIndex = index;
            }
        });
        var newIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
        goToSlideByIndex(slider, newIndex);
    }

    function nextSlide(btn) {
        var slider = btn.parentElement;
        var slides = slider.querySelectorAll('.product-slide');
        var currentIndex = -1;
        slides.forEach(function(slide, index) {
            if (slide.classList.contains('active')) {
                currentIndex = index;
            }
        });
        var newIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
        goToSlideByIndex(slider, newIndex);
    }

    function goToSlide(indicator, index) {
        var slider = indicator.parentElement.parentElement;
        goToSlideByIndex(slider, index);
    }

    function goToSlideByIndex(slider, index) {
        var slides = slider.querySelectorAll('.product-slide');
        var indicators = slider.querySelectorAll('.slider-indicator');
        slides.forEach(function(slide) {
            slide.classList.remove('active');
        });
        indicators.forEach(function(indicator) {
            indicator.classList.remove('active');
        });
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Tab切换功能
        var tabs = document.querySelectorAll('.product-tab');
        var contents = document.querySelectorAll('.product-content');
        var currentFilter = 'all'; // 当前筛选的分类
        var loadMoreBtn = document.getElementById('productLoadMore');
        
        // 更新查看更多按钮状态
        function updateLoadMoreButton() {
            if (!loadMoreBtn) return;
            
            var visibleContents = document.querySelectorAll('.product-content:not([style*="display: none"])');
            var filteredContents = document.querySelectorAll('.product-content');
            var totalCount = 0;
            filteredContents.forEach(function(c) {
                var categoryIds = c.dataset.categoryIds || '';
                var categoryIdArray = categoryIds.split(' ').filter(function(id) { return id !== ''; });
                if (currentFilter === 'all' || categoryIdArray.includes(currentFilter)) {
                    totalCount++;
                }
            });
            loadMoreBtn.style.display = (visibleContents.length < totalCount && totalCount > 0) ? 'block' : 'none';
        }
        
        // 查看更多功能
        if (loadMoreBtn) {
            loadMoreBtn.querySelector('.load-more-btn').addEventListener('click', function() {
                // 找到第一个未显示的匹配内容
                var filteredContents = document.querySelectorAll('.product-content');
                var hiddenContents = [];
                filteredContents.forEach(function(c) {
                    var categoryIds = c.dataset.categoryIds || '';
                    var categoryIdArray = categoryIds.split(' ').filter(function(id) { return id !== ''; });
                    if ((currentFilter === 'all' || categoryIdArray.includes(currentFilter)) && 
                        c.style.display === 'none') {
                        hiddenContents.push(c);
                    }
                });
                if (hiddenContents.length > 0) {
                    hiddenContents[0].style.display = 'block';
                    updateLoadMoreButton();
                }
            });
        }
        
        // 初始化时检查是否需要显示查看更多按钮
        updateLoadMoreButton();
        
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var index = parseInt(tab.dataset.index);
                
                tabs.forEach(function(t) { t.classList.remove('active'); });
                
                // 隐藏所有内容，然后只显示当前Tab对应的内容
                contents.forEach(function(c, i) {
                    c.classList.remove('active');
                    if (i === index) {
                        c.classList.add('active');
                        c.style.display = 'block';
                    } else {
                        c.style.display = 'none';
                    }
                });
                
                tab.classList.add('active');
            });
        });
        
        // 产品分类筛选功能（参考案例区域的简洁逻辑）
        var categoryTabs = document.querySelectorAll('.product-categories-nav .category-tab');
        
        categoryTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                // 更新分类标签状态
                categoryTabs.forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');
                
                var filter = tab.dataset.filter;
                currentFilter = filter; // 更新当前筛选
                
                // 筛选Tab
                tabs.forEach(function(t, index) {
                    var categoryIds = t.dataset.categoryIds || '';
                    var categoryIdArray = categoryIds.split(' ').filter(function(id) { return id !== ''; });
                    
                    if (filter === 'all' || categoryIdArray.includes(filter)) {
                        t.style.display = 'block';
                    } else {
                        t.style.display = 'none';
                    }
                });
                
                // 筛选内容：只显示第一个匹配的内容
                var firstVisibleIndex = -1;
                var count = 0;
                contents.forEach(function(c, index) {
                    var categoryIds = c.dataset.categoryIds || '';
                    var categoryIdArray = categoryIds.split(' ').filter(function(id) { return id !== ''; });
                    
                    if (filter === 'all' || categoryIdArray.includes(filter)) {
                        if (count === 0) {
                            // 第一个匹配的内容显示
                            c.style.display = 'block';
                            c.classList.add('active');
                            firstVisibleIndex = index;
                        } else {
                            // 其他匹配的内容隐藏
                            c.style.display = 'none';
                            c.classList.remove('active');
                        }
                        count++;
                    } else {
                        c.style.display = 'none';
                        c.classList.remove('active');
                    }
                });
                
                // 更新Tab状态
                tabs.forEach(function(t) { t.classList.remove('active'); });
                if (firstVisibleIndex >= 0) {
                    tabs[firstVisibleIndex].classList.add('active');
                }
                
                // 更新查看更多按钮
                updateLoadMoreButton();
            });
        });
        
        // 自动轮播
        var sliders = document.querySelectorAll('.product-image-slider');
        sliders.forEach(function(slider) {
            var slides = slider.querySelectorAll('.product-slide');
            if (slides.length > 1) {
                setInterval(function() {
                    var currentIndex = -1;
                    slides.forEach(function(slide, index) {
                        if (slide.classList.contains('active')) {
                            currentIndex = index;
                        }
                    });
                    var newIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                    goToSlideByIndex(slider, newIndex);
                }, 5000);
            }
        });
    });
    </script>
    <?php
}

// 渲染开发日志区域
function wuchaiwp_render_news_section() {
    $titles_settings = wuchaiwp_get_titles_settings();
    
    // 获取显示设置（区域设置页面）
    $display_settings = get_option('wuchaiwp_enterprise_display_settings', array());
    
    // 获取分类管理设置（分类管理页面）- 用于向后兼容
    $category_settings = get_option('wuchaiwp_enterprise_category_settings', array());
    
    // 默认使用筛选机制，不再使用推荐机制
    $skip_recommend = true;
    

    
    // 分页参数（前端分页，先获取所有数据）
    $posts_per_column = 10;
    $posts_per_page = $posts_per_column * 2; // 两列，每列10条
    
    // 构建查询参数 - 获取所有文章，前端分页
    $args = array(
        'posts_per_page' => -1, // 获取所有文章
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    // 使用筛选机制显示开发日志
    // 优先使用区域设置页面的设置，如果为空则使用分类管理页面的设置
    $selected_post_types = array();
    if (isset($display_settings['news_post_types']) && is_array($display_settings['news_post_types']) && !empty($display_settings['news_post_types'])) {
        $selected_post_types = $display_settings['news_post_types'];
    } elseif (isset($category_settings['news_post_type']) && !empty($category_settings['news_post_type'])) {
        // 兼容旧的分类管理页面设置（单数形式）
        $selected_post_types = array($category_settings['news_post_type']);
    }
    
    // 如果没有选择任何文章类型，使用默认
    if (empty($selected_post_types)) {
        $selected_post_types = array('post');
        echo '<div style="background: #f8d7da; padding: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px; font-family: monospace; font-size: 12px;">';
        echo '<strong>警告：</strong>未设置文章类型，使用默认值: post';
        echo '</div>';
    }
    
    // 优先使用区域设置页面的分类，如果为空则使用分类管理页面的设置
    $selected_categories = array();
    if (isset($display_settings['news_categories']) && is_array($display_settings['news_categories']) && !empty($display_settings['news_categories'])) {
        $selected_categories = $display_settings['news_categories'];
    } elseif (isset($category_settings['news_categories']) && is_array($category_settings['news_categories']) && !empty($category_settings['news_categories'])) {
        $selected_categories = $category_settings['news_categories'];
    }
    
    $args['post_type'] = $selected_post_types;
    
    // 如果设置了分类筛选
    if (!empty($selected_categories) && !empty($selected_post_types)) {
        // 获取所有有效的分类法
        $all_taxonomies = array();
        foreach ($selected_post_types as $post_type) {
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->hierarchical) {
                    $all_taxonomies[] = $taxonomy->name;
                }
            }
        }
        
        // 去重分类法
        $all_taxonomies = array_unique($all_taxonomies);
        
        if (!empty($all_taxonomies)) {
            // 使用嵌套的 tax_query：先按分类法分组，再用 OR 连接
            $args['tax_query'] = array();
            
            foreach ($all_taxonomies as $taxonomy) {
                // 获取该分类法下的有效分类ID
                $valid_terms = get_terms(array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                    'fields' => 'ids'
                ));
                
                // 只保留属于当前分类法的分类
                $applicable_categories = array_intersect($selected_categories, $valid_terms);
                
                if (!empty($applicable_categories)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $applicable_categories
                    );
                }
            }
            
            // 如果有多个分类法条件，使用 OR 关系
            if (count($args['tax_query']) > 1) {
                $args['tax_query']['relation'] = 'OR';
            }
            
            // 如果没有有效的分类查询条件，移除 tax_query
            if (empty($args['tax_query'])) {
                unset($args['tax_query']);
            }
        }
    }
    
    // 获取日志文章
    // 使用 WP_Query 代替 get_posts，确保查询结果正确（修复重复显示问题）
    $news_query = new WP_Query($args);
    $news = $news_query->posts;
    wp_reset_postdata();
    
    // 计算总页数（前端分页）
    $total_posts = count($news);
    $total_pages = ceil($total_posts / $posts_per_page);
    
    // 准备所有新闻数据供前端分页使用
    $all_news_data = array();
    foreach ($news as $post) {
        $year_month = get_the_date('Y年m月', $post->ID);
        // 获取分类ID
        $news_terms = wp_get_post_terms($post->ID, get_object_taxonomies($post->post_type, 'names'));
        $news_term_ids = array();
        foreach ($news_terms as $term) {
            $news_term_ids[] = $term->term_id;
        }
        
        $all_news_data[] = array(
            'year_month' => $year_month,
            'ID' => $post->ID,
            'date' => get_the_date('d', $post->ID),
            'title' => get_the_title($post->ID),
            'permalink' => get_permalink($post->ID),
            'devlog_version' => get_post_meta($post->ID, 'enterprise_devlog_version', true),
            'devlog_type' => get_post_meta($post->ID, 'enterprise_devlog_type', true),
            'devlog_changelog' => get_post_meta($post->ID, 'enterprise_devlog_changelog', true),
            'term_ids' => implode(' ', $news_term_ids)
        );
    }
    

    
    // 按年份/月份分组
    $grouped_news = array();
    foreach ($news as $post) {
        $year_month = get_the_date('Y年m月', $post->ID);
        if (!isset($grouped_news[$year_month])) {
            $grouped_news[$year_month] = array();
        }
        $grouped_news[$year_month][] = $post;
    }
    
    // 分成两列
    $column1 = array();
    $column2 = array();
    $current_column = 1;
    $current_count = 0;
    
    foreach ($grouped_news as $year_month => $posts) {
        foreach ($posts as $post) {
            if ($current_column == 1) {
                $column1[] = array('year_month' => $year_month, 'post' => $post);
            } else {
                $column2[] = array('year_month' => $year_month, 'post' => $post);
            }
            $current_count++;
            if ($current_count >= $posts_per_column) {
                $current_column = 3 - $current_column; // 切换列
                $current_count = 0;
            }
        }
    }
    ?>
    <section id="news" class="news-section">
        <div class="section-header">
            <h2 class="section-title"><?php echo esc_html($titles_settings['news_title'] ?? '📝 开发日志'); ?></h2>
            <p class="section-subtitle"><?php echo esc_html($titles_settings['news_subtitle'] ?? '最新动态与更新'); ?></p>
        </div>
        <div class="news-content">
            
            <!-- 隐藏的JSON数据（供前端分页使用） -->
            <div id="newsData" style="display: none;" data-all-news='<?php echo json_encode($all_news_data); ?>' data-posts-per-page="<?php echo $posts_per_page; ?>" data-posts-per-column="<?php echo $posts_per_column; ?>" data-total-pages="<?php echo $total_pages; ?>"></div>
            
            <!-- 最新公告区域 - 移到下方显示 -->
            <div class="news-announcement">
                <h4><?php echo esc_html($titles_settings['announcement_title']); ?></h4>
                <?php
                // 优先使用后台推荐的公告
                $announcements = wuchaiwp_get_recommended_posts('announcement');
                
                if (!empty($announcements)) {
                    $latest_post = $announcements[0];
                    echo '<a href="' . get_permalink($latest_post->ID) . '">';
                    echo '<h5>' . get_the_title($latest_post->ID) . '</h5>';
                    echo '<p>' . wp_trim_words(get_post_field('post_excerpt', $latest_post->ID), 30) . '</p>';
                    echo '</a>';
                } else {
                    $announcement_post_type = 'announcement';
                    if (wuchaiwp_post_type_exists($announcement_post_type)) {
                        $latest_post = get_posts(array(
                            'post_type' => $announcement_post_type,
                            'posts_per_page' => 1,
                            'post_status' => 'publish'
                        ));
                    } else {
                        $latest_post = get_posts(array(
                            'post_type' => 'post',
                            'posts_per_page' => 1,
                            'category_name' => 'announcement'
                        ));
                    }
                    if ($latest_post) {
                        echo '<a href="' . get_permalink($latest_post[0]->ID) . '">';
                        echo '<h5>' . get_the_title($latest_post[0]->ID) . '</h5>';
                        echo '<p>' . wp_trim_words(get_post_field('post_excerpt', $latest_post[0]->ID), 30) . '</p>';
                        echo '</a>';
                    } else {
                        echo '<p>暂无公告</p>';
                    }
                }
                ?>
            </div>
        
            
             <div class="news-sidebar">
                    <!-- 开发日志分类导航 -->
                    <div class="news-categories">
                        <h4><?php echo esc_html($titles_settings['categories_title'] ?? '📁 分类'); ?></h4>
                        <div class="category-tabs">
                            <button class="category-tab active" data-filter="all">全部</button>
                            <?php
                            $category_settings = wuchaiwp_get_category_settings();
                            $selected_cat_ids = !empty($category_settings['news_categories']) ? $category_settings['news_categories'] : array();
                            
                            // 使用后台设置的文章类型
                            $news_post_type = !empty($category_settings['news_post_type']) ? $category_settings['news_post_type'] : 'news';
                            if (!wuchaiwp_post_type_exists($news_post_type)) {
                                $news_post_type = 'post';
                            }
                            
                            $taxonomies = get_object_taxonomies($news_post_type, 'objects');
                            $has_categories = false;
                            
                            foreach ($taxonomies as $taxonomy) {
                                if ($taxonomy->hierarchical) {
                                    $terms = get_terms(array(
                                        'taxonomy' => $taxonomy->name,
                                        'hide_empty' => true
                                    ));
                                    if (!empty($terms)) {
                                        $has_categories = true;
                                        foreach ($terms as $term) {
                                            // 如果设置了选择的分类，则只显示选中的
                                            if (empty($selected_cat_ids) || in_array($term->term_id, $selected_cat_ids)) {
                                                echo '<button class="category-tab" data-filter="' . $term->term_id . '" data-taxonomy="' . $taxonomy->name . '">';
                                                echo $term->name . ' (' . $term->count . ')';
                                                echo '</button>';
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if (!$has_categories) {
                                $categories = get_categories(array('exclude' => 1));
                                foreach ($categories as $cat) {
                                    if (empty($selected_cat_ids) || in_array($cat->term_id, $selected_cat_ids)) {
                                        echo '<button class="category-tab" data-filter="' . $cat->term_id . '" data-taxonomy="category">';
                                        echo $cat->name . ' (' . $cat->count . ')';
                                        echo '</button>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <div class="news-main-wrapper">
                <div class="news-list-wrapper">
                <!-- 左列 -->
                <div class="news-column">
                    <?php
                    $prev_year_month = '';
                    foreach ($column1 as $item) :
                        $year_month = $item['year_month'];
                        $post = $item['post'];
                        
                        // 直接使用文章ID获取数据，避免 setup_postdata 问题
                        $post_id = $post->ID;
                        $post_date = get_the_date('d', $post_id);
                        $post_title = get_the_title($post_id);
                        $post_permalink = get_permalink($post_id);
                        
                        // 获取文章的分类ID
                        $news_terms = wp_get_post_terms($post_id, get_object_taxonomies($post->post_type, 'names'));
                        $news_term_ids = array();
                        foreach ($news_terms as $term) {
                            $news_term_ids[] = $term->term_id;
                        }
                        $news_term_ids_str = implode(' ', $news_term_ids);
                    ?>
                        <?php if ($year_month != $prev_year_month) : ?>
                        <div class="news-year-month"><?php echo $year_month; ?></div>
                        <?php $prev_year_month = $year_month; ?>
                        <?php endif; ?>
                        <article class="news-item" data-category-ids="<?php echo $news_term_ids_str; ?>">
                            <span class="news-date"><?php echo $post_date; ?></span>
                            <div class="news-title-wrapper">
                                <h3 class="news-title"><a href="<?php echo $post_permalink; ?>"><?php echo $post_title; ?></a></h3>
                                
                                
                                
                                
                                <div class="news-meta">
                                    <?php
                                // 获取开发日志自定义字段
                                $devlog_version = get_post_meta($post_id, 'enterprise_devlog_version', true);
                                $devlog_type = get_post_meta($post_id, 'enterprise_devlog_type', true);
                                $devlog_changelog = get_post_meta($post_id, 'enterprise_devlog_changelog', true);
                                
                                // 如果有开发日志字段，显示版本号和查看按钮
                                if (!empty($devlog_version) || !empty($devlog_changelog)) :
                                    $type_icon = '';
                                    $type_label = '';
                                    switch ($devlog_type) {
                                        case 'feature':
                                            $type_icon = '✨';
                                            $type_label = '新功能';
                                            break;
                                        case 'bugfix':
                                            $type_icon = '🐛';
                                            $type_label = 'Bug修复';
                                            break;
                                        case 'improve':
                                            $type_icon = '⚡';
                                            $type_label = '性能优化';
                                            break;
                                        case 'security':
                                            $type_icon = '🔒';
                                            $type_label = '安全更新';
                                            break;
                                    }
                                ?>
                            
                                <?php if (!empty($devlog_changelog)) : ?>
                        <div id="news-details-<?php echo $post_id; ?>" class="news-details" hidden>
                            <div class="devlog-changelog">
                            <?php if (!empty($devlog_version)) : ?>
                                    <span class="devlog-version">📦 <?php echo esc_html($devlog_version); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($type_icon)) : ?>
                                    <span class="devlog-type" title="<?php echo esc_attr($type_label); ?>"><?php echo $type_icon; ?></span>
                                    <?php endif; ?>
                                <?php 
                                // 将每行更新内容转换为列表项
                                $changelog_lines = explode("\n", trim($devlog_changelog));
                                foreach ($changelog_lines as $line) {
                                    $line = trim($line);
                                    if (!empty($line)) {
                                        echo '<div class="changelog-item">' . esc_html($line) . '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                                
                            </div>
                            <?php if (!empty($devlog_changelog)) : ?>
                            <button class="news-toggle-btn" data-target="news-details-<?php echo $post_id; ?>">查看</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <!-- 右列 -->
                <div class="news-column">
                    <?php
                    $prev_year_month = '';
                    foreach ($column2 as $item) :
                        $year_month = $item['year_month'];
                        $post = $item['post'];
                        
                        // 直接使用文章ID获取数据，避免 setup_postdata 问题
                        $post_id = $post->ID;
                        $post_date = get_the_date('d', $post_id);
                        $post_title = get_the_title($post_id);
                        $post_permalink = get_permalink($post_id);
                        
                        // 获取文章的分类ID
                        $news_terms = wp_get_post_terms($post_id, get_object_taxonomies($post->post_type, 'names'));
                        $news_term_ids = array();
                        foreach ($news_terms as $term) {
                            $news_term_ids[] = $term->term_id;
                        }
                        $news_term_ids_str = implode(' ', $news_term_ids);
                    ?>
                        <?php if ($year_month != $prev_year_month) : ?>
                        <div class="news-year-month"><?php echo $year_month; ?></div>
                        <?php $prev_year_month = $year_month; ?>
                        <?php endif; ?>
                        <article class="news-item" data-category-ids="<?php echo $news_term_ids_str; ?>">
                            <span class="news-date"><?php echo $post_date; ?></span>
                            <div class="news-title-wrapper">
                                <h3 class="news-title"><a href="<?php echo $post_permalink; ?>"><?php echo $post_title; ?></a></h3>
                                <?php
                                // 获取开发日志自定义字段
                                $devlog_version = get_post_meta($post_id, 'enterprise_devlog_version', true);
                                $devlog_type = get_post_meta($post_id, 'enterprise_devlog_type', true);
                                $devlog_changelog = get_post_meta($post_id, 'enterprise_devlog_changelog', true);
                                
                                // 如果有开发日志字段，显示版本号和查看按钮
                                if (!empty($devlog_version) || !empty($devlog_changelog)) :
                                    $type_icon = '';
                                    $type_label = '';
                                    switch ($devlog_type) {
                                        case 'feature':
                                            $type_icon = '✨';
                                            $type_label = '新功能';
                                            break;
                                        case 'bugfix':
                                            $type_icon = '🐛';
                                            $type_label = 'Bug修复';
                                            break;
                                        case 'improve':
                                            $type_icon = '⚡';
                                            $type_label = '性能优化';
                                            break;
                                        case 'security':
                                            $type_icon = '🔒';
                                            $type_label = '安全更新';
                                            break;
                                    }
                                ?>
                                <div class="news-meta">
                                    <?php if (!empty($devlog_version)) : ?>
                                    <span class="devlog-version">📦 <?php echo esc_html($devlog_version); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($type_icon)) : ?>
                                    <span class="devlog-type" title="<?php echo esc_attr($type_label); ?>"><?php echo $type_icon; ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($devlog_changelog)) : ?>
                                <button class="news-toggle-btn" onclick="toggleNewsDetails(this)">查看</button>
                                <div class="news-details" style="display: none;">
                                    <div class="devlog-changelog">
                                        <?php 
                                        // 将每行更新内容转换为列表项
                                        $changelog_lines = explode("\n", trim($devlog_changelog));
                                        foreach ($changelog_lines as $line) {
                                            $line = trim($line);
                                            if (!empty($line)) {
                                                echo '<div class="changelog-item">' . esc_html($line) . '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
               
            </div>
            
            
            
            
        </div>
        <!-- 前端分页按钮 -->
            <?php if ($total_pages > 1) : ?>
            <div class="news-pagination" id="newsPagination">
                <button class="pagination-prev" id="prevPageBtn" onclick="wuchaiwp_load_news_page('prev')" disabled>上一页</button>
                <span class="pagination-info">第 <span id="currentPageNum">1</span> / <?php echo $total_pages; ?> 页</span>
                <button class="pagination-next" id="nextPageBtn" onclick="wuchaiwp_load_news_page('next')">下一页</button>
            </div>
            <?php endif; ?>
            
            <?php if (empty($news)) : ?>
            <div class="no-news">
                <p>暂无开发日志</p>
            </div>
            <?php endif; ?>
            
        
    </section>
    
    <style>
    /* 开发日志样式增强 */
    .news-title-wrapper {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .news-title {
        font-size: 15px;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .news-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
        font-size: 12px;
    }
    
    .devlog-version {
        color: #667eea;
        font-weight: 500;
    }
    
    .devlog-type {
        font-size: 14px;
    }
    
    .news-toggle-btn {
        background: #667eea;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 4px;
        transition: background 0.2s;
    }
    
    .news-toggle-btn:hover {
        background: #5a6fd6;
    }
    
    .news-toggle-btn.active {
        background: #4a5fd1;
    }
    
    .news-details {
        margin-top: 8px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #667eea;
        animation: slideDown 0.3s ease;
        display: none !important;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 0;
    }
    
    .news-details.expanded {
        display: block !important;
        max-height: 500px;
        opacity: 1;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .devlog-changelog {
        font-size: 13px;
        line-height: 1.6;
    }
    
    .changelog-item {
        padding: 4px 0;
        border-bottom: 1px solid #eee;
    }
    
    .changelog-item:last-child {
        border-bottom: none;
    }
    </style>
    
    <script>
    // 切换新闻详情展开/收起
    function toggleNewsDetails(targetId) {
        var details = document.getElementById(targetId);
        var btn = document.querySelector('[data-target="' + targetId + '"]');
        if (details) {
            if (details.hasAttribute('hidden')) {
                details.removeAttribute('hidden');
                details.classList.add('expanded');
                if (btn) {
                    btn.textContent = '收起';
                    btn.classList.add('active');
                }
            } else {
                details.setAttribute('hidden', '');
                details.classList.remove('expanded');
                if (btn) {
                    btn.textContent = '查看';
                    btn.classList.remove('active');
                }
            }
        }
    }
    
    // 当前页码
    var currentNewsPage = 1;
    
    // 前端本地分页加载开发日志
    function wuchaiwp_load_news_page(direction) {
        var newsData = document.getElementById('newsData');
        var newsList = document.querySelector('.news-list-wrapper');
        var pagination = document.querySelector('#newsPagination');
        var prevBtn = document.getElementById('prevPageBtn');
        var nextBtn = document.getElementById('nextPageBtn');
        var currentPageSpan = document.getElementById('currentPageNum');
        
        // 获取数据
        var allNews = JSON.parse(newsData.getAttribute('data-all-news'));
        var postsPerPage = parseInt(newsData.getAttribute('data-posts-per-page'));
        var postsPerColumn = parseInt(newsData.getAttribute('data-posts-per-column'));
        var totalPages = parseInt(newsData.getAttribute('data-total-pages'));
        
        // 计算目标页码
        var targetPage = currentNewsPage;
        if (direction === 'prev') {
            targetPage = currentNewsPage - 1;
        } else if (direction === 'next') {
            targetPage = currentNewsPage + 1;
        }
        
        // 边界检查
        if (targetPage < 1) targetPage = 1;
        if (targetPage > totalPages) targetPage = totalPages;
        
        // 如果页码没有变化，不执行
        if (targetPage === currentNewsPage) return;
        
        // 更新当前页码
        currentNewsPage = targetPage;
        
        // 计算当前页的数据范围
        var startIndex = (currentNewsPage - 1) * postsPerPage;
        var endIndex = startIndex + postsPerPage;
        var currentPageNews = allNews.slice(startIndex, endIndex);
        
        // 渲染新闻列表
        renderNewsList(currentPageNews, postsPerColumn);
        
        // 更新分页状态
        currentPageSpan.textContent = currentNewsPage;
        prevBtn.disabled = currentNewsPage <= 1;
        nextBtn.disabled = currentNewsPage >= totalPages;
        
        // 重新绑定事件
        bindNewsEvents();
        
        // 滚动到新闻区域
        var newsSection = document.querySelector('#news');
        if (newsSection) {
            newsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    
    // 渲染新闻列表
    function renderNewsList(newsData, postsPerColumn) {
        var newsList = document.querySelector('.news-list-wrapper');
        
        // 按年份分组
        var groupedNews = {};
        newsData.forEach(function(item) {
            if (!groupedNews[item.year_month]) {
                groupedNews[item.year_month] = [];
            }
            groupedNews[item.year_month].push(item);
        });
        
        // 分成两列
        var column1 = [];
        var column2 = [];
        var currentColumn = 1;
        var currentCount = 0;
        
        Object.keys(groupedNews).forEach(function(yearMonth) {
            groupedNews[yearMonth].forEach(function(item) {
                if (currentColumn === 1) {
                    column1.push({ year_month: yearMonth, item: item });
                } else {
                    column2.push({ year_month: yearMonth, item: item });
                }
                currentCount++;
                if (currentCount >= postsPerColumn) {
                    currentColumn = 3 - currentColumn;
                    currentCount = 0;
                }
            });
        });
        
        // 生成HTML
        var html = '';
        
        // 左列
        html += '<div class="news-column">';
        var prevYearMonth = '';
        column1.forEach(function(item) {
            var data = item.item;
            if (item.year_month !== prevYearMonth) {
                html += '<div class="news-year-month">' + item.year_month + '</div>';
                prevYearMonth = item.year_month;
            }
            html += generateNewsItemHTML(data);
        });
        html += '</div>';
        
        // 右列
        html += '<div class="news-column">';
        prevYearMonth = '';
        column2.forEach(function(item) {
            var data = item.item;
            if (item.year_month !== prevYearMonth) {
                html += '<div class="news-year-month">' + item.year_month + '</div>';
                prevYearMonth = item.year_month;
            }
            html += generateNewsItemHTML(data);
        });
        html += '</div>';
        
        newsList.innerHTML = html;
    }
    
    // 生成单个新闻项HTML
    function generateNewsItemHTML(data) {
        var typeIcon = '';
        switch (data.devlog_type) {
            case 'feature': typeIcon = '✨'; break;
            case 'bugfix': typeIcon = '🐛'; break;
            case 'improve': typeIcon = '⚡'; break;
            case 'security': typeIcon = '🔒'; break;
        }
        
        var html = '<article class="news-item" data-category-ids="' + (data.term_ids || '') + '">';
        html += '<span class="news-date">' + data.date + '</span>';
        html += '<div class="news-title-wrapper">';
        html += '<h3 class="news-title"><a href="' + data.permalink + '">' + data.title + '</a></h3>';
        
        if (data.devlog_version || data.devlog_changelog) {
            html += '<div class="news-meta">';
            if (data.devlog_changelog) {
                html += '<div id="news-details-' + data.ID + '" class="news-details" hidden>';
                html += '<div class="devlog-changelog">';
                if (data.devlog_version) {
                    html += '<span class="devlog-version">📦 ' + data.devlog_version + '</span>';
                }
                if (typeIcon) {
                    html += '<span class="devlog-type">' + typeIcon + '</span>';
                }
                var changelogLines = data.devlog_changelog.split('\n');
                changelogLines.forEach(function(line) {
                    line = line.trim();
                    if (line) {
                        html += '<div class="changelog-item">' + line + '</div>';
                    }
                });
                html += '</div></div>';
            }
            html += '</div>';
            if (data.devlog_changelog) {
                html += '<button class="news-toggle-btn" data-target="news-details-' + data.ID + '">查看</button>';
            }
        }
        
        html += '</div></article>';
        return html;
    }
    
    // 绑定新闻相关事件
    function bindNewsEvents() {
        // 绑定toggle按钮事件
        var toggleBtns = document.querySelectorAll('.news-toggle-btn');
        toggleBtns.forEach(function(btn) {
            btn.removeEventListener('click', toggleNewsDetailsHandler);
            btn.addEventListener('click', toggleNewsDetailsHandler);
        });
        
        // 绑定分类筛选事件
        bindNewsCategoryFilter();
    }
    
    // toggle按钮点击处理函数
    function toggleNewsDetailsHandler() {
        var targetId = this.getAttribute('data-target');
        toggleNewsDetails(targetId);
    }
    
    // 绑定新闻分类筛选事件
    function bindNewsCategoryFilter() {
        var newsCategoryTabs = document.querySelectorAll('.news-categories .category-tab');
        var newsItems = document.querySelectorAll('.news-item');
        
        newsCategoryTabs.forEach(function(tab) {
            tab.removeEventListener('click', handleCategoryClick);
            tab.addEventListener('click', handleCategoryClick);
        });
    }
    
    function handleCategoryClick() {
        var newsCategoryTabs = document.querySelectorAll('.news-categories .category-tab');
        var newsItems = document.querySelectorAll('.news-item');
        
        // 移除所有分类标签的active类
        newsCategoryTabs.forEach(function(t) { t.classList.remove('active'); });
        this.classList.add('active');
        
        var filter = this.dataset.filter;
        
        if (filter === 'all') {
            // 显示所有日志
            newsItems.forEach(function(item) {
                item.style.display = 'block';
            });
        } else {
            // 根据分类筛选
            newsItems.forEach(function(item) {
                var categoryIds = item.dataset.categoryIds || '';
                var categoryIdArray = categoryIds.split(' ').filter(function(id) { return id !== ''; });
                if (categoryIdArray.includes(filter)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // 开发日志分类筛选功能
        var newsCategoryTabs = document.querySelectorAll('.news-categories .category-tab');
        var newsItems = document.querySelectorAll('.news-item');
        
        newsCategoryTabs.forEach(function(tab) {
            tab.addEventListener('click', handleCategoryClick);
        });
        
        // 重新绑定toggleNewsDetails功能（AJAX加载后需要重新绑定）
        
        // 分类筛选事件已在bindNewsCategoryFilter中绑定
        
        // 重新绑定toggle按钮事件
        
        // 绑定toggle按钮点击事件
        var toggleBtns = document.querySelectorAll('.news-toggle-btn');
        toggleBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = this.getAttribute('data-target');
                toggleNewsDetails(targetId);
            });
        });
        
        // 图片轮播函数
        function prevSlide(btn) {
            var slider = btn.parentElement;
            var slides = slider.querySelectorAll('.product-slide');
            var indicators = slider.querySelectorAll('.slider-indicator');
            var currentIndex = -1;
            
            slides.forEach(function(slide, index) {
                if (slide.classList.contains('active')) {
                    currentIndex = index;
                }
            });
            
            var newIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
            goToSlideByIndex(slider, newIndex);
        }
        
        function nextSlide(btn) {
            var slider = btn.parentElement;
            var slides = slider.querySelectorAll('.product-slide');
            var indicators = slider.querySelectorAll('.slider-indicator');
            var currentIndex = -1;
            
            slides.forEach(function(slide, index) {
                if (slide.classList.contains('active')) {
                    currentIndex = index;
                }
            });
            
            var newIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
            goToSlideByIndex(slider, newIndex);
        }
        
        function goToSlide(indicator, index) {
            var slider = indicator.parentElement.parentElement;
            goToSlideByIndex(slider, index);
        }
        
        function goToSlideByIndex(slider, index) {
            var slides = slider.querySelectorAll('.product-slide');
            var indicators = slider.querySelectorAll('.slider-indicator');
            
            slides.forEach(function(slide) {
                slide.classList.remove('active');
            });
            indicators.forEach(function(indicator) {
                indicator.classList.remove('active');
            });
            
            slides[index].classList.add('active');
            indicators[index].classList.add('active');
        }
        
        // 自动轮播
        var sliders = document.querySelectorAll('.product-image-slider');
        sliders.forEach(function(slider) {
            var slides = slider.querySelectorAll('.product-slide');
            if (slides.length > 1) {
                setInterval(function() {
                    var currentIndex = -1;
                    slides.forEach(function(slide, index) {
                        if (slide.classList.contains('active')) {
                            currentIndex = index;
                        }
                    });
                    var newIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                    goToSlideByIndex(slider, newIndex);
                }, 5000);
            }
        });
    });
    </script>
    <?php
}

// 渲染案例参考区域
function wuchaiwp_render_cases_section() {
    $titles_settings = wuchaiwp_get_titles_settings();
    
    // 获取后台分类设置（确保与分类导航使用相同的配置）
    $category_settings = wuchaiwp_get_category_settings();
    $case_post_type = !empty($category_settings['case_post_type']) ? $category_settings['case_post_type'] : 'case';
    if (!wuchaiwp_post_type_exists($case_post_type)) {
        $case_post_type = 'post';
    }
    
    // 获取所有案例（默认显示18个，3行×6列）
    $cases_per_page = 18; // 默认显示3行，每行6个
    $total_cases = array();
    
    // 优先使用后台推荐的案例
    $cases = wuchaiwp_get_recommended_posts('cases');
    
    // 如果没有推荐案例，使用默认方式获取
    if (empty($cases)) {
        if (wuchaiwp_post_type_exists($case_post_type)) {
            $cases = get_posts(array(
                'post_type' => $case_post_type,
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ));
        } else {
            $cases = get_posts(array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                'meta_key' => '_wp_page_template',
                'meta_value' => 'templates/single/single-case.php'
            ));
        }
    }
    
    $total_cases = $cases;
    $total_count = count($total_cases);
    
    // 获取案例设置
    $cases_settings = wuchaiwp_get_cases_settings();
    $cases_layout = $cases_settings['layout'] ?? 'grid';
    ?>
    <section id="cases" class="cases-section">
        <div class="section-header">
                <h2 class="section-title"><?php echo esc_html($titles_settings['cases_title'] ?? '💼 案例参考'); ?></h2>
                <p class="section-subtitle"><?php echo esc_html($titles_settings['cases_subtitle'] ?? '精选客户案例'); ?></p>
            </div>
            
            <!-- 案例分类导航 -->
            <?php 
            $category_settings = wuchaiwp_get_category_settings();
            $case_post_type = !empty($category_settings['case_post_type']) ? $category_settings['case_post_type'] : 'case';
            if (!wuchaiwp_post_type_exists($case_post_type)) {
                $case_post_type = 'post';
            }
            $case_taxonomies = get_object_taxonomies($case_post_type, 'objects');
            $has_case_categories = false;
            foreach ($case_taxonomies as $tax) {
                if ($tax->hierarchical) {
                    $terms = get_terms(array('taxonomy' => $tax->name, 'hide_empty' => true));
                    if (!empty($terms)) {
                        $has_case_categories = true;
                        break;
                    }
                }
            }
            ?>
            <?php if ($has_case_categories) : ?>
            <div class="case-categories-nav">
                <div class="category-tabs">
                    <button class="category-tab active" data-filter="all">全部</button>
                    <?php 
                    // 获取后台选择的案例分类
                    $selected_case_cat_ids = !empty($category_settings['case_categories']) ? $category_settings['case_categories'] : array();
                    
                    foreach ($case_taxonomies as $taxonomy) : ?>
                        <?php if ($taxonomy->hierarchical) : ?>
                            <?php $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => true)); ?>
                            <?php foreach ($terms as $term) : ?>
                                <?php 
                                // 如果设置了选择的分类，则只显示选中的
                                if (empty($selected_case_cat_ids) || in_array($term->term_id, $selected_case_cat_ids)) : ?>
                                    <button class="category-tab" data-filter="<?php echo $term->term_id; ?>" data-taxonomy="<?php echo $taxonomy->name; ?>">
                                        <?php echo esc_html($term->name); ?>
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 案例内容区域 -->
            <div class="cases-container" id="casesGrid" data-layout="<?php echo esc_attr($cases_layout); ?>">
            <?php
            if (empty($total_cases)) :
            ?>
            <div class="no-posts">
                <p>暂无案例内容</p>
            </div>
            <?php else :
            // 根据布局设置调整显示数量
            $cases_per_page = intval($cases_settings['posts_per_page'] ?? 18);
            $excerpt_length = intval($cases_settings['excerpt_length'] ?? 15);
            
            foreach ($total_cases as $index => $case) :
                // 获取案例的分类ID
                $case_terms = wp_get_post_terms($case->ID, get_object_taxonomies($case->post_type, 'names'));
                $case_term_ids = array();
                foreach ($case_terms as $term) {
                    $case_term_ids[] = $term->term_id;
                }
                $case_term_ids_str = implode(' ', $case_term_ids);
                
                // 默认只显示指定数量
                $is_hidden = $index >= $cases_per_page;
                $case_client = get_post_meta($case->ID, 'enterprise_case_client', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_client', true) ?: '客户案例';
                $case_industry = get_post_meta($case->ID, 'enterprise_case_industry', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_industry', true) ?: '行业';

// 在后面添加：
$case_industry_link = get_post_meta($case->ID, 'enterprise_case_industry_link', true);
                
                
            ?>
            
            <?php if ($cases_layout === 'list') : ?>
            <!-- 列表布局 -->
            <article class="case-list-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-list-image">
                    <?php if (has_post_thumbnail($case->ID)) : ?>
                        <a href="<?php echo get_permalink($case->ID); ?>">
                            <?php echo get_the_post_thumbnail($case->ID, 'medium'); ?>
                        </a>
                    <?php else : ?>
                        <div class="case-placeholder-list">📋</div>
                    <?php endif; ?>
                </div>
                <div class="case-list-info">
                    <div class="case-list-meta-top">
                        <span class="case-client"><?php echo esc_html($case_client); ?></span>
                        <span class="case-industry">🏭 <?php echo esc_html($case_industry); ?></span>
                    </div>
                    <h3 class="case-list-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <p class="case-list-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                    <div class="case-list-meta-bottom">
                        <span class="case-date">📅 <?php echo get_the_date('Y年m月d日', $case->ID); ?></span>
                        <a href="<?php echo get_permalink($case->ID); ?>" class="case-list-link">查看详情 →</a>
                    </div>
                </div>
            </article>
            
            <?php elseif ($cases_layout === 'card') : ?>
            <!-- 卡片布局 -->
            <article class="case-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-card-inner">
                    <div class="case-card-image">
                        <?php if (has_post_thumbnail($case->ID)) : ?>
                            <a href="<?php echo get_permalink($case->ID); ?>">
                                <?php echo get_the_post_thumbnail($case->ID, 'large'); ?>
                            </a>
                        <?php else : ?>
                            <div class="case-placeholder-card">📋</div>
                        <?php endif; ?>
                        <div class="case-card-overlay">
                            <span class="case-client"><?php echo esc_html($case_client); ?></span>
                        </div>
                    </div>
                    <div class="case-card-content">
                        <div class="case-card-meta">
                            <span class="case-industry">🏭 <?php echo esc_html($case_industry); ?></span>
                            <span class="case-date">📅 <?php echo get_the_date('Y年', $case->ID); ?></span>
                        </div>
                        <h3 class="case-card-title">
                            <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                        </h3>
                        <p class="case-card-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                        <a href="<?php echo get_permalink($case->ID); ?>" class="case-card-link">了解更多 →</a>
                    </div>
                </div>
            </article>
            
            <?php elseif ($cases_layout === 'minimal') : ?>
            <!-- 极简布局（无日期，仅标题+副标题+图标） -->
            <?php 
            // 获取图标字段，调用企业编辑页模板中设置的图标字段
            $case_icon = get_post_meta($case->ID, 'enterprise_case_icon', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_icon', true) ?: '';
            ?>
            <article class="case-minimal-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-minimal-content">
                    <h3 class="case-minimal-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <p class="case-minimal-subtitle"><?php echo esc_html($case_industry); ?></p>
                </div>
                <?php if (!empty($case_icon)) : ?>
                <span class="case-minimal-icon"><?php echo esc_html($case_icon); ?></span>
                <?php endif; ?>
            </article>
            
            <?php elseif ($cases_layout === 'simple') : ?>
            <!-- 简约网格布局（无封面） -->
            <?php 
            // 获取图标字段，调用企业编辑页模板中设置的图标字段
            $case_icon = get_post_meta($case->ID, 'enterprise_case_icon', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_icon', true) ?: '';
            ?>
            <article class="case-simple-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-simple-header">
                    <h3 class="case-simple-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <?php if (!empty($case_icon)) : ?>
                    <span class="case-simple-icon"><?php echo esc_html($case_icon); ?></span>
                    <?php endif; ?>
                </div>
                <p class="case-simple-subtitle"><?php echo esc_html($case_industry); ?></p>
                <p class="case-simple-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                <div class="case-simple-footer">
                    <span class="case-client"><?php echo esc_html($case_client); ?></span>
                    <span class="case-date"><?php echo get_the_date('Y年', $case->ID); ?></span>
                </div>
            </article>
            
            <?php elseif ($cases_layout === 'lightbox') : ?>
            <!-- 灯箱网格布局（点击图片显示大图） -->
            <article class="case-lightbox-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-lightbox-image" onclick="openSimpleLightbox('<?php echo get_the_post_thumbnail_url($case->ID, 'full'); ?>')" style="cursor: pointer;">
                    <?php if (has_post_thumbnail($case->ID)) : ?>
                        <?php echo get_the_post_thumbnail($case->ID, 'medium'); ?>
                    <?php else : ?>
                        <div class="case-placeholder-lightbox">📋</div>
                    <?php endif; ?>
                    <div class="case-lightbox-overlay">
                        <span class="case-lightbox-icon">🔍</span>
                    </div>
                </div>
                <div class="case-lightbox-info">
                    <h3 class="case-lightbox-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <!--<p class="case-lightbox-subtitle"><?php //echo esc_html($case_industry); ?></p>-->
                    
                    
                    <p class="case-lightbox-subtitle">
    <?php if ($case_industry_link) : ?>
        <a href="<?php echo esc_url($case_industry_link); ?>" target="_blank"><?php echo esc_html($case_industry); ?></a>
    <?php else : ?>
        <?php echo esc_html($case_industry); ?>
    <?php endif; ?>
</p>
                    
                    
                </div>
            </article>
            
            <?php else : ?>
            <!-- 默认网格布局 -->
            <article class="case-grid-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-grid-image">
                    <?php if (has_post_thumbnail($case->ID)) : ?>
                        <p href="#<?php //echo get_permalink($case->ID); ?>">
                            <?php echo get_the_post_thumbnail($case->ID, 'medium'); ?>
                        </p>
                    <?php else : ?>
                        <div class="case-placeholder">📋</div>
                    <?php endif; ?>
                    <div class="case-overlay">
                        <span class="case-client"><?php echo esc_html($case_client); ?></span>
                    </div>
                </div>
                <div class="case-grid-info">
                    <h3 class="case-grid-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <p class="case-grid-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                    <div class="case-grid-meta">
                        <span class="case-industry">🏭 <?php echo esc_html($case_industry); ?></span>
                        <span class="case-date">📅 <?php echo get_the_date('Y年', $case->ID); ?></span>
                    </div>
                </div>
            </article>
            <?php endif; ?>
            
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- 查看更多按钮 - 只有当案例数量超过默认显示数量时才显示 -->
        <?php if ($total_count > $cases_per_page) : ?>
        <div class="section-more" id="casesMoreSection">
            <button id="loadMoreCases" class="btn-outline">查看更多</button>
        </div>
        <?php endif; ?>
    </section>
    
    <!-- 简单灯箱弹窗 -->
    <div id="simpleLightbox" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); align-items: center; justify-content: center; cursor: pointer;">
        <img id="simpleLightboxImage" src="" alt="" style="max-width: 90%; max-height: 90vh; border-radius: 8px;">
        <span style="position: fixed; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
    </div>
    <script>
    // 简单灯箱函数
    function openSimpleLightbox(imageUrl) {
        var lightbox = document.getElementById('simpleLightbox');
        var img = document.getElementById('simpleLightboxImage');
        var closeBtn = lightbox.querySelector('span');
        
        img.src = imageUrl;
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // 点击关闭
        lightbox.onclick = function(e) {
            if (e.target === lightbox || e.target === closeBtn) {
                lightbox.style.display = 'none';
                document.body.style.overflow = '';
            }
        };
        
        // ESC键关闭
        document.addEventListener('keydown', function closeLightboxOnEsc(e) {
            if (e.key === 'Escape') {
                lightbox.style.display = 'none';
                document.body.style.overflow = '';
                document.removeEventListener('keydown', closeLightboxOnEsc);
            }
        });
    }
    </script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 案例分类筛选功能
        var caseCategoryTabs = document.querySelectorAll('.case-categories-nav .category-tab');
        //var caseCards = document.querySelectorAll('.case-grid-card, .case-list-card, .case-card-inner, .case-simple-card, .case-minimal-card, .case-lightbox-card');
        var caseCards = document.querySelectorAll('.case-grid-card, .case-list-card, .case-card, .case-card-inner, .case-simple-card, .case-minimal-card, .case-lightbox-card');
        
        caseCategoryTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                // 移除所有分类标签的active类
                caseCategoryTabs.forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');
                
                var filter = tab.dataset.filter;
                
                if (filter === 'all') {
                    // 显示所有案例（但只显示未隐藏的）
                    caseCards.forEach(function(card) {
                        if (!card.classList.contains('case-hidden')) {
                            card.style.display = 'block';
                        }
                    });
                } else {
                    // 根据分类筛选
                    caseCards.forEach(function(card) {
                        var categoryIds = card.dataset.categoryIds || '';
                        var categoryIdArray = categoryIds.split(' ').filter(function(id) { return id !== ''; });
                        if (categoryIdArray.includes(filter) && !card.classList.contains('case-hidden')) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        });
        
        // 查看更多功能
        var loadMoreBtn = document.getElementById('loadMoreCases');
        var moreSection = document.getElementById('casesMoreSection');
        var hiddenCards = document.querySelectorAll('.case-grid-card.case-hidden, .case-list-card.case-hidden, .case-simple-card.case-hidden, .case-minimal-card.case-hidden, .case-lightbox-card.case-hidden');
        var displayCount = 6; // 每次加载6个（一行）
        var currentIndex = 0;
        
        if (loadMoreBtn && hiddenCards.length > 0) {
            loadMoreBtn.addEventListener('click', function() {
                var endIndex = currentIndex + displayCount;
                
                for (var i = currentIndex; i < endIndex && i < hiddenCards.length; i++) {
                    hiddenCards[i].classList.remove('case-hidden');
                    hiddenCards[i].style.display = 'block';
                }
                
                currentIndex = endIndex;
                
                // 如果所有案例都已显示，隐藏查看更多按钮
                if (currentIndex >= hiddenCards.length) {
                    moreSection.style.display = 'none';
                }
            });
        }
        
        // 灯箱功能已移至HTML中的内联脚本
        
        function showLightbox(index) {
            if (lightboxImages.length === 0) return;
            
            var imageData = lightboxImages[index];
            lightboxImage.src = imageData.src;
            lightboxTitle.textContent = imageData.title || '';
            lightboxSubtitle.textContent = imageData.subtitle || '';
            lightboxModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        // 关闭灯箱
        function closeCaseLightbox() {
            lightboxModal.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // 导航灯箱
        function navigateCaseLightbox(direction) {
            currentImageIndex += direction;
            if (currentImageIndex < 0) {
                currentImageIndex = lightboxImages.length - 1;
            } else if (currentImageIndex >= lightboxImages.length) {
                currentImageIndex = 0;
            }
            showLightbox(currentImageIndex);
        }
        
        // 点击遮罩关闭灯箱
        lightboxModal.addEventListener('click', function(e) {
            if (e.target === lightboxModal) {
                closeCaseLightbox();
            }
        });
        
        // ESC键关闭灯箱
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && lightboxModal.classList.contains('active')) {
                closeCaseLightbox();
            }
            if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && lightboxModal.classList.contains('active')) {
                e.preventDefault();
                navigateCaseLightbox(e.key === 'ArrowRight' ? 1 : -1);
            }
        });
        
        // 将函数暴露到全局作用域，供HTML onclick属性调用
        window.showLightbox = showLightbox;
        window.closeCaseLightbox = closeCaseLightbox;
        window.navigateCaseLightbox = navigateCaseLightbox;
        
        // 将函数暴露到全局作用域，供HTML onclick属性调用
        window.showLightbox = showLightbox;
        window.closeCaseLightbox = closeCaseLightbox;
        window.navigateCaseLightbox = navigateCaseLightbox;
    });
    </script>
    <?php
}

// 渲染联系我们区域
function wuchaiwp_render_contact_section() {
    $titles_settings = wuchaiwp_get_titles_settings();
    ?>
    <section id="contact" class="contact-section">
        <div class="section-header">
            <h2 class="section-title"><?php echo esc_html($titles_settings['contact_title']); ?></h2>
            <p class="section-subtitle"><?php echo esc_html($titles_settings['contact_subtitle']); ?></p>
        </div>
        <div class="contact-content">
            <div class="contact-info">
                <?php $contact_settings = wuchaiwp_get_contact_settings(); ?>
                <?php if (!empty($contact_settings['address'])) : ?>
                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <div class="contact-detail">
                        <span class="contact-label">公司地址</span>
                        <span class="contact-value"><?php echo esc_html($contact_settings['address']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($contact_settings['phone'])) : ?>
                <div class="contact-item">
                    <span class="contact-icon">📞</span>
                    <div class="contact-detail">
                        <span class="contact-label">联系电话</span>
                        <span class="contact-value"><?php echo esc_html($contact_settings['phone']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($contact_settings['email'])) : ?>
                <div class="contact-item">
                    <span class="contact-icon">✉️</span>
                    <div class="contact-detail">
                        <span class="contact-label">电子邮箱</span>
                        <span class="contact-value"><?php echo esc_html($contact_settings['email']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($contact_settings['work_time'])) : ?>
                <div class="contact-item">
                    <span class="contact-icon">🕐</span>
                    <div class="contact-detail">
                        <span class="contact-label">工作时间</span>
                        <span class="contact-value"><?php echo esc_html($contact_settings['work_time']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                <?php 
                // 显示自定义字段
                $custom_fields = isset($contact_settings['custom_fields']) ? $contact_settings['custom_fields'] : array();
                if (!empty($custom_fields)) :
                    foreach ($custom_fields as $field) :
                        // 自定义字段需要同时有标签和值才显示
                        if (!empty($field['label']) && !empty($field['value'])) :
                ?>
                <div class="contact-item">
                    <span class="contact-icon"><?php echo esc_html($field['icon'] ?: '📌'); ?></span>
                    <div class="contact-detail">
                        <span class="contact-label"><?php echo esc_html($field['label']); ?></span>
                        <span class="contact-value"><?php echo esc_html($field['value']); ?></span>
                    </div>
                </div>
                <?php 
                        endif;
                    endforeach;
                endif;
                ?>
            </div>
            <?php if ($contact_settings['show_contact_form'] === '1') : ?>
            <div class="contact-form">
                <?php echo do_shortcode($contact_settings['contact_form_shortcode']); ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

// 渲染自定义HTML区域
// 渲染自定义HTML区域
function wuchaiwp_render_custom_section($section) {
    // 获取区域ID
    $section_id = $section['id'];
    $section_type = $section['type'];
    $section_key = str_replace('section_', '', $section_id);
    
    // 获取该区域的自定义设置（根据类型使用不同的键）
    $settings_key = 'wuchaiwp_enterprise_' . $section_id . '_settings';
    if ($section_type === 'products') {
        $settings_key = 'wuchaiwp_enterprise_' . $section_id . '_products_settings';
    } elseif ($section_type === 'cases') {
        $settings_key = 'wuchaiwp_enterprise_' . $section_id . '_cases_settings';
    }
    $section_settings = get_option($settings_key, array());
    $category_settings = get_option('wuchaiwp_enterprise_' . $section_id . '_category_settings', array());
    
    // 获取该区域的标题设置
    $titles_settings = wuchaiwp_get_titles_settings();
    $subtitle = '';
    
    // 尝试从多个地方获取副标题
    if (!empty($section['subtitle'])) {
        $subtitle = $section['subtitle'];
    } elseif (!empty($section_settings['subtitle'])) {
        $subtitle = $section_settings['subtitle'];
    } elseif (isset($titles_settings[$section_key . '_subtitle'])) {
        $subtitle = $titles_settings[$section_key . '_subtitle'];
    }
    
    // 获取文章类型（优先使用区域设置，否则使用默认值）
    $post_type = !empty($category_settings['post_type']) ? $category_settings['post_type'] : 'post';
    if (!post_type_exists($post_type)) {
        $post_type = 'post';
    }
    
    // 获取选择的分类
    $selected_categories = !empty($category_settings['categories']) ? $category_settings['categories'] : array();
    
    // 根据区域类型渲染
    switch ($section_type) {
        case 'products':
            wuchaiwp_render_custom_products_section($section, $section_settings, $category_settings);
            break;
        case 'cases':
            wuchaiwp_render_custom_cases_section($section, $section_settings, $category_settings);
            break;
        default:
            // 默认渲染方式
            ?>
            <section id="<?php echo esc_attr($section_id); ?>" class="custom-section">
                <div class="section-header">
                    <h2 class="section-title"><?php echo esc_html($section['icon'] . ' ' . $section['name']); ?></h2>
                    <?php if (!empty($subtitle)) : ?>
                    <p class="section-subtitle"><?php echo esc_html($subtitle); ?></p>
                    <?php endif; ?>
                </div>
                <div class="custom-content">
                    <?php 
                    // 如果设置了推荐文章，显示推荐文章
                    $recommended_posts = wuchaiwp_get_recommended_posts($section_key);
                    
                    if (!empty($recommended_posts)) {
                        // 显示推荐文章列表
                        echo '<div class="recommended-posts">';
                        foreach ($recommended_posts as $post) {
                            echo '<article class="recommended-post">';
                            echo '<h3><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h3>';
                            echo '<p>' . wp_trim_words(get_post_field('post_excerpt', $post->ID) ?: get_post_field('post_content', $post->ID), 20) . '</p>';
                            echo '</article>';
                        }
                        echo '</div>';
                    } elseif (!empty($section['content'])) {
                        // 否则显示自定义内容
                        echo wp_kses_post($section['content']);
                    } else {
                        // 如果都没有，显示提示
                        echo '<p class="empty-content">暂无内容，请在后台设置区域内容或推荐文章</p>';
                    }
                    ?>
                </div>
            </section>
            <?php
            break;
    }
}

// 渲染自定义产品区域
function wuchaiwp_render_custom_products_section($section, $section_settings, $category_settings) {
    $section_id = $section['id'];
    $section_key = str_replace('section_', '', $section_id);
    
    // 获取标题设置（同时检查带section_前缀和不带前缀的键）
    $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
    $section_title = $titles_settings[$section_id . '_title'] ?? $titles_settings[$section_key . '_title'] ?? ($section['name'] ?? '产品介绍');
    $section_subtitle = $titles_settings[$section_id . '_subtitle'] ?? $titles_settings[$section_key . '_subtitle'] ?? $section['subtitle'] ?? '';
    
    
    // 获取文章类型
    $post_type = !empty($category_settings['post_type']) ? $category_settings['post_type'] : 'product';
    if (!post_type_exists($post_type)) {
        $post_type = 'post';
    }
    
    // 获取选择的分类
    $selected_categories = !empty($category_settings['categories']) ? $category_settings['categories'] : array();
    
    // 获取产品设置
    $excerpt_length = !empty($section_settings['excerpt_length']) ? intval($section_settings['excerpt_length']) : 500;
    $show_read_more = !empty($section_settings['show_read_more']) ? $section_settings['show_read_more'] : '1';
    $read_more_text = !empty($section_settings['read_more_text']) ? $section_settings['read_more_text'] : '查看更多';
    
    // 获取产品分类
    $product_taxonomies = get_object_taxonomies($post_type, 'objects');
    
    // 获取产品数据（优先使用推荐文章）
    //$products = wuchaiwp_get_recommended_posts($section_key);
    $products = wuchaiwp_get_recommended_posts($section_id);
    if (empty($products)) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        // 如果有选择的分类，添加分类筛选
        if (!empty($selected_categories) && !empty($product_taxonomies)) {
            $tax_query = array('relation' => 'OR');
            foreach ($product_taxonomies as $tax) {
                if ($tax->hierarchical) {
                    $tax_query[] = array(
                        'taxonomy' => $tax->name,
                        'field' => 'term_id',
                        'terms' => $selected_categories
                    );
                }
            }
            $args['tax_query'] = $tax_query;
        }
        
        $products = get_posts($args);
    }
    
    // 处理产品数据
    $product_data = array();
    foreach ($products as $product) {
        // 获取分类ID
        $term_ids = array();
        foreach ($product_taxonomies as $tax) {
            if ($tax->hierarchical) {
                $terms = get_the_terms($product->ID, $tax->name);
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $term_ids[] = $term->term_id;
                    }
                }
            }
        }
        $term_ids_str = implode(' ', $term_ids);
        
        // 获取文章中的所有图片
        $images = array();
        if (has_post_thumbnail($product->ID)) {
            $images[] = get_the_post_thumbnail_url($product->ID, 'large');
        }
        $content = get_post_field('post_content', $product->ID);
        preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $img_src) {
                if (!preg_match('/^https?:/', $img_src)) {
                    $img_src = home_url($img_src);
                }
                if (!in_array($img_src, $images)) {
                    $images[] = $img_src;
                }
            }
        }
        
        $product_data[] = array(
            'id' => $product->ID,
            'title' => get_the_title($product->ID),
            'permalink' => get_permalink($product->ID),
            'images' => $images,
            'image' => !empty($images) ? $images[0] : '',
            'excerpt' => wp_trim_words(get_post_field('post_excerpt', $product->ID) ?: get_post_field('post_content', $product->ID), $excerpt_length / 15),
            'term_ids' => $term_ids_str
        );
    }
    
    // 获取可用分类
    $available_categories = array();
    foreach ($product_taxonomies as $tax) {
        if ($tax->hierarchical) {
            $terms = get_terms(array('taxonomy' => $tax->name, 'hide_empty' => true));
            foreach ($terms as $term) {
                if (empty($selected_categories) || in_array($term->term_id, $selected_categories)) {
                    $available_categories[] = $term;
                }
            }
        }
    }
    
    ?>
    <section id="<?php echo esc_attr($section_id); ?>" class="products-section">
        
        <div class="section-header">
        <h2 class="section-title"><?php echo esc_html($section['icon'] . ' ' . $section_title); ?></h2>
        <?php if (!empty($section_subtitle)) : ?>
        <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
        <?php endif; ?>
        </div>
        
        
        <?php if (empty($product_data)) : ?>
        <div class="no-posts">
            <p>暂无产品内容</p>
        </div>
        <?php else : ?>
        
        <!-- 产品分类导航 -->
        <?php if (!empty($available_categories)) : ?>
        <div class="product-categories-nav">
            <div class="category-tabs">
                <button class="category-tab active" data-filter="all">全部</button>
                <?php foreach ($available_categories as $term) : ?>
                    <button class="category-tab" data-filter="<?php echo $term->term_id; ?>">
                        <?php echo esc_html($term->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Tab栏 -->
        <div class="product-tabs-wrapper">
            <div class="product-tabs" id="productTabs_<?php echo $section_id; ?>">
                <?php foreach ($product_data as $index => $product) : ?>
                    <button class="product-tab <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" data-category-ids="<?php echo $product['term_ids']; ?>">
                        <?php echo esc_html($product['title']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- 内容区域 -->
        <div class="product-content-wrapper" id="productContentWrapper_<?php echo $section_id; ?>">
            <?php foreach ($product_data as $index => $product) : ?>
            <div class="product-content" data-index="<?php echo $index; ?>" data-category-ids="<?php echo $product['term_ids']; ?>" <?php echo $index > 0 ? 'style="display: none;"' : ''; ?>>
                <div class="product-card">
                    <div class="product-image-col">
                        <?php if (!empty($product['images'])) : ?>
                            <div class="product-image-slider" data-images="<?php echo esc_attr(json_encode($product['images'])); ?>">
                                <?php foreach ($product['images'] as $img_index => $img_src) : ?>
                                    <div class="product-slide <?php echo $img_index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($product['title']); ?>" />
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($product['images']) > 1) : ?>
                                    <button class="slider-prev" onclick="prevSlide(this)">‹</button>
                                    <button class="slider-next" onclick="nextSlide(this)">›</button>
                                    <div class="slider-indicators">
                                        <?php foreach ($product['images'] as $img_index => $img_src) : ?>
                                            <button class="slider-indicator <?php echo $img_index === 0 ? 'active' : ''; ?>" onclick="goToSlide(this, <?php echo $img_index; ?>)"></button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="no-image-placeholder">
                                <span class="no-image-icon">📷</span>
                                <p>暂无图片</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info-col">
                        <h3 class="product-title"><?php echo esc_html($product['title']); ?></h3>
                        <div class="product-excerpt"><?php echo $product['excerpt']; ?></div>
                        <?php if ($show_read_more === '1') : ?>
                        <a href="<?php echo esc_url($product['permalink']); ?>" class="product-link"><?php echo esc_html($read_more_text); ?> →</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
    <?php
}

// 渲染自定义案例区域
function wuchaiwp_render_custom_cases_section($section, $section_settings, $category_settings) {
    $section_id = $section['id'];
    $section_key = str_replace('section_', '', $section_id);
    
    // 获取标题设置（同时检查带section_前缀和不带前缀的键）
    $titles_settings = get_option('wuchaiwp_enterprise_titles_settings', array());
    $section_title = $titles_settings[$section_id . '_title'] ?? $titles_settings[$section_key . '_title'] ?? ($section['name'] ?? '案例展示');
    $section_subtitle = $titles_settings[$section_id . '_subtitle'] ?? $titles_settings[$section_key . '_subtitle'] ?? $section['subtitle'] ?? '';
    
    // 获取文章类型
    $post_type = !empty($category_settings['post_type']) ? $category_settings['post_type'] : 'case';
    if (!post_type_exists($post_type)) {
        $post_type = 'post';
    }
    
    // 获取选择的分类
    $selected_categories = !empty($category_settings['categories']) ? $category_settings['categories'] : array();
    
    // 获取案例设置
    $excerpt_length = !empty($section_settings['excerpt_length']) ? intval($section_settings['excerpt_length']) : 15;
    $posts_per_page = !empty($section_settings['posts_per_page']) ? intval($section_settings['posts_per_page']) : 18;
    $layout = !empty($section_settings['layout']) ? $section_settings['layout'] : 'grid';
    
    // 获取案例分类
    $case_taxonomies = get_object_taxonomies($post_type, 'objects');
    
    // 获取案例数据（优先使用推荐文章）
    //$cases = wuchaiwp_get_recommended_posts($section_key);
    $cases = wuchaiwp_get_recommended_posts($section_id);
    if (empty($cases)) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        // 如果有选择的分类，添加分类筛选
        if (!empty($selected_categories) && !empty($case_taxonomies)) {
            $tax_query = array('relation' => 'OR');
            foreach ($case_taxonomies as $tax) {
                if ($tax->hierarchical) {
                    $tax_query[] = array(
                        'taxonomy' => $tax->name,
                        'field' => 'term_id',
                        'terms' => $selected_categories
                    );
                }
            }
            $args['tax_query'] = $tax_query;
        }
        
        $cases = get_posts($args);
    }
    
    $total_cases = $cases;
    $total_count = count($total_cases);
    
    // 检查是否有分类
    $has_case_categories = false;
    foreach ($case_taxonomies as $tax) {
        if ($tax->hierarchical) {
            $terms = get_terms(array('taxonomy' => $tax->name, 'hide_empty' => false));
            if (!is_wp_error($terms) && !empty($terms)) {
                $has_case_categories = true;
                break;
            }
        }
    }
    
    ?>
    <section id="<?php echo esc_attr($section_id); ?>" class="cases-section">
        <div class="section-header">
            <h2 class="section-title"><?php echo esc_html($section['icon'] . ' ' . $section_title); ?></h2>
            <?php if (!empty($section_subtitle)) : ?>
            <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- 案例分类导航 -->
        <?php if ($has_case_categories) : ?>
        <div class="case-categories-nav">
            <div class="category-tabs">
                <button class="category-tab active" data-filter="all">全部</button>
                <?php 
                foreach ($case_taxonomies as $taxonomy) : ?>
                    <?php if ($taxonomy->hierarchical) : ?>
                        <?php $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => false)); ?>
                        <?php if (is_wp_error($terms)) { continue; } ?>
                        <?php foreach ($terms as $term) : ?>
                            <?php 
                            // 如果设置了选择的分类，则只显示选中的
                            if (empty($selected_categories) || in_array($term->term_id, $selected_categories)) : ?>
                                <button class="category-tab" data-filter="<?php echo $term->term_id; ?>" data-taxonomy="<?php echo $taxonomy->name; ?>">
                                    <?php echo esc_html($term->name); ?>
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 案例内容区域 -->
        <div class="cases-container" id="casesGrid_<?php echo $section_id; ?>" data-layout="<?php echo esc_attr($layout); ?>">
            <?php if (empty($total_cases)) : ?>
            <div class="no-posts">
                <p>暂无案例内容</p>
            </div>
            <?php else : ?>
                <?php foreach ($total_cases as $index => $case) : ?>
                    <?php
                    // 获取案例的分类ID
                    $case_term_ids = array();
                    $case_terms = wp_get_post_terms($case->ID, get_object_taxonomies($case->post_type, 'names'));
                    if (!is_wp_error($case_terms)) {
                        foreach ($case_terms as $term) {
                            $case_term_ids[] = $term->term_id;
                        }
                    }
                    $case_term_ids_str = implode(' ', $case_term_ids);
                    
                    // 默认只显示指定数量
                    $is_hidden = $index >= $posts_per_page;
                    $case_client = get_post_meta($case->ID, 'enterprise_case_client', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_client', true) ?: '客户案例';
                    $case_industry = get_post_meta($case->ID, 'enterprise_case_industry', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_industry', true) ?: '行业';
                    ?>
                    
                    
                                <?php if ($layout === 'list') : ?>
            <!-- 列表布局 -->
            <article class="case-list-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-list-image">
                    <?php if (has_post_thumbnail($case->ID)) : ?>
                        <a href="<?php echo get_permalink($case->ID); ?>">
                            <?php echo get_the_post_thumbnail($case->ID, 'medium'); ?>
                        </a>
                    <?php else : ?>
                        <div class="case-placeholder-list">📋</div>
                    <?php endif; ?>
                </div>
                <div class="case-list-info">
                    <div class="case-list-meta-top">
                        <span class="case-client"><?php echo esc_html($case_client); ?></span>
                        <span class="case-industry">🏭 <?php echo esc_html($case_industry); ?></span>
                    </div>
                    <h3 class="case-list-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <p class="case-list-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                    <div class="case-list-meta-bottom">
                        <span class="case-date">📅 <?php echo get_the_date('Y年m月d日', $case->ID); ?></span>
                        <a href="<?php echo get_permalink($case->ID); ?>" class="case-list-link">查看详情 →</a>
                    </div>
                </div>
            </article>

            <?php elseif ($layout === 'card') : ?>
            <!-- 卡片布局 -->
            <article class="case-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-card-inner">
                    <div class="case-card-image">
                        <?php if (has_post_thumbnail($case->ID)) : ?>
                            <a href="<?php echo get_permalink($case->ID); ?>">
                                <?php echo get_the_post_thumbnail($case->ID, 'large'); ?>
                            </a>
                        <?php else : ?>
                            <div class="case-placeholder-card">📋</div>
                        <?php endif; ?>
                        <div class="case-card-overlay">
                            <span class="case-client"><?php echo esc_html($case_client); ?></span>
                        </div>
                    </div>
                    <div class="case-card-content">
                        <div class="case-card-meta">
                            <span class="case-industry">🏭 <?php echo esc_html($case_industry); ?></span>
                            <span class="case-date">📅 <?php echo get_the_date('Y年', $case->ID); ?></span>
                        </div>
                        <h3 class="case-card-title">
                            <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                        </h3>
                        <p class="case-card-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                        <a href="<?php echo get_permalink($case->ID); ?>" class="case-card-link">了解更多 →</a>
                    </div>
                </div>
            </article>

            <?php elseif ($layout === 'minimal') : ?>
            <!-- 极简布局（无日期，仅标题+副标题+图标） -->
            <?php 
            // 获取图标字段
            $case_icon = get_post_meta($case->ID, 'enterprise_case_icon', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_icon', true) ?: '';
            ?>
            <article class="case-minimal-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-minimal-content">
                    <h3 class="case-minimal-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <p class="case-minimal-subtitle"><?php echo esc_html($case_industry); ?></p>
                </div>
                <?php if (!empty($case_icon)) : ?>
                <span class="case-minimal-icon"><?php echo esc_html($case_icon); ?></span>
                <?php endif; ?>
            </article>

            <?php elseif ($layout === 'simple') : ?>
            <!-- 简约网格布局（无封面） -->
            <?php 
            // 获取图标字段
            $case_icon = get_post_meta($case->ID, 'enterprise_case_icon', true) ?: get_post_meta($case->ID, 'wuchaiwp_case_icon', true) ?: '';
            ?>
            <article class="case-simple-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-simple-header">
                    <h3 class="case-simple-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <?php if (!empty($case_icon)) : ?>
                    <span class="case-simple-icon"><?php echo esc_html($case_icon); ?></span>
                    <?php endif; ?>
                </div>
                <p class="case-simple-subtitle"><?php echo esc_html($case_industry); ?></p>
                <p class="case-simple-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
                <div class="case-simple-footer">
                    <span class="case-client"><?php echo esc_html($case_client); ?></span>
                    <span class="case-date"><?php echo get_the_date('Y年', $case->ID); ?></span>
                </div>
            </article>

            <?php elseif ($layout === 'lightbox') : ?>
            <!-- 灯箱网格布局（点击图片显示大图） -->
            <article class="case-lightbox-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
                <div class="case-lightbox-image" onclick="openSimpleLightbox('<?php echo get_the_post_thumbnail_url($case->ID, 'full'); ?>')" style="cursor: pointer;">
                    <?php if (has_post_thumbnail($case->ID)) : ?>
                        <?php echo get_the_post_thumbnail($case->ID, 'medium'); ?>
                    <?php else : ?>
                        <div class="case-placeholder-lightbox">📋</div>
                    <?php endif; ?>
                    <div class="case-lightbox-overlay">
                        <span class="case-lightbox-icon">🔍</span>
                    </div>
                </div>
                <div class="case-lightbox-info">
                    <h3 class="case-lightbox-title">
                        <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
                    </h3>
                    <p class="case-lightbox-subtitle">
                        <?php if ($case_industry_link) : ?>
                            <a href="<?php echo esc_url($case_industry_link); ?>" target="_blank"><?php echo esc_html($case_industry); ?></a>
                        <?php else : ?>
                            <?php echo esc_html($case_industry); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </article>

            <?php else : ?>
<!-- 默认网格布局 -->
<article class="case-grid-card <?php echo $is_hidden ? 'case-hidden' : ''; ?>" data-category-ids="<?php echo $case_term_ids_str; ?>" data-index="<?php echo $index; ?>">
    <div class="case-grid-image">
        <?php if (has_post_thumbnail($case->ID)) : ?>
            <p href="#<?php //echo get_permalink($case->ID); ?>">
                <?php echo get_the_post_thumbnail($case->ID, 'medium'); ?>
            </p>
        <?php else : ?>
            <div class="case-placeholder">📋</div>
        <?php endif; ?>
        <div class="case-overlay">
            <span class="case-client"><?php echo esc_html($case_client); ?></span>
        </div>
    </div>
    <div class="case-grid-info">
        <h3 class="case-grid-title">
            <a href="<?php echo get_permalink($case->ID); ?>"><?php echo get_the_title($case->ID); ?></a>
        </h3>
        <p class="case-grid-excerpt"><?php echo wp_trim_words(get_post_field('post_excerpt', $case->ID), $excerpt_length); ?></p>
        <div class="case-grid-meta">
            <span class="case-industry">🏭 <?php echo esc_html($case_industry); ?></span>
            <span class="case-date">📅 <?php echo get_the_date('Y年', $case->ID); ?></span>
        </div>
    </div>
</article>
<?php endif; ?>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- 查看更多按钮 -->
        <!-- <?php if ($total_count > $posts_per_page) : ?>
        <div class="case-load-more" id="caseLoadMore_<?php echo $section_id; ?>">
            <button class="load-more-btn">查看更多案例</button>
        </div>
        <?php endif; ?> -->
    </section>
    <?php
}


get_header(); ?>

<?php 
// 获取归档页标题设置（在页面开头定义，确保所有区域都能访问）
$titles_settings = wuchaiwp_get_titles_settings();
?>

<div class="enterprise-homepage">
    <?php
    // 获取区域配置并按状态渲染
    $sections = wuchaiwp_get_sections();
    
    // 内置区域ID列表（这些区域有专门的渲染函数）
    $built_in_sections = array('hero', 'about', 'products', 'news', 'cases', 'contact');
    
    foreach ($sections as $section) {
        if ($section['status'] !== 'active') {
            continue;
        }
        
        $section_id = $section['id'];
        $is_custom_section = strpos($section_id, 'section_') === 0;
        
        // 如果是自定义区域（ID以section_开头），使用自定义渲染函数
        if ($is_custom_section) {
            wuchaiwp_render_custom_section($section);
        } else {
            // 内置区域，使用对应的内置渲染函数
            switch ($section['type']) {
                case 'hero':
                    wuchaiwp_render_hero_section();
                    break;
                case 'about':
                    wuchaiwp_render_about_section();
                    break;
                case 'products':
                    wuchaiwp_render_products_section();
                    break;
                case 'news':
                    wuchaiwp_render_news_section();
                    break;
                case 'cases':
                    wuchaiwp_render_cases_section();
                    break;
                case 'contact':
                    wuchaiwp_render_contact_section();
                    break;
                default:
                    wuchaiwp_render_custom_section($section);
                    break;
            }
        }
    }
    ?>
</div>

<style>
/* 企业官网首页样式 */
.enterprise-homepage {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Hero 区域 */
.hero-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 80px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    margin-bottom: 60px;
    padding: 60px 40px;
}

.hero-content {
    flex: 1;
    color: white;
}

.hero-title {
    font-size: 42px;
    font-weight: 700;
    margin: 0 0 20px 0;
}

.hero-description {
    font-size: 18px;
    margin: 0 0 30px 0;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 15px;
    /*justify-content: center;*/
}

.btn-primary {
    padding: 12px 30px;
    background: white;
    color: #667eea;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-secondary {
    padding: 12px 30px;
    background: transparent;
    color: white;
    border: 2px solid white;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: white;
    color: #667eea;
}

.hero-visual {
    font-size: 120px;
}

/* 通用区块样式 */
.section-header {
    text-align: center;
    margin-bottom: 40px;
}

.section-title {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.section-subtitle {
    font-size: 16px;
    color: #666;
    margin: 0;
}

.section-more {
    text-align: center;
    margin-top: 30px;
}

.btn-outline {
    display: inline-block;
    padding: 10px 25px;
    border: 2px solid #667eea;
    color: #667eea;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

/* 企业简介 */
/* 企业简介 */
.about-section {
    margin-bottom: 60px;
}

.about-content {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* 图文垂直布局容器 */
.about-content-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
}


/*
.about-image-item {
    cursor: pointer;
    overflow: hidden;
    border-radius: 12px;
    aspect-ratio: 16/10;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}
*/


/* 图片网格 */
.about-images-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;  /* 减小间距 */
}

/* 更小的竖图 */
.about-image-item {
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;
    aspect-ratio: 3/4;  /* 保持竖图比例 */
    max-height: 580px;  /* 设置最大高度 */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.about-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* 响应式调整 */
@media (max-width: 1200px) {
    .about-images-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .about-image-item {
        max-height: 460px;
    }
}

@media (max-width: 768px) {
    .about-images-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .about-image-item {
        max-height: 440px;
    }
}

@media (max-width: 480px) {
    .about-images-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }
    .about-image-item {
        max-height: 420px;
        border-radius: 6px;
    }
}

.about-images {
    margin-bottom: 20px;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 15px;
}

.image-item {
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;
    aspect-ratio: 4/3;
    transition: transform 0.3s ease;
}

.image-item:hover {
    transform: scale(1.05);
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.about-featured-image {
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.about-featured-image img {
    width: 100%;
    height: auto;
    display: block;
}


.about-page-header {
    display: flex;
    align-items: center;
    justify-content: center;  /* 标题居中 */
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    position: relative;
    border-bottom: none;  /* 移除原有的蓝色实线 */
}

.about-page-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 5%;
    right: 5%;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(255,255,255,0.7), transparent);
}

.about-page-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.about-post-type {
    display: inline-block;
    padding: 4px 12px;
    background: #667eea;
    color: white;
    font-size: 12px;
    border-radius: 12px;
    font-weight: 500;
}

.about-full-content {
    line-height: 1.8;
    color: #444;
}

.about-full-content p {
    margin: 0 0 15px 0;
}

.about-full-content h2 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 25px 0 15px 0;
}

.about-full-content h3 {
    font-size: 18px;
    font-weight: 600;
    color: #444;
    margin: 20px 0 12px 0;
}

.about-full-content ul, .about-full-content ol {
    margin: 0 0 15px 25px;
}

.about-full-content li {
    margin-bottom: 8px;
}

.about-full-content blockquote {
    border-left: 4px solid #667eea;
    padding-left: 15px;
    margin: 15px 0;
    color: #666;
    font-style: italic;
}

.about-read-more {
    margin-top: 25px;
    text-align: center;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.read-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.read-more-btn .arrow {
    transition: transform 0.3s ease;
}

.read-more-btn:hover .arrow {
    transform: translateX(5px);
}

.about-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 16px;
}

.about-empty-state .empty-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.about-empty-state h4 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 0 0 10px 0;
}

.about-empty-state p {
    color: #666;
    margin: 0 0 20px 0;
}

.about-empty-state .button {
    display: inline-block;
    padding: 10px 25px;
    background: #667eea;
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.3s;
}

.about-empty-state .button:hover {
    background: #5a6fd6;
}

.about-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.stat-value {
    display: block;
    font-size: 36px;
    font-weight: 700;
    color: #667eea;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

/* 图片灯箱样式 */
/* 灯箱样式 */
.lightbox {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
    justify-content: center;
    align-items: center;
}

.lightbox-content {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

/* 切换按钮 - 显示在屏幕两侧 */
.lightbox-prev,
.lightbox-next {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    cursor: pointer;
    font-size: 24px;
    z-index: 1001;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background 0.3s;
}

.lightbox-prev {
    left: 20px;
}

.lightbox-next {
    right: 20px;
}

.lightbox-prev:hover,
.lightbox-next:hover {
    background: rgba(255,255,255,0.4);
}

/* 关闭按钮 */
.lightbox-close {
    position: fixed;
    top: 20px;
    right: 30px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
}

/* 响应式调整 */
@media (max-width: 768px) {
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }
    
    .about-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-value {
        font-size: 28px;
    }
}

@media (max-width: 480px) {
    .images-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .about-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* 产品介绍 */
.products-section {
    margin-bottom: 60px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-cover img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.product-info {
    padding: 20px;
}

.product-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 10px 0;
}

.product-title a {
    color: #2c3e50;
    text-decoration: none;
}

.product-excerpt {
    font-size: 14px;
    color: #666;
    margin: 0 0 12px 0;
}

.product-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.product-tags .tag {
    padding: 3px 10px;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 12px;
    color: #666;
}

/* 开发日志 */
.news-section {
    margin-bottom: 60px;
    background: #f8f9fa;
    padding: 40px;
    border-radius: 12px;
}

.news-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.news-main-wrapper {
    display: flex;
    gap: 40px;
}

.news-list-wrapper {
    flex: 2;
    display: flex;
    gap: 30px;
}

.news-column {
    flex: 1;
}

.news-year-month {
    font-size: 14px;
    font-weight: 600;
    color: #667eea;
    margin: 20px 0 10px 0;
    padding-left: 10px;
    border-left: 3px solid #667eea;
}

.news-column .news-year-month:first-child {
    margin-top: 0;
}

.news-item {
    padding: 12px 15px;
    background: white;
    border-radius: 6px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.news-item:last-child {
    margin-bottom: 0;
}

.news-date {
    font-size: 14px;
    font-weight: 600;
    color: #667eea;
    min-width: 30px;
    text-align: center;
}

.news-title a {
    color: #2c3e50;
    text-decoration: none;
}

.news-title a:hover {
    color: #667eea;
}

.news-excerpt {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.news-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.news-pagination button {
    padding: 8px 16px;
    background: #667eea;
    color: white;
    border: none;
    text-decoration: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

.news-pagination button:hover {
    background: #764ba2;
}

.news-pagination .loading {
    color: #667eea;
    font-style: italic;
}

.pagination-info {
    font-size: 14px;
    color: #666;
}

.news-list {
    flex: 2;
}

.news-main-wrapper .news-sidebar {
    flex: 1;
}

.news-announcement {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    color: #333;
    border: 1px solid #ddd6fe;
    border-left: 4px solid #667eea;
}

.news-announcement h4 {
    margin: 0 0 15px 0;
    color: #667eea;
    font-size: 16px;
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.news-announcement h5 {
    margin: 0 0 8px 0;
    color: #2c3e50;
    font-size: 16px;
}

.news-announcement p {
    margin: 0;
    font-size: 14px;
    color: #666;
    line-height: 1.7;
}

.news-announcement a {
    color: #667eea;
    text-decoration: none;
    display: block;
}

.news-announcement a:hover h5 {
    color: #667eea;
    text-decoration: underline;
}

/* 旧样式保留（用于其他地方） */
.news-highlight {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    color: #333;
    border: 1px solid #e0e4eb;
}

.news-highlight h4 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.news-highlight h5 {
    margin: 0 0 8px 0;
    color: #2c3e50;
    font-size: 15px;
}

.news-highlight p {
    margin: 0;
    font-size: 14px;
    color: #666;
    line-height: 1.6;
}

.news-highlight a {
    color: #667eea;
    text-decoration: none;
    display: block;
}

.news-highlight a:hover h5 {
    color: #667eea;
}

.news-categories h4 {
    margin: 0 0 15px 0;
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

/* 开发日志分类导航 */
.news-categories .category-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.news-categories .category-tab {
    padding: 6px 14px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 15px;
    cursor: pointer;
    font-size: 13px;
    color: #666;
    transition: all 0.3s ease;
}

.news-categories .category-tab:hover {
    border-color: #667eea;
    color: #667eea;
}

.news-categories .category-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

.category-item {
    display: block;
    padding: 8px 15px;
    background: white;
    border-radius: 4px;
    margin-bottom: 8px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}

/* 案例参考 */
.cases-section {
    margin-bottom: 60px;
}

/* 案例分类导航 */
.case-categories-nav {
    margin-bottom: 20px;
}

.case-categories-nav .category-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.case-categories-nav .category-tab {
    padding: 8px 20px;
    background: #f5f5f5;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    color: #666;
    transition: all 0.3s ease;
}

.case-categories-nav .category-tab:hover {
    background: #e8e8e8;
    color: #333;
}

.case-categories-nav .category-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.cases-container {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
}

/* 网格布局样式 */
.cases-container[data-layout="grid"] {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
}

/* 响应式布局 - 案例参考区域 */
@media (max-width: 1200px) {
    .cases-container,
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 768px) {
    .cases-container,
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .cases-container,
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}

.case-grid-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.case-grid-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.case-grid-image {
    position: relative;
    width: 100%;
    padding-top: 75%;
    overflow: hidden;
}

.case-grid-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
}

.case-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 8px 12px;
    background: rgba(0,0,0,0.6);
}

.case-client {
    color: white;
    font-size: 12px;
}

.case-grid-info {
    padding: 15px;
}

.case-grid-title {
    font-size: 15px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.case-grid-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-grid-title a:hover {
    color: #667eea;
}

.case-grid-excerpt {
    font-size: 13px;
    color: #666;
    margin: 0 0 10px 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.case-grid-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 12px;
    color: #888;
}

/* 列表布局样式 */
.cases-container[data-layout="list"] {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.case-list-card {
    display: flex;
    gap: 20px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: box-shadow 0.3s;
}

.case-list-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.case-list-image {
    flex-shrink: 0;
    width: 180px;
    height: 120px;
    overflow: hidden;
    border-radius: 8px;
}

.case-list-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-placeholder-list {
    width: 100%;
    height: 100%;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.case-list-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.case-list-meta-top {
    display: flex;
    gap: 12px;
    font-size: 12px;
    color: #888;
    margin-bottom: 10px;
}

.case-list-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 10px 0;
}

.case-list-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-list-title a:hover {
    color: #667eea;
}

.case-list-excerpt {
    font-size: 14px;
    color: #666;
    margin: 0 0 12px 0;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

.case-list-meta-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.case-list-date {
    font-size: 13px;
    color: #888;
}

.case-list-link {
    font-size: 14px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.case-list-link:hover {
    color: #764ba2;
}

/* 卡片布局样式 */
.cases-container[data-layout="card"] {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
}

.case-card-inner {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.case-card-inner:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.case-card-image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.case-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-placeholder-card {
    width: 100%;
    height: 100%;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
}

.case-card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px 15px;
    background: rgba(0,0,0,0.6);
}

.case-card-content {
    padding: 20px;
}

.case-card-meta {
    display: flex;
    gap: 12px;
    font-size: 12px;
    color: #888;
    margin-bottom: 12px;
}

.case-card-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 12px 0;
}

.case-card-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-card-title a:hover {
    color: #667eea;
}

.case-card-excerpt {
    font-size: 14px;
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.case-card-link {
    display: inline-block;
    font-size: 14px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.case-card-link:hover {
    color: #764ba2;
}

/* 简约网格布局样式 */
.cases-container[data-layout="simple"] {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
}

/* 响应式设计 - 平板端 */
@media (max-width: 992px) {
    .cases-container[data-layout="simple"] {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 12px;
    }
}

/* 响应式设计 - 手机端 */
@media (max-width: 768px) {
    .cases-container[data-layout="simple"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
}

.case-simple-card {
    background: white;
    border-radius: 8px;
    padding: 18px;
    box-shadow: 0 1px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 3px solid #667eea;
}

.case-simple-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.case-simple-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.case-simple-title {
    font-size: 15px;
    font-weight: 600;
    margin: 0;
    flex: 1;
    margin-right: 10px;
}

.case-simple-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-simple-title a:hover {
    color: #667eea;
}

.case-simple-icon {
    font-size: 18px;
    flex-shrink: 0;
}

.case-simple-subtitle {
    font-size: 12px;
    color: #667eea;
    margin: 0 0 10px 0;
    font-weight: 500;
}

.case-simple-excerpt {
    font-size: 13px;
    color: #666;
    margin: 0 0 12px 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.case-simple-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
    font-size: 11px;
    color: #888;
}

.case-simple-footer .case-client {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
}

.case-simple-footer .case-date {
    display: flex;
    align-items: center;
    gap: 4px;
}

.case-simple-footer .case-date::before {
    content: '📅';
    font-size: 10px;
}

/* 响应式设计 - 平板端 */
@media (max-width: 992px) {
    .cases-container[data-layout="simple"] {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 12px;
    }
}

/* 响应式设计 - 手机端 */
@media (max-width: 768px) {
    .cases-container[data-layout="simple"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
}

/* 极简布局样式 */
.cases-container[data-layout="minimal"] {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
}

.case-minimal-card {
    background: white;
    border-radius: 6px;
    padding: 14px 16px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    transition: transform 0.2s, box-shadow 0.2s;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
}

.case-minimal-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    border-bottom-color: #667eea;
}

.case-minimal-content {
    flex: 1;
    overflow: hidden;
}

.case-minimal-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.case-minimal-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-minimal-title a:hover {
    color: #667eea;
}

.case-minimal-subtitle {
    font-size: 11px;
    color: #888;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.case-minimal-icon {
    font-size: 18px;
    flex-shrink: 0;
}

/* 极简布局响应式设计 */
@media (max-width: 992px) {
    .cases-container[data-layout="minimal"] {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
    }
}

@media (max-width: 768px) {
    .cases-container[data-layout="minimal"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
}

/* 响应式布局 */
@media (max-width: 1200px) {
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 900px) {
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .case-list-card {
        flex-direction: column;
    }
    
    .case-list-image {
        width: 100%;
        height: 180px;
    }
}

@media (max-width: 480px) {
    .cases-container[data-layout="grid"] {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}

/* 灯箱网格布局样式 */
.case-lightbox-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.case-lightbox-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.case-lightbox-image {
    position: relative;
    width: 100%;
    padding-top: 75%;
    overflow: hidden;
    cursor: pointer;
}

.case-lightbox-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.case-lightbox-card:hover .case-lightbox-image img {
    transform: scale(1.05);
}

.case-placeholder-lightbox {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
}

.case-lightbox-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s;
    cursor: pointer;
}

.case-lightbox-card:hover .case-lightbox-overlay {
    background: rgba(0,0,0,0.4);
    opacity: 1;
}

.case-lightbox-icon {
    font-size: 32px;
    color: white;
}

.case-lightbox-info {
    padding: 15px;
    text-align: center;
}

.case-lightbox-title {
    font-size: 15px;
    font-weight: 600;
    margin: 0 0 5px 0;
}

.case-lightbox-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-lightbox-title a:hover {
    color: #667eea;
}

.case-lightbox-subtitle {
    font-size: 13px;
    color: #666;
    margin: 0;
}

/* 灯箱弹窗样式 */
.case-lightbox-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s;
}

.case-lightbox-modal.active {
    display: flex;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.case-lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    animation: zoomIn 0.3s;
}

@keyframes zoomIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.case-lightbox-content img {
    max-width: 100%;
    max-height: 85vh;
    border-radius: 8px;
    box-shadow: 0 4px 30px rgba(0,0,0,0.5);
}

.case-lightbox-caption {
    text-align: center;
    color: white;
    margin-top: 15px;
}

.case-lightbox-caption-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 5px 0;
}

.case-lightbox-caption-subtitle {
    font-size: 14px;
    color: #ccc;
    margin: 0;
}

.case-lightbox-close {
    position: absolute;
    top: -45px;
    right: 0;
    color: white;
    font-size: 32px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.case-lightbox-close:hover {
    color: #667eea;
}

.case-lightbox-prev, .case-lightbox-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    padding: 10px;
    transition: all 0.3s;
    user-select: none;
}

.case-lightbox-prev {
    left: -50px;
}

.case-lightbox-next {
    right: -50px;
}

.case-lightbox-prev:hover, .case-lightbox-next:hover {
    color: #667eea;
    background: rgba(255,255,255,0.1);
}

/* 隐藏的案例卡片 */
.case-grid-card.case-hidden, .case-list-card.case-hidden, .case-card.case-hidden, .case-lightbox-card.case-hidden {
    display: none;
}
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.case-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.case-cover {
    position: relative;
    height: 180px;
}

.case-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-placeholder {
    width: 100%;
    height: 100%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
}

.case-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px 15px;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
}

.case-client {
    color: white;
    font-size: 12px;
    font-weight: 500;
}

.case-info {
    padding: 20px;
}

.case-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.case-title a {
    color: #2c3e50;
    text-decoration: none;
}

.case-excerpt {
    font-size: 14px;
    color: #666;
    margin: 0 0 10px 0;
}

.case-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #888;
}

/* 联系我们 */
.contact-section {
    margin-bottom: 60px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 40px;
    border-radius: 12px;
}

.contact-content {
    display: flex;
    gap: 40px;
}

.contact-info {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.contact-icon {
    font-size: 20px;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.contact-detail {
    flex: 1;
    min-width: 0;
}

.contact-label {
    display: block;
    font-size: 12px;
    color: #888;
    margin-bottom: 2px;
}

.contact-value {
    display: block;
    font-size: 14px;
    color: #333;
    font-weight: 500;
    word-break: break-word;
}

.contact-form {
    flex: 1;
    min-width: 300px;
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

/* 响应式布局 */
@media (max-width: 992px) {
    .hero-section {
        flex-direction: column;
        text-align: center;
        display: flex;
    }
    
    /* 全局内容区域 */
    .hero-content {
        order: 1;
        position: relative;
        z-index: 10;
    }
    
    /* 轮播slide改为flex垂直布局 */
    .hero-slide {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
    }
    
    /* 轮播slide内容 */
    .hero-slide-content {
        position: static !important;
        transform: none !important;
        margin-bottom: 10px;
        text-align: center;
        order: 1;
    }
    
    /* 叠加图片显示在轮播内容下方 */
    .hero-product-overlay {
        position: static !important;
        transform: none !important;
        height: auto;
        justify-content: center;
        margin-top: 10px;
        margin-bottom: 40px;
        order: 2;
        z-index: 15;
    }
    
    .hero-product-overlay img {
        max-width: 100%;
        max-height: 150px;
    }
    
    .hero-visual {
        margin-top: 30px;
        order: 3;
    }
    
    .about-content {
        flex-direction: column;
    }
    
    .news-content {
        flex-direction: column;
    }
    
    .contact-content {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 32px;
    }
    
    .hero-section {
        padding: 40px 20px;
    }
    
    .hero-visual {
        font-size: 80px;
    }
    
    .about-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .section-title {
        font-size: 26px;
    }
}

/* 自定义区域样式 */
.custom-section {
    margin-bottom: 60px;
}

.custom-content {
    line-height: 1.8;
    color: #444;
}

.custom-content h3 {
    font-size: 20px;
    color: #2c3e50;
    margin-top: 20px;
}

.custom-content p {
    margin-bottom: 15px;
}

.custom-content a {
    color: #667eea;
    text-decoration: none;
}

.custom-content a:hover {
    text-decoration: underline;
}
</style>

<?php get_footer(); ?>
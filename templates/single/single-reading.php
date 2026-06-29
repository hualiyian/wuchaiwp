<?php
/**
 * Template Name: 阅读模式
 * Description: 文章的简约阅读版本，去除多余元素，专注阅读体验
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php the_title(); ?> - <?php echo get_bloginfo('name'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5dc;
            min-height: 100vh;
        }
        
        .reading-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            background: #fff;
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        /* 工具栏 - 可拖动 */
        .reading-toolbar {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 100;
            cursor: move;
            user-select: none;
            touch-action: none;
        }
        
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 5px;
            padding-right: 15px;
            border-right: 1px solid #eee;
        }
        
        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .font-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: #f0f0f0;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            transition: all 0.2s;
        }
        
        .font-btn:hover {
            background: #3498db;
            color: #fff;
        }
        
        .font-size-display {
            font-size: 12px;
            color: #999;
            min-width: 35px;
            text-align: center;
        }
        
        .close-reading {
            padding: 8px 16px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            transition: background 0.3s;
            cursor: pointer;
        }
        
        .close-reading:hover {
            background: #2980b9;
        }
        
        .reading-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-top: 60px;
        }
        
        .reading-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            line-height: 1.4;
            margin-bottom: 20px;
        }
        
        .reading-meta {
            font-size: 14px;
            color: #666;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .reading-meta a {
            color: #3498db;
            text-decoration: none;
        }
        
        .reading-meta a:hover {
            text-decoration: underline;
        }
        
        .reading-content {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-all;
        }
        
        .reading-content p {
            margin-bottom: 1.5em;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .reading-content h1,
        .reading-content h2,
        .reading-content h3,
        .reading-content h4,
        .reading-content h5,
        .reading-content h6 {
            margin: 24px 0 16px 0;
            font-weight: 600;
            color: #333;
            word-wrap: break-word;
        }
        
        .reading-content h1 { font-size: 24px; }
        .reading-content h2 { font-size: 22px; }
        .reading-content h3 { font-size: 20px; }
        .reading-content h4 { font-size: 18px; }
        
        .reading-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 10px 0;
        }
        
        .reading-content a {
            color: #3498db;
            text-decoration: none;
            word-break: break-all;
        }
        
        .reading-content a:hover {
            text-decoration: underline;
        }
        
        .reading-content blockquote {
            border-left: 4px solid #3498db;
            padding-left: 16px;
            margin: 20px 0;
            color: #666;
            font-style: italic;
        }
        
        .reading-content code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
        }
        
        .reading-content pre {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 15px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .reading-content pre code {
            background: none;
            padding: 0;
        }
        
        .reading-content ul,
        .reading-content ol {
            margin: 15px 0 15px 30px;
        }
        
        .reading-content li {
            margin-bottom: 8px;
            word-wrap: break-word;
        }
        
        /* 文章导航 - 自适应 */
        .reading-navigation {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 30px 0;
            gap: 15px;
        }
        
        .nav-link {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 15px;
            background: #fff;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        
        .nav-link:hover {
            background: #3498db;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .nav-link.disabled {
            opacity: 0.4;
            pointer-events: none;
            cursor: not-allowed;
        }
        
        .nav-arrow {
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .nav-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .nav-label {
            font-size: 11px;
            color: #999;
            flex-shrink: 0;
        }
        
        .nav-link:hover .nav-label {
            color: rgba(255,255,255,0.8);
        }
        
        .nav-title {
            font-size: 13px;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }
        
        .reading-footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #999;
            font-size: 14px;
        }
        
        .reading-footer a {
            color: #666;
            text-decoration: none;
        }
        
        .reading-footer a:hover {
            color: #3498db;
        }
        
        /* 移动端响应式 */
        @media screen and (max-width: 768px) {
            .reading-container {
                padding: 20px 15px;
            }
            
            .reading-toolbar {
                flex-direction: column;
                gap: 10px;
                padding: 10px;
                width: calc(100% - 40px);
                max-width: 300px;
                left: 20px;
                right: 20px;
                transform: none;
            }
            
            .toolbar-left {
                border-right: none;
                border-bottom: 1px solid #eee;
                padding-right: 0;
                padding-bottom: 10px;
            }
            
            .reading-header {
                padding-top: 120px;
            }
            
            .reading-title {
                font-size: 24px;
            }
            
            .reading-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .reading-navigation {
                flex-direction: column;
                gap: 10px;
            }
            
            .nav-link {
                justify-content: center;
            }
            
            .nav-title {
                max-width: 180px;
            }
        }
        
        /* 平板端响应式 */
        @media screen and (min-width: 769px) and (max-width: 1024px) {
            .reading-container {
                max-width: 90%;
            }
            
            .nav-title {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <?php
    global $wpdb;
    $post_id = intval($_GET['wuchaiwp_post_id']);
    
    // 直接从数据库获取文章内容，绕过插件过滤
    $post_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID = %d AND post_status = 'publish'", $post_id));
    
    if ($post_data) :
        $post = $post_data;
        setup_postdata($post);
        
        // 获取上一篇和下一篇文章
        $prev_post = get_adjacent_post(false, '', true);
        $next_post = get_adjacent_post(false, '', false);
    ?>
    
    <!-- 工具栏 -->
    <div class="reading-toolbar" id="readingToolbar">
        <div class="toolbar-left">
            <button class="font-btn" id="font-decrease" title="缩小字体">A-</button>
            <span class="font-size-display" id="font-size">16px</span>
            <button class="font-btn" id="font-reset" title="恢复默认">A</button>
            <button class="font-btn" id="font-increase" title="放大字体">A+</button>
        </div>
        <div class="toolbar-right">
            <a href="<?php echo get_permalink($post_id); ?>" class="close-reading">✕ 退出</a>
        </div>
    </div>
    
    <div class="reading-container">
        <header class="reading-header">
            <h1 class="reading-title"><?php echo esc_html($post->post_title); ?></h1>
            <div class="reading-meta">
                <span>📝 <a href="<?php echo get_author_posts_url($post->post_author); ?>"><?php the_author(); ?></a></span>
                <span>📅 <?php echo get_the_date('Y年m月d日 H:i'); ?></span>
                <span>👁️ <?php echo get_post_meta($post_id, 'wuchaiwp_views', true) ?: '0'; ?></span>
            </div>
        </header>
        
        <article class="reading-content" id="readingContent">
            <?php
            // 直接输出原始内容，不经过 the_content 过滤器
            echo wpautop($post->post_content);
            ?>
        </article>
        
        <!-- 文章导航 -->
        <div class="reading-navigation">
            <?php if ($prev_post) : ?>
                <a href="<?php echo home_url('/?wuchaiwp_action=reading&wuchaiwp_post_id=' . $prev_post->ID); ?>" class="nav-link prev-link">
                    <span class="nav-arrow">←</span>
                    <div class="nav-info">
                        <span class="nav-label">上一篇</span>
                        <span class="nav-title" title="<?php echo esc_attr($prev_post->post_title); ?>"><?php echo esc_html($prev_post->post_title); ?></span>
                    </div>
                </a>
            <?php else : ?>
                <div class="nav-link disabled">
                    <span class="nav-arrow">←</span>
                    <div class="nav-info">
                        <span class="nav-label">上一篇</span>
                        <span class="nav-title">没有了</span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($next_post) : ?>
                <a href="<?php echo home_url('/?wuchaiwp_action=reading&wuchaiwp_post_id=' . $next_post->ID); ?>" class="nav-link next-link">
                    <div class="nav-info">
                        <span class="nav-label">下一篇</span>
                        <span class="nav-title" title="<?php echo esc_attr($next_post->post_title); ?>"><?php echo esc_html($next_post->post_title); ?></span>
                    </div>
                    <span class="nav-arrow">→</span>
                </a>
            <?php else : ?>
                <div class="nav-link disabled">
                    <div class="nav-info">
                        <span class="nav-label">下一篇</span>
                        <span class="nav-title">没有了</span>
                    </div>
                    <span class="nav-arrow">→</span>
                </div>
            <?php endif; ?>
        </div>
        
        <footer class="reading-footer">
            <p>本文来自 <a href="<?php echo get_bloginfo('url'); ?>"><?php echo get_bloginfo('name'); ?></a></p>
            <p style="margin-top: 10px;">转载请注明出处</p>
        </footer>
    </div>
    
    <script>
        // 字体大小控制
        (function() {
            const content = document.getElementById('readingContent');
            const decreaseBtn = document.getElementById('font-decrease');
            const resetBtn = document.getElementById('font-reset');
            const increaseBtn = document.getElementById('font-increase');
            const fontSizeDisplay = document.getElementById('font-size');
            
            const defaultSize = 16;
            let currentSize = defaultSize;
            
            function updateFontSize() {
                content.style.fontSize = currentSize + 'px';
                fontSizeDisplay.textContent = currentSize + 'px';
            }
            
            decreaseBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // 阻止事件冒泡，避免触发拖拽
                if (currentSize > 12) {
                    currentSize -= 2;
                    updateFontSize();
                }
            });
            
            resetBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                currentSize = defaultSize;
                updateFontSize();
            });
            
            increaseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentSize < 28) {
                    currentSize += 2;
                    updateFontSize();
                }
            });
            
            // 初始化
            updateFontSize();
        })();
        
        // 工具栏可拖动
        (function() {
            const toolbar = document.getElementById('readingToolbar');
            let isDragging = false;
            let startX, startY, initialX, initialY;
            
            function onMouseDown(e) {
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                initialX = toolbar.offsetLeft;
                initialY = toolbar.offsetTop;
                
                // 添加拖动时的样式
                toolbar.style.cursor = 'grabbing';
                toolbar.style.transform = 'none';
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            }
            
            function onMouseMove(e) {
                if (!isDragging) return;
                
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                
                // 限制在视窗范围内
                const maxLeft = window.innerWidth - toolbar.offsetWidth;
                const maxTop = window.innerHeight - toolbar.offsetHeight;
                
                let newLeft = initialX + deltaX;
                let newTop = initialY + deltaY;
                
                // 边界检测
                newLeft = Math.max(0, Math.min(newLeft, maxLeft));
                newTop = Math.max(0, Math.min(newTop, maxTop));
                
                toolbar.style.left = newLeft + 'px';
                toolbar.style.top = newTop + 'px';
            }
            
            function onMouseUp() {
                isDragging = false;
                toolbar.style.cursor = 'move';
                
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }
            
            // 桌面端拖动
            toolbar.addEventListener('mousedown', onMouseDown);
            
            // 移动端触摸拖动
            toolbar.addEventListener('touchstart', function(e) {
                isDragging = true;
                const touch = e.touches[0];
                startX = touch.clientX;
                startY = touch.clientY;
                initialX = toolbar.offsetLeft;
                initialY = toolbar.offsetTop;
                
                toolbar.style.cursor = 'grabbing';
                toolbar.style.transform = 'none';
            });
            
            toolbar.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                
                const touch = e.touches[0];
                const deltaX = touch.clientX - startX;
                const deltaY = touch.clientY - startY;
                
                const maxLeft = window.innerWidth - toolbar.offsetWidth;
                const maxTop = window.innerHeight - toolbar.offsetHeight;
                
                let newLeft = initialX + deltaX;
                let newTop = initialY + deltaY;
                
                newLeft = Math.max(0, Math.min(newLeft, maxLeft));
                newTop = Math.max(0, Math.min(newTop, maxTop));
                
                toolbar.style.left = newLeft + 'px';
                toolbar.style.top = newTop + 'px';
            });
            
            toolbar.addEventListener('touchend', function() {
                isDragging = false;
                toolbar.style.cursor = 'move';
            });
        })();
    </script>
    
    <?php
    else :
    ?>
    <div class="reading-container">
        <p style="text-align: center; padding: 50px; color: #999;">文章不存在或已删除</p>
    </div>
    <?php
    endif;
    ?>
</body>
</html>
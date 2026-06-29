<?php
/**
 * Template Name: 博客详情页
 * Description: 简约完整的博客文章详情页模板
 */

get_header(); ?>


<?php if (get_option('wuchaiwp_floating_ball_single', '1') == '1') : ?>
<!-- 悬浮球容器 -->
<div class="floating-ball-wrapper" id="floatingBall">
    <div class="floating-ball">
        <span class="ball-icon">☰</span>
        <span class="ball-badge">●</span>
    </div>
    <div class="floating-menu" id="floatingMenu">
        <ul>
            <li><a href="#" id="menu-toggle-sidebar">
                <span class="menu-icon">☰</span>
                <span class="menu-text">隐藏侧边栏</span>
            </a></li>
            <li><a href="#" id="menu-show-sidebar">
                <span class="menu-icon">☰</span>
                <span class="menu-text">显示侧边栏</span>
            </a></li>
            <li class="menu-divider"></li>
            <li><a href="#" id="menu-back-to-top">
                <span class="menu-icon">⬆️</span>
                <span class="menu-text">回到顶部</span>
            </a></li>
            <li><a href="#" id="menu-scroll-down">
                <span class="menu-icon">⬇️</span>
                <span class="menu-text">向下滚动</span>
            </a></li>
            <li class="menu-divider"></li>
            <li><a href="#" id="menu-qrcode">
                <span class="menu-icon">📱</span>
                <span class="menu-text">生成二维码</span>
            </a></li>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- 二维码弹窗 -->
<div class="qrcode-modal" id="qrcodeModal">
    <div class="qrcode-modal-overlay"></div>
    <div class="qrcode-modal-content">
        <button class="qrcode-close" id="qrcodeClose">✕</button>
        <h3>当前页面二维码</h3>
        <p class="qrcode-tip">使用手机扫描二维码访问此页面</p>
        <div class="qrcode-container" id="qrcodeContainer"></div>
        <p class="qrcode-url" id="qrcodeUrl"></p>
    </div>
</div>



<div class="wrap" style="max-width: 1600px;">
    <table class="frontpage-layout" width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <!-- 主内容区域 -->
                <td class="content-cell">
                    <div id="primary" class="content-area">
                        <main id="main" class="site-main" role="main">

                            <?php while (have_posts()) : the_post(); ?>

                                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-single'); ?>>
                                    
                                    <!-- 面包屑导航 -->
                                    <div class="breadcrumb">
                                 
                                <a href="<?php echo home_url(); ?>">🏠 首页</a>
                                <span class="separator">›</span>
                                
                                <?php 
                                $post_type = get_post_type();
                                $post_type_obj = get_post_type_object($post_type);
                                if ($post_type_obj) {
                                    $archive_link = get_post_type_archive_link($post_type);
                                    echo '<a href="' . esc_url($archive_link) . '">' . esc_html($post_type_obj->labels->name) . '</a>';
                                    echo '<span class="separator">›</span>';
                                }
                                
                                $categories = get_the_terms(get_the_ID(), 'category');
                                if ($categories && !is_wp_error($categories)) {
                                    $category = reset($categories);
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                    echo '<span class="separator">›</span>';
                                }
                                ?>
                                
                                <span class="current"><a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></span>
                            </div>

       

                                    <!-- 标题 -->
                                    <header class="entry-header">
                                        <h1 class="entry-title">
                                            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                                        </h1>
                                        
                                        <!-- 作者信息 -->
                                        <div class="author-bio">
                                            <?php
                                            // 获取作者信息
                                            $author_name = get_post_meta(get_the_ID(), 'wuchaiwp_blog_author_name', true);
                                            $author_avatar = get_post_meta(get_the_ID(), 'wuchaiwp_blog_author_avatar', true);
                                            $author_bio = get_post_meta(get_the_ID(), 'wuchaiwp_blog_author_bio', true);
                                            
                                            // 使用自定义或默认值
                                            $post_author_id = get_post_field('post_author', get_the_ID());
                                            $author_info = get_userdata($post_author_id);
                                            $display_name = !empty($author_name) ? $author_name : get_the_author();
                                            $avatar_url = !empty($author_avatar) ? $author_avatar : get_avatar_url($post_author_id, 48);
                                            $bio_text = !empty($author_bio) ? $author_bio : get_the_author_meta('description');
                                            
                                            // 获取作者空间链接
                                            $author_space_url = home_url('/author-space/' . $author_info->user_nicename);
                                            
                                            // 获取阅读量（优先使用 WP-PostViews 插件的 the_views 函数）
                                            $views = '';
                                            if (function_exists('the_views')) {
                                                // 使用 WP-PostViews 插件
                                                ob_start();
                                                the_views();
                                                $views = ob_get_clean();
                                            } else {
                                                // 使用主题自带的阅读量统计
                                                $views_count = get_post_meta(get_the_ID(), 'wuchaiwp_views', true);
                                                $views = empty($views_count) ? '0' : (int)$views_count;
                                            }
                                            ?>
                                            
                                            <div class="author-info" style="display: inline-flex !important; align-items: center !important; gap: 8px !important; white-space: nowrap !important;">
                                                <span class="author-meta" style="display: inline !important;">
                                                    📅 <?php the_date('Y年m月d日 H:i'); ?> 
                                                    | <a href="<?php echo esc_url($author_space_url); ?>" 
                                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($display_name); ?>" class="avatar-mini" style="width: 24px !important; height: 24px !important; border-radius: 50% !important; display: inline !important; vertical-align: middle !important;">
                                                        <?php echo esc_html($display_name); ?>
                                                    </a>
                                                    | 👁️ <?php echo $views; ?> 
                                                    | 💬 <?php comments_number('0', '1', '%'); ?> 评论
                                                </span>
                                                
                                            </div>
                                    
                                        </div>
                                        
                                        <!-- 功能按钮栏 -->
                                    <div class="toolbar-bar">
                                        <button style="background: none; border: none; cursor: pointer; font-size: 14px; color: #666; padding: 2px 3px; transition: color 0.3s;" id="font-size-decrease" title="缩小字体" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">A-</button>
                                        <button style="background: none; border: none; cursor: pointer; font-size: 14px; color: #666; padding: 2px 2px; transition: color 0.3s;" id="font-size-reset" title="恢复默认字体" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">A</button>
                                        <button style="background: none; border: none; cursor: pointer; font-size: 14px; color: #666; padding: 2px 3px; transition: color 0.3s;" id="font-size-increase" title="放大字体" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">A+</button>
                                        <span style="color: #ddd;">|</span>
                                        <a href="<?php echo home_url('/?wuchaiwp_action=reading&wuchaiwp_post_id=' . get_the_ID()); ?>" style="text-decoration: none; font-size: 14px; color: #666; padding: 2px 3px; transition: color 0.3s;" id="reading-mode" title="阅读模式" target="_blank" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">📖</a>
                                        <button style="background: none; border: none; cursor: pointer; font-size: 14px; color: #666; padding: 2px 3px; transition: color 0.3s;" id="toggle-sidebar" title="隐藏/显示侧边栏" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">☰</button>
                                    </div>

                                    </header>

                                    

                                   

                                    <!-- 文章内容 -->
                                    <div class="entry-content">
                                        <?php the_content(); ?>
                                    </div></br>
                                    
                                    
                                    <!-- 操作按钮 -->
                                    <div class="entry-actions">
                                        <!-- 收藏按钮 -->
                                        <?php
                                        if (function_exists('do_shortcode')) {
                                            echo do_shortcode('[favorite_button]');
                                        }
                                        ?>
                                        <!-- 点赞按钮 -->
                                        <button class="action-btn like-btn" id="like-btn-<?php the_ID(); ?>" onclick="wuchaiwp_like_post(<?php the_ID(); ?>)">
                                            <span class="btn-icon">👍</span>
                                            <span class="like-count" id="like-count-<?php the_ID(); ?>"><?php echo get_post_meta(get_the_ID(), 'wuchaiwp_like_count', true) ?: '0'; ?></span>
                                        </button>
                                        <!-- 分享按钮 -->
                                        <button class="action-btn share-btn" onclick="wuchaiwp_share_article()">
                                            <span class="btn-icon">📤</span>
                                        </button>
                                        <!-- 打赏按钮 -->
                                        <?php
                                        // 获取文章打赏设置，默认显示
                                        $donate_enable = get_post_meta(get_the_ID(), 'wuchaiwp_blog_donate_enable', true);
                                        if ($donate_enable === '' || $donate_enable == 1) {
                                        ?>
                                        <button class="action-btn donate-btn" onclick="wuchaiwp_open_donate_modal()">
                                            <span class="btn-icon">💰</span>
                                        </button>
                                        <?php }
                                        ?>
                                    </div>

                                    <!-- 分割线 -->
                                    <div class="entry-divider"></div>

                                    <!-- 打赏弹窗 -->
                                    <div id="donate-modal" class="donate-modal">
                                        <div class="donate-modal-overlay" onclick="wuchaiwp_close_donate_modal()"></div>
                                        <div class="donate-modal-content">
                                            <button class="donate-close-btn" onclick="wuchaiwp_close_donate_modal()">×</button>
                                            <?php
                                            // 获取打赏标题和描述（优先文章自定义字段，其次全局设置）
                                            $donate_title = get_post_meta(get_the_ID(), 'wuchaiwp_donate_title', true);
                                            $donate_title = empty($donate_title) ? get_option('wuchaiwp_donate_title', '') : $donate_title;
                                            $donate_desc = get_post_meta(get_the_ID(), 'wuchaiwp_donate_desc', true);
                                            $donate_desc = empty($donate_desc) ? get_option('wuchaiwp_donate_desc', '') : $donate_desc;
                                            ?>
                                            <h3 class="donate-title"><?php echo !empty($donate_title) ? esc_html($donate_title) : '💝 支持作者'; ?></h3>
                                            <p class="donate-desc"><?php echo !empty($donate_desc) ? wp_kses_post($donate_desc) : '如果这篇文章对你有帮助，欢迎打赏支持！'; ?></p>
                                            <div class="donate-methods">
                                                <?php
                                                // 获取打赏二维码（优先文章自定义字段，其次全局设置）
                                                $wechat_qr = get_post_meta(get_the_ID(), 'wuchaiwp_donate_wechat', true);
                                                $wechat_qr = empty($wechat_qr) ? get_option('wuchaiwp_donate_wechat', '') : $wechat_qr;
                                                $alipay_qr = get_post_meta(get_the_ID(), 'wuchaiwp_donate_alipay', true);
                                                $alipay_qr = empty($alipay_qr) ? get_option('wuchaiwp_donate_alipay', '') : $alipay_qr;
                                                
                                                if (!empty($wechat_qr)) {
                                                    echo '<div class="donate-method">';
                                                    echo '<div class="donate-icon">💬</div>';
                                                    echo '<span class="donate-name">微信</span>';
                                                    echo '<img src="' . esc_url($wechat_qr) . '" alt="微信打赏" class="donate-qrcode" />';
                                                    echo '</div>';
                                                }
                                                
                                                if (!empty($alipay_qr)) {
                                                    echo '<div class="donate-method">';
                                                    echo '<div class="donate-icon">📱</div>';
                                                    echo '<span class="donate-name">支付宝</span>';
                                                    echo '<img src="' . esc_url($alipay_qr) . '" alt="支付宝打赏" class="donate-qrcode" />';
                                                    echo '</div>';
                                                }
                                                
                                                // 如果没有设置二维码
                                                if (empty($wechat_qr) && empty($alipay_qr)) {
                                                    echo '<div class="donate-empty">';
                                                    echo '<p>作者暂未设置打赏二维码</p>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                     <!-- 分类和标签 -->
                                    <div class="entry-meta-tags">
                                        <?php
                                        // 获取当前文章类型支持的所有分类法
                                        $post_type = get_post_type();
                                        $taxonomies = get_object_taxonomies($post_type, 'objects');
                                        
                                        foreach ($taxonomies as $taxonomy) {
                                            // 跳过内置的分类法（如果不需要显示）
                                            // if (in_array($taxonomy->name, array('category', 'post_tag'))) continue;
                                            
                                            $terms = get_the_terms(get_the_ID(), $taxonomy->name);
                                            
                                            if (!empty($terms) && !is_wp_error($terms)) {
                                                // 判断是分类还是标签类型
                                                $is_category = ($taxonomy->hierarchical) ? true : false;
                                                $badge_class = $is_category ? 'category-badge' : 'tag-badge';
                                                $icon = $is_category ? '📁' : '🏷️';
                                                
                                                echo '<div class="meta-section">';
                                                echo '<span class="section-label">' . $icon . '</span>';
                                                echo '<div class="meta-items">';
                                                
                                                foreach ($terms as $term) {
                                                    $term_link = get_term_link($term);
                                                    if (!is_wp_error($term_link)) {
                                                        echo '<a href="' . esc_url($term_link) . '" class="' . $badge_class . '">';
                                                        echo esc_html($term->name);
                                                        echo '</a>';
                                                    }
                                                }
                                                
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        }
                                        
                                        // 如果没有任何分类法或没有分类/标签
                                        if (empty($taxonomies) || (empty($terms) && is_wp_error($terms))) {
                                            echo '<div class="meta-section">';
                                            echo '<span class="section-label">📁</span>';
                                            echo '<div class="meta-items">';
                                            echo '<span class="empty-badge">暂无分类</span>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>

                                    <!-- 转载说明 -->
                                    <div class="reprint-notice">
                                        <div class="reprint-header1">
                                            <!--<span class="reprint-icon">📝</span>
                                            <span class="reprint-title">转载说明</span>-->
                                        </div>
                                        <div class="reprint-content">
                                            <p>本文由<strong></strong><a href="<?php echo get_author_posts_url(get_post_field('post_author', get_the_ID())); ?>"><?php the_author(); ?></a>于
                                            <strong></strong><?php echo get_the_date('Y年m月d日') . ' ' . get_the_time('H:i:s'); ?>发布于
                                            <strong></strong><a href="<?php echo get_bloginfo('url'); ?>" target="_blank" title="<?php echo get_bloginfo('name'); ?>"><?php echo get_bloginfo('name'); ?></a></p>
                                            <p class="entry-content"><strong>转载请注明出处：</strong><a href="<?php echo get_permalink(); ?>" target="_blank" title="<?php the_title_attribute(); ?>"><?php echo get_permalink();//the_title(); ?></a>|<a href="<?php echo get_bloginfo('url'); ?>" target="_blank" title="<?php echo get_bloginfo('name'); ?>"><?php echo get_bloginfo('name'); ?></a>，作者 <a href="<?php echo get_author_posts_url(get_post_field('post_author', get_the_ID())); ?>"><?php the_author(); ?></a></p>
                                        </div>
                                    </div>

                                    <!-- 版权说明 -->
                                    <div class="copyright-notice">
                                        <?php
                                        // 获取后台设置的版权说明（可在主题自定义选项中设置）
                                        $copyright_text = get_option('wuchaiwp_copyright_text', '');
                                        if (!empty($copyright_text)) {
                                            echo '<span class="copyright-icon">©</span>';
                                            echo '<span class="copyright-content">' . wp_kses_post($copyright_text) . '</span>';
                                        } else {
                                            // 默认版权说明
                                            echo '<span class="copyright-icon">©</span>';
                                            echo '<span class="copyright-content">本文为原创文章，未经许可禁止转载。版权所有。</span>';
                                        }
                                        ?>
                                    </div>

                                    <!-- 文章来源 -->
                                    <?php
                                    $blog_source = get_post_meta(get_the_ID(), 'wuchaiwp_blog_source', true);
                                    if (!empty($blog_source)) {
                                        echo '<div class="article-source">';
                                        echo '<span class="source-label">🔗 原文链接：</span>';
                                        echo '<a href="' . esc_url($blog_source) . '" target="_blank" rel="noopener">' . esc_html($blog_source) . '</a>';
                                        echo '</div>';
                                    }
                                    ?>

                                    <!-- 文章导航 -->
                                    <div class="post-navigation">
                                        <div class="nav-previous">
                                            <?php previous_post_link('%link', '<span class="nav-arrow">←</span> 上一篇：%title'); ?>
                                        </div>
                                        <div class="nav-next">
                                            <?php next_post_link('%link', '下一篇：%title <span class="nav-arrow">→</span>'); ?>
                                        </div>
                                    </div>

                                    <!-- 相关推荐文章 -->
                                    <?php
                                    $recommend_posts = get_post_meta(get_the_ID(), 'wuchaiwp_blog_recommend_posts', true);
                                    if (!empty($recommend_posts) && is_array($recommend_posts)) {
                                        $related_posts = get_posts(array(
                                            'post__in' => $recommend_posts,
                                            'orderby' => 'post__in',
                                            'posts_per_page' => 5
                                        ));
                                        
                                        if (!empty($related_posts)) {
                                            echo '<div class="related-posts">';
                                            echo '<h3>📖 相关推荐</h3>';
                                            echo '<ul>';
                                            foreach ($related_posts as $related_post) {
                                                echo '<li>';
                                                echo '<a href="' . get_permalink($related_post->ID) . '">' . get_the_title($related_post->ID) . '</a>';
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>

                                    <!-- 文章底部小工具区域 -->
                                    <div class="single-post-bottom-widgets">
                                        <?php if (is_active_sidebar('single-post-bottom')) : ?>
                                            <?php dynamic_sidebar('single-post-bottom'); ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- 评论区域 -->
                                    <div class="comments-area">
                                        <?php 
                                        // 使用WordPress默认的评论状态检查
                                        if (comments_open()) {
                                            comments_template();
                                        } else {
                                            echo '<p style="color: #666; padding: 20px; background: #f8f9fa; border-radius: 8px;">评论功能已关闭</p>';
                                        }
                                        ?>
                                    </div>

                                </article>

                            <?php endwhile; ?>

                        </main><!-- #main -->
                    </div><!-- #primary -->
                </td>

                <!-- 侧边栏区域 -->
                <!-- 将原来的侧边栏代码替换为 -->
<td class="sidebar-cell">
    <aside id="secondary" class="widget-area blog-sidebar" role="complementary">
        <?php dynamic_sidebar('single-post-sidebar'); ?>
    </aside>
</td>
                
            </tr>
        </tbody>
    </table>
</div><!-- .wrap -->

<style>
/* ===== 表格布局样式 ===== */
.frontpage-layout {
    width: 100%;
    border-collapse: collapse;
}

/* 覆盖全局样式，确保内容区域不受限制 */
.wrap .frontpage-layout #primary,
.wrap .frontpage-layout .content-area,
.wrap .frontpage-layout .site-main {
    max-width: none !important;
    width: 100% !important;
    margin: 0 !important;
    float: none !important;
}

.frontpage-layout tbody {
    border: none;
}

.frontpage-layout tr {
    border: none;
}

.frontpage-layout td {
    border: none;
    border-collapse: collapse;
}

.frontpage-layout .content-cell {
    vertical-align: top;
    padding-right: 40px;
    width: 75%;
}

.frontpage-layout .sidebar-cell {
    vertical-align: top;
    width: 25%;
    min-width: 280px;
}

/* 响应式布局 */
@media screen and (max-width: 768px) {
    .frontpage-layout {
        display: block !important;
        width: 100% !important;
    }
    
    .frontpage-layout tbody {
        display: block !important;
    }
    
    .frontpage-layout tr {
        display: block !important;
    }
    
    .frontpage-layout .search-row,
    .frontpage-layout .content-cell,
    .frontpage-layout .sidebar-cell {
        display: block !important;
        width: 100% !important;
        padding-right: 0 !important;
    }
    
    .frontpage-layout .sidebar-cell {
        margin-top: 20px !important;
    }
}

/* ===== 博客详情页样式 ===== */

/* 面包屑导航 */
.breadcrumb {
    margin-bottom: 20px;
    font-size: 14px;
    color: #666;
}

.breadcrumb a {
    color: #3498db;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb .separator {
    margin: 0 8px;
    color: #ccc;
}

.breadcrumb .current {
    color: #333;
    font-weight: 500;
}

/* 标题区域 */
.entry-header .entry-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.4;
}

.entry-header .entry-title a {
    color: #333;
    text-decoration: none;
}

.entry-header .entry-title a:hover {
    color: #3498db;
}

/* 作者信息 */
.author-bio {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px 20px;
    background: #f8f9fa;
    border-radius: var(--wuchaiwp-border-radius, 0px);
    margin-bottom: 8px;
}

/* 功能按钮栏 */
.toolbar-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 20px;
    background: #f8f9fa;
    border-radius: var(--wuchaiwp-border-radius, 0px);
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .author-bio {
        padding: 12px 15px;
        gap: 10px;
        margin-bottom: 6px;
    }
    
    .toolbar-bar {
        padding: 8px 15px;
        gap: 10px;
        margin-bottom: 15px;
    }
}

.author-avatar {
    flex-shrink: 0;
}

.author-avatar a {
    display: block;
    border-radius: 50%;
    overflow: hidden;
}

.author-avatar .avatar-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.author-name {
    display: block;
    font-weight: 600;
    color: #333;
    font-size: 16px;
    margin-bottom: 5px;
    text-decoration: none;
}

.author-name:hover {
    color: #3498db;
}

.author-meta {
    font-size: 14px;
    color: #666;
}

/* 操作按钮 */
.entry-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
    margin-top: 15px;
    padding: 10px 0;
}

/* 分割线 */
.entry-divider {
    height: 1px;
    background: #eee;
    margin: 10px 0;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background: transparent;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 18px;
    color: #666;
    transition: all 0.2s ease;
}

.action-btn:hover {
    background: #f5f5f5;
    color: #333;
    transform: scale(1.1);
}

.action-btn.active {
    color: #e74c3c;
}

.action-btn.like-btn.liked {
    color: #e74c3c;
}

.btn-text {
    display: none;
}

.like-count {
    font-size: 14px;
    font-weight: 500;
    color: #666;
}
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
    border-color: #c0392b;
}

.action-btn.like-btn.liked {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
    border-color: #bdc3c7;
}

.like-count {
    margin-left: 4px;
    font-weight: 600;
}

/* 打赏弹窗样式 */
.donate-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.donate-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
}

.donate-modal-content {
    position: relative;
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.donate-close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    border: none;
    background: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    transition: color 0.2s ease;
    line-height: 1;
    padding: 5px;
}

.donate-close-btn:hover {
    color: #333;
}

.donate-title {
    margin: 0 0 10px 0;
    font-size: 22px;
    color: #333;
}

.donate-desc {
    margin: 0 0 20px 0;
    color: #666;
    font-size: 14px;
}

.donate-methods {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.donate-method {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.donate-icon {
    font-size: 32px;
}

.donate-name {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

.donate-qrcode {
    width: 150px;
    height: 150px;
    border-radius: 8px;
    border: 1px solid #eee;
    padding: 5px;
}

.donate-empty {
    padding: 30px;
    color: #999;
}

.btn-icon {
    font-size: 16px;
}

.btn-text {
    font-weight: 500;
}

/* 分类和标签 */
.entry-meta-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
    padding-bottom: 10px;
}

.meta-section {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.section-label {
    font-size: 16px;
}

.meta-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: #3498db;
    color: #fff;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
}

.category-badge:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(41, 128, 185, 0.4);
}

.tag-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: #fff;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(240, 147, 251, 0.3);
}

.tag-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(240, 147, 251, 0.4);
}

.empty-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    background: #f5f5f5;
    color: #999;
    border-radius: 20px;
    font-size: 13px;
}

/* 文章内容 */
.entry-content {
    line-height: 1.8;
    color: #444;
    font-size: 16px;
    max-width: 100%;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-all;
}

.entry-content p {
    margin-bottom: 1.5em;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.entry-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 10px 0;
}

/* 代码块和预格式化文本 */
.entry-content pre {
    max-width: 100%;
    overflow-x: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.entry-content code {
    word-wrap: break-word;
}

/* 表格自适应 */
.entry-content table {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    display: block;
}

.entry-content th,
.entry-content td {
    word-wrap: break-word;
    white-space: normal;
}

/* 长链接和URL */
.entry-content a {
    word-break: break-all;
}

/* 长文本处理 */
.entry-content h1,
.entry-content h2,
.entry-content h3,
.entry-content h4,
.entry-content h5,
.entry-content h6 {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.entry-content ul,
.entry-content ol {
    padding-left: 20px;
}

.entry-content li {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* 版权说明 */
.copyright-notice {
    margin: 20px 0;
    padding: 15px 20px;
    background: linear-gradient(135deg, #fef9f3 0%, #fff5eb 100%);
    border-left: 4px solid #f39c12;
    border-radius: 0 8px 8px 0;
    font-size: 14px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 10px;
}

.copyright-icon {
    font-size: 20px;
    color: #f39c12;
    font-weight: bold;
}

.copyright-content {
    line-height: 1.6;
}

/* 转载说明 */
.reprint-notice {
    margin: 20px 0;
    padding: 18px 20px;
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
    border-left: 4px solid #3498db;
    border-radius: 0 8px 8px 0;
}

.reprint-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px dashed #b8d4e8;
}

.reprint-icon {
    font-size: 18px;
}

.reprint-title {
    font-size: 16px;
    font-weight: 600;
    color: #2980b9;
}

.reprint-content {
    font-size: 14px;
    color: #555;
    line-height: 1.8;
}

.reprint-content p {
    margin: 6px 0;
}

.reprint-content a {
    color: #3498db;
    text-decoration: none;
}

.reprint-content a:hover {
    text-decoration: underline;
}
}

/* 文章来源 */
.article-source {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    font-size: 14px;
}

.source-label {
    font-weight: 600;
}

.article-source a {
    color: #3498db;
    word-break: break-all;
}

/* 文章导航 */
.post-navigation {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    background: #f8f9fa;
    border-radius: var(--wuchaiwp-border-radius, 0px);
    margin: 30px 0;
}

.post-navigation a {
    color: #3498db;
    text-decoration: none;
    font-size: 14px;
}

.post-navigation a:hover {
    text-decoration: underline;
}

.nav-arrow {
    font-size: 16px;
}

/* 相关推荐 */
.related-posts {
    margin: 30px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: var(--wuchaiwp-border-radius, 0px);
}

.related-posts h3 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 18px;
    color: #333;
}

.related-posts ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.related-posts li {
    padding: 8px 0;
    border-bottom: 1px dashed #ddd;
}

.related-posts li:last-child {
    border-bottom: none;
}

.related-posts a {
    color: #333;
    text-decoration: none;
}

.related-posts a:hover {
    color: #3498db;
}

/* 评论区域 */
.comments-area {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #eee;
    clear: both;
    float: none !important;
    width: 100% !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: static !important;
}

/* 评论标题 */
.comments-area h3 {
    font-size: 20px;
    margin-bottom: 24px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* 确保评论列表正常显示 */
.comments-area #comments {
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.comments-area .comment-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* 评论项 */
.comments-area .comment-body {
    padding: 24px;
    background: #fff;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.comments-area .comment-body:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

/* 评论作者信息 */
.comments-area .comment-author {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}

.comments-area .comment-author .avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: 3px solid #f0f0f0;
    transition: border-color 0.3s ease;
}

.comments-area .comment-body:hover .comment-author .avatar {
    border-color: #3498db;
}

.comments-area .comment-author .fn {
    font-weight: 600;
    font-size: 15px;
    color: #333;
    text-decoration: none;
}

.comments-area .comment-author .fn:hover {
    color: #3498db;
}

/* 评论元信息 */
.comments-area .comment-meta {
    font-size: 13px;
    color: #999;
    margin-top: 4px;
}

.comments-area .comment-meta a {
    color: #999;
    text-decoration: none;
}

.comments-area .comment-meta a:hover {
    color: #3498db;
}

/* 评论内容 */
.comments-area .comment-content {
    font-size: 15px;
    line-height: 1.8;
    color: #444;
    padding: 16px;
    background: #fafafa;
    border-radius: 8px;
    margin-bottom: 16px;
}

.comments-area .comment-content p {
    margin: 0;
}

/* 回复按钮 */
.comments-area .reply {
    margin-top: 0;
    text-align: right;
}

.comments-area .reply a {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 14px;
    background: transparent;
    color: #666;
    text-decoration: none;
    border-radius: 20px;
    font-size: 13px;
    transition: all 0.2s ease;
    border: 1px solid #ddd;
}

.comments-area .reply a:hover {
    background: #3498db;
    color: #fff;
    border-color: #3498db;
}

/* 回复@用户样式 */
.comments-area .comment-content .reply-to {
    display: inline-block;
    padding: 4px 10px;
    background: #e8f4fd;
    color: #3498db;
    border-radius: 4px;
    font-size: 12px;
    margin-right: 8px;
    font-weight: 500;
}

/* 嵌套评论 */
.comments-area .children {
    list-style: none;
    padding-left: 40px;
    margin-top: 15px;
}

.comments-area .children .comment-body {
    background: #fafafa;
    border: 1px solid #eee;
    margin-bottom: 12px;
}

/* 评论表单样式 */
.comments-area #commentform {
    margin-top: 30px;
    padding: 30px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border: 1px solid #f0f0f0;
}

.comments-area #commentform p {
    margin-bottom: 20px;
}

.comments-area #commentform label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.comments-area #commentform input,
.comments-area #commentform textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box;
    background: #fafafa;
    transition: all 0.2s ease;
}

.comments-area #commentform input:focus,
.comments-area #commentform textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    background: #fff;
}

.comments-area #commentform textarea {
    min-height: 140px;
    resize: vertical;
}

/* 评论表单提示 */
.comments-area #commentform .comment-notes {
    font-size: 13px;
    color: #999;
    margin-bottom: 15px;
}

.comments-area #commentform .form-submit {
    margin-top: 24px;
}

.comments-area #commentform .submit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.comments-area #commentform .submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
}

/* 评论数量 */
.comments-area .comments-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 24px;
}

/* 没有评论时的提示 */
.comments-area .no-comments {
    text-align: center;
    padding: 40px;
    color: #999;
    background: #fafafa;
    border-radius: 12px;
}

/* Tab组件样式 */
.blog-tab-widget {
    background: #fff;
    border-radius: var(--wuchaiwp-border-radius, 0px);
    overflow: hidden;
    width: 100%;
    min-width: 250px;
    box-sizing: border-box;
}

.tab-nav {
    display: flex;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    justify-content: space-around;
}

.tab-nav .tab-btn {
    width: 25%;
    min-width: 60px;
    padding: 10px 4px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 12px;
    color: #666;
    transition: all 0.3s ease;
    border-bottom: 2px solid transparent;
    text-align: center;
}

.tab-nav .tab-btn:hover {
    color: #3498db;
}

.tab-nav .tab-btn.active {
    color: #3498db;
    border-bottom-color: #3498db;
    background: #fff;
}

.tab-content {
    padding: 10px 0;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

.tab-posts-list {
    list-style: none;
    padding: 0 15px;
    margin: 0;
}

.tab-posts-list li {
    padding: 8px 0;
    border-bottom: 1px dashed #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tab-posts-list li:last-child {
    border-bottom: none;
}

.tab-posts-list li a {
    flex: 1;
    color: #333;
    text-decoration: none;
    font-size: 13px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.tab-posts-list li a:hover {
    color: #3498db;
}

.tab-posts-list .post-meta {
    font-size: 12px;
    color: #999;
    margin-left: 10px;
    white-space: nowrap;
}

.tab-posts-list .empty-item {
    color: #999;
    text-align: center;
    padding: 20px;
}

/* 两列卡片布局样式 - 横向布局 */
.two-column-cards-horizontal {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-top: 15px;
}

.two-column-cards-horizontal .card-horizontal {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    gap: 14px;
    padding: 12px;
}

.two-column-cards-horizontal .card-horizontal:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.two-column-cards-horizontal .card-thumb {
    flex-shrink: 0;
    width: 90px;
    height: 90px;
    overflow: hidden;
    border-radius: 8px;
}

.two-column-cards-horizontal .card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.two-column-cards-horizontal .card-horizontal:hover .card-img {
    transform: scale(1.05);
}

.two-column-cards-horizontal .card-body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.two-column-cards-horizontal .card-title {
    margin: 0 0 6px 0;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
}

.two-column-cards-horizontal .card-title a {
    color: #333;
    text-decoration: none;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.two-column-cards-horizontal .card-title a:hover {
    color: #3498db;
}

.two-column-cards-horizontal .card-excerpt {
    margin: 0 0 8px 0;
    font-size: 12px;
    color: #666;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-shrink: 0;
}

.two-column-cards-horizontal .card-meta {
    display: flex;
    gap: 12px;
    font-size: 11px;
    color: #999;
    margin-top: auto;
}

/* 两列列表布局样式 */
.two-column-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 15px;
}

.two-column-list .list-column {
    list-style: none;
    padding: 0;
    margin: 0;
}

.two-column-list .list-column li {
    padding: 12px 0;
    border-bottom: 1px dashed #eee;
    position: relative;
}

.two-column-list .list-column li:last-child {
    border-bottom: none;
}

.two-column-list .list-column li a {
    color: #333;
    text-decoration: none;
    font-size: 14px;
    line-height: 1.5;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.two-column-list .list-column li a:hover {
    color: #3498db;
}

.two-column-list .list-meta {
    display: flex;
    gap: 12px;
    font-size: 12px;
    color: #999;
    margin-top: 6px;
}

/* 响应式布局 - 移动端变为一列 */
@media screen and (max-width: 768px) {
    .two-column-cards-horizontal,
    .two-column-list {
        grid-template-columns: 1fr;
    }
    
    .two-column-cards-horizontal .card-horizontal {
        padding: 10px;
    }
    
    .two-column-cards-horizontal .card-thumb {
        width: 80px;
        height: 80px;
    }
}

/* 侧边栏样式 */
.blog-sidebar .widget {
    margin-bottom: 30px;
    padding: 20px;
    background: #fff;
    border-radius: var(--wuchaiwp-border-radius, 0px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.blog-sidebar .widget h3 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 16px;
    color: #333;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.blog-sidebar .author-profile,
.blog-sidebar .author-profile-widget {
    text-align: center;
}

.blog-sidebar .author-profile-widget.text-center {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.blog-sidebar .author-profile a {
    text-decoration: none;
}

.blog-sidebar .profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}

.blog-sidebar .profile-avatar:hover {
    transform: scale(1.05);
}

.blog-sidebar .author-profile h4 {
    margin: 0 0 8px 0;
    font-size: 16px;
    color: #333;
}

.blog-sidebar .author-profile h4 a {
    color: #333;
}

.blog-sidebar .author-profile h4 a:hover {
    color: #3498db;
}

.blog-sidebar .author-profile p {
    font-size: 14px;
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
}

.author-space-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #3498db;
    color: #fff;
    border-radius: 20px;
    font-size: 14px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.author-space-link:hover {
    background: #2980b9;
}

.link-icon {
    font-size: 14px;
}

/* 分类标签样式 */
.blog-sidebar .categories-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 0;
    margin: 0;
}

.blog-sidebar .category-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: #3498db;
    color: #fff;
    border-radius: 20px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
}

.blog-sidebar .category-tag:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(41, 128, 185, 0.4);
    color: #fff;
}

.blog-sidebar .category-tag .count {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
}

/* 热门文章和推荐文章列表样式 */
.blog-sidebar .popular-widget ul,
.blog-sidebar .recommend-widget ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.blog-sidebar .popular-widget li,
.blog-sidebar .recommend-widget li {
    padding: 8px 0;
    border-bottom: 1px dashed #eee;
}

.blog-sidebar .popular-widget li:last-child,
.blog-sidebar .recommend-widget li:last-child {
    border-bottom: none;
}

.blog-sidebar .popular-widget a,
.blog-sidebar .recommend-widget a {
    color: #333;
    text-decoration: none;
    font-size: 14px;
}

.blog-sidebar .popular-widget a:hover,
.blog-sidebar .recommend-widget a:hover {
    color: #3498db;
}

.blog-sidebar .popular-widget .views {
    float: right;
    color: #999;
    font-size: 12px;
}

@media (max-width: 768px) {
    .entry-header .entry-title {
        font-size: 24px;
    }
    
    .post-navigation {
        flex-direction: column;
        gap: 10px;
    }
    
    .entry-actions {
        flex-wrap: wrap;
    }
}


/* 悬浮球样式 */
.floating-ball-wrapper {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
}

.floating-ball {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: move;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    user-select: none;
    touch-action: none;
}

.floating-ball:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 30px rgba(102, 126, 234, 0.5);
}

.floating-ball.dragging {
    cursor: grabbing;
    opacity: 0.8;
}

.ball-icon {
    font-size: 22px;
    color: #fff;
}

.ball-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 16px;
    height: 16px;
    background: #ff4757;
    border-radius: 50%;
    color: #fff;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

/* 悬浮菜单 */
.floating-menu {
    position: absolute;
    bottom: 70px;
    right: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    padding: 8px 0;
    min-width: 160px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.floating-menu.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.floating-menu ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.floating-menu li {
    margin: 0;
    padding: 0;
}

.floating-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.2s;
}

.floating-menu a:hover {
    background: #f5f5f5;
}

.menu-icon {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.menu-text {
    flex: 1;
}

.menu-divider {
    height: 1px;
    background: #eee;
    margin: 8px 0;
}

/* 二维码弹窗样式 */
.qrcode-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2000;
    display: none;
    justify-content: center;
    align-items: center;
}

.qrcode-modal.active {
    display: flex;
}

.qrcode-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.qrcode-modal-content {
    position: relative;
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    max-width: 320px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.qrcode-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    border-radius: 0;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.2s;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qrcode-close:hover {
    background: transparent;
    color: #333;
}

.qrcode-modal-content h3 {
    margin: 0 0 10px;
    color: #333;
    font-size: 1.2rem;
}

.qrcode-tip {
    margin: 0 0 20px;
    color: #666;
    font-size: 0.9rem;
}

.qrcode-container {
    width: 200px;
    height: 200px;
    margin: 0 auto 20px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.qrcode-url {
    margin: 0;
    padding: 10px;
    background: #f5f5f5;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #666;
    word-break: break-all;
}

/* 响应式 */
@media (max-width: 768px) {
    .floating-ball-wrapper {
        bottom: 20px;
        right: 20px;
    }
    .floating-ball {
        width: 50px;
        height: 50px;
    }
    .floating-menu {
        min-width: 140px;
    }
}


</style>

<script>
// 打赏功能
function wuchaiwp_open_donate_modal() {
    document.getElementById('donate-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function wuchaiwp_close_donate_modal() {
    document.getElementById('donate-modal').style.display = 'none';
    document.body.style.overflow = '';
}

// 点击弹窗外部关闭
(function() {
    const donateModal = document.getElementById('donate-modal');
    if (donateModal) {
        donateModal.addEventListener('click', function(e) {
            if (e.target === donateModal.querySelector('.donate-modal-overlay')) {
                wuchaiwp_close_donate_modal();
            }
        });
    }
})();

// ESC键关闭弹窗
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const donateModal = document.getElementById('donate-modal');
        if (donateModal && donateModal.style.display === 'flex') {
            wuchaiwp_close_donate_modal();
        }
    }
});

// 点赞功能 - 支持点赞/取消点赞切换
function wuchaiwp_like_post(post_id) {
    var button = document.getElementById('like-btn-' + post_id);
    var countElement = document.getElementById('like-count-' + post_id);
    
    var isLiked = button.classList.contains('liked');
    var currentCount = parseInt(countElement.textContent) || 0;
    
    if (isLiked) {
        // 取消点赞
        button.classList.remove('liked');
        var newCount = Math.max(0, currentCount - 1);
        countElement.textContent = newCount;
        
        // 后台异步发送取消点赞请求
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=wuchaiwp_like_post&post_id=' + post_id + '&action_type=unlike',
            timeout: 5000,
            keepalive: true
        }).catch(function(error) {
            console.warn('取消点赞请求异常:', error);
            countElement.textContent = currentCount;
            button.classList.add('liked');
        });
    } else {
        // 点赞
        button.classList.add('liked');
        var newCount = currentCount + 1;
        countElement.textContent = newCount;
        
        // 后台异步发送点赞请求
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=wuchaiwp_like_post&post_id=' + post_id + '&action_type=like',
            timeout: 5000,
            keepalive: true
        }).catch(function(error) {
            console.warn('点赞请求异常:', error);
            countElement.textContent = currentCount;
            button.classList.remove('liked');
        });
    }
}

// 分享功能
function wuchaiwp_share_article() {
    var url = '<?php echo get_permalink(); ?>';
    var title = '<?php echo urlencode(get_the_title()); ?>';
    
    if (navigator.share) {
        // 使用原生分享API
        navigator.share({
            title: '<?php echo get_the_title(); ?>',
            text: '<?php echo urlencode(get_the_excerpt()); ?>',
            url: url
        }).then(() => {
            console.log('分享成功');
        }).catch((err) => {
            console.log('分享取消或失败:', err);
        });
    } else {
        // 复制链接到剪贴板
        var tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        alert('链接已复制到剪贴板！');
    }
}

// 字体大小调整功能
(function() {
    const fontSizeDecrease = document.getElementById('font-size-decrease');
    const fontSizeReset = document.getElementById('font-size-reset');
    const fontSizeIncrease = document.getElementById('font-size-increase');
    const articleContent = document.querySelector('.entry-content');
    const body = document.body;
    
    let currentFontSize = 16; // 默认字体大小
    const minFontSize = 12;
    const maxFontSize = 24;
    const step = 2;
    
    fontSizeDecrease.addEventListener('click', function() {
        if (currentFontSize > minFontSize) {
            currentFontSize -= step;
            articleContent.style.fontSize = currentFontSize + 'px';
        }
    });
    
    fontSizeReset.addEventListener('click', function() {
        currentFontSize = 16;
        articleContent.style.fontSize = currentFontSize + 'px';
    });
    
    fontSizeIncrease.addEventListener('click', function() {
        if (currentFontSize < maxFontSize) {
            currentFontSize += step;
            articleContent.style.fontSize = currentFontSize + 'px';
        }
    });
})();



// 侧边栏切换功能
(function() {
    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.querySelector('.sidebar-cell');
    const contentCell = document.querySelector('.content-cell');
    
    let sidebarVisible = true;
    
    toggleSidebarBtn.addEventListener('click', function() {
        sidebarVisible = !sidebarVisible;
        
        if (sidebarVisible) {
            // 显示侧边栏
            if (sidebar) sidebar.style.display = '';
            if (contentCell) contentCell.style.width = '';
            toggleSidebarBtn.innerHTML = '<span>☰</span><span class="btn-text"></span>';
        } else {
            // 隐藏侧边栏
            if (sidebar) sidebar.style.display = 'none';
            if (contentCell) contentCell.style.width = '100%';
            toggleSidebarBtn.innerHTML = '<span>☰</span><span class="btn-text"></span>';
        }
    });
})();

// Tab组件切换功能
(function() {
    const tabWidgets = document.querySelectorAll('.blog-tab-widget');
    
    tabWidgets.forEach(function(widget) {
        const tabBtns = widget.querySelectorAll('.tab-btn');
        const tabPanels = widget.querySelectorAll('.tab-panel');
        
        // 默认显示第一个tab
        if (tabBtns.length > 0) {
            tabBtns[0].classList.add('active');
        }
        if (tabPanels.length > 0) {
            tabPanels[0].classList.add('active');
        }
        
        // 添加点击事件
        tabBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const tabKey = this.getAttribute('data-tab');
                
                // 移除所有active类
                tabBtns.forEach(function(b) { b.classList.remove('active'); });
                tabPanels.forEach(function(p) { p.classList.remove('active'); });
                
                // 添加当前active类
                this.classList.add('active');
                const activePanel = widget.querySelector('#tab-' + tabKey);
                if (activePanel) {
                    activePanel.classList.add('active');
                }
            });
        });
    });
})();



// 悬浮球功能
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const floatingBall = document.getElementById('floatingBall');
        const ball = floatingBall.querySelector('.floating-ball');
        const floatingMenu = document.getElementById('floatingMenu');
        const sidebar = document.querySelector('.sidebar-cell');
        const contentCell = document.querySelector('.content-cell');
        const menuToggleSidebar = document.getElementById('menu-toggle-sidebar');
        const menuShowSidebar = document.getElementById('menu-show-sidebar');
        const menuBackToTop = document.getElementById('menu-back-to-top');
        const menuScrollDown = document.getElementById('menu-scroll-down');
        const menuQrcode = document.getElementById('menu-qrcode');
        const qrcodeModal = document.getElementById('qrcodeModal');
        const qrcodeClose = document.getElementById('qrcodeClose');
        const qrcodeContainer = document.getElementById('qrcodeContainer');
        
        let sidebarVisible = true;
        let isDragging = false;
        let startX, startY, initialX, initialY;
        
        // 更新菜单显示状态
        function updateMenuText() {
            if (sidebarVisible) {
                menuToggleSidebar.style.display = 'flex';
                menuShowSidebar.style.display = 'none';
            } else {
                menuToggleSidebar.style.display = 'none';
                menuShowSidebar.style.display = 'flex';
            }
        }
        
        // 点击悬浮球切换菜单
        ball.addEventListener('click', function(e) {
            e.stopPropagation();
            floatingMenu.classList.toggle('active');
            updateMenuText();
        });
        
        // 阻止菜单区域的点击事件冒泡
        floatingMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // 隐藏侧边栏
        menuToggleSidebar.addEventListener('click', function(e) {
            e.preventDefault();
            sidebarVisible = false;
            if (sidebar) sidebar.style.display = 'none';
            if (contentCell) {
                contentCell.style.width = '100%';
                contentCell.style.paddingRight = '0';
            }
            updateMenuText();
        });
        
        // 显示侧边栏
        menuShowSidebar.addEventListener('click', function(e) {
            e.preventDefault();
            sidebarVisible = true;
            if (sidebar) sidebar.style.display = '';
            if (contentCell) {
                contentCell.style.width = '';
                contentCell.style.paddingRight = '';
            }
            updateMenuText();
        });
        
        // 回到顶部
        menuBackToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // 向下滚动
        menuScrollDown.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = window.innerHeight * 0.8;
            const currentPosition = window.scrollY;
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            const targetPosition = Math.min(currentPosition + scrollAmount, maxScroll);
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
        
        // 生成二维码
        menuQrcode.addEventListener('click', function(e) {
            e.preventDefault();
            const currentUrl = window.location.href;
            const qrcodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(currentUrl);
            
            const qrcodeImg = document.createElement('img');
            qrcodeImg.src = qrcodeUrl;
            qrcodeImg.alt = '页面二维码';
            qrcodeImg.style.maxWidth = '100%';
            qrcodeImg.style.maxHeight = '100%';
            
            qrcodeContainer.innerHTML = '';
            qrcodeContainer.appendChild(qrcodeImg);
            
            const qrcodeUrlElement = document.getElementById('qrcodeUrl');
            if (qrcodeUrlElement) {
                qrcodeUrlElement.textContent = currentUrl;
            }
            
            qrcodeModal.classList.add('active');
        });
        
        // 关闭二维码弹窗
        qrcodeClose.addEventListener('click', function() {
            qrcodeModal.classList.remove('active');
        });
        
        qrcodeModal.querySelector('.qrcode-modal-overlay').addEventListener('click', function() {
            qrcodeModal.classList.remove('active');
        });
        
        // 悬浮球拖动功能
        ball.addEventListener('mousedown', function(e) {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            initialX = floatingBall.offsetLeft;
            initialY = floatingBall.offsetTop;
            ball.classList.add('dragging');
            floatingMenu.classList.remove('active');
            
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });
        
        ball.addEventListener('touchstart', function(e) {
            isDragging = true;
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            initialX = floatingBall.offsetLeft;
            initialY = floatingBall.offsetTop;
            ball.classList.add('dragging');
            floatingMenu.classList.remove('active');
        });
        
        ball.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            const touch = e.touches[0];
            moveBall(touch.clientX, touch.clientY);
        });
        
        ball.addEventListener('touchend', function() {
            isDragging = false;
            ball.classList.remove('dragging');
        });
        
        function onMouseMove(e) {
            if (!isDragging) return;
            moveBall(e.clientX, e.clientY);
        }
        
        function onMouseUp() {
            isDragging = false;
            ball.classList.remove('dragging');
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }
        
        function moveBall(clientX, clientY) {
            const deltaX = clientX - startX;
            const deltaY = clientY - startY;
            
            const maxLeft = window.innerWidth - floatingBall.offsetWidth;
            const maxTop = window.innerHeight - floatingBall.offsetHeight;
            
            let newLeft = initialX + deltaX;
            let newTop = initialY + deltaY;
            
            newLeft = Math.max(0, Math.min(newLeft, maxLeft));
            newTop = Math.max(0, Math.min(newTop, maxTop));
            
            floatingBall.style.left = newLeft + 'px';
            floatingBall.style.top = newTop + 'px';
            floatingBall.style.right = 'auto';
            floatingBall.style.bottom = 'auto';
        }
        
        updateMenuText();
    });
})();




</script>

<?php get_footer(); ?>
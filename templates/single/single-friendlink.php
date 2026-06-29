<?php
/**
 * Template Name: 友情链接详情页
 * Description: 简约完整的友情链接详情页模板
 */

get_header(); ?>


<div class="wrap" style="max-width: 1600px;">
    <table class="frontpage-layout" width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <!-- 主内容区域 -->
                <td class="content-cell">
                    <div id="primary" class="content-area">
                        <main id="main" class="site-main" role="main">

                            <?php while (have_posts()) : the_post(); ?>

                                <?php
                                // 获取友情链接自定义字段
                                $friendlink_url = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_url', true);
                                $friendlink_desc = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_desc', true);
                                $friendlink_logo = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_logo', true);
                                $friendlink_status = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_status', true);
                                $friendlink_sort = get_post_meta(get_the_ID(), 'wuchaiwp_friendlink_sort', true);
                                ?>

                                <article id="post-<?php the_ID(); ?>" <?php post_class('friendlink-single'); ?>>
                                    
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
                                        <span class="current"><?php the_title(); ?></span>
                                    </div>

                                    <!-- 标题 -->
                                    <header class="entry-header">
                                        <h1 class="entry-title"><?php the_title(); ?></h1>
                                    </header>

                                    <!-- 友情链接内容 -->
                                    <div class="friendlink-detail">
                                        <div class="friendlink-main">
                                            <!-- Logo -->
                                            <div class="friendlink-logo-large">
                                                <?php if ($friendlink_logo) : ?>
                                                    <img src="<?php echo esc_url($friendlink_logo); ?>" alt="<?php the_title(); ?>" class="logo-img-large">
                                                <?php else : ?>
                                                    <div class="logo-placeholder-large">
                                                        <span class="logo-icon-large">🌐</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- 基本信息 -->
                                            <div class="friendlink-info">
                                                <h2 class="info-title"><?php the_title(); ?></h2>
                                                
                                                <?php if ($friendlink_desc) : ?>
                                                    <p class="info-desc"><?php echo esc_html($friendlink_desc); ?></p>
                                                <?php endif; ?>
                                                
                                                <div class="info-url">
                                                    <span class="url-label">🔗 网站地址：</span>
                                                    <a href="<?php echo !empty($friendlink_url) ? esc_url($friendlink_url) : '#'; ?>" target="_blank" rel="noopener" class="url-link">
                                                        <?php echo esc_html($friendlink_url); ?>
                                                    </a>
                                                </div>
                                                
                                                <!-- 访问按钮 -->
                                                <?php if ($friendlink_url) : ?>
                                                    <a href="<?php echo esc_url($friendlink_url); ?>" target="_blank" rel="noopener" class="visit-btn">
                                                        <span class="btn-icon">🚀</span>
                                                        <span class="btn-text">访问网站</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- 统计信息 -->
                                        <div class="friendlink-stats">
                                            <div class="stat-box">
                                                <span class="stat-value">📅</span>
                                                <span class="stat-label">创建时间</span>
                                                <span class="stat-data"><?php the_date('Y年m月d日'); ?></span>
                                            </div>
                                            <div class="stat-box">
                                                <span class="stat-value">📊</span>
                                                <span class="stat-label">状态</span>
                                                <span class="stat-data"><?php echo $friendlink_status == 'active' ? '✅ 正常' : '🔴 待审核'; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 文章导航 -->
                                    <div class="post-navigation">
                                        <div class="nav-previous">
                                            <?php previous_post_link('%link', '<span class="nav-arrow">←</span> 上一个'); ?>
                                        </div>
                                        <div class="nav-next">
                                            <?php next_post_link('%link', '下一个 <span class="nav-arrow">→</span>'); ?>
                                        </div>
                                    </div>

                                    <!-- 返回归档页 -->
                                    <div class="back-to-archive">
                                        <a href="<?php echo get_post_type_archive_link('friendlink'); ?>">
                                            <span class="back-icon">←</span>
                                            <span class="back-text">返回友情链接列表</span>
                                        </a>
                                    </div>

                                </article>

                            <?php endwhile; ?>

                        </main><!-- #main -->
                    </div><!-- #primary -->
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

/* ===== 面包屑导航 ===== */
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

/* ===== 标题区域 ===== */
.entry-header .entry-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.4;
}

/* ===== 友情链接详情 ===== */
.friendlink-detail {
    width: 100%;
}

.friendlink-main {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    display: flex;
    gap: 30px;
    align-items: center;
}

.friendlink-logo-large {
    flex-shrink: 0;
}

.friendlink-logo-large .logo-img-large {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
}

.logo-placeholder-large {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-icon-large {
    font-size: 48px;
}

.friendlink-info {
    flex: 1;
}

.info-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0 0 12px 0;
}

.info-desc {
    font-size: 16px;
    color: #666;
    line-height: 1.7;
    margin: 0 0 16px 0;
}

.info-url {
    font-size: 14px;
    color: #888;
    margin-bottom: 20px;
}

.url-label {
    font-weight: 500;
}

.url-link {
    color: #3498db;
    text-decoration: none;
    word-break: break-all;
}

.url-link:hover {
    text-decoration: underline;
}

.visit-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: #fff;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.visit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
}

.btn-icon {
    font-size: 18px;
}

/* ===== 统计信息 ===== */
.friendlink-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.stat-box {
    flex: 1;
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.stat-value {
    font-size: 28px;
    margin-bottom: 8px;
    display: block;
}

.stat-label {
    font-size: 13px;
    color: #999;
    display: block;
    margin-bottom: 5px;
}

.stat-data {
    font-size: 16px;
    color: #333;
    font-weight: 500;
    display: block;
}

/* ===== 文章导航 ===== */
.post-navigation {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 20px;
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

/* ===== 返回归档页 ===== */
.back-to-archive {
    text-align: center;
    padding-bottom: 20px;
}

.back-to-archive a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #666;
    text-decoration: none;
    padding: 10px 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.back-to-archive a:hover {
    background: #f5f5f5;
    border-color: #3498db;
    color: #3498db;
}

.back-icon {
    font-size: 14px;
}

/* ===== 响应式布局 ===== */
@media (max-width: 768px) {
    .friendlink-main {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .friendlink-info {
        text-align: center;
    }
    
    .info-url {
        word-break: break-all;
    }
    
    .friendlink-stats {
        flex-direction: column;
    }
    
    .post-navigation {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}
</style>

<?php get_footer(); ?>
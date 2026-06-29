<?php
/**
 * Template Name: 多区域内容布局首页
 * Description: 按照分类、专题区块显示的多区域内容布局首页，包含右边栏
 *
 * @package wuchaiwp
 */

get_header(); ?>

<?php
// 获取各区域文章类型设置的辅助函数
function wuchaiwp_get_section_post_types($section_id) {
    // 获取各区域独立设置
    $section_post_types = get_option('wuchaiwp_multi_section_post_types', array());
    
    // 如果该区域有独立设置且不为空，使用独立设置
    if (isset($section_post_types[$section_id]) && !empty($section_post_types[$section_id])) {
        return $section_post_types[$section_id];
    }
    
    // 否则使用全局设置
    return get_option('wuchaiwp_multi_post_types', array('post'));
}
?>

<div class="wrap">
<?php if (get_option('wuchaiwp_floating_ball_frontpage', '1') == '1') : ?>
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
        <div class="qrcode-container" id="qrcodeContainer">
            <!-- 二维码将在这里生成 -->
        </div>
        <!--<p class="qrcode-url"><?php //echo esc_url(get_permalink()); ?></p>-->
        <p class="qrcode-url" id="qrcodeUrl"></p>
    </div>
</div>

<style>
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
</style>

<style>
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

<table class="frontpage-layout" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<!-- 第一行：搜索栏 -->
		<tr>
			<td colspan="2" class="search-row">
				<!-- Hero区域 -->
				<section id="hero">
					<div class="main">
						<h1 class="hero-title"><a href="<?php //echo home_url('/'); ?>" class="hero-link"><?php //echo get_option('wuchai_hero_title', bloginfo('name', 'display')); ?></a></h1>
						<p class="hero-description"><a href="<?php //echo home_url('/'); ?>" class="hero-link"><?php //echo get_option('wuchai_hero_description', bloginfo('description', 'display')); ?></a></p>
						<!-- 搜索区域 - 使用吾侪资料搜索框插件 -->
						<div class="hero-search-wrapper">
							<div class="hero-search-container">
								<?php echo do_shortcode('[wuchai_search_box placeholder="搜索..." results_container="wuchai-search-results"]'); ?>
							</div>
						</div>
					</div>
				</section>
			</td>
		</tr>
		<!-- 第二行：内容区域和侧边栏 -->
		<tr>
			<td class="content-cell">
				<div id="primary" class="content-area">
					<main id="main" class="site-main" role="main">
						<!-- 搜索结果区域 -->
						<section id="wuchai-search-results-container" style="display: none;">
							<?php echo do_shortcode('[wuchai_search_results id="wuchai-search-results"]'); ?>
						</section>

						<!-- 主内容区域 -->
						<div id="main-content-area">
				<!-- 专题推荐区块 -->
				<section class="home-section featured-section">
					<!--<div class="section-header">
						<h2 class="section-title">
							<span class="section-icon">🎯</span>
							专题推荐
						</h2>
					</div>-->
					<div class="section-content featured-grid">
						<?php
						// 获取后台设置
							$featured_count = intval(get_option('wuchaiwp_multi_featured_posts_count', 6));
							$featured_source = get_option('wuchaiwp_multi_featured_source', 'sticky');
							$selected_posts = get_option('wuchaiwp_multi_featured_posts', array());
							$post_types = wuchaiwp_get_section_post_types('featured');
						
						// 根据来源获取文章
						switch ($featured_source) {
							case 'selected':
								$args = array(
									'post_type' => $post_types,
									'posts_per_page' => $featured_count,
									'post__in' => empty($selected_posts) ? array(0) : $selected_posts,
									'ignore_sticky_posts' => 1,
									'orderby' => 'post__in'
								);
								break;
							case 'featured':
								$args = array(
									'post_type' => $post_types,
									'posts_per_page' => $featured_count,
									'meta_key' => 'featured_post',
									'meta_value' => '1'
								);
								break;
							case 'latest':
								$args = array(
									'post_type' => $post_types,
									'posts_per_page' => $featured_count,
									'ignore_sticky_posts' => 1
								);
								break;
							case 'sticky':
							default:
								$sticky_posts = get_option('sticky_posts');
								$args = array(
									'post_type' => $post_types,
									'posts_per_page' => $featured_count,
									'post__in' => empty($sticky_posts) ? array(0) : $sticky_posts,
									'ignore_sticky_posts' => 1
								);
								break;
						}
						
						$query = new WP_Query($args);
						$posts = $query->have_posts() ? $query->posts : array();
						
						// 如果没有结果，回退到最新文章
						if (empty($posts)) {
							$args = array('post_type' => $post_types, 'posts_per_page' => $featured_count);
							$query = new WP_Query($args);
							$posts = $query->posts;
						}
						
						foreach ($posts as $index => $post) :
							setup_postdata($post);
							$is_featured = $index === 0;
							?>
							<article class="featured-card <?php echo $is_featured ? 'featured-main' : 'featured-secondary'; ?>">
								
								<div class="featured-thumbnail">
										<a href="<?php the_permalink(); ?>">
											<?php if (has_post_thumbnail()) : 
												the_post_thumbnail($is_featured ? 'large' : 'medium'); 
											else : ?>
												<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'%3E%3Crect fill='%23e0e0e0' width='400' height='300'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='32' x='50%25' y='50%25' text-anchor='middle' dominant-baseline='middle'%3E%3C/text%3E%3C/svg%3E" alt="" />
											<?php endif; ?>
										</a>
										<?php if ($is_featured) : ?>
										<div class="post-badge">🎯 专题推荐</div>
										<?php endif; ?>
									</div>
   
								<div class="featured-content">
									<?php if (is_sticky()) : ?>
									<span class="sticky-badge">📌 置顶</span>
									<?php endif; ?>
									<h3 class="featured-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<?php if ($is_featured) : ?>
									<p class="featured-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
									<?php endif; ?>
								</div>
							</article>
							<?php
							endforeach;
							wp_reset_postdata();
						?>
					</div>
			</section>

			<!-- 自定义文章类型专题区域 -->
			<?php
			$selected_cpt = get_option('wuchaiwp_multi_cpt_display', array());
			$cpt_covers = get_option('wuchaiwp_multi_cpt_covers', array());
			if (!empty($selected_cpt)) :
			?>
			<section class="home-section">
				<!--<div class="section-header">
					<h2 class="section-title">
						<span class="section-icon">📚</span>
						专题栏目
					</h2>
				</div>-->
				<div class="section-content custom-post-types-grid">
					<?php
					foreach ($selected_cpt as $cpt_name) :
						$post_type = get_post_type_object($cpt_name);
						if (!$post_type) continue;
						
						// 获取该文章类型的文章数量
						$count = wp_count_posts($cpt_name)->publish;
						
						// 获取归档链接
						$archive_link = get_post_type_archive_link($cpt_name);
						
						// 获取封面图片（优先使用后台设置的封面）
						$cover_image = '';
						if (isset($cpt_covers[$cpt_name]) && $cpt_covers[$cpt_name]) {
							// 使用后台设置的封面
							$cover_image = wp_get_attachment_url($cpt_covers[$cpt_name]);
						} else {
							// 自动获取该类型第一篇文章的特色图片
							$posts = get_posts(array(
								'post_type' => $cpt_name,
								'posts_per_page' => 1,
								'ignore_sticky_posts' => 1
							));
							if (!empty($posts) && has_post_thumbnail($posts[0]->ID)) {
								$cover_image = get_the_post_thumbnail_url($posts[0]->ID, 'medium');
							}
						}
					?>
					<article class="custom-post-type-card">
						<a href="<?php echo esc_url($archive_link); ?>">
							<div class="custom-post-type-thumbnail" style="background-image: url(<?php echo $cover_image ? esc_url($cover_image) : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"%3E%3Crect fill="%23e0e0e0" width="400" height="300"/%3E%3Ctext fill="%23999" font-family="sans-serif" font-size="32" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3E📚%3C/text%3E%3C/svg%3E'; ?>)">
								<div class="custom-post-type-overlay"></div>
								
								<div class="custom-post-type-badge">📚 专题栏目</div>  <!-- 添加这一行 -->
    
								<div class="custom-post-type-content">
									<h3 class="custom-post-type-title"><?php echo esc_html($post_type->labels->name); ?></h3>
									<span class="custom-post-type-count"><?php echo $count; ?> 篇文章</span>
								</div>
							</div>
						</a>
					</article>
					<?php endforeach; ?>
				</div>
			</section>
			<?php endif; ?>

			<!-- 最新发布区块 -->
				<section class="home-section">
					<!--<div class="section-header">
						<h2 class="section-title">
							<span class="section-icon">🔥</span>
							最新发布
						</h2>
					</div>-->
					<div class="section-content row">
						<?php
					$latest_count = intval(get_option('wuchaiwp_multi_latest_posts_count', 4));
					$post_types = wuchaiwp_get_section_post_types('latest');
					$args = array(
						'post_type' => $post_types,
						'posts_per_page' => $latest_count,
						'orderby' => 'date',
						'order' => 'DESC',
						'ignore_sticky_posts' => 1
					);
					$query = new WP_Query($args);
					$first_post = true;
					if ($query->have_posts()) :
						while ($query->have_posts()) : $query->the_post();
					?>
					<article class="post-card col-md-6">
						<div class="post-thumbnail">
							<a href="<?php the_permalink(); ?>">
								<?php if (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('medium'); ?>
								<?php else : ?>
									<div class="default-thumbnail"><span class="default-icon"></span></div>
								<?php endif; ?>
							</a>
							<?php if ($first_post) : ?>
							<div class="post-badge">🔥 最新发布</div>
							<?php $first_post = false; endif; ?>
						</div>
						<div class="post-content">
							<div class="post-meta">
								<span class="post-category"><?php the_category(', '); ?></span>
								<span class="post-date"><?php the_date(); ?></span>
							</div>
							<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
							<div class="post-footer">
								<span class="post-author"><?php the_author(); ?></span>
								<span class="post-comments"><?php comments_number('0', '1', '%'); ?> 评论</span>
							</div>
						</div>
					</article>
					<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</section>

		<!-- 热门推荐区块 -->
				<section class="home-section">
					<!--<div class="section-header">
						<h2 class="section-title">
							<span class="section-icon">⭐</span>
							热门推荐
						</h2>
					</div>-->
					<div class="section-content row">
						<?php
					$hot_count = intval(get_option('wuchaiwp_multi_hot_posts_count', 4));
					$post_types = wuchaiwp_get_section_post_types('hot');
					$args = array(
						'post_type' => $post_types,
						'posts_per_page' => $hot_count,
						'orderby' => 'comment_count',
						'order' => 'DESC',
						'ignore_sticky_posts' => 1
					);
					$query = new WP_Query($args);
					$first_post = true;
					if ($query->have_posts()) :
						while ($query->have_posts()) : $query->the_post();
					?>
					<article class="post-card col-md-6">
						<div class="post-thumbnail">
							<a href="<?php the_permalink(); ?>">
								<?php if (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('medium'); ?>
								<?php else : ?>
									<div class="default-thumbnail"><span class="default-icon"></span></div>
								<?php endif; ?>
							</a>
							<?php if ($first_post) : ?>
							<div class="post-badge">⭐ 热门推荐</div>
							<?php $first_post = false; endif; ?>
						</div>
						<div class="post-content">
							<div class="post-meta">
								<span class="post-category"><?php the_category(', '); ?></span>
								<span class="post-date"><?php the_date(); ?></span>
							</div>
							<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
							<div class="post-footer">
								<span class="post-author"><?php the_author(); ?></span>
								<span class="post-comments"><?php comments_number('0', '1', '%'); ?> 评论</span>
							</div>
						</div>
					</article>
					<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>
				</div>
			</section>

		<!-- 分类区块 -->
				<?php
				$category_count = intval(get_option('wuchaiwp_multi_category_count', 2));
				$category_posts_count = intval(get_option('wuchaiwp_multi_category_posts_count', 3));
				$categories = get_categories(array(
					'orderby' => 'name',
					'order' => 'ASC',
					'hide_empty' => true,
					'number' => $category_count
				));
				foreach ($categories as $category) :
				?>
				<section class="home-section">
							<div class="section-header">
								<h2 class="section-title">
									<span class="section-icon">📁</span>
									<a href="<?php echo get_category_link($category->term_id); ?>" class="category-title-link"><?php echo esc_html($category->name); ?></a>
									<span class="category-post-count">(<?php echo $category->count; ?>篇)</span>
								</h2>
								<a href="<?php echo get_category_link($category->term_id); ?>" class="section-more">查看更多 →</a>
							</div>
							<div class="section-content row">
								<?php
								$post_types = wuchaiwp_get_section_post_types('category');
								$args = array(
									'post_type' => $post_types,
									'posts_per_page' => $category_posts_count,
									'cat' => $category->term_id,
									'ignore_sticky_posts' => 1
								);
						$query = new WP_Query($args);
						if ($query->have_posts()) :
							while ($query->have_posts()) : $query->the_post();
						?>
						<article class="post-card col-md-4">
						<div class="post-thumbnail">
							<a href="<?php the_permalink(); ?>">
								<?php if (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('medium'); ?>
								<?php else : ?>
									<div class="default-thumbnail"><span class="default-icon"></span></div>
								<?php endif; ?>
							</a>
						</div>
						<div class="post-content">
								<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
							</div>
						</article>
						<?php
							endwhile;
							wp_reset_postdata();
						endif;
						?>
					</div>
				</section>
				<?php endforeach; ?>

				<!-- 底部小工具区域 -->
			<div class="frontpage-widget-area">
				<?php if (is_active_sidebar('frontpage-footer-widgets')) : ?>
					<?php dynamic_sidebar('frontpage-footer-widgets'); ?>
				<?php endif; ?>
			</div>

				<style>
				/* 多区域首页样式 */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 20px;
    text-align: center;
    color: white;
    margin-bottom: 20px;
}

.hero-section .container {
    max-width: 1200px;
    margin: 0 auto;
}

.hero-title {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.hero-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 20px;
}

.hero-search-wrapper {
    width: 100%;
    max-width: 100% !important;
    padding: -1px !important;
    margin: -1px !important;
    box-sizing: border-box;
}

.hero-search-container {
    width: 100%;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    box-sizing: border-box;
}

#hero {
    margin-bottom: 15px;
    background: transparent;
    padding: 0;
}

#hero .main {
    padding: 0;
    margin: 0;
}

/* 确保搜索框插件充满宽度 */
.wuchai-search-box {
    width: 100% !important;
    max-width: 100% !important;
}

/* Table布局样式 */
.frontpage-layout {
    width: 100%;
    border-collapse: collapse;
    border: none;
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

.frontpage-layout .search-row {
    width: 100%;
}

.frontpage-layout .content-cell {
    vertical-align: top;
    padding-right: 30px;
    border: none;
}

.frontpage-layout .sidebar-cell {
    vertical-align: top;
    width: 25%;
    min-width: 250px;
    border: none;
}

.hero-search {
    width: 100%;
    max-width: none;
    margin: 0;
}

.hero-search .search-form {
    display: flex;
    width: 100%;
    box-sizing: border-box;
}

.hero-search .search-field {
    flex: 1;
    padding: 12px 15px;
    border: none;
    border-radius: 4px 0 0 4px;
    font-size: 1rem;
    box-sizing: border-box;
}

.hero-search .search-submit {
    padding: 12px 20px;
    background: #fff;
    color: #667eea;
    border: none;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    font-weight: bold;
    white-space: nowrap;
}

/* 首页内容区域 */
.frontpage-multi {
    float: none;
    width: auto;
    padding-right: 0;
    box-sizing: border-box;
    flex: 1;
}

/* 区块样式 */
.home-section {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 0;
    border-bottom: none;
}

.section-title {
    font-size: 1.4rem;
    color: #2c3e50;
    margin: 0;
    font-weight: 600;
}

.section-icon {
    margin-right: 10px;
    font-size: 1.2rem;
}


/* 专题栏目样式 */
.custom-post-type-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 10px;
    background: rgba(0, 0, 0, 0.6);
    color: #fff;
    font-size: 0.7rem;
    border-radius: 4px;
    z-index: 1;
}


/* 分类标题链接样式 */
.category-title-link {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s;
}

.category-title-link:hover {
    color: #667eea;
    text-decoration: underline;
}

/* 分类文章数量样式 */
.category-post-count {
    color: #999;
    font-size: 0.85rem;
    font-weight: normal;
    margin-left: 5px;
}

.section-more {
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s;
}

.section-more:hover {
    color: #5a6fd6;
    text-decoration: underline;
}

/* 文章卡片 */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.col-md-6 {
    width: 50%;
    padding: 0 10px;
    box-sizing: border-box;
    margin-bottom: 16px;
}

.col-md-4 {
    width: 33.33%;
    padding: 0 10px;
    box-sizing: border-box;
    margin-bottom: 16px;
}

.post-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.post-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/*.post-thumbnail {
    overflow: hidden;
}*/

.post-thumbnail {
    position: relative;  /* 添加这一行 */
    overflow: hidden;
    border-radius: 8px;
}


.post-thumbnail img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.05);
}

.default-thumbnail {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.default-icon {
    font-size: 36px;
    color: #999;
}

.post-content {
    padding: 15px;
}

.post-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 0.85rem;
    color: #888;
}

.post-category {
    color: #667eea;
}

.post-title {
    font-size: 1.1rem;
    margin: 0 0 10px;
    line-height: 1.4;
}

.post-title a {
    color: #2c3e50;
    text-decoration: none;
}

.post-title a:hover {
    color: #667eea;
}

.post-excerpt {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.6;
    margin: 0 0 10px;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #888;
}

/* 文章封面重叠标签 */
.post-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 5px 12px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    font-size: 12px;
    font-weight: 500;
    border-radius: 4px;
    z-index: 10;
}

/* 自定义文章类型专题区域样式 */
.custom-post-types-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-top: 15px;
}

.custom-post-type-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.custom-post-type-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.custom-post-type-card a {
    text-decoration: none;
    display: block;
}

.custom-post-type-thumbnail {
    position: relative;
    height: 120px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.custom-post-type-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
}

.custom-post-type-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px 12px;
    color: #fff;
}

.custom-post-type-title {
    margin: 0 0 3px 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #fff;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.custom-post-type-card:hover .custom-post-type-title {
    color: #fff;
}

.custom-post-type-count {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.85);
}

/* 响应式布局 */
@media (max-width: 1200px) {
    .custom-post-types-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .custom-post-types-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .custom-post-type-thumbnail {
        height: 100px;
    }
    
    .custom-post-type-content {
        padding: 10px;
    }
    
    .custom-post-type-title {
        font-size: 0.85rem;
    }
    
    .custom-post-type-badge {
        font-size: 0.65rem;
        padding: 3px 8px;
        top: 6px;
        left: 6px;
    }
}

@media (max-width: 480px) {
    .custom-post-types-grid {
        grid-template-columns: 1fr;
    }
    
    .custom-post-type-thumbnail {
        height: 120px;
    }
}


 /* 内容区域容器 */
    #primary,
    .content-area,
    #main,
    .site-main,
    #main-content-area {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box;
        overflow-x: hidden;
    }


/* 专题区块 */
.featured-section {
    background: #fafafa;
}


.featured-thumbnail {
    position: relative;  /* 添加这一行 */
}


.featured-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px;
}

.featured-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.featured-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.featured-main {
    grid-column: span 2;
    display: flex;
}

.featured-main .featured-thumbnail {
    flex-shrink: 0;
    width: 45%;
}

.featured-main .featured-thumbnail img {
    height: 100%;
    object-fit: cover;
}

.featured-main .featured-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.featured-secondary .featured-thumbnail img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.featured-secondary .featured-content {
    padding: 12px;
}

.featured-title {
    font-size: 1rem;
    margin: 0;
    line-height: 1.4;
}

.featured-title a {
    color: #2c3e50;
    text-decoration: none;
}

.featured-title a:hover {
    color: #667eea;
}

.featured-excerpt {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.6;
    margin: 10px 0 0;
}

/* 首页底部小工具样式 - 每个小工具独立显示为单独的区块 */
.frontpage-widget-area {
    display: block;
    width: 100%;
    margin: 0 0 20px 0;
    padding: 0;
}

/* 首页底部小工具卡片样式 - 与文章详情页侧边栏小工具样式一致 */
.frontpage-widget-area .widget {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    border: 1px solid #f0f0f0;
}

.frontpage-widget-area .widget-title {
    margin-top: 0;
    margin-bottom: 18px;
    font-size: 1.2rem;
    color: #2c3e50;
    padding-bottom: 12px;
    border-bottom: 2px solid #3498db;
    font-weight: 600;
    position: relative;
}

.frontpage-widget-area .widget-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 40px;
    height: 2px;
    background: #667eea;
}

/* 文章列表小工具样式 - 与侧边栏样式一致 */
.frontpage-widget-area .popular-widget ul,
.frontpage-widget-area .recommend-widget ul,
.frontpage-widget-area .widget ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.frontpage-widget-area .popular-widget li,
.frontpage-widget-area .recommend-widget li,
.frontpage-widget-area .widget li {
    padding: 10px 0;
    border-bottom: 1px dashed #eee;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.frontpage-widget-area .popular-widget li:last-child,
.frontpage-widget-area .recommend-widget li:last-child,
.frontpage-widget-area .widget li:last-child {
    border-bottom: none;
}

.frontpage-widget-area .popular-widget li::before,
.frontpage-widget-area .recommend-widget li::before,
.frontpage-widget-area .widget li::before {
    content: '▸';
    color: #3498db;
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 2px;
}

.frontpage-widget-area .popular-widget a,
.frontpage-widget-area .recommend-widget a,
.frontpage-widget-area .widget a {
    color: #333;
    text-decoration: none;
    font-size: 14px;
    flex: 1;
    line-height: 1.5;
    transition: color 0.3s;
}

.frontpage-widget-area .popular-widget a:hover,
.frontpage-widget-area .recommend-widget a:hover,
.frontpage-widget-area .widget a:hover {
    color: #3498db;
}

.frontpage-widget-area .popular-widget .views,
.frontpage-widget-area .recommend-widget .views,
.frontpage-widget-area .widget .views {
    color: #999;
    font-size: 12px;
    flex-shrink: 0;
}

/* 相关文章小工具样式 */
.frontpage-widget-area .related-posts-widget .related-post-item {
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px dashed #eee;
}

.frontpage-widget-area .related-posts-widget .related-post-item:last-child {
    border-bottom: none;
}

.frontpage-widget-area .related-posts-widget .related-post-thumbnail {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    overflow: hidden;
    border-radius: 6px;
}

.frontpage-widget-area .related-posts-widget .related-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.frontpage-widget-area .related-posts-widget .related-post-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.frontpage-widget-area .related-posts-widget .related-post-title {
    font-size: 14px;
    color: #333;
    text-decoration: none;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.frontpage-widget-area .related-posts-widget .related-post-title:hover {
    color: #3498db;
}

.frontpage-widget-area .related-posts-widget .related-post-meta {
    font-size: 12px;
    color: #999;
}

/* 响应式布局 */
@media (max-width: 992px) {
    .frontpage-multi {
        width: 100%;
        padding-right: 0;
    }
    
    .col-md-6 {
        width: 100%;
    }
    
    .col-md-4 {
        width: 50%;
    }
    
    .featured-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    .featured-main {
        grid-column: span 2;
    }
}

/* 确保不影响全局标题栏和菜单栏 */
header,
.site-header,
.main-navigation,
.navbar {
    margin: 0 !important;
    padding: 0 !important;
    width: auto !important;
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 1.6rem;
    }
    
    .hero-search-wrapper {
        padding: 0 !important;
    }
    
    .hero-search-container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    
    /* 移动端区块内边距优化 */
    .home-section {
        padding: 12px;
        margin-bottom: 12px;
    }
    
    .col-md-6,
    .col-md-4 {
        width: 100%;
        padding: 0 6px;
        margin-bottom: 12px;
    }
    
    .row {
        margin: 0 -6px;
    }
    
    .featured-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .featured-main {
        grid-column: span 1;
        flex-direction: column;
    }
    
    .featured-main .featured-thumbnail {
        width: 100%;
        height: 160px;
    }
    
    .featured-main .featured-content {
        padding: 12px;
    }
    
    .featured-secondary .featured-content {
        padding: 10px;
    }
    
    /* 手机端table布局自适应 - 侧边栏自动掉下来 */
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
        padding: 0 !important;
        min-width: auto !important;
    }
    
    .frontpage-layout .sidebar-cell {
        margin-top: 12px !important;
    }
    
    /* 移动端文章卡片内容内边距优化 */
    .post-content {
        padding: 12px;
    }
    
    /* 移动端小工具区域内边距优化 */
    .frontpage-widget-area .widget {
        padding: 15px;
        margin-bottom: 12px;
    }
    
    /* 移动端专题栏目卡片优化 */
    .custom-post-type-thumbnail {
        height: 100px;
    }
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
        grid-template-columns: 1fr !important;
        gap: 12px;
    }
    
    .two-column-cards-horizontal .card-horizontal {
        padding: 10px;
    }
    
    .two-column-cards-horizontal .card-thumb {
        width: 80px;
        height: 80px;
    }
    
    /* 两列列表移动端优化 */
    .two-column-list .list-column {
        padding: 0;
        margin: 0;
    }
    
    .two-column-list .list-column li {
        padding: 10px 0;
        border-bottom: 1px dashed #eee;
    }
    
    .two-column-list .list-column li a {
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: unset !important;
        font-size: 14px;
        line-height: 1.5;
        color: #333;
        display: block;
        padding-right: 0;
    }
    
    .two-column-list .list-meta {
        flex-wrap: wrap;
        gap: 8px;
        font-size: 12px;
        color: #999;
        margin-top: 6px;
    }
}





			
				</style>
				
				
				</section>
			</div><!-- #main-content-area -->

						</div><!-- #main-content-area -->
					</main><!-- #main -->
				</div><!-- #primary -->
			</td>
			<td class="sidebar-cell">
				<?php get_sidebar(); ?>
			</td>
		</tr>
	</tbody>
</table>
</div><!-- .wrap -->

<?php get_footer(); ?>



<script type="text/javascript">
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
		
		// 点击页面其他地方关闭菜单
		document.addEventListener('click', function() {
			floatingMenu.classList.remove('active');
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
			//floatingMenu.classList.remove('active');
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
			//floatingMenu.classList.remove('active');
			updateMenuText();
		});
		
		// 回到顶部
		const menuBackToTop = document.getElementById('menu-back-to-top');
		menuBackToTop.addEventListener('click', function(e) {
			e.preventDefault();
			// 平滑滚动到顶部
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
			//floatingMenu.classList.remove('active');
		});
		
		// 向下滚动
		const menuScrollDown = document.getElementById('menu-scroll-down');
		menuScrollDown.addEventListener('click', function(e) {
			e.preventDefault();
			// 平滑向下滚动一个视窗高度
			const scrollAmount = window.innerHeight * 0.8; // 滚动80%视窗高度
			const currentPosition = window.scrollY;
			const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
			const targetPosition = Math.min(currentPosition + scrollAmount, maxScroll);
			
			window.scrollTo({
				top: targetPosition,
				behavior: 'smooth'
			});
		//	floatingMenu.classList.remove('active');
		});
		
		// 生成二维码
		const menuQrcode = document.getElementById('menu-qrcode');
		const qrcodeModal = document.getElementById('qrcodeModal');
		const qrcodeClose = document.getElementById('qrcodeClose');
		const qrcodeContainer = document.getElementById('qrcodeContainer');
		
		menuQrcode.addEventListener('click', function(e) {
			e.preventDefault();
			//floatingMenu.classList.remove('active');
			showQrcode();
		});
		
		qrcodeClose.addEventListener('click', function() {
			qrcodeModal.classList.remove('active');
		});
		
		// 点击遮罩关闭
		qrcodeModal.querySelector('.qrcode-modal-overlay').addEventListener('click', function() {
			qrcodeModal.classList.remove('active');
		});
		
		function showQrcode() {
			const currentUrl = window.location.href;
			// 使用 Google Charts API 生成二维码
			//const qrcodeUrl = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' + encodeURIComponent(currentUrl) + '&choe=UTF-8';
			
			
			// 使用 goqr.me API 生成二维码
const qrcodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(currentUrl);
			
			// 创建二维码图片
			const qrcodeImg = document.createElement('img');
			qrcodeImg.src = qrcodeUrl;
			qrcodeImg.alt = '页面二维码';
			qrcodeImg.style.maxWidth = '100%';
			qrcodeImg.style.maxHeight = '100%';
			
			// 清空容器并添加图片
			qrcodeContainer.innerHTML = '';
			qrcodeContainer.appendChild(qrcodeImg);
			
			
			// 设置显示的URL
    const qrcodeUrlElement = document.getElementById('qrcodeUrl');
    if (qrcodeUrlElement) {
        qrcodeUrlElement.textContent = currentUrl;
    }
			
			
			// 显示弹窗
			qrcodeModal.classList.add('active');
		}
		
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
		
		// 触摸拖动
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
			
			// 边界检测
			newLeft = Math.max(0, Math.min(newLeft, maxLeft));
			newTop = Math.max(0, Math.min(newTop, maxTop));
			
			floatingBall.style.left = newLeft + 'px';
			floatingBall.style.top = newTop + 'px';
			floatingBall.style.right = 'auto';
			floatingBall.style.bottom = 'auto';
		}
		
		// 初始化
		updateMenuText();
	});
})();

// 搜索结果显示/隐藏交互
(function() {
	document.addEventListener('DOMContentLoaded', function() {
		var searchResultsContainer = document.getElementById('wuchai-search-results-container');
		var mainContentArea = document.getElementById('main-content-area');
		var searchInput = document.querySelector('.wuchai-search-input');
		
		// 监听搜索输入
		if (searchInput) {
			searchInput.addEventListener('input', function() {
				var query = this.value.trim();
				if (query.length > 0) {
					// 有搜索词时，显示搜索结果，隐藏主内容
					if (searchResultsContainer) searchResultsContainer.style.display = 'block';
					if (mainContentArea) mainContentArea.style.display = 'none';
				} else {
					// 无搜索词时，隐藏搜索结果，显示主内容
					if (searchResultsContainer) searchResultsContainer.style.display = 'none';
					if (mainContentArea) mainContentArea.style.display = 'block';
				}
			});
		}
		
		// 点击热门搜索词时隐藏主内容
		var popularTerms = document.querySelectorAll('.wuchai-popular-term');
		popularTerms.forEach(function(term) {
			term.addEventListener('click', function() {
				if (searchResultsContainer) searchResultsContainer.style.display = 'block';
				if (mainContentArea) mainContentArea.style.display = 'none';
			});
		});
		
		// 点击清除按钮时恢复显示
		var clearBtn = document.querySelector('.wuchai-search-clear');
		if (clearBtn) {
			clearBtn.addEventListener('click', function() {
				if (searchResultsContainer) searchResultsContainer.style.display = 'none';
				if (mainContentArea) mainContentArea.style.display = 'block';
			});
		}
	});
})();
</script>
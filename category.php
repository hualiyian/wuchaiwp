<?php get_header(); ?>

<style>
/* 分类页面容器样式 */
.category-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    box-sizing: border-box;
}

/* 归档标题 */
.archive-header {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #eee;
}

.archive-title {
    font-size: 1.8rem;
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.archive-description {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
    margin: 0;
}

/* 网格布局 */
.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* 文章卡片 */
.template-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.template-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

/* 缩略图区域 */
.template-thumbnail {
    position: relative;
    overflow: hidden;
}

.template-thumbnail img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.template-card:hover .template-thumbnail img {
    transform: scale(1.05);
}

.template-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 5px 12px;
    background: rgba(255,255,255,0.95);
    color: #667eea;
    font-size: 12px;
    font-weight: 500;
    border-radius: 4px;
    z-index: 10;
}

/* 内容区域 */
.template-info {
    padding: 16px;
}

.template-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.template-title a {
    color: #2c3e50;
    text-decoration: none;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.template-title a:hover {
    color: #667eea;
}

.template-excerpt {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.6;
    margin: 0 0 12px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 元信息 */
.template-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px dashed #eee;
}

.template-author {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #888;
}

.template-author img {
    border-radius: 50%;
}

/* 作者链接样式 */
.template-author .author-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #888;
    text-decoration: none;
    transition: color 0.3s;
}

.template-author .author-link:hover {
    color: #667eea;
}

/* 封面链接样式 */
.template-thumbnail .thumbnail-link {
    display: block;
}

.template-author .author-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #888;
    text-decoration: none;
    transition: color 0.3s;
}

.template-author .author-link:hover {
    color: #667eea;
}

.template-thumbnail .thumbnail-link {
    display: block;
}

.template-stats {
    display: flex;
    gap: 12px;
}

.template-stat {
    font-size: 0.85rem;
    color: #888;
}

/* 分页 */
.pagination {
    text-align: center;
    padding-top: 20px;
}

/* 响应式布局 */
@media (max-width: 768px) {
    .category-container {
        padding: 12px;
        max-width: 100%;
        width: 100%;
    }
    
    .archive-title {
        font-size: 1.5rem;
    }
    
    .templates-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .template-card {
        border-radius: 8px;
    }
    
    .template-thumbnail img {
        height: 160px;
    }
    
    .template-info {
        padding: 14px;
    }
    
    .template-title {
        font-size: 1rem;
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
    }
    
    .template-title a {
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .template-meta {
        flex-wrap: wrap;
        gap: 10px;
    }
}

@media (max-width: 480px) {
    .category-container {
        padding: 10px;
    }
    
    .template-thumbnail img {
        height: 140px;
    }
    
    .template-badge {
        font-size: 11px;
        padding: 4px 10px;
        top: 8px;
        left: 8px;
    }
}
</style>

<div class="category-container">
	<header class="archive-header">
		<h1 class="archive-title"><?php echo single_cat_title('', false); ?></h1>
		<?php if (category_description()) : ?>
			<p class="archive-description"><?php echo category_description(); ?></p>
		<?php endif; ?>
	</header>

	<div class="templates-grid">
		<?php
		if (have_posts()) :
			while (have_posts()) : the_post();
				?>
				<article class="template-card">
					<div class="template-thumbnail">
						<a href="<?php the_permalink(); ?>" class="thumbnail-link">
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('medium'); ?>
							<?php else : ?>
								<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'%3E%3Crect fill='%23f0f0f0' width='400' height='300'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='24' x='50%25' y='50%25' text-anchor='middle' dominant-baseline='middle'%3E%3C/text%3E%3C/svg%3E" alt="默认缩略图">
							<?php endif; ?>
						</a>
						<span class="template-badge"><?php the_category(', '); ?></span>
					</div>
					<div class="template-info">
						<h3 class="template-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="template-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
						<div class="template-meta">
							<div class="template-author">
								<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="author-link">
									<?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
									<span><?php the_author(); ?></span>
								</a>
							</div>
							<div class="template-stats">
								<span class="template-stat">👁️<?php echo get_post_meta(get_the_ID(), 'wuchaiwp_views', true) ?: '0'; ?></span>
								<span class="template-stat">💬 <?php comments_number('0', '1', '%'); ?></span>
							</div>
						</div>
					</div>
				</article>
				<?php
			endwhile;
		else :
			echo '<p class="text-center" style="padding: 40px 20px; color: #999;">该分类暂无内容</p>';
		endif;
		?>
	</div>

	<div class="pagination">
		<?php the_posts_navigation(); ?>
	</div>
</div>

<?php get_footer(); ?>
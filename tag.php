<?php get_header(); ?>

<div class="container">
	<header class="archive-header">
		<h1 class="archive-title"><?php echo single_tag_title('', false); ?></h1>
		<?php if (tag_description()) : ?>
			<p class="archive-description"><?php echo tag_description(); ?></p>
		<?php endif; ?>
	</header>

	<div class="templates-grid">
		<?php
		if (have_posts()) :
			while (have_posts()) : the_post();
				?>
				<article class="template-card">
					<div class="template-thumbnail">
						<?php if (has_post_thumbnail()) : ?>
							<?php the_post_thumbnail('template-thumbnail'); ?>
						<?php else : ?>
							<img src="https://via.placeholder.com/400x300" alt="默认缩略图">
						<?php endif; ?>
						<span class="template-badge"><?php the_tags('', ', ', ''); ?></span>
					</div>
					<div class="template-info">
						<h3 class="template-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="template-excerpt"><?php the_excerpt(); ?></p>
						<div class="template-meta">
							<div class="template-author">
								<?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
								<span><?php the_author(); ?></span>
							</div>
							<div class="template-stats">
								<span class="template-stat">👁️<?php echo get_post_meta(get_the_ID(), 'wuchai_views', true) ?: '0'; ?></span>
								<span class="template-stat">⬇️<?php echo get_post_meta(get_the_ID(), 'wuchai_downloads', true) ?: '0'; ?></span>
							</div>
						</div>
					</div>
				</article>
				<?php
			endwhile;
		else :
			echo '<p class="text-center col-span-full">该标签暂无内容</p>';
		endif;
		?>
	</div>

	<div class="pagination">
		<?php the_posts_navigation(); ?>
	</div>
</div>

<?php get_footer(); ?>
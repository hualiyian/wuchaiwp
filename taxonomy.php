<?php
/**
 * Template Name: 通用分类归档模板
 * Description: 用于所有自定义文章类型分类的通用模板
 */
get_header(); ?>

<div class="container">
	<?php
	$taxonomy = get_query_var('taxonomy');
	$term = get_queried_object();
	$post_type = get_query_var('post_type') ?: 'post';
	
	// 获取当前分类的子分类
	$child_terms = array();
	if ($term && isset($term->term_id)) {
		$child_terms = get_terms(array(
			'taxonomy' => $taxonomy,
			'parent' => $term->term_id,
			'hide_empty' => false
		));
	}
	
	// 获取当前分类法的所有顶级分类
	$sibling_terms = get_terms(array(
		'taxonomy' => $taxonomy,
		'parent' => 0,
		'hide_empty' => false
	));
	?>

	<header class="archive-header">
		<h1 class="archive-title"><?php echo $term->name; ?></h1>
		<?php if (!empty($term->description)) : ?>
			<p class="archive-description"><?php echo $term->description; ?></p>
		<?php endif; ?>
	</header>

	<div class="taxonomy-content" id="taxonomyContent">
		<?php
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				$custom_url = get_post_meta(get_the_ID(), 'wuchai_custom_url', true);
				$post_link = !empty($custom_url) ? esc_url($custom_url) : get_permalink();
				$target_blank = !empty($custom_url) ? ' target="_blank" rel="noopener noreferrer"' : '';
				?>
				<article class="taxonomy-card">
					<div class="taxonomy-thumbnail">
						<a href="<?php echo $post_link; ?>"<?php echo $target_blank; ?>>
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('medium'); ?>
							<?php else : ?>
								<img src="https://via.placeholder.com/400x250" alt="默认缩略图">
							<?php endif; ?>
						</a>
						<?php if (taxonomy_exists($taxonomy)) : ?>
							<?php $terms = get_the_terms(get_the_ID(), $taxonomy); ?>
							<?php if (!empty($terms)) : ?>
								<span class="taxonomy-badge"><?php echo esc_html($terms[0]->name); ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div class="taxonomy-info">
						<h3 class="taxonomy-title"><a href="<?php echo $post_link; ?>"<?php echo $target_blank; ?>><?php the_title(); ?></a></h3>
						<p class="taxonomy-excerpt"><?php the_excerpt(); ?></p>
						<div class="taxonomy-meta">
							<span class="taxonomy-date"><?php the_date(); ?></span>
							<span class="taxonomy-views">👁️ <?php echo get_post_meta(get_the_ID(), 'wuchai_views', true) ?: '0'; ?></span>
							<span class="taxonomy-downloads">⬇️ <?php echo get_post_meta(get_the_ID(), 'wuchai_downloads', true) ?: '0'; ?></span>
						</div>
					</div>
				</article>
				<?php
			}
			wp_reset_postdata();
		} else {
			echo '<p class="text-center">暂无内容</p>';
		}
		?>
	</div>

	<div class="pagination">
		<?php the_posts_navigation(); ?>
	</div>
</div>

<?php get_footer(); ?>
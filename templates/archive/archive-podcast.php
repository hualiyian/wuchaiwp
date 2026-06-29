<?php
/**
 * Template Name: 播客归档页
 * Description: 播客节目列表页面模板
 */

get_header(); ?>

<div class="podcast-archive-container">
    <!-- 页面标题 -->
    <header class="archive-header">
        <h1 class="archive-title">🎧 播客频道</h1>
        <p class="archive-description">聆听精彩内容，探索声音世界</p>
    </header>

    <!-- 统计信息 -->
    <div class="archive-stats">
        <div class="stat-item">
            <span class="stat-value"><?php echo wp_count_posts('podcast')->publish; ?></span>
            <span class="stat-label">节目总数</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">📡</span>
            <span class="stat-label"><a href="<?php echo get_feed_link(); ?>">订阅 RSS</a></span>
        </div>
    </div>

    <!-- 分类筛选 -->
    <div class="filter-bar">
        <span class="filter-label">📁 分类：</span>
        <div class="filter-tags">
            <a href="<?php echo get_post_type_archive_link('podcast'); ?>" class="filter-tag active">全部</a>
            <?php
            $categories = get_categories(array('post_type' => 'podcast'));
            foreach ($categories as $cat) :
            ?>
            <a href="<?php echo get_category_link($cat->term_id); ?>" class="filter-tag">
                <?php echo $cat->name; ?> (<?php echo $cat->count; ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 播客列表 -->
    <div class="podcast-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="podcast-card">
                    <!-- 封面 -->
                    <div class="card-cover">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('class' => 'cover-img')); ?>
                            <?php else : ?>
                                <div class="default-cover">
                                    <span class="cover-icon">🎙️</span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <span class="card-duration"><?php echo get_post_meta(get_the_ID(), 'powerpress_duration', true) ?: '--'; ?></span>
                    </div>

                    <!-- 内容 -->
                    <div class="card-content">
                        <h3 class="card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        
                        <div class="card-meta">
                            <span class="meta-author">👤 <?php the_author(); ?></span>
                            <span class="meta-date">📅 <?php the_date('Y-m-d'); ?></span>
                        </div>

                        <!-- 迷你播放器 -->
                        <div class="mini-player">
                            <?php powerpress_audio_player(); ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>

            <!-- 分页 -->
            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '←',
                    'next_text' => '→',
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="no-podcasts">
                <span class="no-podcasts-icon">🎙️</span>
                <p>暂无播客节目</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.podcast-archive-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
}

.archive-header {
    text-align: center;
    margin-bottom: 30px;
}

.archive-title {
    font-size: 36px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.archive-description {
    font-size: 16px;
    color: #666;
}

.archive-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
    margin-bottom: 30px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 32px;
    font-weight: 700;
    color: #667eea;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

.stat-label a {
    color: #667eea;
    text-decoration: none;
}

.filter-bar {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.filter-label {
    font-weight: 500;
    color: #333;
}

.filter-tags {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-tag {
    padding: 6px 14px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    color: #333;
    transition: all 0.2s;
}

.filter-tag.active,
.filter-tag:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.podcast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
}

.podcast-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.podcast-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.card-cover {
    position: relative;
    overflow: hidden;
}

.cover-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.default-cover {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cover-icon {
    font-size: 48px;
}

.card-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    padding: 4px 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    border-radius: 4px;
    font-size: 12px;
}

.card-content {
    padding: 20px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.card-title a {
    color: #2c3e50;
    text-decoration: none;
}

.card-title a:hover {
    color: #667eea;
}

.card-excerpt {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin: 0 0 15px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #888;
    margin-bottom: 15px;
}

.mini-player {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.pagination {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: 30px;
}

.pagination .page-numbers {
    display: inline-block;
    padding: 10px 18px;
    margin: 0 4px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    color: #333;
    text-decoration: none;
}

.pagination .page-numbers.current {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.no-podcasts {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.no-podcasts-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.no-podcasts p {
    font-size: 16px;
    color: #666;
}

/* 响应式布局 */
@media (max-width: 768px) {
    .podcast-archive-container {
        padding: 15px;
    }
    
    .archive-title {
        font-size: 28px;
    }
    
    .archive-stats {
        gap: 30px;
    }
    
    .podcast-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .filter-bar {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<?php get_footer(); ?>
<?php
/**
 * Blog Tab Widget
 * 带tab切换的文章列表组件
 */
class Wuchaiwp_Blog_Tab_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_blog_tab',
            __('文章Tab组件', 'wuchaiwp'),
            array('description' => __('显示热门、推荐、热评、最新文章的tab切换组件', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '📰 文章');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $tab_order = isset($instance['tab_order']) ? explode(',', $instance['tab_order']) : array('hot', 'latest', 'comment', 'recommend');
        $number = isset($instance['number']) ? intval($instance['number']) : 5;
        
        $tabs = array(
            'hot' => array('name' => '🔥 热门', 'key' => 'hot'),
            'latest' => array('name' => '✨ 最新', 'key' => 'latest'),
            'comment' => array('name' => '💬 热评', 'key' => 'comment'),
            'recommend' => array('name' => '⭐ 推荐', 'key' => 'recommend')
        );
        ?>
        
        <!-- 固定宽度的Tab组件 -->
        <div class="blog-tab-widget" style="width: 100%; min-width: 250px; box-sizing: border-box;">
            <!-- Tab导航 -->
            <div class="tab-nav" style="display: flex; border-bottom: 1px solid #eee; background: #f8f9fa; justify-content: space-around;">
                <?php foreach ($tab_order as $tab_key) : ?>
                    <?php if (isset($tabs[$tab_key])) : ?>
                        <button class="tab-btn" data-tab="<?php echo $tab_key; ?>" 
                            style="width: 25%; min-width: 60px; padding: 10px 4px; background: none; border: none; cursor: pointer; font-size: 12px; color: #666; text-align: center; border-bottom: 2px solid transparent; transition: all 0.3s ease;">
                            <?php echo $tabs[$tab_key]['name']; ?>
                        </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Tab内容 -->
            <div class="tab-content" style="padding: 10px 0; width: 100%;">
                <?php foreach ($tab_order as $tab_key) : ?>
                    <?php if (isset($tabs[$tab_key])) : ?>
                        <div class="tab-panel" id="tab-<?php echo $tab_key; ?>" style="display: none;">
                            <ul class="tab-posts-list" style="list-style: none; padding: 0 15px; margin: 0; width: 100%;">
                                <?php $this->render_posts($tab_key, $number); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        
        <style>
            .blog-tab-widget .tab-btn.active {
                color: #3498db;
                border-bottom-color: #3498db;
                background: #fff;
            }
            .blog-tab-widget .tab-panel.active {
                display: block !important;
            }
            .blog-tab-widget .tab-posts-list li {
                padding: 8px 0;
                border-bottom: 1px dashed #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                min-width: 230px;
                box-sizing: border-box;
            }
            .blog-tab-widget .tab-posts-list li:last-child {
                border-bottom: none;
            }
            .blog-tab-widget .tab-posts-list li a {
                flex: 1;
                color: #333;
                text-decoration: none;
                font-size: 13px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: 160px;
                display: inline-block;
            }
            .blog-tab-widget .tab-posts-list li a:hover {
                color: #3498db;
            }
            .blog-tab-widget .tab-posts-list .post-meta {
                font-size: 12px;
                color: #999;
                margin-left: 10px;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .blog-tab-widget .tab-posts-list .empty-item {
                color: #999;
                text-align: center;
                padding: 20px;
            }
        </style>
        
        <?php
        echo $args['after_widget'];
    }
    
    private function render_posts($type, $number) {
        switch ($type) {
            case 'hot':
                $meta_key = function_exists('the_views') ? 'views' : 'wuchaiwp_views';
                $posts = new WP_Query(array(
                    'posts_per_page' => $number,
                    'meta_key' => $meta_key,
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC'
                ));
                break;
            case 'latest':
                $posts = new WP_Query(array(
                    'posts_per_page' => $number,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                break;
            case 'comment':
                $posts = new WP_Query(array(
                    'posts_per_page' => $number,
                    'orderby' => 'comment_count',
                    'order' => 'DESC'
                ));
                break;
            case 'recommend':
                $posts = new WP_Query(array(
                    'posts_per_page' => $number,
                    'meta_key' => 'wuchaiwp_blog_recommend',
                    'meta_value' => '1',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                break;
            default:
                $posts = new WP_Query(array('posts_per_page' => $number));
        }
        
        if ($posts->have_posts()) {
            while ($posts->have_posts()) : $posts->the_post();
                ?>
                <li>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <?php echo wp_trim_words(get_the_title(), 12, '...'); ?>
                    </a>
                    <span class="post-meta">
                        <?php 
                        if ($type == 'hot') {
                            $views = '';
                            if (function_exists('the_views')) {
                                ob_start();
                                the_views();
                                $views = ob_get_clean();
                            } else {
                                $views_count = get_post_meta(get_the_ID(), 'wuchaiwp_views', true);
                                $views = empty($views_count) ? '0' : (int)$views_count;
                            }
                            echo '👁️ ' . $views;
                        } elseif ($type == 'comment') {
                            echo '💬 ' . get_comments_number();
                        } else {
                            echo '📅 ' . get_the_date('m/d');
                        }
                        ?>
                    </span>
                </li>
                <?php
            endwhile;
            wp_reset_postdata();
        } else {
            echo '<li class="empty-item">暂无文章</li>';
        }
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '📰 文章';
        $number = isset($instance['number']) ? intval($instance['number']) : 5;
        $tab_order = isset($instance['tab_order']) ? $instance['tab_order'] : 'hot,latest,comment,recommend';
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" value="<?php echo esc_attr($number); ?>" min="1" max="10">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('tab_order'); ?>"><?php _e('Tab顺序:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('tab_order'); ?>" name="<?php echo $this->get_field_name('tab_order'); ?>" type="text" value="<?php echo esc_attr($tab_order); ?>" placeholder="hot,latest,comment,recommend">
            <small style="display:block;color:#666;margin-top:4px;">可用值: hot(热门), latest(最新), comment(热评), recommend(推荐)</small>
        </p>
        
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 5;
        $instance['tab_order'] = (!empty($new_instance['tab_order'])) ? strip_tags($new_instance['tab_order']) : 'hot,latest,comment,recommend';
        return $instance;
    }
}
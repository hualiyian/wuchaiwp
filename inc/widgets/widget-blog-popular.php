<?php
/**
 * Blog Popular Posts Widget
 * 显示热门文章
 */
class Wuchaiwp_Blog_Popular_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_blog_popular',
            __('热门文章', 'wuchaiwp'),
            array('description' => __('显示阅读量最高的文章', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '🔥 热门文章');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $number = isset($instance['number']) ? intval($instance['number']) : 5;
        
        // 优先使用 WP-PostViews 插件的 views meta key
        $views_meta_key = function_exists('the_views') ? 'views' : 'wuchaiwp_views';
        
        $popular_posts = new WP_Query(array(
            'posts_per_page' => $number,
            'meta_key' => $views_meta_key,
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ));
        
        if ($popular_posts->have_posts()) {
            ?>
            <ul class="popular-posts-list">
                <?php while ($popular_posts->have_posts()) : $popular_posts->the_post(); ?>
                    <?php 
                    // 优先使用 the_views 函数
                    $views = '';
                    if (function_exists('the_views')) {
                        ob_start();
                        the_views();
                        $views = ob_get_clean();
                    } else {
                        $views_count = get_post_meta(get_the_ID(), 'wuchaiwp_views', true);
                        $views = empty($views_count) ? '0' : (int)$views_count;
                    }
                    ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <span class="views">👁️ <?php echo $views; ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php
            wp_reset_postdata();
        } else {
            echo '<p class="empty-message">暂无热门文章</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '🔥 热门文章';
        $number = isset($instance['number']) ? intval($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" value="<?php echo esc_attr($number); ?>" min="1" max="20">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 5;
        return $instance;
    }
}
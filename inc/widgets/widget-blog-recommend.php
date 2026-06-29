<?php
/**
 * Blog Recommended Posts Widget
 * 显示推荐文章
 */
class Wuchaiwp_Blog_Recommend_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_blog_recommend',
            __('推荐文章', 'wuchaiwp'),
            array('description' => __('显示推荐的文章', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '⭐ 推荐文章');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $number = isset($instance['number']) ? intval($instance['number']) : 3;
        
        $recommend_posts = new WP_Query(array(
            'posts_per_page' => $number,
            'meta_key' => 'wuchaiwp_blog_recommend',
            'meta_value' => 1
        ));
        
        if ($recommend_posts->have_posts()) {
            ?>
            <ul class="recommend-posts-list">
                <?php while ($recommend_posts->have_posts()) : $recommend_posts->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endwhile; ?>
            </ul>
            <?php
            wp_reset_postdata();
        } else {
            echo '<p class="empty-message">暂无推荐文章</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '⭐ 推荐文章';
        $number = isset($instance['number']) ? intval($instance['number']) : 3;
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
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 3;
        return $instance;
    }
}
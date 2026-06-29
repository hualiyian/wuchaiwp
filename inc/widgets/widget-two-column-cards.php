<?php
/**
 * Two Column Cards Widget
 * 两列卡片式文章展示小工具 - 横向布局
 */
class Wuchaiwp_Two_Column_Cards_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_two_column_cards',
            __('两列卡片文章', 'wuchaiwp'),
            array('description' => __('以两列卡片形式展示文章，横向布局：左图右文', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '📚 相关文章');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $number = isset($instance['number']) ? intval($instance['number']) : 4;
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
        
        $posts = new WP_Query(array(
            'posts_per_page' => $number,
            'post_type' => $post_type,
            'ignore_sticky_posts' => true
        ));
        
        if ($posts->have_posts()) {
            ?>
            <div class="two-column-cards-horizontal">
                <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                    <article class="card-horizontal">
                        <div class="card-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('thumbnail', array('class' => 'card-img')); ?>
                                <?php else : ?>
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Crect fill='%23e0e0e0' width='100' height='100'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='20' x='50%25' y='50%25' text-anchor='middle' dominant-baseline='middle'%3E%3C/text%3E%3C/svg%3E" alt="默认缩略图" class="card-img">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <p class="card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
                            <div class="card-meta">
                                <span class="card-date"><?php the_date('Y/m/d'); ?></span>
                                <span class="card-views">👁️ <?php echo get_post_meta(get_the_ID(), 'wuchaiwp_views', true) ?: '0'; ?></span>
                                <span class="card-comments">💬 <?php echo comments_number('0', '1', '%'); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        } else {
            echo '<p class="empty-message">暂无文章</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '📚 相关文章';
        $number = isset($instance['number']) ? intval($instance['number']) : 4;
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
        
        $post_types = get_post_types(array('public' => true), 'objects');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" value="<?php echo esc_attr($number); ?>" min="2" max="10">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('文章类型:', 'wuchaiwp'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
                <?php foreach ($post_types as $pt) : ?>
                    <option value="<?php echo esc_attr($pt->name); ?>" <?php selected($post_type, $pt->name); ?>><?php echo esc_html($pt->labels->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 4;
        $instance['post_type'] = (!empty($new_instance['post_type'])) ? strip_tags($new_instance['post_type']) : 'post';
        return $instance;
    }
}
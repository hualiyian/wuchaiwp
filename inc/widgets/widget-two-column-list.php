<?php
/**
 * Two Column List Widget
 * 两列列表式文章展示小工具
 */
class Wuchaiwp_Two_Column_List_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_two_column_list',
            __('两列列表文章', 'wuchaiwp'),
            array('description' => __('以两列列表形式展示文章，移动端自动变为一列', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '📝 最新文章');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $number = isset($instance['number']) ? intval($instance['number']) : 6;
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
        
        $posts = new WP_Query(array(
            'posts_per_page' => $number,
            'post_type' => $post_type,
            'ignore_sticky_posts' => true
        ));
        
        if ($posts->have_posts()) {
            $posts_array = array();
            while ($posts->have_posts()) {
                $posts->the_post();
                $posts_array[] = array(
                    'title' => get_the_title(),
                    'link' => get_permalink(),
                    'date' => get_the_date('Y/m/d'),  // 修改：显示年份
                    'views' => get_post_meta(get_the_ID(), 'wuchaiwp_views', true) ?: '0'
                );
            }
            wp_reset_postdata();
            
            $half = ceil(count($posts_array) / 2);
            $column1 = array_slice($posts_array, 0, $half);
            $column2 = array_slice($posts_array, $half);
            ?>
            <div class="two-column-list">
                <ul class="list-column">
                    <?php foreach ($column1 as $post) : ?>
                        <li>
                            <a href="<?php echo esc_url($post['link']); ?>"><?php echo esc_html($post['title']); ?></a>
                            <span class="list-meta">
                                <span><?php echo esc_html($post['date']); ?></span>
                                <span>👁️ <?php echo esc_html($post['views']); ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <ul class="list-column">
                    <?php foreach ($column2 as $post) : ?>
                        <li>
                            <a href="<?php echo esc_url($post['link']); ?>"><?php echo esc_html($post['title']); ?></a>
                            <span class="list-meta">
                                <span><?php echo esc_html($post['date']); ?></span>
                                <span>👁️ <?php echo esc_html($post['views']); ?></span>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        } else {
            echo '<p class="empty-message">暂无文章</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '📝 最新文章';
        $number = isset($instance['number']) ? intval($instance['number']) : 6;
        $post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
        
        $post_types = get_post_types(array('public' => true), 'objects');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" value="<?php echo esc_attr($number); ?>" min="2" max="20">
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
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 6;
        $instance['post_type'] = (!empty($new_instance['post_type'])) ? strip_tags($new_instance['post_type']) : 'post';
        return $instance;
    }
}
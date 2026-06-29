<?php
/**
 * Blog Categories Widget
 * 显示文章分类
 */
class Wuchaiwp_Blog_Categories_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_blog_categories',
            __('博客分类', 'wuchaiwp'),
            array('description' => __('显示文章分类列表', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '📁 文章分类');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $orderby = isset($instance['orderby']) ? $instance['orderby'] : 'name';
        $order = isset($instance['order']) ? $instance['order'] : 'ASC';
        $hide_empty = isset($instance['hide_empty']) ? (bool)$instance['hide_empty'] : false;
        
        $categories = get_categories(array(
            'orderby' => $orderby,
            'order' => $order,
            'hide_empty' => $hide_empty
        ));
        
        if (!empty($categories)) {
            ?>
            <div class="categories-tags">
                <?php foreach ($categories as $category) : ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="category-tag">
                        <?php echo esc_html($category->name); ?>
                        <span class="count">(<?php echo $category->count; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php
        } else {
            echo '<p class="empty-message">暂无分类</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '📁 文章分类';
        $orderby = isset($instance['orderby']) ? $instance['orderby'] : 'name';
        $order = isset($instance['order']) ? $instance['order'] : 'ASC';
        $hide_empty = isset($instance['hide_empty']) ? (bool)$instance['hide_empty'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('排序方式:', 'wuchaiwp'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
                <option value="name" <?php selected($orderby, 'name'); ?>><?php _e('名称', 'wuchaiwp'); ?></option>
                <option value="count" <?php selected($orderby, 'count'); ?>><?php _e('数量', 'wuchaiwp'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('排序方向:', 'wuchaiwp'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
                <option value="ASC" <?php selected($order, 'ASC'); ?>><?php _e('升序', 'wuchaiwp'); ?></option>
                <option value="DESC" <?php selected($order, 'DESC'); ?>><?php _e('降序', 'wuchaiwp'); ?></option>
            </select>
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty'); ?>" value="1" <?php checked($hide_empty); ?>>
            <label for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php _e('隐藏空分类', 'wuchaiwp'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['orderby'] = (!empty($new_instance['orderby'])) ? strip_tags($new_instance['orderby']) : 'name';
        $instance['order'] = (!empty($new_instance['order'])) ? strip_tags($new_instance['order']) : 'ASC';
        $instance['hide_empty'] = isset($new_instance['hide_empty']) ? 1 : 0;
        return $instance;
    }
}
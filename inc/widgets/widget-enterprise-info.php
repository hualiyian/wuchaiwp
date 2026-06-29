<?php
/**
 * Enterprise Info Widget
 * 显示企业信息
 */
class Wuchaiwp_Enterprise_Info_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_enterprise_info',
            __('企业信息', 'wuchaiwp'),
            array('description' => __('显示企业基本信息', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '🏢 关于我们');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $description = isset($instance['description']) ? $instance['description'] : get_bloginfo('description');
        $link_text = isset($instance['link_text']) ? $instance['link_text'] : '了解更多 →';
        $link_url = isset($instance['link_url']) ? $instance['link_url'] : '#about';
        
        ?>
        <p><?php echo esc_html($description ?: '暂无企业介绍'); ?></p>
        <a href="<?php echo esc_url($link_url); ?>" class="widget-link"><?php echo esc_html($link_text); ?></a>
        <?php
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '🏢 关于我们';
        $description = isset($instance['description']) ? $instance['description'] : get_bloginfo('description');
        $link_text = isset($instance['link_text']) ? $instance['link_text'] : '了解更多 →';
        $link_url = isset($instance['link_url']) ? $instance['link_url'] : '#about';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('企业描述:', 'wuchaiwp'); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="3"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('链接文字:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('link_url'); ?>"><?php _e('链接地址:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="text" value="<?php echo esc_attr($link_url); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? strip_tags($new_instance['description']) : '';
        $instance['link_text'] = (!empty($new_instance['link_text'])) ? strip_tags($new_instance['link_text']) : '';
        $instance['link_url'] = (!empty($new_instance['link_url'])) ? esc_url_raw($new_instance['link_url']) : '';
        return $instance;
    }
}
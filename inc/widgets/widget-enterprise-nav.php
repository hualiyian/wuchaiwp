<?php
/**
 * Enterprise Quick Nav Widget
 * 快速导航小工具
 */
class Wuchaiwp_Enterprise_Nav_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_enterprise_nav',
            __('快速导航', 'wuchaiwp'),
            array('description' => __('显示企业快速导航链接', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '📌 快速导航');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // 获取导航链接
        $links = array();
        for ($i = 1; $i <= 5; $i++) {
            $link_text = isset($instance['link_text_' . $i]) ? $instance['link_text_' . $i] : '';
            $link_url = isset($instance['link_url_' . $i]) ? $instance['link_url_' . $i] : '';
            if (!empty($link_text) && !empty($link_url)) {
                $links[] = array('text' => $link_text, 'url' => $link_url);
            }
        }
        
        if (!empty($links)) {
            ?>
            <ul class="quick-nav">
                <?php foreach ($links as $link) : ?>
                <li><a href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['text']); ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php
        }
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '📌 快速导航';
        
        // 默认链接
        $defaults = array(
            'link_text_1' => '产品介绍',
            'link_url_1' => '#products',
            'link_text_2' => '开发日志',
            'link_url_2' => '#news',
            'link_text_3' => '案例参考',
            'link_url_3' => '#cases',
            'link_text_4' => '联系我们',
            'link_url_4' => '#contact',
        );
        
        $instance = wp_parse_args($instance, $defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <?php for ($i = 1; $i <= 5; $i++) : ?>
        <hr style="margin: 10px 0;">
        <p>
            <label for="<?php echo $this->get_field_id('link_text_' . $i); ?>"><?php _e('链接 ' . $i . ' 文字:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('link_text_' . $i); ?>" name="<?php echo $this->get_field_name('link_text_' . $i); ?>" type="text" value="<?php echo esc_attr($instance['link_text_' . $i]); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('link_url_' . $i); ?>"><?php _e('链接 ' . $i . ' 地址:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('link_url_' . $i); ?>" name="<?php echo $this->get_field_name('link_url_' . $i); ?>" type="text" value="<?php echo esc_attr($instance['link_url_' . $i]); ?>">
        </p>
        <?php endfor; ?>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        
        for ($i = 1; $i <= 5; $i++) {
            $instance['link_text_' . $i] = (!empty($new_instance['link_text_' . $i])) ? strip_tags($new_instance['link_text_' . $i]) : '';
            $instance['link_url_' . $i] = (!empty($new_instance['link_url_' . $i])) ? esc_url_raw($new_instance['link_url_' . $i]) : '';
        }
        
        return $instance;
    }
}
?>
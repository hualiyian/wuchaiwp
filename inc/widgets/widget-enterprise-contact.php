<?php
/**
 * Enterprise Contact Widget
 * 联系方式小工具
 */
class Wuchaiwp_Enterprise_Contact_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_enterprise_contact',
            __('联系方式', 'wuchaiwp'),
            array('description' => __('显示企业联系方式', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '📞 联系我们');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $address = isset($instance['address']) ? $instance['address'] : '';
        $phone = isset($instance['phone']) ? $instance['phone'] : '';
        $email = isset($instance['email']) ? $instance['email'] : '';
        $custom_color = isset($instance['custom_color']) ? $instance['custom_color'] : '#667eea';
        
        ?>
        <div class="contact-info" style="background: transparent; padding: 15px; border-radius: 8px;">
            <?php if (!empty($address)) : ?>
            <p style="margin: 8px 0;"><span style="margin-right: 8px;">📍</span><?php echo esc_html($address); ?></p>
            <?php endif; ?>
            <?php if (!empty($phone)) : ?>
            <p style="margin: 8px 0;"><span style="margin-right: 8px;">📞</span><?php echo esc_html($phone); ?></p>
            <?php endif; ?>
            <?php if (!empty($email)) : ?>
            <p style="margin: 8px 0;"><span style="margin-right: 8px;">✉️</span><a href="mailto:<?php echo antispambot($email); ?>" style="text-decoration: none;"><?php echo antispambot($email); ?></a></p>
            <?php endif; ?>
        </div>
        <?php
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '📞 联系我们';
        $address = isset($instance['address']) ? $instance['address'] : '北京市朝阳区科技园区A座18层';
        $phone = isset($instance['phone']) ? $instance['phone'] : '400-888-8888';
        $email = isset($instance['email']) ? $instance['email'] : 'contact@example.com';
        $custom_color = isset($instance['custom_color']) ? $instance['custom_color'] : '#667eea';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('address'); ?>"><?php _e('公司地址:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" type="text" value="<?php echo esc_attr($address); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e('联系电话:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="text" value="<?php echo esc_attr($phone); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('电子邮箱:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo esc_attr($email); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('custom_color'); ?>"><?php _e('背景渐变色:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('custom_color'); ?>" name="<?php echo $this->get_field_name('custom_color'); ?>" type="color" value="<?php echo esc_attr($custom_color); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['address'] = (!empty($new_instance['address'])) ? strip_tags($new_instance['address']) : '';
        $instance['phone'] = (!empty($new_instance['phone'])) ? strip_tags($new_instance['phone']) : '';
        $instance['email'] = (!empty($new_instance['email'])) ? sanitize_email($new_instance['email']) : '';
        $instance['custom_color'] = (!empty($new_instance['custom_color'])) ? sanitize_hex_color($new_instance['custom_color']) : '#667eea';
        return $instance;
    }
}
<?php
/**
 * Blog Author Widget
 * 显示文章作者信息
 */
class Wuchaiwp_Blog_Author_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wuchaiwp_blog_author',
            __('博客作者', 'wuchaiwp'),
            array('description' => __('显示文章作者信息', 'wuchaiwp'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '👤 关于作者');
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        global $post;
        if ($post) {
            $post_author_id = get_post_field('post_author', $post->ID);
            $author_info = get_userdata($post_author_id);
            
            if ($author_info) {
                $author_name = get_post_meta($post->ID, 'wuchaiwp_blog_author_name', true);
                $author_avatar = get_post_meta($post->ID, 'wuchaiwp_blog_author_avatar', true);
                $author_bio = get_post_meta($post->ID, 'wuchaiwp_blog_author_bio', true);
                
                $display_name = !empty($author_name) ? $author_name : $author_info->display_name;
                $avatar_url = !empty($author_avatar) ? $author_avatar : get_avatar_url($post_author_id);
                $bio_text = !empty($author_bio) ? $author_bio : get_the_author_meta('description', $post_author_id);
                $author_space_url = home_url('/author-space/' . $author_info->user_nicename);
                
                ?>
                <div class="author-profile-widget text-center">
                    <a href="<?php echo esc_url($author_space_url); ?>" title="访问作者空间">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php //echo esc_attr($display_name); ?>" class="profile-avatar">
                    </a>
                    <h4><a href="<?php echo esc_url($author_space_url); ?>"><?php echo esc_html($display_name); ?></a></h4>
                    <p><?php echo esc_html($bio_text ?: '暂无简介'); ?></p>
                    <a href="<?php echo esc_url($author_space_url); ?>" class="author-space-link">
                        <span class="link-icon">🚀</span>访问作者空间
                    </a>
                </div>
                <?php
            }
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '👤 关于作者';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'wuchaiwp'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}
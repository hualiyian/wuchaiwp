<?php
/**
 * 版权说明设置
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Copyright_Settings {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        // 版权设置
        register_setting('wuchaiwp_copyright_settings', 'wuchaiwp_copyright_text', array('sanitize_callback' => 'wp_kses_post'));
    }

    public function render_copyright_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('您没有权限访问此页面。'));
        }
        
        if (isset($_POST['wuchaiwp_copyright_submit']) && check_admin_referer('wuchaiwp_copyright_nonce')) {
            $copyright_text = isset($_POST['wuchaiwp_copyright_text']) ? wp_kses_post($_POST['wuchaiwp_copyright_text']) : '';
            update_option('wuchaiwp_copyright_text', $copyright_text);
            echo '<div class="updated"><p>设置已保存！</p></div>';
        }
        
        $copyright_text = get_option('wuchaiwp_copyright_text', '');
        ?>
        <div class="wrap">
            <h1>版权说明设置</h1>
            <form method="post" action="">
                <?php wp_nonce_field('wuchaiwp_copyright_nonce'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">网站版权说明</th>
                        <td>
                            <textarea name="wuchaiwp_copyright_text" rows="10" cols="50" class="large-text"><?php echo esc_textarea($copyright_text); ?></textarea>
                            <p class="description">在此输入网站的版权说明内容，将显示在文章详情页的版权区域底部。</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="wuchaiwp_copyright_submit" class="button-primary" value="保存设置">
                </p>
            </form>
        </div>
        <?php
    }
}

// 初始化
new Wuchaiwp_Copyright_Settings();

/**
 * 渲染版权说明设置页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_copyright_settings() {
    $settings = new Wuchaiwp_Copyright_Settings();
    $settings->render_copyright_settings();
}
<?php
/**
 * 打赏设置
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Donate_Settings {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function enqueue_scripts($hook) {
        // 只在打赏设置页面加载媒体库脚本
        if ($hook === 'toplevel_page_wuchaiwp-settings' || strpos($hook, 'wuchaiwp-donate-settings') !== false) {
            wp_enqueue_media();
        }
    }

    public function register_settings() {
        // 打赏设置
        register_setting('wuchaiwp_donate_settings', 'wuchaiwp_donate_wechat', array('sanitize_callback' => 'esc_url_raw'));
        register_setting('wuchaiwp_donate_settings', 'wuchaiwp_donate_alipay', array('sanitize_callback' => 'esc_url_raw'));
        register_setting('wuchaiwp_donate_settings', 'wuchaiwp_donate_title', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('wuchaiwp_donate_settings', 'wuchaiwp_donate_desc', array('sanitize_callback' => 'wp_kses_post'));
    }

    public function render_donate_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('您没有权限访问此页面。'));
        }
        
        if (isset($_POST['wuchaiwp_donate_submit']) && check_admin_referer('wuchaiwp_donate_nonce')) {
            update_option('wuchaiwp_donate_wechat', esc_url_raw($_POST['wuchaiwp_donate_wechat']));
            update_option('wuchaiwp_donate_alipay', esc_url_raw($_POST['wuchaiwp_donate_alipay']));
            update_option('wuchaiwp_donate_title', sanitize_text_field($_POST['wuchaiwp_donate_title']));
            update_option('wuchaiwp_donate_desc', wp_kses_post($_POST['wuchaiwp_donate_desc']));
            echo '<div class="updated"><p>设置已保存！</p></div>';
        }
        
        $donate_wechat = get_option('wuchaiwp_donate_wechat', '');
        $donate_alipay = get_option('wuchaiwp_donate_alipay', '');
        $donate_title = get_option('wuchaiwp_donate_title', '');
        $donate_desc = get_option('wuchaiwp_donate_desc', '');
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>💰 打赏设置</h1>
                <p class="description">设置文章打赏功能，支持微信和支付宝二维码</p>
            </div>
            
            <div class="wuchaiwp-form">
                <form method="post" action="">
                    <?php wp_nonce_field('wuchaiwp_donate_nonce'); ?>
                    
                    <div class="form-section">
                        <h3>📝 打赏弹窗设置</h3>
                        
                        <div class="form-row">
                            <label>弹窗标题</label>
                            <input type="text" name="wuchaiwp_donate_title" value="<?php echo esc_attr($donate_title); ?>" class="large-text">
                            <p class="help">打赏弹窗显示的标题，默认为「💝 支持作者」</p>
                        </div>
                        
                        <div class="form-row">
                            <label>弹窗描述</label>
                            <textarea name="wuchaiwp_donate_desc" rows="3" cols="50" class="large-text"><?php echo esc_textarea($donate_desc); ?></textarea>
                            <p class="help">打赏弹窗显示的描述文字，默认为「如果这篇文章对你有帮助，欢迎打赏支持！」</p>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>💬 微信打赏</h3>
                        
                        <div class="form-row">
                            <label>微信收款码图片</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" name="wuchaiwp_donate_wechat" id="wuchaiwp_donate_wechat" value="<?php echo esc_url($donate_wechat); ?>" class="large-text" style="flex: 1;">
                                <button type="button" class="button button-secondary" id="upload_wechat_btn">选择图片</button>
                            </div>
                            <p class="help">点击「选择图片」按钮从媒体库上传或选择图片。推荐尺寸：150×150像素</p>
                        </div>
                        
                        <?php if (!empty($donate_wechat)) : ?>
                        <div class="form-row">
                            <label>预览</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <img src="<?php echo esc_url($donate_wechat); ?>" alt="微信收款码" style="max-width: 150px; border: 1px solid #eee; padding: 5px; border-radius: 8px;">
                                <button type="button" class="button button-link" onclick="document.getElementById('wuchaiwp_donate_wechat').value = ''; this.parentElement.remove();">移除图片</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-section">
                        <h3>📱 支付宝打赏</h3>
                        
                        <div class="form-row">
                            <label>支付宝收款码图片</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" name="wuchaiwp_donate_alipay" id="wuchaiwp_donate_alipay" value="<?php echo esc_url($donate_alipay); ?>" class="large-text" style="flex: 1;">
                                <button type="button" class="button button-secondary" id="upload_alipay_btn">选择图片</button>
                            </div>
                            <p class="help">点击「选择图片」按钮从媒体库上传或选择图片。推荐尺寸：150×150像素</p>
                        </div>
                        
                        <?php if (!empty($donate_alipay)) : ?>
                        <div class="form-row">
                            <label>预览</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <img src="<?php echo esc_url($donate_alipay); ?>" alt="支付宝收款码" style="max-width: 150px; border: 1px solid #eee; padding: 5px; border-radius: 8px;">
                                <button type="button" class="button button-link" onclick="document.getElementById('wuchaiwp_donate_alipay').value = ''; this.parentElement.remove();">移除图片</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-section">
                        <h3>💡 使用说明</h3>
                        <ul style="padding-left: 20px;">
                            <li>1. 打开微信或支付宝，进入「收款码」页面</li>
                            <li>2. 保存收款码图片到本地</li>
                            <li>3. 在WordPress媒体库中上传该图片</li>
                            <li>4. 复制图片URL并粘贴到对应输入框中</li>
                            <li>5. 保存设置后，打赏功能即可生效</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <input type="submit" name="wuchaiwp_donate_submit" class="button-primary" value="保存设置">
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // 微信收款码上传
                $('#upload_wechat_btn').click(function(e) {
                    e.preventDefault();
                    
                    var mediaUploader = wp.media({
                        title: '选择微信收款码',
                        button: { text: '选择图片' },
                        multiple: false
                    });
                    
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#wuchaiwp_donate_wechat').val(attachment.url);
                    });
                    
                    mediaUploader.open();
                });
                
                // 支付宝收款码上传
                $('#upload_alipay_btn').click(function(e) {
                    e.preventDefault();
                    
                    var mediaUploader = wp.media({
                        title: '选择支付宝收款码',
                        button: { text: '选择图片' },
                        multiple: false
                    });
                    
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#wuchaiwp_donate_alipay').val(attachment.url);
                    });
                    
                    mediaUploader.open();
                });
            });
        </script>
        <?php
    }
}

// 初始化
new Wuchaiwp_Donate_Settings();

/**
 * 渲染打赏设置页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_donate_settings() {
    $settings = new Wuchaiwp_Donate_Settings();
    $settings->render_donate_settings();
}
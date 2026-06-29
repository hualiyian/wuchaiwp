<?php
/**
 * 样式预设管理
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Style_Presets {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        // 样式预设
        register_setting('wuchaiwp_preset_settings', 'wuchaiwp_style_presets', array('sanitize_callback' => array($this, 'sanitize_presets')));
    }

    public function sanitize_presets($value) {
        if (is_array($value)) {
            foreach ($value as $key => $preset) {
                if (!isset($preset['name']) || empty($preset['name'])) {
                    unset($value[$key]);
                }
            }
        }
        return $value;
    }

    public function render_style_presets() {
        $presets = get_option('wuchaiwp_style_presets', array());
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>样式预设管理</h1>
                <p class="description">保存和管理您的样式预设，快速切换网站外观</p>
            </div>

            <div class="presets-container">
                <div class="presets-list">
                    <h3>已保存的预设</h3>
                    
                    <?php if (empty($presets)) : ?>
                        <p class="empty-message">暂无保存的预设，您可以在外观设置中创建预设</p>
                    <?php else : ?>
                        <div class="presets-grid">
                            <?php foreach ($presets as $key => $preset) : ?>
                                <div class="preset-card" data-preset-key="<?php echo esc_attr($key); ?>">
                                    <div class="preset-header" style="background: <?php echo $preset['theme_color']; ?>">
                                        <span class="preset-name"><?php echo esc_html($preset['name']); ?></span>
                                    </div>
                                    <div class="preset-colors">
                                        <div class="color-swatch" style="background: <?php echo $preset['theme_color']; ?>;" title="主题色"></div>
                                        <div class="color-swatch" style="background: <?php echo $preset['secondary_color']; ?>;" title="次要色"></div>
                                        <div class="color-swatch" style="background: <?php echo $preset['background_color']; ?>;" title="背景色"></div>
                                        <div class="color-swatch" style="background: <?php echo $preset['text_color']; ?>;" title="文字色"></div>
                                    </div>
                                    <div class="preset-actions">
                                        <button class="preset-action load-preset" data-preset-key="<?php echo esc_attr($key); ?>">🎯 应用</button>
                                        <button class="preset-action delete-preset" data-preset-key="<?php echo esc_attr($key); ?>">🗑️ 删除</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="presets-info">
                    <h3>💡 使用说明</h3>
                    <ul>
                        <li>在外观设置页面点击"保存为预设"可以保存当前样式</li>
                        <li>点击预设卡片上的"应用"按钮可以快速切换样式</li>
                        <li>预设会保存所有外观设置，包括颜色、字体和尺寸</li>
                        <li>删除预设不会影响当前网站的样式</li>
                    </ul>

                    <h3>🎨 内置预设</h3>
                    <div class="built-in-presets">
                        <div class="preset-card" data-preset-key="default">
                            <div class="preset-header" style="background: #3498db;">
                                <span class="preset-name">默认主题</span>
                            </div>
                            <div class="preset-colors">
                                <div class="color-swatch" style="background: #3498db;"></div>
                                <div class="color-swatch" style="background: #9b59b6;"></div>
                                <div class="color-swatch" style="background: #f5f5f5;"></div>
                                <div class="color-swatch" style="background: #333333;"></div>
                            </div>
                            <div class="preset-actions">
                                <button class="preset-action load-preset" data-preset-key="default">🎯 应用默认</button>
                            </div>
                        </div>
                        
                        <div class="preset-card" data-preset-key="dark">
                            <div class="preset-header" style="background: #2c3e50;">
                                <span class="preset-name">深色主题</span>
                            </div>
                            <div class="preset-colors">
                                <div class="color-swatch" style="background: #34495e;"></div>
                                <div class="color-swatch" style="background: #1abc9c;"></div>
                                <div class="color-swatch" style="background: #1a1a2e;"></div>
                                <div class="color-swatch" style="background: #ecf0f1;"></div>
                            </div>
                            <div class="preset-actions">
                                <button class="preset-action load-preset" data-preset-key="dark">🎯 应用深色</button>
                            </div>
                        </div>

                        <div class="preset-card" data-preset-key="warm">
                            <div class="preset-header" style="background: #e67e22;">
                                <span class="preset-name">暖色主题</span>
                            </div>
                            <div class="preset-colors">
                                <div class="color-swatch" style="background: #e67e22;"></div>
                                <div class="color-swatch" style="background: #d35400;"></div>
                                <div class="color-swatch" style="background: #fdf6e3;"></div>
                                <div class="color-swatch" style="background: #4a4a4a;"></div>
                            </div>
                            <div class="preset-actions">
                                <button class="preset-action load-preset" data-preset-key="warm">🎯 应用暖色</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

// 初始化
new Wuchaiwp_Style_Presets();

/**
 * 渲染样式预设页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_style_presets() {
    $settings = new Wuchaiwp_Style_Presets();
    $settings->render_style_presets();
}
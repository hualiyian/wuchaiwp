<?php
/**
 * 多区域首页设置
 *
 * @package wuchaiwp
 */

class Wuchaiwp_Multi_Frontpage_Settings {

    public function __construct() {
        if (!current_user_can('administrator')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        // 多区域首页设置
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_latest_posts_count', array('sanitize_callback' => 'absint', 'default' => 4));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_hot_posts_count', array('sanitize_callback' => 'absint', 'default' => 4));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_category_count', array('sanitize_callback' => 'absint', 'default' => 2));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_category_posts_count', array('sanitize_callback' => 'absint', 'default' => 3));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_featured_posts_count', array('sanitize_callback' => 'absint', 'default' => 6));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_featured_source', array('sanitize_callback' => array($this, 'sanitize_featured_source'), 'default' => 'sticky'));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_featured_posts', array('sanitize_callback' => array($this, 'sanitize_post_ids'), 'default' => array()));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_post_types', array('sanitize_callback' => array($this, 'sanitize_post_types'), 'default' => array('post')));
        // 各区域文章类型设置
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_section_post_types', array('sanitize_callback' => array($this, 'sanitize_section_post_types'), 'default' => array()));
        // 自定义文章类型显示设置
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_cpt_display', array('sanitize_callback' => array($this, 'sanitize_cpt_display'), 'default' => array()));
        register_setting('wuchaiwp_multi_frontpage_settings', 'wuchaiwp_multi_cpt_covers', array('sanitize_callback' => array($this, 'sanitize_cpt_covers'), 'default' => array()));
    }

    public function sanitize_post_types($value) {
        if (!is_array($value)) {
            return array('post');
        }
        return array_map('sanitize_text_field', $value);
    }

    public function sanitize_featured_source($value) {
        $allowed = array('sticky', 'featured', 'selected', 'latest');
        return in_array($value, $allowed) ? $value : 'sticky';
    }

    public function sanitize_post_ids($value) {
        if (!is_array($value)) {
            return array();
        }
        return array_map('absint', $value);
    }

    public function sanitize_cpt_display($value) {
        if (!is_array($value)) {
            return array();
        }
        return array_map('sanitize_text_field', $value);
    }

    public function sanitize_section_post_types($value) {
        if (!is_array($value)) {
            return array();
        }
        $sanitized = array();
        foreach ($value as $section_id => $post_types) {
            if (is_array($post_types)) {
                $sanitized[$section_id] = array_map('sanitize_text_field', $post_types);
            }
        }
        return $sanitized;
    }

    public function sanitize_cpt_covers($value) {
        if (!is_array($value)) {
            return array();
        }
        $sanitized = array();
        foreach ($value as $cpt => $image_id) {
            $sanitized[$cpt] = absint($image_id);
        }
        return $sanitized;
    }

    public function render_multi_frontpage_settings() {
        ?>
        <div class="wrap wuchaiwp-admin">
            <div class="wuchaiwp-header">
                <h1>多区域首页设置</h1>
                <p class="description">设置多区域首页各区块的显示内容</p>
            </div>
            
            <form method="post" action="options.php" class="wuchaiwp-form">
                <?php settings_fields('wuchaiwp_multi_frontpage_settings'); ?>
                
                <div class="form-section">
                    <h3>📊 文章数量设置</h3>
                    
                    <div class="form-row">
                        <label>最新发布数量</label>
                        <input type="number" name="wuchaiwp_multi_latest_posts_count" value="<?php echo esc_attr(get_option('wuchaiwp_multi_latest_posts_count', 4)); ?>" min="1" max="20">
                        <p class="help">设置最新发布区块显示的文章数量</p>
                    </div>
                    
                    <div class="form-row">
                        <label>热门推荐数量</label>
                        <input type="number" name="wuchaiwp_multi_hot_posts_count" value="<?php echo esc_attr(get_option('wuchaiwp_multi_hot_posts_count', 4)); ?>" min="1" max="20">
                        <p class="help">设置热门推荐区块显示的文章数量</p>
                    </div>
                    
                    <div class="form-row">
                        <label>分类区块数量</label>
                        <input type="number" name="wuchaiwp_multi_category_count" value="<?php echo esc_attr(get_option('wuchaiwp_multi_category_count', 2)); ?>" min="1" max="5">
                        <p class="help">设置显示的分类区块数量</p>
                    </div>
                    
                    <div class="form-row">
                        <label>每个分类文章数量</label>
                        <input type="number" name="wuchaiwp_multi_category_posts_count" value="<?php echo esc_attr(get_option('wuchaiwp_multi_category_posts_count', 3)); ?>" min="1" max="10">
                        <p class="help">每个分类区块显示的文章数量</p>
                    </div>
                    
                    <div class="form-row">
                        <label>专题推荐数量</label>
                        <input type="number" name="wuchaiwp_multi_featured_posts_count" value="<?php echo esc_attr(get_option('wuchaiwp_multi_featured_posts_count', 6)); ?>" min="1" max="12">
                        <p class="help">设置专题推荐区块显示的文章数量</p>
                    </div>
                </div>

                <div class="form-section">
                    <h3>📄 文章类型设置</h3>
                    
                    <div class="form-row">
                        <label>全局显示的文章类型（所有区域默认使用）</label>
                        <div class="checkbox-group">
                            <?php
                            $selected_post_types = get_option('wuchaiwp_multi_post_types', array('post'));
                            $post_types = get_post_types(array('public' => true), 'objects');
                            foreach ($post_types as $post_type) :
                                if ($post_type->name === 'attachment') continue;
                            ?>
                                <label><input type="checkbox" name="wuchaiwp_multi_post_types[]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, $selected_post_types)); ?>> <?php echo esc_html($post_type->labels->name); ?></label>
                            <?php endforeach; ?>
                        </div>
                        <p class="help">选择要在首页显示的文章类型，可多选。此设置为全局默认值，各区域可单独覆盖</p>
                    </div>
                </div>

                <div class="form-section">
                    <h3>📍 各区域文章类型筛选</h3>
                    <p class="description">为首页不同区域设置独立的文章类型筛选，不设置则使用全局默认值</p>
                    
                    <?php
                    // 定义首页各区域
                    $frontpage_sections = array(
                        'latest' => array('name' => '🔹 最新发布', 'description' => '首页顶部最新文章展示区'),
                        'hot' => array('name' => '🔥 热门推荐', 'description' => '热门文章排行区'),
                        'category' => array('name' => '📁 分类区块', 'description' => '分类文章展示区'),
                        'featured' => array('name' => '⭐ 专题推荐', 'description' => '专题文章推荐区'),
                        'cpt' => array('name' => '📚 自定义文章类型', 'description' => '自定义文章类型专题区'),
                    );
                    
                    // 获取各区域的文章类型设置
                    $section_post_types = get_option('wuchaiwp_multi_section_post_types', array());
                    
                    foreach ($frontpage_sections as $section_id => $section_info) :
                        $section_selected = isset($section_post_types[$section_id]) ? $section_post_types[$section_id] : array();
                    ?>
                    <div style="border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 15px; background: #fafafa;">
                        <h4><?php echo esc_html($section_info['name']); ?></h4>
                        <p style="font-size: 12px; color: #666; margin-bottom: 10px;"><?php echo esc_html($section_info['description']); ?></p>
                        
                        <div class="checkbox-group" style="flex-wrap: wrap; gap: 10px;">
                            <?php foreach ($post_types as $post_type) :
                                if ($post_type->name === 'attachment') continue;
                            ?>
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="checkbox" name="wuchaiwp_multi_section_post_types[<?php echo esc_attr($section_id); ?>][]" 
                                           value="<?php echo esc_attr($post_type->name); ?>" 
                                           <?php checked(in_array($post_type->name, $section_selected)); ?>>
                                    <?php echo esc_html($post_type->labels->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="help" style="font-size: 11px; margin-top: 10px;">
                            <strong>提示：</strong>不选择任何文章类型则继承全局设置；选择后将覆盖全局设置，仅显示选中的文章类型
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-section">
                    <h3>📚 自定义文章类型专题设置</h3>
                    
                    <div class="form-row">
                        <label>显示的自定义文章类型</label>
                        <div class="checkbox-group">
                            <?php
                            $selected_cpt = get_option('wuchaiwp_multi_cpt_display', array());
                            $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
                            if (empty($custom_post_types)) :
                            ?>
                                <p style="color: #999;">暂无自定义文章类型</p>
                            <?php else :
                                foreach ($custom_post_types as $cpt) :
                            ?>
                                <label><input type="checkbox" name="wuchaiwp_multi_cpt_display[]" value="<?php echo esc_attr($cpt->name); ?>" <?php checked(in_array($cpt->name, $selected_cpt)); ?>> <?php echo esc_html($cpt->labels->name); ?></label>
                            <?php endforeach;
                            endif; ?>
                        </div>
                        <p class="help">选择要在首页专题栏目区域显示的自定义文章类型</p>
                    </div>
                    
                    <div class="form-row">
                        <label>自定义文章类型封面</label>
                        <div style="max-width: 800px;">
                            <?php
                            $cpt_covers = get_option('wuchaiwp_multi_cpt_covers', array());
                            $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
                            if (empty($custom_post_types)) :
                            ?>
                                <p style="color: #999;">暂无自定义文章类型</p>
                            <?php else :
                                foreach ($custom_post_types as $cpt) :
                                    $cover_id = isset($cpt_covers[$cpt->name]) ? $cpt_covers[$cpt->name] : 0;
                                    $cover_url = $cover_id ? wp_get_attachment_url($cover_id) : '';
                            ?>
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                                    <div style="width: 100px; height: 80px; overflow: hidden; border-radius: 4px; background: #eee; flex-shrink: 0;">
                                        <?php if ($cover_url) : ?>
                                            <img src="<?php echo esc_url($cover_url); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                        <?php else : ?>
                                            <span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; color: #ccc; font-size: 24px;">📷</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="font-weight: bold; display: block;"><?php echo esc_html($cpt->labels->name); ?></label>
                                        <input type="hidden" name="wuchaiwp_multi_cpt_covers[<?php echo esc_attr($cpt->name); ?>]" id="wuchaiwp_cpt_cover_<?php echo esc_attr($cpt->name); ?>" value="<?php echo esc_attr($cover_id); ?>" />
                                        <button type="button" class="wuchaiwp-upload-cover button" data-target="wuchaiwp_cpt_cover_<?php echo esc_attr($cpt->name); ?>">
                                            <?php echo $cover_id ? '更换封面' : '选择封面'; ?>
                                        </button>
                                        <?php if ($cover_id) : ?>
                                            <button type="button" class="wuchaiwp-remove-cover button button-secondary" data-target="wuchaiwp_cpt_cover_<?php echo esc_attr($cpt->name); ?>">
                                                移除封面
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach;
                            endif; ?>
                        </div>
                        <p class="help">为每个自定义文章类型设置封面图片，优先使用此处设置的封面；如果未设置，则自动获取该类型第一篇文章的特色图片</p>
                    </div>
                </div>

                <div class="form-section">
                    <h3>🎯 专题推荐设置</h3>
                    
                    <div class="form-row">
                        <label>专题文章来源</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                            <?php
                            $sources = array(
                                'sticky' => '📌 置顶文章',
                                'featured' => '⭐ 标记为专题的文章',
                                'selected' => '✏️ 手动选择文章',
                                'latest' => '🔥 最新文章',
                            );
                            
                            $selected_source = get_option('wuchaiwp_multi_featured_source', 'sticky');
                            
                            foreach ($sources as $key => $label) :
                            ?>
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="radio" name="wuchaiwp_multi_featured_source" value="<?php echo esc_attr($key); ?>" <?php checked($selected_source, $key); ?> />
                                    <?php echo esc_html($label); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label>手动选择专题文章</label>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 250px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">可用文章</label>
                                <input type="text" id="wuchaiwp_post_search" placeholder="搜索文章标题..." class="regular-text" style="width: 100%; margin-bottom: 5px;" />
                                <select id="wuchaiwp_available_posts" multiple="multiple" style="width: 100%; height: 200px;">
                                    <?php
                                    $selected_posts = get_option('wuchaiwp_multi_featured_posts', array());
                                    // 获取所有公开的文章类型
                                    $public_post_types = get_post_types(array('public' => true), 'objects');
                                    $post_type_names = array();
                                    foreach ($public_post_types as $pt) {
                                        if ($pt->name !== 'attachment') {
                                            $post_type_names[] = $pt->name;
                                        }
                                    }
                                    $args = array('post_type' => $post_type_names, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC');
                                    $posts = get_posts($args);
                                    foreach ($posts as $post) :
                                        if (!in_array($post->ID, $selected_posts)) :
                                            $post_type_obj = get_post_type_object($post->post_type);
                                            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type;
                                    ?>
                                    <option value="<?php echo esc_attr($post->ID); ?>" data-post-type="<?php echo esc_attr($post->post_type); ?>">[<?php echo esc_html($post_type_label); ?>] <?php echo esc_html($post->post_title); ?></option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <div style="display: flex; flex-direction: column; justify-content: center; gap: 5px;">
                                <button type="button" id="wuchaiwp_add_post" class="btn btn-secondary">▶ 添加</button>
                                <button type="button" id="wuchaiwp_remove_post" class="btn btn-secondary">◀ 移除</button>
                            </div>

                            <div style="flex: 1; min-width: 250px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">已选择文章</label>
                                <select name="wuchaiwp_multi_featured_posts[]" id="wuchaiwp_selected_posts" multiple="multiple" style="width: 100%; height: 200px;">
                                    <?php
                                    foreach ($posts as $post) :
                                        if (in_array($post->ID, $selected_posts)) :
                                            $post_type_obj = get_post_type_object($post->post_type);
                                            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type;
                                    ?>
                                    <option value="<?php echo esc_attr($post->ID); ?>" selected>[<?php echo esc_html($post_type_label); ?>] <?php echo esc_html($post->post_title); ?></option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <p class="help">从左侧选择文章，点击添加按钮移动到右侧；或双击文章进行添加/移除。文章标题前会显示所属文章类型</p>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php submit_button('保存设置', 'primary', 'submit', false); ?>
                </div>
            </form>
            
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // 封面上传功能
                    var wuchaiwp_cover_frame;
                    
                    // 使用事件委托确保按钮存在后再绑定
                    $(document).on('click', '.wuchaiwp-upload-cover', function(e) {
                        e.preventDefault();
                        var target_id = $(this).data('target');
                        
                        // 检查wp.media是否可用
                        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                            alert('媒体库功能不可用，请刷新页面重试');
                            return;
                        }
                        
                        if (wuchaiwp_cover_frame) {
                            wuchaiwp_cover_frame.open();
                            return;
                        }
                        
                        wuchaiwp_cover_frame = wp.media.frames.wuchaiwp_cover_frame = wp.media({
                            title: '从媒体库选择封面图片',
                            button: {
                                text: '使用这张图片'
                            },
                            multiple: false
                        });
                        
                        wuchaiwp_cover_frame.on('select', function() {
                            var attachment = wuchaiwp_cover_frame.state().get('selection').first().toJSON();
                            $('#' + target_id).val(attachment.id);
                            // 更新预览图
                            var button = $('.wuchaiwp-upload-cover[data-target="' + target_id + '"]');
                            var parent = button.closest('div');
                            var preview = parent.prev('div');
                            preview.html('<img src="' + attachment.url + '" style="width: 100%; height: 100%; object-fit: cover;" />');
                            // 更新按钮文字
                            button.text('更换封面');
                            // 添加移除按钮
                            if (!parent.find('.wuchaiwp-remove-cover').length) {
                                button.after('<button type="button" class="wuchaiwp-remove-cover button button-secondary" data-target="' + target_id + '">移除封面</button>');
                            }
                        });
                        
                        wuchaiwp_cover_frame.open();
                    });
                    
                    // 移除封面
                    $(document).on('click', '.wuchaiwp-remove-cover', function(e) {
                        e.preventDefault();
                        var target_id = $(this).data('target');
                        $('#' + target_id).val('');
                        var button = $(this);
                        var parent = button.closest('div');
                        var preview = parent.prev('div');
                        preview.html('<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; color: #ccc; font-size: 24px;">📷</span>');
                        parent.find('.wuchaiwp-upload-cover').text('选择封面');
                        button.remove();
                    });
                    
                    $('#wuchaiwp_selected_posts option').prop('selected', true);
                    $('#wuchaiwp_available_posts option').prop('selected', false);

                    $('#wuchaiwp_post_search').on('keyup', function() {
                        var searchText = $(this).val().toLowerCase();
                        $('#wuchaiwp_available_posts option').each(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) !== -1);
                        });
                    });

                    $('#wuchaiwp_add_post').click(function() {
                        var selectedOpts = $('#wuchaiwp_available_posts option:selected');
                        if (selectedOpts.length === 0) {
                            alert('请先选择要添加的文章');
                            return;
                        }
                        selectedOpts.each(function() {
                            $(this).remove().appendTo('#wuchaiwp_selected_posts').prop('selected', true);
                        });
                        $('#wuchaiwp_available_posts option').prop('selected', false);
                    });

                    $('#wuchaiwp_remove_post').click(function() {
                        var selectedOpts = $('#wuchaiwp_selected_posts option:selected');
                        if (selectedOpts.length === 0) {
                            alert('请先选择要移除的文章');
                            return;
                        }
                        selectedOpts.each(function() {
                            $(this).remove().appendTo('#wuchaiwp_available_posts').prop('selected', false);
                        });
                        $('#wuchaiwp_selected_posts option').prop('selected', true);
                    });

                    $('#wuchaiwp_available_posts').dblclick(function(e) {
                        var target = $(e.target);
                        if (target.is('option')) {
                            target.prop('selected', true);
                            $('#wuchaiwp_add_post').click();
                        }
                    });

                    $('#wuchaiwp_selected_posts').dblclick(function(e) {
                        var target = $(e.target);
                        if (target.is('option')) {
                            $('#wuchaiwp_selected_posts option').prop('selected', false);
                            target.prop('selected', true);
                            $('#wuchaiwp_remove_post').click();
                        }
                    });

                    $('form').submit(function() {
                        $('#wuchaiwp_selected_posts option').prop('selected', true);
                    });
                });
            </script>
        </div>
        <?php
    }
}

// 初始化
new Wuchaiwp_Multi_Frontpage_Settings();

/**
 * 渲染多区域首页设置页面（供 Wuchaiwp_Settings 类调用）
 */
function wuchaiwp_render_multi_frontpage_settings() {
    $settings = new Wuchaiwp_Multi_Frontpage_Settings();
    $settings->render_multi_frontpage_settings();
}
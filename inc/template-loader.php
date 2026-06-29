<?php
/**
 * Template Loader
 * 参考 wuchai-muban-ziliao 的模板加载机制
 * 支持详情页、归档页、编辑页模板选择
 * 
 * @package wuchaiwp
 */

/**
 * 首页模板加载器
 */
function wuchaiwp_frontpage_template_loader($template) {
    // 只有在首页时才处理模板选择
    if (!is_front_page() && !is_home()) {
        return $template;
    }
    
    // 获取用户选择的首页模板
    $selected_template = get_option('wuchaiwp_frontpage_template', 'default');
    
    // 如果选择的是 default，不改变默认行为
    if ('default' === $selected_template) {
        return $template;
    }
    
    // 构建可能的模板路径
    $template_paths = array(
        'templates/home/home-' . $selected_template . '.php',
        'template-parts/frontpage/frontpage-' . $selected_template . '.php',
        'frontpage-' . $selected_template . '.php',
    );
    
    // 查找可用的模板文件
    foreach ($template_paths as $path) {
        $custom_template = locate_template($path);
        if (!empty($custom_template)) {
            return $custom_template;
        }
    }
    
    return $template;
}

// 为静态首页添加过滤器
add_filter('frontpage_template', 'wuchaiwp_frontpage_template_loader');

// 为博客首页（显示最新文章）添加过滤器
add_filter('home_template', 'wuchaiwp_frontpage_template_loader');

/**
 * 自定义文章类型详情页模板加载器
 * 优先使用模板选择设置，回退到wuchai-muban-ziliao配置
 */
function wuchaiwp_custom_post_type_template($template) {
    global $post;
    
    if ($post && is_singular()) {
        $post_type = get_post_type($post->ID);
        
        // 优先从模板选择设置获取模板（直接读取数据库，不依赖后台类）
        $selected_template = wuchaiwp_get_post_type_template_setting($post_type, 'single');
        
        if ($selected_template !== 'default') {
            // 查找对应的模板文件
            $custom_template = locate_template(array(
                'templates/single/single-' . $selected_template . '.php',
                'single-' . $selected_template . '.php'
            ));
            
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        // 获取自定义文章类型配置（兼容旧版本）
        if (function_exists('wuchaiwp_get_custom_post_types')) {
            $custom_post_types = wuchaiwp_get_custom_post_types();
        } else {
            $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
            if (empty($custom_post_types)) {
                $custom_post_types = get_option('wuchai_custom_post_types', array());
            }
        }
        if (isset($custom_post_types[$post_type]) && !empty($custom_post_types[$post_type]['template'])) {
            $template_type = $custom_post_types[$post_type]['template'];
            
            $custom_template = locate_template(array(
                'templates/single/single-' . $template_type . '.php',
                'single-' . $template_type . '.php',
                'single-custom.php'
            ));
            
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        // 检查主题自定义模板配置
        $custom_templates = get_option('wuchaiwp_custom_templates', array());
        foreach ($custom_templates as $slug => $data) {
            if (file_exists(get_template_directory() . '/templates/single/single-' . $slug . '.php')) {
                if (isset($custom_post_types[$post_type]) && $custom_post_types[$post_type]['template'] == $slug) {
                    $custom_template = locate_template('templates/single/single-' . $slug . '.php');
                    if ($custom_template) {
                        return $custom_template;
                    }
                }
            }
        }
    }
    
    return $template;
}
add_filter('single_template', 'wuchaiwp_custom_post_type_template');

/**
 * 获取文章类型模板设置（独立函数，前后台都可用）
 */
function wuchaiwp_get_post_type_template_setting($post_type, $template_type) {
    // 直接从数据库读取设置，不依赖后台类
    $templates = get_option('wuchaiwp_post_type_templates', array());
    if (isset($templates[$post_type][$template_type])) {
        return $templates[$post_type][$template_type];
    }
    return 'default';
}

/**
 * 自定义文章类型归档模板
 * 优先使用模板选择设置，回退到wuchai-muban-ziliao配置
 * 支持默认文章类型(post)的归档模板选择
 */
function wuchaiwp_custom_post_type_archive_template($template) {
    // 只处理文章类型归档页和首页，排除分类、标签等其他归档
    if ((is_post_type_archive() && !is_category() && !is_tag() && !is_tax()) || is_home()) {
        $post_type = get_query_var('post_type');
        
        // 如果 post_type 是数组，取第一个元素
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        
        // 如果没有获取到 post_type，且是首页，设置为默认文章类型
        if (empty($post_type) && is_home()) {
            $post_type = 'post';
        }
        
        if (!empty($post_type)) {
            // 优先从模板选择设置获取模板（直接读取数据库，不依赖后台类）
            $selected_template = wuchaiwp_get_post_type_template_setting($post_type, 'archive');
            
            if ($selected_template !== 'default') {
                $custom_template = locate_template(array(
                    'templates/archive/archive-' . $selected_template . '.php',
                    'archive-' . $selected_template . '.php'
                ));
                
                if ($custom_template) {
                    return $custom_template;
                }
            }
            
            // 获取自定义文章类型配置（兼容旧版本）
            if (function_exists('wuchaiwp_get_custom_post_types')) {
                $custom_post_types = wuchaiwp_get_custom_post_types();
            } else {
                $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
                if (empty($custom_post_types)) {
                    $custom_post_types = get_option('wuchai_custom_post_types', array());
                }
            }
            
            // 对于默认文章类型(post)和自定义文章类型都检查配置
            if ($post_type === 'post' || isset($custom_post_types[$post_type])) {
                if (isset($custom_post_types[$post_type]) && !empty($custom_post_types[$post_type]['template'])) {
                    $template_type = $custom_post_types[$post_type]['template'];
                    
                    $custom_template = locate_template(array(
                        'templates/archive/archive-' . $template_type . '.php',
                        'archive-' . $template_type . '.php',
                        'archive-custom.php'
                    ));
                    
                    if ($custom_template) {
                        return $custom_template;
                    }
                }
            }
            
            // 检查主题自定义模板配置
            $custom_templates = get_option('wuchaiwp_custom_templates', array());
            foreach ($custom_templates as $slug => $data) {
                if (file_exists(get_template_directory() . '/templates/archive/archive-' . $slug . '.php')) {
                    if (isset($custom_post_types[$post_type]) && $custom_post_types[$post_type]['template'] == $slug) {
                        $custom_template = locate_template('templates/archive/archive-' . $slug . '.php');
                        if ($custom_template) {
                            return $custom_template;
                        }
                    }
                }
            }
        }
    }
    
    return $template;
}
add_filter('archive_template', 'wuchaiwp_custom_post_type_archive_template');
add_filter('home_template', 'wuchaiwp_custom_post_type_archive_template');

/**
 * 加载编辑页模板
 * 优先使用模板选择设置，回退到wuchai-muban-ziliao配置
 */
function wuchaiwp_load_edit_template() {
    // 使用 current_screen 钩子确保 $post 和当前屏幕信息已可用
    $screen = get_current_screen();
    
    if (!$screen || $screen->base !== 'post') {
        return;
    }
    
    global $post;
    $post_type = $screen->post_type;
    
    // 如果没有获取到 post_type，尝试从 $post 对象获取
    if (empty($post_type) && $post) {
        $post_type = get_post_type($post->ID);
    }
    
    if (empty($post_type)) {
        return;
    }
    
    // 确保是在编辑页面
    if (!is_admin()) {
        return;
    }
        
        // 优先从模板选择设置获取模板
        if (class_exists('Wuchaiwp_Template_Selection')) {
            $selected_template = Wuchaiwp_Template_Selection::get_post_type_template($post_type, 'edit');
            
            if ($selected_template !== 'default') {
                $edit_template_path = get_template_directory() . '/inc/admin/edit-templates/edit-' . $selected_template . '.php';
                if (file_exists($edit_template_path)) {
                    require_once $edit_template_path;
                    
                    $func_prefix = str_replace('-', '_', $selected_template);
                    $register_func = 'wuchaiwp_register_' . $func_prefix . '_fields';
                    if (function_exists($register_func)) {
                        call_user_func($register_func, $post_type);
                    }
                    return; // 找到模板后直接返回
                }
            }
        }
        
        // 获取自定义文章类型配置（兼容旧版本）
        if (function_exists('wuchaiwp_get_custom_post_types')) {
            $custom_post_types = wuchaiwp_get_custom_post_types();
        } else {
            $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
            if (empty($custom_post_types)) {
                $custom_post_types = get_option('wuchai_custom_post_types', array());
            }
        }
        if (isset($custom_post_types[$post_type]) && !empty($custom_post_types[$post_type]['template'])) {
            $template_type = $custom_post_types[$post_type]['template'];
            
            $edit_template_path = get_template_directory() . '/inc/admin/edit-templates/edit-' . $template_type . '.php';
            if (file_exists($edit_template_path)) {
                require_once $edit_template_path;
                
                $func_prefix = str_replace('-', '_', $template_type);
                $register_func = 'wuchaiwp_register_' . $func_prefix . '_fields';
                if (function_exists($register_func)) {
                    call_user_func($register_func, $post_type);
                }
            }
        }
    }

add_action('current_screen', 'wuchaiwp_load_edit_template');

/**
 * 自定义分类和标签模板
 */
function wuchaiwp_custom_taxonomy_template($template) {
    if (is_tax()) {
        $taxonomy = get_query_var('taxonomy');
        
        // 获取所有自定义分类法（兼容旧版本）
        if (function_exists('wuchaiwp_get_custom_taxonomies')) {
            $custom_taxonomies = wuchaiwp_get_custom_taxonomies();
        } else {
            $custom_taxonomies = get_option('wuchaiwp_custom_taxonomies', array());
            if (empty($custom_taxonomies)) {
                $custom_taxonomies = get_option('wuchai_custom_taxonomies', array());
            }
        }
        
        // 检查是否是自定义分类法
        if (isset($custom_taxonomies[$taxonomy])) {
            $custom_template = locate_template('taxonomy-custom.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        // 尝试查找特定分类法的模板
        $taxonomy_template = locate_template('taxonomy-' . $taxonomy . '.php');
        if ($taxonomy_template) {
            return $taxonomy_template;
        }
    }
    
    return $template;
}
add_filter('taxonomy_template', 'wuchaiwp_custom_taxonomy_template');

/**
 * 自定义文章类型归档页模板
 */
function wuchaiwp_custom_post_type_archive_template_fix($template) {
    if (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        
        // 如果 post_type 是数组，取第一个元素
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        
        // 获取所有自定义文章类型（兼容旧版本）
        if (function_exists('wuchaiwp_get_custom_post_types')) {
            $custom_post_types = wuchaiwp_get_custom_post_types();
        } else {
            $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
            if (empty($custom_post_types)) {
                $custom_post_types = get_option('wuchai_custom_post_types', array());
            }
        }
        
        // 检查是否是自定义文章类型
        if (isset($custom_post_types[$post_type])) {
            // 优先检查模板选择设置（直接读取数据库，不依赖后台类）
            $selected_template = wuchaiwp_get_post_type_template_setting($post_type, 'archive');
            
            if ($selected_template !== 'default') {
                $custom_template = locate_template(array(
                    'templates/archive/archive-' . $selected_template . '.php',
                    'archive-' . $selected_template . '.php'
                ));
                
                if ($custom_template) {
                    return $custom_template;
                }
            }
            
            // 尝试查找特定文章类型的归档模板
            $archive_template = locate_template('archive-' . $post_type . '.php');
            if ($archive_template) {
                return $archive_template;
            }
            
            // 回退到通用自定义归档模板
            $custom_template = locate_template('archive-custom.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
    }
    
    return $template;
}
add_filter('archive_template', 'wuchaiwp_custom_post_type_archive_template_fix', 20);

/**
 * 自定义文章类型详情页模板
 */
function wuchaiwp_custom_post_type_single_template_fix($template) {
    if (is_singular()) {
        global $post;
        $post_type = get_post_type($post->ID);
        
        // 获取所有自定义文章类型（兼容旧版本）
        if (function_exists('wuchaiwp_get_custom_post_types')) {
            $custom_post_types = wuchaiwp_get_custom_post_types();
        } else {
            $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
            if (empty($custom_post_types)) {
                $custom_post_types = get_option('wuchai_custom_post_types', array());
            }
        }
        
        // 检查是否是自定义文章类型
        if (isset($custom_post_types[$post_type])) {
            // 尝试查找特定文章类型的详情模板
            $single_template = locate_template('single-' . $post_type . '.php');
            if ($single_template) {
                return $single_template;
            }
            
            // 回退到通用自定义详情模板
            $custom_template = locate_template('single-custom.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
    }
    
    return $template;
}
add_filter('single_template', 'wuchaiwp_custom_post_type_single_template_fix', 20);
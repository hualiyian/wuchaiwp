<?php
/**
 * 自定义文章类型和分类法注册
 *
 * @package wuchaiwp
 */

// 获取自定义文章类型配置（兼容旧版本）
function wuchaiwp_get_custom_post_types() {
    $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
    if (empty($custom_post_types)) {
        $custom_post_types = get_option('wuchai_custom_post_types', array());
    }
    return $custom_post_types;
}

// 获取自定义分类法配置（兼容旧版本）
function wuchaiwp_get_custom_taxonomies() {
    $custom_taxonomies = get_option('wuchaiwp_custom_taxonomies', array());
    if (empty($custom_taxonomies)) {
        $custom_taxonomies = get_option('wuchai_custom_taxonomies', array());
    }
    return $custom_taxonomies;
}

// 注册自定义文章类型
function wuchaiwp_register_custom_post_types() {
    $cpts = wuchaiwp_get_custom_post_types();
    foreach ($cpts as $slug => $args) {
        // 确保必要参数存在
        if (!isset($args['public'])) {
            $args['public'] = true;
        }
        if (!isset($args['has_archive'])) {
            $args['has_archive'] = true;
        }
        if (!isset($args['rewrite'])) {
            $args['rewrite'] = array('slug' => $slug, 'with_front' => false);
        }
        if (!isset($args['query_var'])) {
            $args['query_var'] = true;
        }
        register_post_type($slug, $args);
    }
}
add_action('init', 'wuchaiwp_register_custom_post_types');

// 注册自定义分类法
function wuchaiwp_register_custom_taxonomies() {
    $taxonomies = wuchaiwp_get_custom_taxonomies();
    foreach ($taxonomies as $slug => $args) {
        $post_types = isset($args['post_types']) ? $args['post_types'] : array();
        unset($args['post_types']); // 移除 post_types，避免传递给 register_taxonomy
        
        // 确保 rewrite 参数存在
        if (!isset($args['rewrite'])) {
            $args['rewrite'] = array('slug' => $slug, 'with_front' => false);
        }
        register_taxonomy($slug, $post_types, $args);
    }
}
add_action('init', 'wuchaiwp_register_custom_taxonomies');

// 刷新重写规则
function wuchaiwp_flush_rewrite_rules() {
    wuchaiwp_register_custom_post_types();
    wuchaiwp_register_custom_taxonomies();
    flush_rewrite_rules(true);
}

// 激活主题时刷新重写规则
function wuchaiwp_activate_theme() {
    wuchaiwp_flush_rewrite_rules();
}
register_activation_hook(get_template_directory() . '/functions.php', 'wuchaiwp_activate_theme');

// 管理界面刷新重写规则（通过 GET 参数触发）
function wuchaiwp_handle_flush_rewrite() {
    if (isset($_GET['wuchaiwp_flush_rewrite']) && current_user_can('manage_options')) {
        wuchaiwp_flush_rewrite_rules();
        wp_redirect(remove_query_arg('wuchaiwp_flush_rewrite'));
        exit;
    }
}
add_action('admin_init', 'wuchaiwp_handle_flush_rewrite');

// ======== 核心：手动解析URL，不依赖重写规则 ========

// 修改自定义文章类型的永久链接结构 - 使用 post_type/YYYYMMDD-ID 格式
function wuchaiwp_custom_post_type_permalink($permalink, $post, $leavename) {
    $custom_post_types = wuchaiwp_get_custom_post_types();
    
    if ($post && isset($custom_post_types[$post->post_type])) {
        // 使用 post_type/YYYYMMDD-ID 格式
        $date_str = get_the_date('Ymd', $post);
        $post_id = $post->ID;
        $post_type = $post->post_type;
        
        // 构建带文章类型的URL：post_type/YYYYMMDD-ID
        $permalink = home_url('/' . $post_type . '/' . $date_str . '-' . $post_id . '/');
    }
    
    return $permalink;
}
add_filter('post_type_link', 'wuchaiwp_custom_post_type_permalink', 10, 3);

// 通过 pre_get_posts 手动解析URL，不依赖WordPress重写规则
function wuchaiwp_fix_custom_post_type_main_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        // 首页不处理，直接返回
        if ($query->is_home() || $query->is_front_page()) {
            return;
        }
        
        // 获取所有自定义文章类型
        $custom_post_types = wuchaiwp_get_custom_post_types();
        
        if (empty($custom_post_types)) {
            return;
        }
        
        // 获取请求URI
        $request = $_SERVER['REQUEST_URI'];
        $request = trim($request, '/');
        
        // 去掉查询字符串
        if (strpos($request, '?') !== false) {
            $request = substr($request, 0, strpos($request, '?'));
        }
        
        $path_parts = explode('/', $request);
        
        // 情况1: /{post_type}/（归档页）
        if (count($path_parts) == 1 && isset($custom_post_types[$path_parts[0]])) {
            $query->set('post_type', $path_parts[0]);
            $query->is_archive = true;
            $query->is_home = false;
            $query->is_front_page = false;
            return;
        }
        
        // 情况2: /{post_type}/{post-slug-or-date-id}/（详情页）
        if (count($path_parts) >= 2 && isset($custom_post_types[$path_parts[0]])) {
            $post_type_key = $path_parts[0];
            $post_identifier = $path_parts[1];
            
            // 检查是否是 YYYYMMDD-ID 格式
            if (strpos($post_identifier, '-') !== false) {
                $parts = explode('-', $post_identifier);
                if (count($parts) == 2 && strlen($parts[0]) == 8 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $post_id = intval($parts[1]);
                    if ($post_id > 0) {
                        $post = get_post($post_id);
                        if ($post && $post->post_type === $post_type_key) {
                            $query->set('post_type', $post_type_key);
                            $query->set('p', $post_id);
                            $query->is_single = true;
                            $query->is_singular = true;
                            $query->is_home = false;
                            $query->is_front_page = false;
                            return;
                        }
                    }
                }
            }
            
            // 尝试按slug查询
            $posts = get_posts(array(
                'name' => $post_identifier,
                'post_type' => $post_type_key,
                'post_status' => 'publish',
                'numberposts' => 1
            ));
            
            if (!empty($posts)) {
                $query->set('post_type', $post_type_key);
                $query->set('name', $post_identifier);
                $query->is_single = true;
                $query->is_singular = true;
                $query->is_home = false;
                $query->is_front_page = false;
                return;
            }
        }
        
        // 情况3: /{taxonomy}/{term}/（分类/标签页）
        $custom_taxonomies = wuchaiwp_get_custom_taxonomies();
        if (count($path_parts) >= 2 && isset($custom_taxonomies[$path_parts[0]])) {
            $taxonomy = $path_parts[0];
            $term_slug = $path_parts[1];
            
            $term = get_term_by('slug', $term_slug, $taxonomy);
            if ($term) {
                $query->set('taxonomy', $taxonomy);
                $query->set('term', $term_slug);
                $query->is_tax = true;
                $query->is_archive = true;
                $query->is_home = false;
                $query->is_front_page = false;
                return;
            }
        }
    }
}
add_action('pre_get_posts', 'wuchaiwp_fix_custom_post_type_main_query', 1);
?>
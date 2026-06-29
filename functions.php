<?php
/**
 * Twenty Seventeen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 */

/**
 * 阅读模式处理
 */
function wuchaiwp_reading_mode_template($template) {
    if (isset($_GET['wuchaiwp_action']) && $_GET['wuchaiwp_action'] === 'reading' && isset($_GET['wuchaiwp_post_id'])) {
        $post_id = intval($_GET['wuchaiwp_post_id']);
        if ($post_id > 0 && get_post_status($post_id) === 'publish') {
            $custom_template = get_template_directory() . '/templates/single/single-reading.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
    }
    return $template;
}
add_filter('template_include', 'wuchaiwp_reading_mode_template');

/**
 * Twenty Seventeen only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wuchaiwp_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/wuchaiwp
	 * If you're building a theme based on Twenty Seventeen, use a find and replace
	 * to change 'wuchaiwp' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'wuchaiwp' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enables custom line height for blocks
	 */
	add_theme_support( 'custom-line-height' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'wuchaiwp-featured-image', 2000, 1200, true );

	add_image_size( 'wuchaiwp-thumbnail-avatar', 100, 100, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus(
		array(
			'top'    => __( 'Top Menu', 'wuchaiwp' ),
			'social' => __( 'Social Links Menu', 'wuchaiwp' ),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
			'navigation-widgets',
		)
	);

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://wordpress.org/support/article/post-formats/
	 */
	add_theme_support(
		'post-formats',
		array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
			'gallery',
			'audio',
		)
	);

	// Add theme support for Custom Logo.
	add_theme_support(
		'custom-logo',
		array(
			'width'      => 250,
			'height'     => 250,
			'flex-width' => true,
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
	  */
	add_editor_style( array( 'assets/css/editor-style.css', wuchaiwp_fonts_url() ) );

	// Load regular editor styles into the new block-based editor.
	add_theme_support( 'editor-styles' );

	// Load default block styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(
		'widgets'     => array(
			// Place three core-defined widgets in the sidebar area.
			'sidebar-1' => array(
				'text_business_info',
				'search',
				'text_about',
			),

			// Add the core-defined business info widget to the footer 1 area.
			'sidebar-2' => array(
				'text_business_info',
			),

			// Put two core-defined widgets in the footer 2 area.
			'sidebar-3' => array(
				'text_about',
				'search',
			),
		),

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts'       => array(
			'home',
			'about'            => array(
				'thumbnail' => '{{image-sandwich}}',
			),
			'contact'          => array(
				'thumbnail' => '{{image-espresso}}',
			),
			'blog'             => array(
				'thumbnail' => '{{image-coffee}}',
			),
			'homepage-section' => array(
				'thumbnail' => '{{image-espresso}}',
			),
		),

		// Create the custom image attachments used as post thumbnails for pages.
		'attachments' => array(
			'image-espresso' => array(
				'post_title' => _x( 'Espresso', 'Theme starter content', 'wuchaiwp' ),
				'file'       => 'assets/images/espresso.jpg', // URL relative to the template directory.
			),
			'image-sandwich' => array(
				'post_title' => _x( 'Sandwich', 'Theme starter content', 'wuchaiwp' ),
				'file'       => 'assets/images/sandwich.jpg',
			),
			'image-coffee'   => array(
				'post_title' => _x( 'Coffee', 'Theme starter content', 'wuchaiwp' ),
				'file'       => 'assets/images/coffee.jpg',
			),
		),

		// Default to a static front page and assign the front and posts pages.
		'options'     => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{home}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set the front page section theme mods to the IDs of the core-registered pages.
		'theme_mods'  => array(
			'panel_1' => '{{homepage-section}}',
			'panel_2' => '{{about}}',
			'panel_3' => '{{blog}}',
			'panel_4' => '{{contact}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus'   => array(
			// Assign a menu to the "top" location.
			'top'    => array(
				'name'  => __( 'Top Menu', 'wuchaiwp' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			// Assign a menu to the "social" location.
			'social' => array(
				'name'  => __( 'Social Links Menu', 'wuchaiwp' ),
				'items' => array(
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters Twenty Seventeen array of starter content.
	 *
	 * @since Twenty Seventeen 1.1
	 *
	 * @param array $starter_content Array of starter content.
	 */
	$starter_content = apply_filters( 'wuchaiwp_starter_content', $starter_content );

	add_theme_support( 'starter-content', $starter_content );
}
add_action( 'after_setup_theme', 'wuchaiwp_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wuchaiwp_content_width() {

	$content_width = $GLOBALS['content_width'];

	// Get layout.
	$page_layout = get_theme_mod( 'page_layout' );

	// Check if layout is one column.
	if ( 'one-column' === $page_layout ) {
		if ( wuchaiwp_is_frontpage() ) {
			$content_width = 644;
		} elseif ( is_page() ) {
			$content_width = 740;
		}
	}

	// Check if is single post and there is no sidebar.
	if ( is_single() && ! is_active_sidebar( 'sidebar-1' ) ) {
		$content_width = 740;
	}

	/**
	 * Filters Twenty Seventeen content width of the theme.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param int $content_width Content width in pixels.
	 */
	$GLOBALS['content_width'] = apply_filters( 'wuchaiwp_content_width', $content_width );
}
add_action( 'template_redirect', 'wuchaiwp_content_width', 0 );

/**
 * Register custom fonts.
 */
function wuchaiwp_fonts_url() {
	$fonts_url = '';

	/*
	 * translators: If there are characters in your language that are not supported
	 * by Libre Franklin, translate this to 'off'. Do not translate into your own language.
	 */
	$libre_franklin = _x( 'on', 'Libre Franklin font: on or off', 'wuchaiwp' );

	if ( 'off' !== $libre_franklin ) {
		$font_families = array();

		$font_families[] = 'Libre Franklin:300,300i,400,400i,600,600i,800,800i';

		$query_args = array(
			'family'  => urlencode( implode( '|', $font_families ) ),
			'subset'  => urlencode( 'latin,latin-ext' ),
			'display' => urlencode( 'fallback' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Add preconnect for Google Fonts.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed.
 * @return array URLs to print for resource hints.
 */
function wuchaiwp_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'wuchaiwp-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'wuchaiwp_resource_hints', 10, 2 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wuchaiwp_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Home Sidebar', 'wuchaiwp' ),
			'id'            => 'sidebar-home',
			'description'   => __( 'Add widgets here to appear in your sidebar on home page and archive pages.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	
	
	 register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'wuchaiwp' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	

	// 文章详情页专属侧边栏
	register_sidebar(
		array(
			'name'          => __( 'Single Post Sidebar', 'wuchaiwp' ),
			'id'            => 'single-post-sidebar',
			'description'   => __( 'Add widgets here to appear in your sidebar on single blog post pages.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// 文章内容页底部小工具区域
	register_sidebar(
		array(
			'name'          => __( 'Single Post Bottom Widget Area', 'wuchaiwp' ),
			'id'            => 'single-post-bottom',
			'description'   => __( 'Add widgets here to appear at the bottom of single blog post content pages.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// 企业官网侧边栏
	register_sidebar(
		array(
			'name'          => __( '企业官网侧边栏', 'wuchaiwp' ),
			'id'            => 'enterprise-sidebar',
			'description'   => __( '企业官网详情页侧边栏，可添加企业信息、快速导航、联系方式等小工具', 'wuchaiwp' ),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer 1', 'wuchaiwp' ),
			'id'            => 'sidebar-2',
			'description'   => __( 'Add widgets here to appear in your footer.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer 2', 'wuchaiwp' ),
			'id'            => 'sidebar-3',
			'description'   => __( 'Add widgets here to appear in your footer.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Frontpage Footer Widgets', 'wuchaiwp' ),
			'id'            => 'frontpage-footer-widgets',
			'description'   => __( 'Add widgets here to appear at the bottom of the frontpage main content area.', 'wuchaiwp' ),
			'before_widget' => '<section id="%1$s" class="home-section frontpage-widget-area"><div class="widget %2$s">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'wuchaiwp_widgets_init' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $link Link to single post/page.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function wuchaiwp_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf(
		'<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Post title. */
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'wuchaiwp' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'wuchaiwp_excerpt_more' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function wuchaiwp_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'wuchaiwp_javascript_detection', 0 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function wuchaiwp_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'wuchaiwp_pingback_header' );

/**
 * Display custom color CSS.
 */
function wuchaiwp_colors_css_wrap() {
	if ( 'custom' !== get_theme_mod( 'colorscheme' ) && ! is_customize_preview() ) {
		return;
	}

	require_once get_parent_theme_file_path( '/inc/color-patterns.php' );
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );

	$customize_preview_data_hue = '';
	if ( is_customize_preview() ) {
		$customize_preview_data_hue = 'data-hue="' . $hue . '"';
	}
	?>
	<style type="text/css" id="custom-theme-colors" <?php echo $customize_preview_data_hue; ?>>
		<?php echo wuchaiwp_custom_colors_css(); ?>
	</style>
	<?php
}
add_action( 'wp_head', 'wuchaiwp_colors_css_wrap' );

/**
 * Enqueues scripts and styles.
 */
function wuchaiwp_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'wuchaiwp-fonts', wuchaiwp_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'wuchaiwp-style', get_stylesheet_uri(), array(), '20201208' );

	// Theme block stylesheet.
	wp_enqueue_style( 'wuchaiwp-block-style', get_theme_file_uri( '/assets/css/blocks.css' ), array( 'wuchaiwp-style' ), '20190105' );

	// Load the dark colorscheme.
	if ( 'dark' === get_theme_mod( 'colorscheme', 'light' ) || is_customize_preview() ) {
		wp_enqueue_style( 'wuchaiwp-colors-dark', get_theme_file_uri( '/assets/css/colors-dark.css' ), array( 'wuchaiwp-style' ), '20190408' );
	}

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'wuchaiwp-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'wuchaiwp-style' ), '20161202' );
		wp_style_add_data( 'wuchaiwp-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'wuchaiwp-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'wuchaiwp-style' ), '20161202' );
	wp_style_add_data( 'wuchaiwp-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '20161020' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'wuchaiwp-skip-link-focus-fix', get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ), array(), '20161114', true );

	$wuchaiwp_l10n = array(
		'quote' => wuchaiwp_get_svg( array( 'icon' => 'quote-right' ) ),
	);

	if ( has_nav_menu( 'top' ) ) {
		wp_enqueue_script( 'wuchaiwp-navigation', get_theme_file_uri( '/assets/js/navigation.js' ), array( 'jquery' ), '20161203', true );
		$wuchaiwp_l10n['expand']   = __( 'Expand child menu', 'wuchaiwp' );
		$wuchaiwp_l10n['collapse'] = __( 'Collapse child menu', 'wuchaiwp' );
		$wuchaiwp_l10n['icon']     = wuchaiwp_get_svg(
			array(
				'icon'     => 'angle-down',
				'fallback' => true,
			)
		);
	}

	wp_enqueue_script( 'wuchaiwp-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '20190121', true );

	wp_enqueue_script( 'jquery-scrollto', get_theme_file_uri( '/assets/js/jquery.scrollTo.js' ), array( 'jquery' ), '2.1.2', true );

	wp_localize_script( 'wuchaiwp-skip-link-focus-fix', 'wuchaiwpScreenReaderText', $wuchaiwp_l10n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wuchaiwp_scripts' );

/**
 * Enqueues styles for the block-based editor.
 *
 * @since Twenty Seventeen 1.8
 */
function wuchaiwp_block_editor_styles() {
	// Block styles.
	wp_enqueue_style( 'wuchaiwp-block-editor-style', get_theme_file_uri( '/assets/css/editor-blocks.css' ), array(), '20201208' );
	// Add custom fonts.
	wp_enqueue_style( 'wuchaiwp-fonts', wuchaiwp_fonts_url(), array(), null );
}
add_action( 'enqueue_block_editor_assets', 'wuchaiwp_block_editor_styles' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function wuchaiwp_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	if ( 740 <= $width ) {
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if ( is_active_sidebar( 'sidebar-1' ) || is_archive() || is_search() || is_home() || is_page() ) {
		if ( ! ( is_page() && 'one-column' === get_theme_mod( 'page_options' ) ) && 767 <= $width ) {
			$sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}

/**
 * Output custom CSS from theme settings.
 */
function wuchaiwp_output_custom_css() {
	// 获取外观设置 - 使用简约黑白配色作为默认值
	$container_width = get_option('wuchaiwp_container_width', '1200');
	$content_width = get_option('wuchaiwp_content_width', '800');
	$content_sidebar_ratio = get_option('wuchaiwp_content_sidebar_ratio', '3-1');
	$sidebar_margin = get_option('wuchaiwp_sidebar_margin', '30');
	$font_family = get_option('wuchaiwp_font_family', 'default');
	$font_size = get_option('wuchaiwp_font_size', '16');
	$line_height = get_option('wuchaiwp_line_height', '1.6');
	$theme_color = get_option('wuchaiwp_theme_color', '#333333');
	$secondary_color = get_option('wuchaiwp_secondary_color', '#666666');
	$background_color = get_option('wuchaiwp_background_color', '#ffffff');
	$text_color = get_option('wuchaiwp_text_color', '#333333');
	$link_color = get_option('wuchaiwp_link_color', '#666666');
	$border_radius = get_option('wuchaiwp_border_radius', '0');
	$shadow_intensity = get_option('wuchaiwp_shadow_intensity', '0');

	// 根据比例计算内容和侧边栏宽度
	$content_width_percent = '75%';
	$sidebar_width_percent = '25%';
	switch ($content_sidebar_ratio) {
		case '4-1':
			$content_width_percent = '80%';
			$sidebar_width_percent = '20%';
			break;
		case '2-1':
			$content_width_percent = '66.666%';
			$sidebar_width_percent = '33.333%';
			break;
		case '5-2':
			$content_width_percent = '71.428%';
			$sidebar_width_percent = '28.572%';
			break;
		case 'full':
			$content_width_percent = '100%';
			$sidebar_width_percent = '0';
			break;
		default:
			$content_width_percent = '75%';
			$sidebar_width_percent = '25%';
	}

	// 全屏模式隐藏侧边栏
	$sidebar_display = ($content_sidebar_ratio == 'full') ? 'none' : 'block';

	// 字体族映射
	$font_families = array(
		'default' => 'inherit',
		'pingfang' => '"PingFang SC", "Microsoft YaHei", sans-serif',
		'noto' => '"Noto Sans SC", sans-serif',
		'roboto' => 'Roboto, sans-serif',
		'sans' => '"Helvetica Neue", Arial, sans-serif',
		'serif' => 'Georgia, serif',
	);

	$selected_font = isset($font_families[$font_family]) ? $font_families[$font_family] : $font_families['default'];

	// 生成CSS
	$css = "<style type='text/css'>
:root {
	--wuchaiwp-theme-color: {$theme_color};
	--wuchaiwp-secondary-color: {$secondary_color};
	--wuchaiwp-background-color: {$background_color};
	--wuchaiwp-text-color: {$text_color};
	--wuchaiwp-link-color: {$link_color};
	--wuchaiwp-border-radius: {$border_radius}px;
	--wuchaiwp-shadow-intensity: {$shadow_intensity}px;
}

body {
	font-family: {$selected_font};
	font-size: {$font_size}px;
	line-height: {$line_height};
	color: {$text_color};
	background-color: {$background_color};
}

.wrap {
	max-width: {$container_width}px;
	margin-left: auto;
	margin-right: auto;
}

/* 导航栏容器宽度 */
.navigation-top {
	max-width: 100%;
}

.navigation-top .wrap {
	max-width: {$container_width}px;
	margin-left: auto;
	margin-right: auto;
}

/* 站点头部容器 */
.site-header {
	max-width: 100%;
}

.site-header .wrap {
	max-width: {$container_width}px;
	margin-left: auto;
	margin-right: auto;
}

/* 内容区域宽度 */
.site-content {
	max-width: {$container_width}px;
	margin-left: auto;
	margin-right: auto;
}

/* ===== 内容区域和侧边栏布局 ===== */
.has-sidebar .content-area {
	display: flex;
	gap: {$sidebar_margin}px;
}

.has-sidebar .content-area #primary {
	flex: 3;
	min-width: 0;
}

.has-sidebar .content-area #secondary {
	flex: 1;
	min-width: 250px;
	display: block;
}

/* 全屏模式隐藏侧边栏 */
.has-sidebar.content-sidebar #secondary,
.has-sidebar.sidebar-content #secondary {
	display: block;
}

a {
	color: {$link_color};
}

a:hover {
	color: {$secondary_color};
}

button, .button, input[type='submit'] {
	background-color: {$theme_color};
	border-radius: {$border_radius}px;
}

button:hover, .button:hover, input[type='submit']:hover {
	background-color: {$secondary_color};
}

.card, .widget, .post, article {
	border-radius: {$border_radius}px;
	box-shadow: none;
}

.site-title a {
	color: {$theme_color};
}

/* ===== 导航栏样式 ===== */
.main-navigation {
	background-color: #ffffff !important;
	border-bottom: none !important;
}

.main-navigation ul li a {
	color: {$text_color} !important;
}

.main-navigation ul li a:hover,
.main-navigation ul li a:focus {
	color: {$text_color} !important;
	background-color: #f5f5f5 !important;
}

.main-navigation ul li.current-menu-item a {
	color: {$theme_color} !important;
	background-color: transparent !important;
	font-weight: bold !important;
	box-shadow: none !important;
}

/* 子菜单样式 */
.main-navigation ul ul {
	background-color: #ffffff !important;
	border: 1px solid #eeeeee !important;
	border-radius: {$border_radius}px;
}

.main-navigation ul ul li a {
	color: {$text_color} !important;
}

.main-navigation ul ul li a:hover {
	background-color: #f5f5f5 !important;
}

.main-navigation ul ul li.current-menu-item a {
	color: {$theme_color} !important;
	background-color: transparent !important;
	font-weight: bold !important;
}

/* 导航栏菜单按钮 */
.menu-toggle {
	background-color: {$theme_color} !important;
	color: #ffffff !important;
	border-radius: {$border_radius}px;
}

.menu-toggle:hover {
	background-color: {$secondary_color} !important;
}

/* ===== 站点标题和描述 ===== */
.site-branding .site-title a {
	color: {$theme_color} !important;
}

.site-branding .site-description {
	color: {$text_color} !important;
}

/* ===== 页脚样式 ===== */
.site-footer {
	background-color: #ffffff !important;
	color: {$text_color} !important;
	border-top: 1px solid #eeeeee !important;
}

.site-footer a {
	color: {$link_color} !important;
}

.site-footer a:hover {
	color: {$theme_color} !important;
}

/* ===== 文章标题样式 ===== */
.entry-title a {
	color: {$text_color} !important;
}

.entry-title a:hover {
	color: {$theme_color} !important;
}

/* ===== 按钮样式 ===== */
.button-primary {
	background-color: {$theme_color} !important;
	border-color: {$theme_color} !important;
	color: #ffffff !important;
	border-radius: {$border_radius}px;
}

.button-primary:hover {
	background-color: {$secondary_color} !important;
	border-color: {$secondary_color} !important;
}

/* ===== 边框和分隔线 ===== */
.entry-content hr {
	border-color: {$theme_color} !important;
}

.widget-title {
	border-bottom-color: {$theme_color} !important;
}

/* ===== 评论区域 ===== */
.comment-reply-title {
	color: {$theme_color} !important;
}



/* ===== 分页导航 ===== */
.page-numbers.current {
	background-color: {$theme_color} !important;
	color: #ffffff !important;
	border-radius: {$border_radius}px;
}

.page-numbers:hover {
	background-color: {$secondary_color} !important;
	color: #ffffff !important;
}
</style>";

	// 根据比例调整布局 - 仅影响内容区域，不影响header
	switch ($content_sidebar_ratio) {
		case '4-1':
			$css .= '<style type="text/css">
				body.has-sidebar .site-content .wrap { display: flex !important; gap: ' . $sidebar_margin . 'px !important; flex-wrap: nowrap !important; align-items: flex-start !important; }
				body.has-sidebar .site-content .wrap #primary.content-area { float: none !important; width: auto !important; flex: 4 !important; max-width: none !important; margin: 0 !important; }
				body.has-sidebar .site-content .wrap #secondary.widget-area { float: none !important; width: auto !important; flex: 1 !important; max-width: none !important; display: block !important; min-width: 200px !important; margin: 0 !important; }
				@media screen and (max-width: 768px) {
					body.has-sidebar .site-content .wrap { display: block !important; }
					body.has-sidebar .site-content .wrap #primary.content-area { width: 100% !important; flex: none !important; }
					body.has-sidebar .site-content .wrap #secondary.widget-area { width: 100% !important; flex: none !important; margin-top: ' . $sidebar_margin . 'px !important; }
				}
			</style>';
			break;
		case '2-1':
			$css .= '<style type="text/css">
				body.has-sidebar .site-content .wrap { display: flex !important; gap: ' . $sidebar_margin . 'px !important; flex-wrap: nowrap !important; align-items: flex-start !important; }
				body.has-sidebar .site-content .wrap #primary.content-area { float: none !important; width: auto !important; flex: 2 !important; max-width: none !important; margin: 0 !important; }
				body.has-sidebar .site-content .wrap #secondary.widget-area { float: none !important; width: auto !important; flex: 1 !important; max-width: none !important; display: block !important; min-width: 250px !important; margin: 0 !important; }
				@media screen and (max-width: 768px) {
					body.has-sidebar .site-content .wrap { display: block !important; }
					body.has-sidebar .site-content .wrap #primary.content-area { width: 100% !important; flex: none !important; }
					body.has-sidebar .site-content .wrap #secondary.widget-area { width: 100% !important; flex: none !important; margin-top: ' . $sidebar_margin . 'px !important; }
				}
			</style>';
			break;
		case '5-2':
			$css .= '<style type="text/css">
				body.has-sidebar .site-content .wrap { display: flex !important; gap: ' . $sidebar_margin . 'px !important; flex-wrap: nowrap !important; align-items: flex-start !important; }
				body.has-sidebar .site-content .wrap #primary.content-area { float: none !important; width: auto !important; flex: 5 !important; max-width: none !important; margin: 0 !important; }
				body.has-sidebar .site-content .wrap #secondary.widget-area { float: none !important; width: auto !important; flex: 2 !important; max-width: none !important; display: block !important; min-width: 200px !important; margin: 0 !important; }
				@media screen and (max-width: 768px) {
					body.has-sidebar .site-content .wrap { display: block !important; }
					body.has-sidebar .site-content .wrap #primary.content-area { width: 100% !important; flex: none !important; }
					body.has-sidebar .site-content .wrap #secondary.widget-area { width: 100% !important; flex: none !important; margin-top: ' . $sidebar_margin . 'px !important; }
				}
			</style>';
			break;
		case 'full':
			$css .= '<style type="text/css">
				body.has-sidebar .site-content .wrap { display: flex !important; gap: 0 !important; flex-wrap: nowrap !important; align-items: flex-start !important; }
				body.has-sidebar .site-content .wrap #primary.content-area { float: none !important; width: 100% !important; flex: 1 !important; max-width: none !important; margin: 0 !important; }
				body.has-sidebar .site-content .wrap #secondary.widget-area { float: none !important; width: auto !important; display: none !important; margin: 0 !important; }
				@media screen and (max-width: 768px) {
					body.has-sidebar .site-content .wrap { display: block !important; }
					body.has-sidebar .site-content .wrap #primary.content-area { width: 100% !important; flex: none !important; }
				}
			</style>';
			break;
		default: // 默认3-1比例
			$css .= '<style type="text/css">
				body.has-sidebar .site-content .wrap { display: flex !important; gap: ' . $sidebar_margin . 'px !important; flex-wrap: nowrap !important; align-items: flex-start !important; }
				body.has-sidebar .site-content .wrap #primary.content-area { float: none !important; width: auto !important; flex: 3 !important; max-width: none !important; margin: 0 !important; }
				body.has-sidebar .site-content .wrap #secondary.widget-area { float: none !important; width: auto !important; flex: 1 !important; max-width: none !important; display: block !important; min-width: 250px !important; margin: 0 !important; }
				@media screen and (max-width: 768px) {
					body.has-sidebar .site-content .wrap { display: block !important; }
					body.has-sidebar .site-content .wrap #primary.content-area { width: 100% !important; flex: none !important; }
					body.has-sidebar .site-content .wrap #secondary.widget-area { width: 100% !important; flex: none !important; margin-top: ' . $sidebar_margin . 'px !important; }
				}
			</style>';
			break;
	}

	echo $css;
}
add_action('wp_head', 'wuchaiwp_output_custom_css');
add_filter( 'wp_calculate_image_sizes', 'wuchaiwp_content_image_sizes_attr', 10, 2 );

/**
 * Filters the `sizes` value in the header image markup.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $html   The HTML image tag markup being filtered.
 * @param object $header The custom header object returned by 'get_custom_header()'.
 * @param array  $attr   Array of the attributes for the image tag.
 * @return string The filtered header image HTML.
 */
function wuchaiwp_header_image_tag( $html, $header, $attr ) {
	if ( isset( $attr['sizes'] ) ) {
		$html = str_replace( $attr['sizes'], '100vw', $html );
	}
	return $html;
}
add_filter( 'get_header_image_tag', 'wuchaiwp_header_image_tag', 10, 3 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array $attr       Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size       Registered image size or flat array of height and width dimensions.
 * @return array The filtered attributes for the image markup.
 */
function wuchaiwp_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( is_archive() || is_search() || is_home() ) {
		$attr['sizes'] = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
	} else {
		$attr['sizes'] = '100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'wuchaiwp_post_thumbnail_sizes_attr', 10, 3 );

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $template front-page.php.
 * @return string The template to be used: blank if is_home() is true (defaults to index.php),
 *                otherwise $template.
 */
function wuchaiwp_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template', 'wuchaiwp_front_page_template' );

/**
 * Modifies tag cloud widget arguments to display all tags in the same font size
 * and use list format for better accessibility.
 *
 * @since Twenty Seventeen 1.4
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array The filtered arguments for tag cloud widget.
 */
function wuchaiwp_widget_tag_cloud_args( $args ) {
	$args['largest']  = 1;
	$args['smallest'] = 1;
	$args['unit']     = 'em';
	$args['format']   = 'list';

	return $args;
}
add_filter( 'widget_tag_cloud_args', 'wuchaiwp_widget_tag_cloud_args' );

/**
 * Gets unique ID.
 *
 * This is a PHP implementation of Underscore's uniqueId method. A static variable
 * contains an integer that is incremented with each call. This number is returned
 * with the optional prefix. As such the returned value is not universally unique,
 * but it is unique across the life of the PHP process.
 *
 * @since Twenty Seventeen 2.0
 *
 * @see wp_unique_id() Themes requiring WordPress 5.0.3 and greater should use this instead.
 *
 * @param string $prefix Prefix for the returned ID.
 * @return string Unique ID.
 */
function wuchaiwp_unique_id( $prefix = '' ) {
	static $id_counter = 0;
	if ( function_exists( 'wp_unique_id' ) ) {
		return wp_unique_id( $prefix );
	}
	return $prefix . (string) ++$id_counter;
}

/**
 * Implement the Custom Header feature.
 */
require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path( '/inc/template-functions.php' );

/**
 * 博客阅读量统计函数
 */
function wuchaiwp_get_views($post_id) {
    $views = get_post_meta($post_id, 'wuchaiwp_views', true);
    return empty($views) ? 0 : (int)$views;
}

function wuchaiwp_set_views() {
    global $post;
    if (is_single()) {
        $post_id = $post->ID;
        $views = wuchaiwp_get_views($post_id);
        update_post_meta($post_id, 'wuchaiwp_views', $views + 1);
    }
}
add_action('wp_head', 'wuchaiwp_set_views');

function wuchaiwp_get_month_post_count() {
    $args = array(
        'post_type' => get_post_type(),
        'date_query' => array(
            array(
                'year'  => date('Y'),
                'month' => date('m'),
            ),
        ),
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);
    return $query->post_count;
}

/**
 * Customizer additions.
 */
require get_parent_theme_file_path( '/inc/customizer.php' );

/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path( '/inc/icon-functions.php' );

/**
 * Block Patterns.
 */
require get_template_directory() . '/inc/block-patterns.php';

/**
 * Theme Settings.
 */
require get_template_directory() . '/inc/theme-settings.php';

/**
 * Template Loader.
 */
require get_template_directory() . '/inc/template-loader.php';


function remove_logo($wp_toolbar) { 
    $wp_toolbar->remove_node('wp-logo'); //去掉Wordpress LOGO 
} 
add_action('admin_bar_menu', 'remove_logo', 999); 


// 去除后台标题中的“—— WordPress”
add_filter('admin_title', 'zm_custom_admin_title', 10, 2);
function zm_custom_admin_title($admin_title, $title){
    return $title.' &lsaquo; '.get_bloginfo('name');
}

//去除登录标题中的“- WordPress”
add_filter('login_title', 'zm_custom_login_title', 10, 2);
    function zm_custom_login_title($login_title, $title){
        return $title.' &lsaquo; '.get_bloginfo('name');
}
 
// 隐藏左上角WordPress标志
function hidden_admin_bar_remove() {
    global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'hidden_admin_bar_remove', 0);
 
// 屏蔽后台页脚WordPress版本信息
function change_footer_admin () {return '';}
add_filter('admin_footer_text', 'change_footer_admin', 9999);
function change_footer_version() {return '';}
add_filter( 'update_footer', 'change_footer_version', 9999);
 
//登录界面的logo删除与替换
function custom_loginlogo() {
echo '<style type="text/css">
h1 a {background-image: url('.get_bloginfo('template_directory').'/images/login_logo.png) !important; }
</style>';
}
add_action('login_head', 'custom_loginlogo');

/**
 * 输出自定义样式到前端
 */
function wuchaiwp_custom_styles() {
	$styles = '';

	// 根据页面类型应用不同的间距设置
	if ( is_front_page() || is_home() ) {
		// 首页：应用首页间距设置
		$frontpage_content_menu_spacing = get_theme_mod( 'frontpage_content_menu_spacing', '' );
		if ( $frontpage_content_menu_spacing !== '' && is_numeric( $frontpage_content_menu_spacing ) ) {
			$styles .= '.site-content { padding-top: ' . esc_attr( (float) $frontpage_content_menu_spacing ) . 'em !important; }\n';
		}
	} else {
		// 内容页：应用内容页间距设置
		$contentpage_content_menu_spacing = get_theme_mod( 'contentpage_content_menu_spacing', '' );
		if ( $contentpage_content_menu_spacing !== '' && is_numeric( $contentpage_content_menu_spacing ) ) {
			$styles .= '.site-content { padding-top: ' . esc_attr( (float) $contentpage_content_menu_spacing ) . 'em !important; }\n';
		}
	}

	// 文章标题字体大小
	$title_font_size = get_theme_mod( 'title_font_size', '' );
	if ( ! empty( $title_font_size ) && is_numeric( $title_font_size ) ) {
		$styles .= '.entry-title { font-size: ' . esc_attr( (int) $title_font_size ) . 'px !important; }\n';
	}

	// 正文字体大小
	$body_font_size = get_theme_mod( 'body_font_size', '' );
	if ( ! empty( $body_font_size ) && is_numeric( $body_font_size ) ) {
		$styles .= 'body, button, input, select, textarea { font-size: ' . esc_attr( (int) $body_font_size ) . 'px !important; }\n';
	}

	// 正文字体颜色
	$body_text_color = get_theme_mod( 'body_text_color', '' );
	if ( ! empty( $body_text_color ) ) {
		$styles .= 'body, button, input, select, textarea { color: ' . esc_attr( $body_text_color ) . ' !important; }\n';
	}

	// 标题颜色
	$title_text_color = get_theme_mod( 'title_text_color', '' );
	if ( ! empty( $title_text_color ) ) {
		$styles .= '.entry-title, .entry-title a { color: ' . esc_attr( $title_text_color ) . ' !important; }\n';
		$styles .= 'h1, h2, h3, h4, h5, h6 { color: ' . esc_attr( $title_text_color ) . ' !important; }\n';
	}

	// 链接颜色
	$link_color = get_theme_mod( 'link_color', '' );
	if ( ! empty( $link_color ) ) {
		$styles .= 'a { color: ' . esc_attr( $link_color ) . ' !important; }\n';
	}

	// 链接下划线样式
	$link_underline = get_theme_mod( 'link_underline', '' );
	if ( ! empty( $link_underline ) ) {
		if ( 'none' === $link_underline ) {
			$styles .= 'a, .entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-title a, .entry-meta a, .entry-footer a, .entry-content .more-link { text-decoration: none !important; border-bottom: none !important; }\n';
			$styles .= '.entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-content .more-link { box-shadow: none !important; }\n';
		} elseif ( 'underline' === $link_underline ) {
			$styles .= 'a, .entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-content .more-link { text-decoration: underline !important; border-bottom: none !important; }\n';
			$styles .= '.entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-content .more-link { box-shadow: none !important; }\n';
		} elseif ( 'hover' === $link_underline ) {
			$styles .= 'a, .entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-title a, .entry-meta a, .entry-footer a, .entry-content .more-link { text-decoration: none !important; border-bottom: none !important; }\n';
			$styles .= '.entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-content .more-link { box-shadow: none !important; }\n';
			$styles .= 'a:hover, .entry-content a:hover, .entry-summary a:hover, .comment-content a:hover, .widget a:hover, .widget-area a:hover, .site-footer .widget-area a:hover, .posts-navigation a:hover, .entry-content .more-link:hover { text-decoration: underline !important; border-bottom: none !important; }\n';
		}
	}

	// 段落间距（段落之间的空行）
	$paragraph_spacing = get_theme_mod( 'paragraph_spacing', '' );
	if ( $paragraph_spacing !== '' && is_numeric( $paragraph_spacing ) ) {
		$styles .= '.entry-content p, .entry-summary p, .comment-content p, article p { margin-bottom: ' . esc_attr( (float) $paragraph_spacing ) . 'em !important; margin-top: 0 !important; padding-bottom: 0 !important; }\n';
	}

	// 段落内空行间距
	$empty_line_height = get_theme_mod( 'empty_line_height', '' );
	if ( ! empty( $empty_line_height ) && is_numeric( $empty_line_height ) ) {
		$styles .= '.entry-content p, .entry-summary p, .comment-content p, article p { line-height: ' . esc_attr( (float) $empty_line_height ) . ' !important; }\n';
	}

	// 侧边栏行间距
	$sidebar_line_height = get_theme_mod( 'sidebar_line_height', '' );
	if ( ! empty( $sidebar_line_height ) && is_numeric( $sidebar_line_height ) ) {
		$styles .= '.widget-area .widget, .widget-area .widget li, .widget-area .widget p, .widget-area .widget a { line-height: ' . esc_attr( (float) $sidebar_line_height ) . ' !important; }\n';
	}

	// 侧边栏段落间距（空行）
	$sidebar_paragraph_spacing = get_theme_mod( 'sidebar_paragraph_spacing', '' );
	if ( $sidebar_paragraph_spacing !== '' && is_numeric( $sidebar_paragraph_spacing ) ) {
		$styles .= '#secondary .widget-area * p, #secondary .widget-area * li, #secondary .widget-area * a, .widget-area * p, .widget-area * li, .widget-area * a { margin-bottom: ' . esc_attr( (float) $sidebar_paragraph_spacing ) . 'em !important; margin-top: 0 !important; padding-bottom: 0 !important; }\n';
	}

	// 侧边栏小工具间距
	$sidebar_widget_spacing = get_theme_mod( 'sidebar_widget_spacing', '' );
	if ( $sidebar_widget_spacing !== '' && is_numeric( $sidebar_widget_spacing ) ) {
		$styles .= 'body #secondary .widget-area .widget, body .widget-area .widget { padding-bottom: ' . esc_attr( (float) $sidebar_widget_spacing ) . 'em !important; }\n';
	}

	// 小工具列表项边框
	$hide_widget_border = get_theme_mod( 'hide_widget_border', false );
	if ( 1 === (int) $hide_widget_border ) {
		$styles .= '.widget-area .widget ul li, .widget-area .widget ol li, .widget-area .widget li { border-top: none !important; border-bottom: none !important; padding-top: 0.25em !important; padding-bottom: 0.25em !important; }\n';
		$styles .= '.widget-area .widget_rss ul li { border-top: none !important; border-bottom: none !important; padding-top: 0.5em !important; padding-bottom: 0.5em !important; }\n';
	}

	// 字体选择
	$body_font = get_theme_mod( 'body_font', '' );
	if ( ! empty( $body_font ) && 'default' !== $body_font ) {
		$font_family = '';
		switch ( $body_font ) {
			case 'sans-serif':
				$font_family = 'sans-serif';
				break;
			case 'serif':
				$font_family = 'serif';
				break;
			case 'monospace':
				$font_family = 'monospace';
				break;
		}
		if ( ! empty( $font_family ) ) {
			$styles .= 'body, button, input, select, textarea { font-family: ' . esc_attr( $font_family ) . ' !important; }\n';
		}
	}

	// 底部对齐
	$footer_center = get_theme_mod( 'footer_center', '' );
	if ( ! empty( $footer_center ) && 'left' !== $footer_center ) {
		$styles .= '.site-footer .wrap { text-align: ' . esc_attr( $footer_center ) . ' !important; }\n';
		$styles .= '.site-footer .widget-area { text-align: ' . esc_attr( $footer_center ) . ' !important; }\n';
		$justify = 'center' === $footer_center ? 'center' : 'flex-end';
		$styles .= '.social-navigation { justify-content: ' . $justify . ' !important; }\n';
	}

	// 隐藏移动端菜单
	$hide_mobile_menu = get_theme_mod( 'hide_mobile_menu', false );
	if ( 1 === (int) $hide_mobile_menu ) {
		$styles .= '.menu-toggle, .js .menu-toggle { display: none !important; }\n';
	}

	// 移动端侧边栏菜单样式
	$mobile_sidebar_menu = get_theme_mod( 'mobile_sidebar_menu', 'default' );
	if ( 'sidebar' === $mobile_sidebar_menu ) {
		$styles .= '@media screen and (max-width: 767px) {\n';
		$styles .= '.main-navigation > div > ul { display: none !important; }\n';
		$styles .= '.sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }\n';
		$styles .= '.sidebar-overlay.active { opacity: 1; visibility: visible; }\n';
		$styles .= '.sidebar-navigation { position: fixed; top: 0; left: -280px; width: 280px; height: 100%; background: #fff; z-index: 9999; transition: left 0.3s ease; box-shadow: 2px 0 10px rgba(0,0,0,0.2); }\n';
		$styles .= '.sidebar-navigation.active { left: 0; }\n';
		$styles .= '.sidebar-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: flex-end; }\n';
		$styles .= '.sidebar-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #333; }\n';
		$styles .= '.sidebar-content { padding: 20px; }\n';
		$styles .= '.sidebar-menu { list-style: none; padding: 0; margin: 0; }\n';
		$styles .= '.sidebar-menu li { margin-bottom: 10px; }\n';
		$styles .= '.sidebar-menu a { display: block; padding: 12px 15px; text-decoration: none; color: #333; border-radius: 4px; transition: background 0.2s ease; }\n';
		$styles .= '.sidebar-menu a:hover { background: #f5f5f5; }\n';
		$styles .= '.sidebar-menu ul { list-style: none; padding-left: 20px; margin: 5px 0; }\n';
		$styles .= '.sidebar-menu ul li { margin-bottom: 5px; }\n';
		$styles .= '}@media screen and (min-width: 768px) {\n';
		$styles .= '.sidebar-overlay, .sidebar-navigation { display: none !important; }\n';
		$styles .= '}\n';
	}

	// 文章列表布局
	$post_layout_columns = get_theme_mod( 'post_layout_columns', '1' );
	if ( '2' === $post_layout_columns ) {
		$styles .= 'body.post-layout-2col .site-content .site-main { display: flex !important; flex-wrap: wrap !important; gap: 2% !important; }\n';
		$styles .= 'body.post-layout-2col .site-content .site-main article { flex: 0 0 49% !important; width: 49% !important; margin-bottom: 2em !important; }\n';
		$styles .= '@media screen and (max-width: 767px) { body.post-layout-2col .site-content .site-main article { flex: 0 0 100% !important; width: 100% !important; } }\n';
	} elseif ( '3' === $post_layout_columns ) {
		$styles .= 'body.post-layout-3col .site-content .site-main { display: flex !important; flex-wrap: wrap !important; gap: 2% !important; }\n';
		$styles .= 'body.post-layout-3col .site-content .site-main article { flex: 0 0 32% !important; width: 32% !important; margin-bottom: 2em !important; }\n';
		$styles .= '@media screen and (max-width: 1023px) { body.post-layout-3col .site-content .site-main article { flex: 0 0 49% !important; width: 49% !important; } }\n';
		$styles .= '@media screen and (max-width: 767px) { body.post-layout-3col .site-content .site-main article { flex: 0 0 100% !important; width: 100% !important; } }\n';
	}

	if ( ! empty( $styles ) ) {
		echo '<style type="text/css" id="wuchaiwp-custom-styles">' . $styles . '</style>';
	}
}
add_action( 'wp_head', 'wuchaiwp_custom_styles', 99 );

/**
 * 控制摘要中的图片数量
 */
function wuchaiwp_excerpt_image_limit( $excerpt ) {
	$image_count = get_theme_mod( 'excerpt_image_count', 1 );
	
	if ( $image_count == 0 ) {
		// 不显示图片
		$excerpt = preg_replace( '/<img[^>]*>/i', '', $excerpt );
	} elseif ( $image_count == 1 ) {
		// 只显示第一张图片
		preg_match( '/<img[^>]*>/i', $excerpt, $first_image );
		$excerpt = preg_replace( '/<img[^>]*>/i', '', $excerpt );
		if ( ! empty( $first_image[0] ) ) {
			$excerpt = $first_image[0] . $excerpt;
		}
	}
	// 如果 $image_count > 1，保持原样
	
	return $excerpt;
}
add_filter( 'the_excerpt', 'wuchaiwp_excerpt_image_limit' );

/**
 * 添加文章布局body类
 */
function wuchaiwp_post_layout_body_class( $classes ) {
	$post_layout_columns = get_theme_mod( 'post_layout_columns', '1' );
	if ( '2' === $post_layout_columns ) {
		$classes[] = 'post-layout-2col';
	} elseif ( '3' === $post_layout_columns ) {
		$classes[] = 'post-layout-3col';
	}
	return $classes;
}
add_filter( 'body_class', 'wuchaiwp_post_layout_body_class' );

/**
 * 直接输出文章布局样式
 */
function wuchaiwp_post_layout_styles() {
	// 只在文章列表页应用多列布局，排除文章详情页和页面
	if ( is_single() || is_page() ) {
		return;
	}
	
	$post_layout_columns = get_theme_mod( 'post_layout_columns', '1' );
	if ( '1' === $post_layout_columns ) {
		return;
	}
	?>
	<style type="text/css">
		<?php if ( '2' === $post_layout_columns ) : ?>
			/* 文章列表多列布局 - 仅影响文章内容区域 */
			.site-content .content-area > .site-main { display: flex !important; flex-wrap: wrap !important; gap: 2% !important; }
			.site-content .content-area > .site-main > article { flex: 0 0 49% !important; width: 49% !important; margin-bottom: 2em !important; float: none !important; clear: none !important; }
			@media screen and (max-width: 767px) { 
				.site-content .content-area > .site-main > article { flex: 0 0 100% !important; width: 100% !important; }
				/* 移动端重置，确保不影响导航栏 */
				.main-navigation, .main-navigation * { flex: none !important; width: auto !important; }
			}
		<?php elseif ( '3' === $post_layout_columns ) : ?>
			/* 文章列表多列布局 - 仅影响文章内容区域 */
			.site-content .content-area > .site-main { display: flex !important; flex-wrap: wrap !important; gap: 2% !important; }
			.site-content .content-area > .site-main > article { flex: 0 0 32% !important; width: 32% !important; margin-bottom: 2em !important; float: none !important; clear: none !important; }
			@media screen and (max-width: 1023px) { .site-content .content-area > .site-main > article { flex: 0 0 49% !important; width: 49% !important; } }
			@media screen and (max-width: 767px) { 
				.site-content .content-area > .site-main > article { flex: 0 0 100% !important; width: 100% !important; }
				/* 移动端重置，确保不影响导航栏 */
				.main-navigation, .main-navigation * { flex: none !important; width: auto !important; }
			}
		<?php endif; ?>
	</style>
	<?php
}
add_action( 'wp_head', 'wuchaiwp_post_layout_styles', 100 );






// 注册一个简单的测试文章类型
/*function wuchaiwp_register_test_cpt() {
    $args = array(
        'public' => true,
        'label' => '33333',
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => '33333', 'with_front' => false),
        'query_var' => true,
    );
    register_post_type('test_cpt', $args);
}
add_action('init', 'wuchaiwp_register_test_cpt');
 
// 自定义永久链接格式
function wuchaiwp_test_cpt_permalink($permalink, $post) {
    if ($post->post_type === '33333') {
        $permalink = home_url('/333332/' . $post->post_name . '/');
    }
    return $permalink;
}
add_filter('post_type_link', 'wuchaiwp_test_cpt_permalink', 10, 2);
 
// 手动解析URL（不依赖重写规则）
function wuchaiwp_test_cpt_redirect() {
    global $wp_query;
    
    if (is_admin()) return;
    
    $request = trim($_SERVER['REQUEST_URI'], '/');
    if (strpos($request, '?') !== false) {
        $request = substr($request, 0, strpos($request, '?'));
    }
    
    // 处理归档页: /test-cpt/
    if ($request === 'test-cpt') {
        $wp_query->init();
        $wp_query->set('post_type', 'test_cpt');
        $wp_query->is_archive = true;
        $wp_query->is_post_type_archive = true;
        $wp_query->is_home = false;
        $wp_query->query(array('post_type' => 'test_cpt'));
        
        $template = locate_template('archive-test_cpt.php');
        if (!$template) $template = locate_template('archive.php');
        if ($template) {
            include($template);
            exit;
        }
    }
    
    // 处理详情页: /test-cpt/post-name/
    if (strpos($request, 'test-cpt/') === 0) {
        $post_name = str_replace('test-cpt/', '', $request);
        $posts = get_posts(array(
            'name' => $post_name,
            'post_type' => 'test_cpt',
            'post_status' => 'publish',
            'numberposts' => 1
        ));
        
        if (!empty($posts)) {
            $post = $posts[0];
            $wp_query->init();
            $wp_query->set('p', $post->ID);
            $wp_query->set('post_type', 'test_cpt');
            $wp_query->is_single = true;
            $wp_query->is_singular = true;
            $wp_query->is_home = false;
            $wp_query->query(array('p' => $post->ID, 'post_type' => 'test_cpt'));
            
            $GLOBALS['post'] = $wp_query->post;
            setup_postdata($wp_query->post);
            
            $template = locate_template('single-test_cpt.php');
            if (!$template) $template = locate_template('single.php');
            if ($template) {
                include($template);
                exit;
            }
        }
    }
}
add_action('template_redirect', 'wuchaiwp_test_cpt_redirect', 1);
 */



// ==================== 简化版：动态注册和显示自定义文章类型 ====================
function wuchaiwp_simple_cpt_setup() {
    // 1. 从数据库获取自定义文章类型配置
    $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
    
    // 2. 动态注册自定义文章类型
    foreach ($custom_post_types as $slug => $args) {
        // 设置必要参数
        $args['public'] = true;
        $args['has_archive'] = true;
        $args['rewrite'] = array('slug' => $slug, 'with_front' => false);
        $args['query_var'] = true;
        
        // 确保 supports 参数存在，如果不存在则设置默认值
        if (!isset($args['supports']) || !is_array($args['supports'])) {
            $args['supports'] = array('title', 'editor');
        }
        
        register_post_type($slug, $args);
    }
}
add_action('init', 'wuchaiwp_simple_cpt_setup');

// 3. 自定义永久链接
function wuchaiwp_simple_cpt_permalink($permalink, $post) {
    $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
    if ($post && isset($custom_post_types[$post->post_type])) {
        $permalink = home_url('/' . $post->post_type . '/' . $post->post_name . '/');
    }
    return $permalink;
}
add_filter('post_type_link', 'wuchaiwp_simple_cpt_permalink', 10, 2);



// 4. URL解析和模板加载（支持模板选择）
function wuchaiwp_simple_cpt_redirect() {
    global $wp_query, $post;
    
    if (is_admin()) return;
    
    // 首页不处理自定义文章类型重定向，避免影响默认文章显示
    if (is_front_page() || is_home()) return;
    
    $request = trim($_SERVER['REQUEST_URI'], '/');
    if (strpos($request, '?')) $request = substr($request, 0, strpos($request, '?'));
    
    $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
    $template_settings = get_option('wuchaiwp_post_type_templates', array());
    
    foreach ($custom_post_types as $post_type => $args) {
        // 详情页: /post_type/post_name/
        if (strpos($request, $post_type . '/') === 0) {
            $post_name = substr($request, strlen($post_type) + 1);
            $post_name = trim($post_name, '/');
            
            $posts = get_posts(array(
                'name' => $post_name,
                'post_type' => $post_type,
                'post_status' => 'publish',
                'numberposts' => 1
            ));
            
            if (!empty($posts)) {
                $post = $posts[0];
                
                $wp_query->init();
                $wp_query->set('p', $post->ID);
                $wp_query->set('post_type', $post_type);
                $wp_query->is_single = true;
                $wp_query->is_singular = true;
                $wp_query->is_home = false;
                $wp_query->is_archive = false;
                $wp_query->query(array('p' => $post->ID, 'post_type' => $post_type));
                
                if ($wp_query->have_posts()) {
                    $GLOBALS['post'] = $wp_query->post;
                    setup_postdata($wp_query->post);
                    
                    // 获取用户选择的模板
                    $selected_template = 'default';
                    if (isset($template_settings[$post_type]['single'])) {
                        $selected_template = $template_settings[$post_type]['single'];
                    }
                    
                    // 按优先级查找模板
                    $template_candidates = array();
                    if ($selected_template !== 'default') {
                        $template_candidates[] = 'single-' . $selected_template . '.php';
                        $template_candidates[] = 'templates/single/single-' . $selected_template . '.php';
                    }
                    $template_candidates[] = 'single-' . $post_type . '.php';
                    $template_candidates[] = 'single-custom.php';
                    $template_candidates[] = 'single.php';
                    
                    foreach ($template_candidates as $candidate) {
                        $template = locate_template($candidate);
                        if ($template && file_exists($template)) {
                            include($template);
                            exit;
                        }
                    }
                }
            }
        }
        
        // 归档页: /post_type/
        if ($request === $post_type) {
            $wp_query->init();
            $wp_query->set('post_type', $post_type);
            $wp_query->is_archive = true;
            $wp_query->is_post_type_archive = true;
            $wp_query->is_home = false;
            $wp_query->query(array('post_type' => $post_type));
            
            // 获取用户选择的归档模板
            $selected_template = 'default';
            if (isset($template_settings[$post_type]['archive'])) {
                $selected_template = $template_settings[$post_type]['archive'];
            }
            
            // 按优先级查找模板
            $template_candidates = array();
            if ($selected_template !== 'default') {
                $template_candidates[] = 'archive-' . $selected_template . '.php';
                $template_candidates[] = 'templates/archive/archive-' . $selected_template . '.php';
            }
            $template_candidates[] = 'archive-' . $post_type . '.php';
            $template_candidates[] = 'archive-custom.php';
            $template_candidates[] = 'archive.php';
            
            foreach ($template_candidates as $candidate) {
                $template = locate_template($candidate);
                if ($template && file_exists($template)) {
                    include($template);
                    exit;
                }
            }
        }
    }
}
add_action('template_redirect', 'wuchaiwp_simple_cpt_redirect', 1);




// 5. 激活时刷新规则
function wuchaiwp_simple_cpt_activate() {
    wuchaiwp_simple_cpt_setup();
    flush_rewrite_rules(true);
}
register_activation_hook(__FILE__, 'wuchaiwp_simple_cpt_activate');



// ==================== 修复默认分类显示自定义文章类型 ====================
function wuchaiwp_show_cpt_in_category($query) {
    // 只在前台且是分类归档页面时生效
    if (!is_admin() && $query->is_main_query() && $query->is_category()) {
        // 获取所有自定义文章类型
        $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
        $post_types = array('post'); // 默认文章类型
        
        // 添加所有自定义文章类型
        foreach ($custom_post_types as $post_type => $args) {
            $post_types[] = $post_type;
        }
        
        // 设置查询参数
        $query->set('post_type', $post_types);
    }
}
add_action('pre_get_posts', 'wuchaiwp_show_cpt_in_category');

// 同样修复标签归档页面
function wuchaiwp_show_cpt_in_tag($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_tag()) {
        $custom_post_types = get_option('wuchaiwp_custom_post_types', array());
        $post_types = array('post');
        
        foreach ($custom_post_types as $post_type => $args) {
            $post_types[] = $post_type;
        }
        
        $query->set('post_type', $post_types);
    }
}
add_action('pre_get_posts', 'wuchaiwp_show_cpt_in_tag');


/**
 * 注册博客侧边栏小工具
 */
function wuchaiwp_register_blog_widgets() {
    require_once get_template_directory() . '/inc/widgets/widget-blog-author.php';
    require_once get_template_directory() . '/inc/widgets/widget-blog-categories.php';
    require_once get_template_directory() . '/inc/widgets/widget-blog-popular.php';
    require_once get_template_directory() . '/inc/widgets/widget-blog-recommend.php';
    require_once get_template_directory() . '/inc/widgets/widget-blog-tab.php';
    require_once get_template_directory() . '/inc/widgets/widget-two-column-cards.php';
    require_once get_template_directory() . '/inc/widgets/widget-two-column-list.php';
    require_once get_template_directory() . '/inc/widgets/widget-enterprise-info.php';
    require_once get_template_directory() . '/inc/widgets/widget-enterprise-nav.php';
    require_once get_template_directory() . '/inc/widgets/widget-enterprise-contact.php';
    
    register_widget('Wuchaiwp_Blog_Author_Widget');
    register_widget('Wuchaiwp_Blog_Categories_Widget');
    register_widget('Wuchaiwp_Blog_Popular_Widget');
    register_widget('Wuchaiwp_Blog_Recommend_Widget');
    register_widget('Wuchaiwp_Blog_Tab_Widget');
    register_widget('Wuchaiwp_Two_Column_Cards_Widget');
    register_widget('Wuchaiwp_Two_Column_List_Widget');
    register_widget('Wuchaiwp_Enterprise_Info_Widget');
    register_widget('Wuchaiwp_Enterprise_Nav_Widget');
    register_widget('Wuchaiwp_Enterprise_Contact_Widget');
}
add_action('widgets_init', 'wuchaiwp_register_blog_widgets');

/**
 * 设置默认侧边栏小工具
 */
function wuchaiwp_set_default_widgets() {
    // 检查是否已经设置过默认小工具
    if (get_option('wuchaiwp_default_widgets_set', false)) {
        return;
    }
    
    $sidebars_widgets = get_option('sidebars_widgets', array());
    // 首页侧边栏保持为空，由用户自行配置
    if (empty($sidebars_widgets['sidebar-1'])) {
        $sidebars_widgets['sidebar-1'] = array();
    }
    // 文章详情页侧边栏设置博客相关小工具（只在为空时设置）
    if (empty($sidebars_widgets['single-post-sidebar'])) {
        $sidebars_widgets['single-post-sidebar'] = array(
            'wuchaiwp_blog_author-1',      // 1. 作者信息
            'wuchaiwp_blog_tab-1',          // 2. Tab组件（热门、最新、热评、推荐）
            'wuchaiwp_blog_categories-1'    // 3. 文章分类
            // 热门和推荐不再单独显示，整合到Tab组件中
        );
    }
    // 企业官网侧边栏设置企业相关小工具（只在为空时设置）
    if (empty($sidebars_widgets['enterprise-sidebar'])) {
        $sidebars_widgets['enterprise-sidebar'] = array(
            'wuchaiwp_enterprise_info-1',     // 1. 企业信息
            'wuchaiwp_enterprise_nav-1',      // 2. 快速导航
            'wuchaiwp_enterprise_contact-1'   // 3. 联系方式
        );
    }
    update_option('sidebars_widgets', $sidebars_widgets);
    
    // 只在小工具配置为空时设置默认值
    if (empty(get_option('widget_wuchaiwp_blog_author'))) {
        update_option('widget_wuchaiwp_blog_author', array('1' => array('title' => '👤 关于作者')));
    }
    if (empty(get_option('widget_wuchaiwp_blog_categories'))) {
        update_option('widget_wuchaiwp_blog_categories', array('1' => array('title' => '📁 文章分类', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0)));
    }
    if (empty(get_option('widget_wuchaiwp_blog_popular'))) {
        update_option('widget_wuchaiwp_blog_popular', array('1' => array('title' => '🔥 热门文章', 'number' => 5)));
    }
    if (empty(get_option('widget_wuchaiwp_blog_recommend'))) {
        update_option('widget_wuchaiwp_blog_recommend', array('1' => array('title' => '⭐ 推荐文章', 'number' => 3)));
    }
    
    // 设置企业管理小工具的默认配置
    if (empty(get_option('widget_wuchaiwp_enterprise_info'))) {
        update_option('widget_wuchaiwp_enterprise_info', array(
            '1' => array(
                'title' => '🏢 关于我们',
                'description' => get_bloginfo('description'),
                'link_text' => '了解更多 →',
                'link_url' => '#about'
            )
        ));
    }
    if (empty(get_option('widget_wuchaiwp_enterprise_nav'))) {
        update_option('widget_wuchaiwp_enterprise_nav', array(
            '1' => array(
                'title' => '📌 快速导航',
                'link_text_1' => '产品介绍',
                'link_url_1' => '#products',
                'link_text_2' => '开发日志',
                'link_url_2' => '#news',
                'link_text_3' => '案例参考',
                'link_url_3' => '#cases',
                'link_text_4' => '联系我们',
                'link_url_4' => '#contact'
            )
        ));
    }
    if (empty(get_option('widget_wuchaiwp_enterprise_contact'))) {
        update_option('widget_wuchaiwp_enterprise_contact', array(
            '1' => array(
                'title' => '📞 联系我们',
                'address' => '北京市朝阳区科技园区A座18层',
                'phone' => '400-888-8888',
                'email' => 'contact@example.com',
                'custom_color' => '#667eea'
            )
        ));
    }
    
    // 标记已经设置过默认小工具
    update_option('wuchaiwp_default_widgets_set', true);
}
add_action('after_switch_theme', 'wuchaiwp_set_default_widgets');

/**
 * 主题更新功能 - 自建API首选 + GitHub备用
 */

// 更新配置常量
// ==================== 请修改以下配置 ====================
define('WUCHAIWP_UPDATE_API_URL', 'http://wuchai.net/wp-content/theme-updates/wuchaiwp-update.json'); // 你的API地址
define('WUCHAIWP_GITHUB_REPO', ''); // GitHub仓库格式: username/repo（备用方案）
// 自动从 style.css 获取当前主题版本号
$current_theme = wp_get_theme();
define('WUCHAIWP_CURRENT_VERSION', $current_theme->get('Version'));

// 调试模式：设置为true可以显示调试信息，方便排查问题
//define('WUCHAIWP_UPDATE_DEBUG', true);
// 测试模式：设置为true强制显示更新通知（用于测试）
//define('WUCHAIWP_UPDATE_TEST_MODE', false);

/**
 * 获取主题更新信息（自建API）
 */
function wuchaiwp_get_update_from_api() {
    $response = wp_remote_get(WUCHAIWP_UPDATE_API_URL, array(
        'timeout' => 10,
        'headers' => array('Accept' => 'application/json')
    ));
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!is_array($data) || empty($data['version']) || empty($data['package'])) {
        return false;
    }
    
    return $data;
}

/**
 * 获取主题更新信息（GitHub备用）
 */
function wuchaiwp_get_update_from_github() {
    $api_url = 'https://api.github.com/repos/' . WUCHAIWP_GITHUB_REPO . '/releases/latest';
    
    $response = wp_remote_get($api_url, array(
        'timeout' => 10,
        'headers' => array(
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'Wuchaiwp Theme Update Checker'
        )
    ));
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!is_array($data) || empty($data['tag_name'])) {
        return false;
    }
    
    // GitHub标签通常是v开头，如v2.9
    $version = str_replace('v', '', $data['tag_name']);
    $download_url = '';
    
    // 获取ZIP下载链接
    if (!empty($data['assets']) && is_array($data['assets'])) {
        foreach ($data['assets'] as $asset) {
            if (strpos($asset['name'], '.zip') !== false) {
                $download_url = $asset['browser_download_url'];
                break;
            }
        }
    }
    
    // 如果没有asset，使用GitHub的zip下载URL
    if (empty($download_url)) {
        $download_url = 'https://github.com/' . WUCHAIWP_GITHUB_REPO . '/archive/refs/tags/' . $data['tag_name'] . '.zip';
    }
    
    return array(
        'version' => $version,
        'package' => $download_url,
        'url' => $data['html_url']
    );
}

/**
 * 获取最新更新信息（优先API，备用GitHub）
 */
function wuchaiwp_get_latest_update() {
    // 首选：自建API
    $update_data = wuchaiwp_get_update_from_api();
    
    // 如果API失败，使用GitHub作为备用
    if (!$update_data) {
        $update_data = wuchaiwp_get_update_from_github();
    }
    
    return $update_data;
}

/**
 * 集成到WordPress自动更新系统
 */
function wuchaiwp_auto_update_check($transient) {
    // 如果没有检查过，直接返回
    if (empty($transient->checked)) {
        return $transient;
    }
    
    // 获取更新信息
    $update_data = wuchaiwp_get_latest_update();
    
    if (!$update_data) {
        return $transient;
    }
    
    // 比较版本号
    if (version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) {
        $theme_slug = get_template(); // 获取当前主题slug
        
        $transient->response[$theme_slug] = array(
            'new_version' => $update_data['version'],
            'package' => $update_data['package'],
            'url' => !empty($update_data['url']) ? $update_data['url'] : 'https://github.com/' . WUCHAIWP_GITHUB_REPO,
            'requires' => '4.7',
            'requires_php' => '5.2.4'
        );
    }
    
    return $transient;
}
add_filter('pre_set_site_transient_update_themes', 'wuchaiwp_auto_update_check');

/**
 * 自定义更新信息显示
 */
function wuchaiwp_custom_update_info($result, $action, $args) {
    if ('theme_update' !== $action) {
        return $result;
    }
    
    $theme_slug = get_template();
    
    if (!isset($args['slug']) || $args['slug'] !== $theme_slug) {
        return $result;
    }
    
    $update_data = wuchaiwp_get_latest_update();
    
    if ($update_data && version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) {
        return (object) array(
            'new_version' => $update_data['version'],
            'package' => $update_data['package'],
            'url' => !empty($update_data['url']) ? $update_data['url'] : 'https://github.com/' . WUCHAIWP_GITHUB_REPO
        );
    }
    
    return $result;
}
add_filter('theme_update_checked', 'wuchaiwp_custom_update_info', 10, 3);

/**
 * 管理员通知 - 显示醒目的更新提示
 */
function wuchaiwp_update_notice() {
    
    
    ?>
    <style>
        .notice.notice-success.is-dismissible {
            display: none !important;
        }
    </style>
    <?php
    
    
    // 只在管理员后台显示
    if (!is_admin() || !current_user_can('update_themes')) {
        return;
    }
    
    
    
    // ============ 添加以下代码 ============
    // 获取当前页面
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    
    // 限制只在以下页面显示通知
    $allowed_screens = array(
        'dashboard',      // 仪表盘
        'update-core',    // 更新页面  
        'themes',         // 主题管理页
    );
    
    // 如果不在允许的页面列表中，不显示通知
    if (!in_array($screen->id, $allowed_screens)) {
        return;
    }
    // ============ 添加结束 ============
    
    
    
    // 调试信息
   // $debug_info = array();
    
    // 获取更新信息
    //$update_data = wuchaiwp_get_latest_update();
    
    // 测试模式：强制显示更新通知
    //if (WUCHAIWP_UPDATE_TEST_MODE && !$update_data) {
     //   $update_data = array(
      //      'version' => '2.9',
      //      'package' => 'https://example.com/wuchaiwp.zip',
      //      'url' => 'https://example.com/themes/wuchaiwp/'
      //  );
   // }
    
    // 调试：记录API获取结果
   // if (WUCHAIWP_UPDATE_DEBUG) {
   //     $debug_info[] = 'API地址: ' . WUCHAIWP_UPDATE_API_URL;
    //    $debug_info[] = 'GitHub仓库: ' . WUCHAIWP_GITHUB_REPO;
    //    $debug_info[] = '当前版本: ' . WUCHAIWP_CURRENT_VERSION;
     //   $debug_info[] = '更新数据获取: ' . ($update_data ? '成功' : '失败');
      //  if ($update_data) {
      //      $debug_info[] = '最新版本: ' . $update_data['version'];
       //     $debug_info[] = '下载链接: ' . (!empty($update_data['package']) ? '有' : '无');
      //  }
   // }
    
    // 显示调试信息
   // if (WUCHAIWP_UPDATE_DEBUG) {
    //    ?>
    <!--    <div class="notice notice-info is-dismissible" style="background: #f0f7fd; border-left-color: #0073aa;">
            <h4>🔍 吾侪主题&wuchaiwp更新信息</h4>
            <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 13px;">
                <?php //foreach ($debug_info as $info): ?>
                    <li><?php //echo $info; ?></li>
                <?php //endforeach; ?>
            </ul>
        </div>-->
        <?php
   // }
    
    // 获取当前安装的主题版本
    $theme = wp_get_theme();
    $installed_version = $theme->get('Version');
    
    // 如果没有更新数据，直接返回
    if (!$update_data) {
        return;
    }
    
    // 比较版本号
    if (version_compare($update_data['version'], $installed_version, '>')) {
        $update_url = admin_url('themes.php?page=wuchaiwp-update');
        $download_url = !empty($update_data['package']) ? $update_data['package'] : '';
        ?>
        <div class="notice notice-success is-dismissible wuchaiwp-update-notice" style="display: block !important;">
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <div style="font-size: 40px; line-height: 1;">🚀</div>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 8px 0; font-size: 16px; color: #0073aa;">
                        <?php _e('吾侪主题&wuchaiwp更新可用！', 'wuchaiwp'); ?>
                    </h3>
                    <p style="margin: 0 0 12px 0; color: #666;">
                        <?php printf(
                            __('当前版本: <strong style="color: #333;">%s</strong> → 最新版本: <strong style="color: #00a32a;">%s</strong>', 'wuchaiwp'),
                            WUCHAIWP_CURRENT_VERSION,
                            $update_data['version']
                        ); ?>
                    </p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <?php if (!empty($download_url)): ?>
                            <a href="<?php echo esc_url($download_url); ?>" 
                               class="button button-primary" 
                               target="_blank"
                               style="background: #00a32a; border-color: #00a32a; box-shadow: none;">
                                <span class="dashicons dashicons-download" style="margin-right: 4px;"></span>
                                <?php _e('立即下载更新', 'wuchaiwp'); ?>
                            </a>
                        <?php else: ?>
                            <button class="button button-primary" disabled 
                                    style="background: #ccc; border-color: #ccc; cursor: not-allowed;">
                                <span class="dashicons dashicons-download" style="margin-right: 4px;"></span>
                                <?php _e('暂无下载链接', 'wuchaiwp'); ?>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo esc_url($update_url); ?>" class="button button-secondary">
                            <span class="dashicons dashicons-update" style="margin-right: 4px;"></span>
                            <?php _e('后台更新', 'wuchaiwp'); ?>
                        </a>
                        <?php if (!empty($update_data['url'])): ?>
                            <a href="<?php echo esc_url($update_data['url']); ?>" 
                               class="button button-secondary" 
                               target="_blank">
                                <span class="dashicons dashicons-external" style="margin-right: 4px;"></span>
                                <?php _e('查看详情', 'wuchaiwp'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .wuchaiwp-update-notice {
                border-left: 4px solid #00a32a !important;
                background: linear-gradient(135deg, #f0fff4 0%, #ffffff 100%);
                padding: 15px !important;
                box-shadow: 0 2px 8px rgba(0, 163, 42, 0.15);
            }
            .wuchaiwp-update-notice .button {
                padding: 6px 14px;
            }
            .wuchaiwp-update-notice .button-primary:hover:not(:disabled) {
                background: #008a24 !important;
                border-color: #008a24 !important;
            }
        </style>
        <?php
    }
}
add_action('admin_notices', 'wuchaiwp_update_notice');

/**
 * 在外观菜单显示更新徽章
 */
function wuchaiwp_add_update_badge() {
    global $menu;
    
    // 获取更新信息
    $update_data = wuchaiwp_get_latest_update();
    
    if (!$update_data) {
        return;
    }
    
    // 比较版本号
    if (version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) {
        // 找到外观菜单并添加徽章
        foreach ($menu as $key => $item) {
            if ($item[2] == 'themes.php') {
                $menu[$key][0] .= ' <span class="update-plugins count-1"><span class="plugin-count">1</span></span>';
                break;
            }
        }
    }
}
add_action('admin_menu', 'wuchaiwp_add_update_badge');

/**
 * 自定义主题更新图标
 */
function wuchaiwp_update_icon() {
    ?>
    <style>
        .update-plugins.wuchaiwp-update {
            background-color: #46b450;
        }
    </style>
    <?php
}
add_action('admin_head', 'wuchaiwp_update_icon');

/**
 * 添加仪表盘小部件
 */
function wuchaiwp_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'wuchaiwp_update_widget',
        '吾侪主题&wuchaiwp更新',
        'wuchaiwp_render_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'wuchaiwp_add_dashboard_widget');

/**
 * 渲染仪表盘小部件
 */
function wuchaiwp_render_dashboard_widget() {
    $update_data = wuchaiwp_get_latest_update();
    
    // 测试模式
    if (WUCHAIWP_UPDATE_TEST_MODE && !$update_data) {
        $update_data = array(
            'version' => '2.9',
            'package' => 'http://wuchai.net/wp-content/theme-updates/wuchaiwp-update.json',
            'url' => 'http://wuchai.net/themes/wuchaiwp/'
        );
    }
    
    if ($update_data && version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) {
        ?>
        <div style="padding: 10px; background: #f0fff4; border-radius: 8px; border: 1px solid #c6e8c6;">
            <h4 style="margin: 0 0 8px 0; color: #0073aa;">🚀 主题更新可用!</h4>
            <p style="margin: 0 0 12px 0; color: #666; font-size: 13px;">
                当前: <strong><?php echo WUCHAIWP_CURRENT_VERSION; ?></strong> → 
                最新: <strong style="color: #00a32a;"><?php echo $update_data['version']; ?></strong>
            </p>
            <div style="display: flex; gap: 8px;">
                <?php if (!empty($update_data['package'])): ?>
                    <a href="<?php echo esc_url($update_data['package']); ?>" 
                       class="button button-primary" target="_blank"
                       style="padding: 4px 12px; font-size: 12px;">
                        下载更新
                    </a>
                <?php endif; ?>
                <a href="<?php echo admin_url('themes.php?page=wuchaiwp-update'); ?>" 
                   class="button button-secondary"
                   style="padding: 4px 12px; font-size: 12px;">
                    查看详情
                </a>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div style="padding: 10px; background: #f9f9f9; border-radius: 8px;">
            <h4 style="margin: 0 0 8px 0; color: #666;">✅ 主题已是最新版本</h4>
            <p style="margin: 0; color: #999; font-size: 13px;">
                当前版本: <?php echo WUCHAIWP_CURRENT_VERSION; ?>
            </p>
        </div>
        <?php
    }
}

/**
 * 添加更新页面菜单
 */
function wuchaiwp_add_update_page() {
    add_submenu_page(
        'themes.php',
        '吾侪主题&wuchaiwp题更新',
        '主题更新',
        'manage_options',
        'wuchaiwp-update',
        'wuchaiwp_render_update_page'
    );
}
add_action('admin_menu', 'wuchaiwp_add_update_page', 100);

/**
 * 渲染更新页面
 */
/**
 * 一键更新主题 - AJAX处理
 */
function wuchaiwp_one_click_update() {
    // 安全检查
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    if (!check_ajax_referer('wuchaiwp_update_nonce', 'nonce', false)) {
        wp_send_json_error('安全验证失败');
    }
    
    $update_data = wuchaiwp_get_latest_update();
    
    if (!$update_data || empty($update_data['package'])) {
        wp_send_json_error('未找到更新包');
    }
    
    // 检查版本
    if (!version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) {
        wp_send_json_error('当前已是最新版本');
    }
    
    // WordPress文件系统API
    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();
    global $wp_filesystem;
    
    if (!$wp_filesystem) {
        wp_send_json_error('无法初始化文件系统');
    }
    
    // 临时文件路径
    $temp_file = wp_tempnam('wuchaiwp-update');
    $temp_path = dirname($temp_file) . '/wuchaiwp-update';
    
    // 下载更新包
    $response = wp_remote_get($update_data['package'], array('timeout' => 60));
    
    if (is_wp_error($response)) {
        wp_send_json_error('下载更新包失败: ' . $response->get_error_message());
    }
    
    $body = wp_remote_retrieve_body($response);
    
    if (empty($body)) {
        wp_send_json_error('下载的更新包为空');
    }
    
    // 保存到临时文件
    if (!$wp_filesystem->put_contents($temp_file, $body, FS_CHMOD_FILE)) {
        wp_send_json_error('无法保存更新包');
    }
    
    // 创建临时目录
    if (!$wp_filesystem->mkdir($temp_path, FS_CHMOD_DIR)) {
        $wp_filesystem->delete($temp_file);
        wp_send_json_error('无法创建临时目录');
    }
    
    // 解压ZIP
    $unzip_result = unzip_file($temp_file, $temp_path);
    
    if (is_wp_error($unzip_result)) {
        $wp_filesystem->delete($temp_file);
        $wp_filesystem->delete($temp_path, true);
        wp_send_json_error('解压失败: ' . $unzip_result->get_error_message());
    }
    
    // 获取主题目录
    $theme_dir = get_template_directory();
    $theme_slug = get_template();
    
    // 找到解压后的主题文件夹
    $files = $wp_filesystem->dirlist($temp_path);
    $extracted_dir = '';
    
    foreach ($files as $file) {
        if ($file['type'] == 'd') {
            $extracted_dir = $temp_path . '/' . $file['name'];
            break;
        }
    }
    
    if (empty($extracted_dir) || !$wp_filesystem->is_dir($extracted_dir)) {
        $wp_filesystem->delete($temp_file);
        $wp_filesystem->delete($temp_path, true);
        wp_send_json_error('未找到解压后的主题文件夹');
    }
    
    // 复制文件到主题目录
    $copy_result = copy_dir($extracted_dir, $theme_dir);
    
    if (is_wp_error($copy_result)) {
        $wp_filesystem->delete($temp_file);
        $wp_filesystem->delete($temp_path, true);
        wp_send_json_error('复制文件失败: ' . $copy_result->get_error_message());
    }
    
    // 清理临时文件
    $wp_filesystem->delete($temp_file);
    $wp_filesystem->delete($temp_path, true);
    
    wp_send_json_success(array(
        'message' => '更新成功！主题已升级到 v' . $update_data['version'],
        'version' => $update_data['version']
    ));
}
add_action('wp_ajax_wuchaiwp_one_click_update', 'wuchaiwp_one_click_update');

/**
 * 渲染更新页面
 */
function wuchaiwp_render_update_page() {
    $update_data = wuchaiwp_get_latest_update();
    
    // 测试模式
    if (WUCHAIWP_UPDATE_TEST_MODE && !$update_data) {
        $update_data = array(
            'version' => '2.9',
            'package' => 'https://example.com/wuchaiwp.zip',
            'url' => 'https://example.com/themes/wuchaiwp/',
            'description' => '修复了若干bug，优化了性能，添加了新功能。'
        );
    }
    ?>
    <div class="wrap">
        <h1>🚀 吾侪主题&wuchaiwp更新</h1>
        
        <div class="card" style="max-width: 800px;">
            <h2>当前状态</h2>
            <div style="display: flex; align-items: center; gap: 20px; padding: 20px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    <?php echo ($update_data && version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) ? '🔄' : '✅'; ?>
                </div>
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #333;">
                        <?php echo ($update_data && version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) ? '有新版本可用' : '主题已是最新'; ?>
                    </h3>
                    <p style="margin: 0; color: #666;">
                        当前版本: <strong><?php echo WUCHAIWP_CURRENT_VERSION; ?></strong>
                        <?php if ($update_data): ?>
                            → 最新版本: <strong style="color: #00a32a;"><?php echo $update_data['version']; ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <?php if ($update_data && version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')): ?>
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>更新内容</h2>
            <div style="padding: 20px; background: #f0fff4; border-radius: 8px;">
                <?php if (!empty($update_data['description'])): ?>
                    <p style="color: #333;"><?php echo $update_data['description']; ?></p>
                <?php endif; ?>
                
                <form method="post" action="" style="margin-top: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <?php wp_nonce_field('wuchaiwp_update_nonce', 'wuchaiwp_update_nonce'); ?>
                    <input type="hidden" name="wuchaiwp_update_action" value="update">
                    
                    <?php if (!empty($update_data['package'])): ?>
                        <input type="submit" class="button button-primary" value="一键更新到 v<?php echo $update_data['version']; ?>">
                        <a href="<?php echo esc_url($update_data['package']); ?>" 
                           class="button button-secondary" target="_blank">
                            <span class="dashicons dashicons-download" style="margin-right: 4px;"></span>
                            手动下载更新包
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($update_data['url'])): ?>
                        <a href="<?php echo esc_url($update_data['url']); ?>" 
                           class="button button-secondary" target="_blank">
                            <span class="dashicons dashicons-external" style="margin-right: 4px;"></span>
                            查看更新详情
                        </a>
                    <?php endif; ?>
                </form>
                <style>@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>
                <?php
                // 处理表单提交
                if (isset($_POST['wuchaiwp_update_action']) && $_POST['wuchaiwp_update_action'] == 'update') {
                    // 安全检查
                    if (!current_user_can('manage_options')) {
                        $update_error = '权限不足';
                    } elseif (!wp_verify_nonce($_POST['wuchaiwp_update_nonce'], 'wuchaiwp_update_nonce')) {
                        $update_error = '安全验证失败';
                    } else {
                        // 执行更新
                        $update_data = wuchaiwp_get_latest_update();
                        
                        if (!$update_data || empty($update_data['package'])) {
                            $update_error = '未找到更新包';
                        } elseif (!version_compare($update_data['version'], WUCHAIWP_CURRENT_VERSION, '>')) {
                            $update_error = '当前已是最新版本';
                        } else {
                            // WordPress文件系统API
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                            WP_Filesystem();
                            global $wp_filesystem;
                            
                            if (!$wp_filesystem) {
                                $update_error = '无法初始化文件系统';
                            } else {
                                // 临时文件路径
                                $temp_file = wp_tempnam('wuchaiwp-update');
                                $temp_path = dirname($temp_file) . '/wuchaiwp-update';
                                
                                // 下载更新包
                                $response = wp_remote_get($update_data['package'], array('timeout' => 60));
                                
                                if (is_wp_error($response)) {
                                    $update_error = '下载更新包失败: ' . $response->get_error_message();
                                } else {
                                    $body = wp_remote_retrieve_body($response);
                                    
                                    if (empty($body)) {
                                        $update_error = '下载的更新包为空';
                                    } elseif (!$wp_filesystem->put_contents($temp_file, $body, FS_CHMOD_FILE)) {
                                        $update_error = '无法保存更新包';
                                    } elseif (!$wp_filesystem->mkdir($temp_path, FS_CHMOD_DIR)) {
                                        $wp_filesystem->delete($temp_file);
                                        $update_error = '无法创建临时目录';
                                    } else {
                                        // 解压ZIP
                                        $unzip_result = unzip_file($temp_file, $temp_path);
                                        
                                        if (is_wp_error($unzip_result)) {
                                            $wp_filesystem->delete($temp_file);
                                            $wp_filesystem->delete($temp_path, true);
                                            $update_error = '解压失败: ' . $unzip_result->get_error_message();
                                        } else {
                                            // 获取主题目录
                                            $theme_dir = get_template_directory();
                                            
                                            // 找到解压后的主题文件夹
                                            $files = $wp_filesystem->dirlist($temp_path);
                                            $extracted_dir = '';
                                            
                                            foreach ($files as $file) {
                                                if ($file['type'] == 'd') {
                                                    $extracted_dir = $temp_path . '/' . $file['name'];
                                                    break;
                                                }
                                            }
                                            
                                            if (empty($extracted_dir) || !$wp_filesystem->is_dir($extracted_dir)) {
                                                $wp_filesystem->delete($temp_file);
                                                $wp_filesystem->delete($temp_path, true);
                                                $update_error = '未找到解压后的主题文件夹';
                                            } else {
                                                // 复制文件到主题目录
                                                $copy_result = copy_dir($extracted_dir, $theme_dir);
                                                
                                                if (is_wp_error($copy_result)) {
                                                    $wp_filesystem->delete($temp_file);
                                                    $wp_filesystem->delete($temp_path, true);
                                                    $update_error = '复制文件失败: ' . $copy_result->get_error_message();
                                                } else {
                                                    // 清理临时文件
                                                    $wp_filesystem->delete($temp_file);
                                                    $wp_filesystem->delete($temp_path, true);
                                                    $update_success = '更新成功！主题已升级到 v' . $update_data['version'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                
                <?php if (isset($update_error)): ?>
                <div style="margin-top: 15px; padding: 12px 16px; border-radius: 4px; background: #fef0f0;">
                    <span style="color: #dc3232;">✗ <?php echo $update_error; ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (isset($update_success)): ?>
                <div style="margin-top: 15px; padding: 12px 16px; border-radius: 4px; background: #f0fff4;">
                    <span style="color: #00a32a;">✓ <?php echo $update_success; ?></span>
                    <p style="margin: 8px 0 0 0; color: #666; font-size: 13px;">页面将在3秒后刷新...</p>
                    <script>setTimeout(function(){ location.reload(); }, 3000);</script>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>更新配置</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">自建API地址</th>
                    <td>
                        <code style="background: #f9f9f9; padding: 4px 8px; border-radius: 4px;">
                            <?php echo WUCHAIWP_UPDATE_API_URL; ?>
                        </code>
                        <?php if (strpos(WUCHAIWP_UPDATE_API_URL, 'your-domain') !== false): ?>
                            <p class="description" style="color: #dc3232;">
                                ⚠️ 请修改 <code>functions.php</code> 中的API地址配置
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">GitHub仓库</th>
                    <td>
                        <code style="background: #f9f9f9; padding: 4px 8px; border-radius: 4px;">
                            <?php echo WUCHAIWP_GITHUB_REPO; ?>
                        </code>
                    </td>
                </tr>
                <tr>
                    <th scope="row">更新检查状态</th>
                    <td>
                        <?php if ($update_data): ?>
                            <span style="color: #00a32a;">✓ API连接正常</span>
                        <?php else: ?>
                            <span style="color: #dc3232;">✗ API连接失败</span>
                            <p class="description">请检查API地址是否正确，或配置GitHub仓库作为备用</p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}

/**
 * 处理文章点赞AJAX请求
 */
function wuchaiwp_like_post() {
    if (!isset($_POST['post_id'])) {
        wp_send_json_error('参数错误');
    }
    
    $post_id = intval($_POST['post_id']);
    $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : 'like';
    
    if (!get_post($post_id)) {
        wp_send_json_error('文章不存在');
    }
    
    // 获取当前点赞数
    $like_count = get_post_meta($post_id, 'wuchaiwp_like_count', true);
    $like_count = empty($like_count) ? 0 : intval($like_count);
    
    if ($action_type === 'unlike') {
        // 取消点赞
        $like_count = max(0, $like_count - 1);
    } else {
        // 点赞
        $like_count++;
    }
    
    update_post_meta($post_id, 'wuchaiwp_like_count', $like_count);
    
    wp_send_json_success(array('count' => $like_count));
}
add_action('wp_ajax_wuchaiwp_like_post', 'wuchaiwp_like_post');
add_action('wp_ajax_nopriv_wuchaiwp_like_post', 'wuchaiwp_like_post');

/**
 * 设置新文章默认开启评论
 */
function wuchaiwp_default_comment_status($post_id) {
    // 只对新文章生效
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // 获取文章类型
    $post_type = get_post_type($post_id);
    
    // 只对文章类型生效（可根据需要调整）
    if (!in_array($post_type, array('post', 'page'))) {
        return;
    }
    
    // 设置默认评论状态为开启
    update_post_meta($post_id, '_comments_open', 'open');
}
add_action('save_post', 'wuchaiwp_default_comment_status', 10, 1);

/**
 * 修改默认评论状态过滤器
 */
function wuchaiwp_default_comment_status_filter($status) {
    return 'open';
}
add_filter('default_comment_status', 'wuchaiwp_default_comment_status_filter');

/**
 * 修改评论回复按钮文字为汉字
 */
function wuchaiwp_modify_comment_reply_link($args, $comment, $post) {
    $args['reply_text'] = '回复';
    return $args;
}
add_filter('comment_reply_link_args', 'wuchaiwp_modify_comment_reply_link', 10, 3);

/**
 * 在回复评论时添加@用户名
 */
function wuchaiwp_add_reply_at_username($comment_text, $comment) {
    // 检查是否有父评论（即是否是回复）
    if ($comment->comment_parent > 0) {
        // 获取父评论
        $parent_comment = get_comment($comment->comment_parent);
        if ($parent_comment) {
            $parent_author = $parent_comment->comment_author;
            // 添加@用户名
            $comment_text = '<span class="reply-to">@' . esc_html($parent_author) . '</span>' . $comment_text;
        }
    }
    return $comment_text;
}
add_filter('comment_text', 'wuchaiwp_add_reply_at_username', 10, 2);

/**
 * 修改评论回复标题文字为汉字
 */
function wuchaiwp_modify_comment_replies_title($title) {
    $title = str_replace('Replies to', '回复', $title);
    return $title;
}
add_filter('comment_form_title', 'wuchaiwp_modify_comment_replies_title');
add_filter('comments_title', 'wuchaiwp_modify_comment_replies_title');

/**
 * 标题跳转链接功能
 * 允许在页面/文章编辑页设置自定义跳转链接
 */
function wuchaiwp_title_permalink_filter($permalink, $post) {
    if (!is_admin() && is_singular()) {
        $title_link = get_post_meta($post->ID, 'wuchaiwp_enterprise_title_link', true);
        if (!empty($title_link)) {
            return esc_url($title_link);
        }
    }
    return $permalink;
}
add_filter('post_link', 'wuchaiwp_title_permalink_filter', 10, 2);
add_filter('page_link', 'wuchaiwp_title_permalink_filter', 10, 2);
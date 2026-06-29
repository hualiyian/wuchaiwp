<?php
/**
 * Twenty Seventeen: Customizer
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function wuchaiwp_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	// 升级Pro专业版
	$wp_customize->add_section(
		'upgrade_pro',
		array(
			'title'    => __( '升级 Pro 专业版', 'wuchaiwp' ),
			'priority' => 5,
		)
	);

	$wp_customize->add_setting(
		'upgrade_pro_link',
		array(
			'default' => '',
		)
	);

	$wp_customize->add_control(
		'upgrade_pro_link',
		array(
			'label'       => __( '解锁更多高级功能', 'wuchaiwp' ),
			'description' => sprintf( __( '<strong>升级到 Pro 专业版</strong>，解锁更多高级功能：自定义布局、高级颜色方案、专业小工具等。<a href="%s" target="_blank" style="color: #0073aa; font-weight: bold;">立即下载 Pro 版本</a>', 'wuchaiwp' ), 'http://wuchai.net/themes/' ),
			'section'     => 'upgrade_pro',
			'type'        => 'hidden',
		)
	);

	// 添加样式设置面板
	$wp_customize->add_section(
		'styling_options',
		array(
			'title'    => __( '样式设置', 'wuchaiwp' ),
			'priority' => 120,
		)
	);

	// 首页内容与菜单间距
	$wp_customize->add_setting(
		'frontpage_content_menu_spacing',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'frontpage_content_menu_spacing',
		array(
			'label'       => __( '首页内容与菜单间距（em）', 'wuchaiwp' ),
			'description' => __( '控制首页内容区域与顶部菜单的距离，默认值: 2.5', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 5,
				'step' => 0.1,
			),
		)
	);

	// 内容页内容与菜单间距
	$wp_customize->add_setting(
		'contentpage_content_menu_spacing',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'contentpage_content_menu_spacing',
		array(
			'label'       => __( '内容页内容与菜单间距（em）', 'wuchaiwp' ),
			'description' => __( '控制文章页、页面等内容区域与顶部菜单的距离，默认值: 2.5', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 5,
				'step' => 0.1,
			),
		)
	);

	// 标题字体大小
	$wp_customize->add_setting(
		'title_font_size',
		array(
			'default'           => 24,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'title_font_size',
		array(
			'label'       => __( '文章标题字体大小（px）', 'wuchaiwp' ),
			'description' => __( '默认值: 24', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 16,
				'max'  => 48,
				'step' => 1,
			),
		)
	);

	// 正文字体大小
	$wp_customize->add_setting(
		'body_font_size',
		array(
			'default'           => 15,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'body_font_size',
		array(
			'label'       => __( '正文字体大小（px）', 'wuchaiwp' ),
			'description' => __( '默认值: 15', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 20,
				'step' => 1,
			),
		)
	);

	// 字体颜色
	$wp_customize->add_setting(
		'body_text_color',
		array(
			'default'           => '#333333',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'body_text_color',
			array(
				'label'    => __( '正文字体颜色', 'wuchaiwp' ),
				'section'  => 'styling_options',
			)
		)
	);

	// 标题颜色
	$wp_customize->add_setting(
		'title_text_color',
		array(
			'default'           => '#333333',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'title_text_color',
			array(
				'label'    => __( '标题颜色', 'wuchaiwp' ),
				'section'  => 'styling_options',
			)
		)
	);

	// 链接颜色
	$wp_customize->add_setting(
		'link_color',
		array(
			'default'           => '#222222',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'link_color',
			array(
				'label'    => __( '链接颜色', 'wuchaiwp' ),
				'section'  => 'styling_options',
			)
		)
	);

	// 链接下划线样式
	$wp_customize->add_setting(
		'link_underline',
		array(
			'default'           => 'none',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_underline',
		)
	);

	$wp_customize->add_control(
		'link_underline',
		array(
			'label'   => __( '链接下划线样式', 'wuchaiwp' ),
			'section' => 'styling_options',
			'type'    => 'radio',
			'choices' => array(
				'none'       => __( '无', 'wuchaiwp' ),
				'underline'  => __( '下划线', 'wuchaiwp' ),
				'hover'      => __( '悬停时显示', 'wuchaiwp' ),
			),
		)
	);

	// 段落间距（段落之间的空行）
	$wp_customize->add_setting(
		'paragraph_spacing',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'paragraph_spacing',
		array(
			'label'       => __( '段落间距（空行）', 'wuchaiwp' ),
			'description' => __( '控制段落之间的空行间距，单位em，默认值: 1.5。可设置负数让段落重叠', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 3,
				'step' => 0.05,
			),
		)
	);

	// 段落内空行间距（行高）
	$wp_customize->add_setting(
		'empty_line_height',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'empty_line_height',
		array(
			'label'       => __( '段落内空行间距', 'wuchaiwp' ),
			'description' => __( '控制段落内部换行后的行间距，单位em，默认值: 1.66', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 3,
				'step' => 0.1,
			),
		)
	);

	// 侧边栏行间距
	$wp_customize->add_setting(
		'sidebar_line_height',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'sidebar_line_height',
		array(
			'label'       => __( '侧边栏行间距', 'wuchaiwp' ),
			'description' => __( '控制侧边栏内容的行间距，单位em，默认值: 1.6', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 2.5,
				'step' => 0.1,
			),
		)
	);

	// 侧边栏段落间距（空行）
	$wp_customize->add_setting(
		'sidebar_paragraph_spacing',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'sidebar_paragraph_spacing',
		array(
			'label'       => __( '侧边栏段落间距（空行）', 'wuchaiwp' ),
			'description' => __( '控制侧边栏段落之间的空行间距，单位em，默认值: 1.5', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => -1,
				'max'  => 3,
				'step' => 0.05,
			),
		)
	);

	// 侧边栏小工具间距
	$wp_customize->add_setting(
		'sidebar_widget_spacing',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_number',
		)
	);

	$wp_customize->add_control(
		'sidebar_widget_spacing',
		array(
			'label'       => __( '侧边栏小工具间距', 'wuchaiwp' ),
			'description' => __( '控制侧边栏各小工具之间的垂直间距，单位em，默认值: 3', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 5,
				'step' => 0.1,
			),
		)
	);

	// 小工具列表项边框
	$wp_customize->add_setting(
		'hide_widget_border',
		array(
			'default'           => false,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	// 隐藏移动端菜单
	$wp_customize->add_setting(
		'hide_mobile_menu',
		array(
			'default'           => false,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'hide_mobile_menu',
		array(
			'label'       => __( '隐藏移动端菜单', 'wuchaiwp' ),
			'description' => __( '勾选后在移动端设备上隐藏汉堡菜单按钮', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'checkbox',
		)
	);

	// 移动端侧边栏菜单
	$wp_customize->add_setting(
		'mobile_sidebar_menu',
		array(
			'default'           => 'default',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_mobile_menu',
		)
	);

	$wp_customize->add_control(
		'mobile_sidebar_menu',
		array(
			'label'       => __( '移动端侧边栏菜单样式', 'wuchaiwp' ),
			'description' => __( '选择移动端侧边栏菜单的显示方式', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'radio',
			'choices'     => array(
				'default'   => __( '默认下拉菜单', 'wuchaiwp' ),
				'sidebar'   => __( '侧边栏滑出菜单', 'wuchaiwp' ),
			),
		)
	);

	// 移动端菜单按钮位置
	$wp_customize->add_setting(
		'mobile_menu_position',
		array(
			'default'           => 'center',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_menu_position',
		)
	);

	$wp_customize->add_control(
		'mobile_menu_position',
		array(
			'label'       => __( '移动端菜单按钮位置', 'wuchaiwp' ),
			'description' => __( '设置移动端菜单按钮在标题栏的位置', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'radio',
			'choices'     => array(
				'left'      => __( '左侧', 'wuchaiwp' ),
				'center'    => __( '居中', 'wuchaiwp' ),
				'right'     => __( '右侧', 'wuchaiwp' ),
			),
		)
	);

	// 显示文章分类标签
	$wp_customize->add_setting(
		'show_post_categories_tags',
		array(
			'default'           => false,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'show_post_categories_tags',
		array(
			'label'       => __( '显示文章分类标签', 'wuchaiwp' ),
			'description' => __( '勾选后在文章内容卡片底部显示分类和标签，点击可进入对应页面', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'checkbox',
		)
	);

	// 摘要图片数量
	$wp_customize->add_setting(
		'excerpt_image_count',
		array(
			'default'           => 1,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'excerpt_image_count',
		array(
			'label'       => __( '摘要图片数量', 'wuchaiwp' ),
			'description' => __( '控制文章摘要中显示的图片数量，0表示不显示图片', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 5,
				'step' => 1,
			),
		)
	);

	// 文章列表布局
	$wp_customize->add_setting(
		'post_layout_columns',
		array(
			'default'           => '1',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_post_layout',
		)
	);

	$wp_customize->add_control(
		'post_layout_columns',
		array(
			'label'       => __( '文章列表布局', 'wuchaiwp' ),
			'description' => __( '控制文章列表的列数显示', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'radio',
			'choices'     => array(
				'1' => __( '单列', 'wuchaiwp' ),
				'2' => __( '两列', 'wuchaiwp' ),
				'3' => __( '三列', 'wuchaiwp' ),
			),
		)
	);

	$wp_customize->add_control(
		'hide_widget_border',
		array(
			'label'       => __( '隐藏小工具列表项边框', 'wuchaiwp' ),
			'description' => __( '勾选后隐藏小工具列表项之间的上下边框线', 'wuchaiwp' ),
			'section'     => 'styling_options',
			'type'        => 'checkbox',
		)
	);



	// 字体选择
	$wp_customize->add_setting(
		'body_font',
		array(
			'default'           => 'default',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_font',
		)
	);

	$wp_customize->add_control(
		'body_font',
		array(
			'label'   => __( '正文字体', 'wuchaiwp' ),
			'section' => 'styling_options',
			'type'    => 'select',
			'choices' => array(
				'default'     => __( '默认字体', 'wuchaiwp' ),
				'sans-serif'  => __( '无衬线体', 'wuchaiwp' ),
				'serif'       => __( '衬线体', 'wuchaiwp' ),
				'monospace'   => __( '等宽字体', 'wuchaiwp' ),
			),
		)
	);

	// 底部居中
	$wp_customize->add_setting(
		'footer_center',
		array(
			'default'           => 'left',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_footer_align',
		)
	);

	$wp_customize->add_control(
		'footer_center',
		array(
			'label'   => __( '底部内容对齐方式', 'wuchaiwp' ),
			'section' => 'styling_options',
			'type'    => 'radio',
			'choices' => array(
				'left'    => __( '左对齐', 'wuchaiwp' ),
				'center'  => __( '居中', 'wuchaiwp' ),
				'right'   => __( '右对齐', 'wuchaiwp' ),
			),
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'blogname',
		array(
			'selector'        => '.site-title a',
			'render_callback' => 'wuchaiwp_customize_partial_blogname',
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'blogdescription',
		array(
			'selector'        => '.site-description',
			'render_callback' => 'wuchaiwp_customize_partial_blogdescription',
		)
	);

	/**
	 * Custom colors.
	 */
	$wp_customize->add_setting(
		'colorscheme',
		array(
			'default'           => 'light',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'wuchaiwp_sanitize_colorscheme',
		)
	);

	$wp_customize->add_setting(
		'colorscheme_hue',
		array(
			'default'           => 250,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint', // The hue is stored as a positive integer.
		)
	);

	$wp_customize->add_control(
		'colorscheme',
		array(
			'type'     => 'radio',
			'label'    => __( 'Color Scheme', 'wuchaiwp' ),
			'choices'  => array(
				'light'  => __( 'Light', 'wuchaiwp' ),
				'dark'   => __( 'Dark', 'wuchaiwp' ),
				'custom' => __( 'Custom', 'wuchaiwp' ),
			),
			'section'  => 'colors',
			'priority' => 5,
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'colorscheme_hue',
			array(
				'mode'     => 'hue',
				'section'  => 'colors',
				'priority' => 6,
			)
		)
	);

	/**
	 * Theme options.
	 */
	$wp_customize->add_section(
		'theme_options',
		array(
			'title'    => __( 'Theme Options', 'wuchaiwp' ),
			'priority' => 130, // Before Additional CSS.
		)
	);

	$wp_customize->add_setting(
		'page_layout',
		array(
			'default'           => 'two-column',
			'sanitize_callback' => 'wuchaiwp_sanitize_page_layout',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'page_layout',
		array(
			'label'           => __( 'Page Layout', 'wuchaiwp' ),
			'section'         => 'theme_options',
			'type'            => 'radio',
			'description'     => __( 'When the two-column layout is assigned, the page title is in one column and content is in the other.', 'wuchaiwp' ),
			'choices'         => array(
				'one-column' => __( 'One Column', 'wuchaiwp' ),
				'two-column' => __( 'Two Column', 'wuchaiwp' ),
			),
			'active_callback' => 'wuchaiwp_is_view_with_layout_option',
		)
	);

	/**
	 * Filters the number of front page sections in Twenty Seventeen.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param int $num_sections Number of front page sections.
	 */
	$num_sections = apply_filters( 'wuchaiwp_front_page_sections', 4 );

	// Create a setting and control for each of the sections available in the theme.
	for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {
		$wp_customize->add_setting(
			'panel_' . $i,
			array(
				'default'           => false,
				'sanitize_callback' => 'absint',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'panel_' . $i,
			array(
				/* translators: %d: The front page section number. */
				'label'           => sprintf( __( 'Front Page Section %d Content', 'wuchaiwp' ), $i ),
				'description'     => ( 1 !== $i ? '' : __( 'Select pages to feature in each area from the dropdowns. Add an image to a section by setting a featured image in the page editor. Empty sections will not be displayed.', 'wuchaiwp' ) ),
				'section'         => 'theme_options',
				'type'            => 'dropdown-pages',
				'allow_addition'  => true,
				'active_callback' => 'wuchaiwp_is_static_front_page',
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'panel_' . $i,
			array(
				'selector'            => '#panel' . $i,
				'render_callback'     => 'wuchaiwp_front_page_section',
				'container_inclusive' => true,
			)
		);
	}
}
add_action( 'customize_register', 'wuchaiwp_customize_register' );

/**
 * Sanitize the page layout options.
 *
 * @param string $input Page layout.
 */
function wuchaiwp_sanitize_page_layout( $input ) {
	$valid = array(
		'one-column' => __( 'One Column', 'wuchaiwp' ),
		'two-column' => __( 'Two Column', 'wuchaiwp' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	}

	return '';
}

/**
 * Sanitize the colorscheme.
 *
 * @param string $input Color scheme.
 */
function wuchaiwp_sanitize_colorscheme( $input ) {
	$valid = array( 'light', 'dark', 'custom' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'light';
}

/**
 * Sanitize number values.
 *
 * @param float $input Number value.
 */
function wuchaiwp_sanitize_number( $input ) {
	return is_numeric( $input ) ? (float) $input : 2.5;
}

/**
 * Sanitize underline options.
 *
 * @param string $input Underline option.
 */
function wuchaiwp_sanitize_underline( $input ) {
	$valid = array( 'none', 'underline', 'hover' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'none';
}

/**
 * Sanitize font options.
 *
 * @param string $input Font option.
 */
function wuchaiwp_sanitize_font( $input ) {
	$valid = array( 'default', 'sans-serif', 'serif', 'monospace' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'default';
}

/**
 * Sanitize footer alignment options.
 *
 * @param string $input Alignment option.
 */
function wuchaiwp_sanitize_footer_align( $input ) {
	$valid = array( 'left', 'center', 'right' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'left';
}

/**
 * Sanitize select options for widget list border.
 *
 * @param string $input Select option.
 */
function wuchaiwp_sanitize_select( $input ) {
	$valid = array( 'show', 'hide' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'show';
}

/**
 * Sanitize post layout options.
 */
function wuchaiwp_sanitize_post_layout( $input ) {
	$valid = array( '1', '2', '3' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return '1';
}

/**
 * Sanitize mobile menu options.
 */
function wuchaiwp_sanitize_mobile_menu( $input ) {
	$valid = array( 'default', 'sidebar' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'default';
}

/**
 * Sanitize mobile menu position options.
 */
function wuchaiwp_sanitize_menu_position( $input ) {
	$valid = array( 'left', 'center', 'right' );

	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}

	return 'center';
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Twenty Seventeen 1.0
 *
 * @see wuchaiwp_customize_register()
 *
 * @return void
 */
function wuchaiwp_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Twenty Seventeen 1.0
 *
 * @see wuchaiwp_customize_register()
 *
 * @return void
 */
function wuchaiwp_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Return whether we're previewing the front page and it's a static page.
 */
function wuchaiwp_is_static_front_page() {
	return ( is_front_page() && ! is_home() );
}

/**
 * Return whether we're on a view that supports a one or two column layout.
 */
function wuchaiwp_is_view_with_layout_option() {
	// This option is available on all pages. It's also available on archives when there isn't a sidebar.
	return ( is_page() || ( is_archive() && ! is_active_sidebar( 'sidebar-1' ) ) );
}

/**
 * Bind JS handlers to instantly live-preview changes.
 */
function wuchaiwp_customize_preview_js() {
	wp_enqueue_script( 'wuchaiwp-customize-preview', get_theme_file_uri( '/assets/js/customize-preview.js' ), array( 'customize-preview' ), '20161002', true );
}
add_action( 'customize_preview_init', 'wuchaiwp_customize_preview_js' );

/**
 * Load dynamic logic for the customizer controls area.
 */
function wuchaiwp_panels_js() {
	wp_enqueue_script( 'wuchaiwp-customize-controls', get_theme_file_uri( '/assets/js/customize-controls.js' ), array(), '20161020', true );
}
add_action( 'customize_controls_enqueue_scripts', 'wuchaiwp_panels_js' );
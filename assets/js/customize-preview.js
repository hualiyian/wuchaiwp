/**
 * File customize-preview.js.
 *
 * Instantly live-update customizer settings in the preview for improved user experience.
 */

(function( $ ) {

	// Collect information from customize-controls.js about which panels are opening.
	wp.customize.bind( 'preview-ready', function() {

		// Initially hide the theme option placeholders on load.
		$( '.panel-placeholder' ).hide();

		wp.customize.preview.bind( 'section-highlight', function( data ) {

			// Only on the front page.
			if ( ! $( 'body' ).hasClass( 'wuchaiwp-front-page' ) ) {
				return;
			}

			// When the section is expanded, show and scroll to the content placeholders, exposing the edit links.
			if ( true === data.expanded ) {
				$( 'body' ).addClass( 'highlight-front-sections' );
				$( '.panel-placeholder' ).slideDown( 200, function() {
					$.scrollTo( $( '#panel1' ), {
						duration: 600,
						offset: { 'top': -70 } // Account for sticky menu.
					});
				});

			// If we've left the panel, hide the placeholders and scroll back to the top.
			} else {
				$( 'body' ).removeClass( 'highlight-front-sections' );
				// Don't change scroll when leaving - it's likely to have unintended consequences.
				$( '.panel-placeholder' ).slideUp( 200 );
			}
		});
	});

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		});
	});
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		});
	});

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css({
					clip: 'rect(1px, 1px, 1px, 1px)',
					position: 'absolute'
				});
				// Add class for different logo styles if title and description are hidden.
				$( 'body' ).addClass( 'title-tagline-hidden' );
			} else {

				// Check if the text color has been removed and use default colors in theme stylesheet.
				if ( ! to.length ) {
					$( '#wuchaiwp-custom-header-styles' ).remove();
				}
				$( '.site-title, .site-description' ).css({
					clip: 'auto',
					position: 'relative'
				});
				$( '.site-branding, .site-branding a, .site-description, .site-description a' ).css({
					color: to
				});
				// Add class for different logo styles if title and description are visible.
				$( 'body' ).removeClass( 'title-tagline-hidden' );
			}
		});
	});

	// Color scheme.
	wp.customize( 'colorscheme', function( value ) {
		value.bind( function( to ) {

			// Update color body class.
			$( 'body' )
				.removeClass( 'colors-light colors-dark colors-custom' )
				.addClass( 'colors-' + to );
		});
	});

	// Custom color hue.
	wp.customize( 'colorscheme_hue', function( value ) {
		value.bind( function( to ) {

			// Update custom color CSS.
			var style = $( '#custom-theme-colors' ),
				hue = style.data( 'hue' ),
				css = style.html();

			// Equivalent to css.replaceAll, with hue followed by comma to prevent values with units from being changed.
			css = css.split( hue + ',' ).join( to + ',' );
			style.html( css ).data( 'hue', to );
		});
	});

	// Page layouts.
	wp.customize( 'page_layout', function( value ) {
		value.bind( function( to ) {
			if ( 'one-column' === to ) {
				$( 'body' ).addClass( 'page-one-column' ).removeClass( 'page-two-column' );
			} else {
				$( 'body' ).removeClass( 'page-one-column' ).addClass( 'page-two-column' );
			}
		} );
	} );

	// 内容与菜单间距
	wp.customize( 'content_menu_spacing', function( value ) {
		value.bind( function( to ) {
			$( '.site-content' ).css( 'padding-top', to + 'em' );
		} );
	} );

	// 文章标题字体大小
	wp.customize( 'title_font_size', function( value ) {
		value.bind( function( to ) {
			$( '.entry-title' ).css( 'font-size', to + 'px' );
		} );
	} );

	// 正文字体大小
	wp.customize( 'body_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body, button, input, select, textarea' ).css( 'font-size', to + 'px' );
		} );
	} );

	// 正文字体颜色
	wp.customize( 'body_text_color', function( value ) {
		value.bind( function( to ) {
			$( 'body, button, input, select, textarea' ).css( 'color', to );
		} );
	} );

	// 标题颜色
	wp.customize( 'title_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.entry-title, .entry-title a, h1, h2, h3, h4, h5, h6' ).css( 'color', to );
		} );
	} );

	// 链接颜色
	wp.customize( 'link_color', function( value ) {
		value.bind( function( to ) {
			$( 'a' ).css( 'color', to );
		} );
	} );

	// 链接下划线样式
	wp.customize( 'link_underline', function( value ) {
		value.bind( function( to ) {
			var $links = $( 'a, .entry-content a, .entry-summary a, .comment-content a, .widget a, .widget-area a, .site-footer .widget-area a, .posts-navigation a, .entry-content .more-link' );
			if ( 'none' === to ) {
				$links.css( { 'text-decoration': 'none', 'box-shadow': 'none', 'border-bottom': 'none' } );
			} else if ( 'underline' === to ) {
				$links.css( { 'text-decoration': 'underline', 'box-shadow': 'none', 'border-bottom': 'none' } );
			} else if ( 'hover' === to ) {
				$links.css( { 'text-decoration': 'none', 'box-shadow': 'none', 'border-bottom': 'none' } );
				$links.hover(
					function() { $( this ).css( { 'text-decoration': 'underline', 'border-bottom': 'none' } ); },
					function() { $( this ).css( { 'text-decoration': 'none', 'border-bottom': 'none' } ); }
				);
			}
		} );
	} );

	// 段落间距（段落之间的空行）
	wp.customize( 'paragraph_spacing', function( value ) {
		value.bind( function( to ) {
			if ( to !== '' && ! isNaN( to ) ) {
				$( '.entry-content p, .entry-summary p, .comment-content p, article p' ).css( {
					'margin-bottom': to + 'em',
					'margin-top': '0',
					'padding-bottom': '0'
				} );
			} else {
				$( '.entry-content p, .entry-summary p, .comment-content p, article p' ).css( {
					'margin-bottom': '',
					'margin-top': '',
					'padding-bottom': ''
				} );
			}
		} );
	} );

	// 段落内空行间距
	wp.customize( 'empty_line_height', function( value ) {
		value.bind( function( to ) {
			if ( to && ! isNaN( to ) ) {
				$( '.entry-content p, .entry-summary p, .comment-content p, article p' ).css( 'line-height', to );
			} else {
				$( '.entry-content p, .entry-summary p, .comment-content p, article p' ).css( 'line-height', '' );
			}
		} );
	} );

	// 侧边栏行间距
	wp.customize( 'sidebar_line_height', function( value ) {
		value.bind( function( to ) {
			if ( to && ! isNaN( to ) ) {
				$( '.widget-area .widget, .widget-area .widget li, .widget-area .widget p, .widget-area .widget a' ).css( 'line-height', to );
			} else {
				$( '.widget-area .widget, .widget-area .widget li, .widget-area .widget p, .widget-area .widget a' ).css( 'line-height', '' );
			}
		} );
	} );

	// 侧边栏段落间距（空行）
	wp.customize( 'sidebar_paragraph_spacing', function( value ) {
		value.bind( function( to ) {
			var $elements = $( '#secondary .widget-area * p, #secondary .widget-area * li, #secondary .widget-area * a, .widget-area * p, .widget-area * li, .widget-area * a' );
			if ( to !== '' && ! isNaN( to ) ) {
				$elements.css( {
					'margin-bottom': to + 'em',
					'margin-top': '0',
					'padding-bottom': '0'
				} );
			} else {
				$elements.css( {
					'margin-bottom': '',
					'margin-top': '',
					'padding-bottom': ''
				} );
			}
		} );
	} );

	// 侧边栏小工具间距
	wp.customize( 'sidebar_widget_spacing', function( value ) {
		value.bind( function( to ) {
			var $widgets = $( 'body #secondary .widget-area .widget, body .widget-area .widget' );
			if ( to !== '' && ! isNaN( to ) ) {
				$widgets.css( 'padding-bottom', to + 'em' );
			} else {
				$widgets.css( 'padding-bottom', '' );
			}
		} );
	} );

	// 小工具列表项边框
	wp.customize( 'hide_widget_border', function( value ) {
		value.bind( function( to ) {
			var $listItems = $( '.widget-area .widget ul li, .widget-area .widget ol li, .widget-area .widget li' );
			var $rssItems = $( '.widget-area .widget_rss ul li' );
			if ( 1 === parseInt( to ) ) {
				$listItems.css( { 'border-top': 'none', 'border-bottom': 'none', 'padding-top': '0.25em', 'padding-bottom': '0.25em' } );
				$rssItems.css( { 'border-top': 'none', 'border-bottom': 'none', 'padding-top': '0.5em', 'padding-bottom': '0.5em' } );
			} else {
				$listItems.css( { 'border-top': '', 'border-bottom': '', 'padding-top': '', 'padding-bottom': '' } );
				$rssItems.css( { 'border-top': '', 'border-bottom': '', 'padding-top': '', 'padding-bottom': '' } );
			}
		} );
	} );

	// 字体选择
	wp.customize( 'body_font', function( value ) {
		value.bind( function( to ) {
			var font_family = '';
			switch ( to ) {
				case 'sans-serif':
					font_family = 'sans-serif';
					break;
				case 'serif':
					font_family = 'serif';
					break;
				case 'monospace':
					font_family = 'monospace';
					break;
				default:
					font_family = '"Libre Franklin", "Helvetica Neue", helvetica, arial, sans-serif';
			}
			$( 'body, button, input, select, textarea' ).css( 'font-family', font_family );
		} );
	} );

	// 底部对齐
	wp.customize( 'footer_center', function( value ) {
		value.bind( function( to ) {
			$( '.site-footer .wrap, .site-footer .widget-area' ).css( 'text-align', to );
			var justify = 'flex-start';
			if ( 'center' === to ) {
				justify = 'center';
			} else if ( 'right' === to ) {
				justify = 'flex-end';
			}
			$( '.social-navigation' ).css( 'justify-content', justify );
		} );
	} );

	// 隐藏移动端菜单
	wp.customize( 'hide_mobile_menu', function( value ) {
		value.bind( function( to ) {
			if ( 1 === parseInt( to ) ) {
				$( '.menu-toggle, .js .menu-toggle' ).css( 'display', 'none' );
			} else {
				$( '.menu-toggle, .js .menu-toggle' ).css( 'display', '' );
			}
		} );
	} );

	// Whether a header image is available.
	function hasHeaderImage() {
		var image = wp.customize( 'header_image' )();
		return '' !== image && 'remove-header' !== image;
	}

	// Whether a header video is available.
	function hasHeaderVideo() {
		var externalVideo = wp.customize( 'external_header_video' )(),
			video = wp.customize( 'header_video' )();

		return '' !== externalVideo || ( 0 !== video && '' !== video );
	}

	// Toggle a body class if a custom header exists.
	$.each( [ 'external_header_video', 'header_image', 'header_video' ], function( index, settingId ) {
		wp.customize( settingId, function( setting ) {
			setting.bind(function() {
				if ( hasHeaderImage() ) {
					$( document.body ).addClass( 'has-header-image' );
				} else {
					$( document.body ).removeClass( 'has-header-image' );
				}

				if ( ! hasHeaderVideo() ) {
					$( document.body ).removeClass( 'has-header-video' );
				}
			} );
		} );
	} );

} )( jQuery );
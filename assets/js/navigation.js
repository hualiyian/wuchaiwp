/* global wuchaiwpScreenReaderText */
/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

(function( $ ) {
	var masthead, menuToggle, siteNavContain, siteNavigation;

	function initMainNavigation( container ) {

		// Add dropdown toggle that displays child menu items.
		var dropdownToggle = $( '<button />', { 'class': 'dropdown-toggle', 'aria-expanded': false })
			.append( wuchaiwpScreenReaderText.icon )
			.append( $( '<span />', { 'class': 'screen-reader-text', text: wuchaiwpScreenReaderText.expand }) );

		container.find( '.menu-item-has-children > a, .page_item_has_children > a' ).after( dropdownToggle );

		// Set the active submenu dropdown toggle button initial state.
		container.find( '.current-menu-ancestor > button' )
			.addClass( 'toggled-on' )
			.attr( 'aria-expanded', 'true' )
			.find( '.screen-reader-text' )
			.text( wuchaiwpScreenReaderText.collapse );
		// Set the active submenu initial state.
		container.find( '.current-menu-ancestor > .sub-menu' ).addClass( 'toggled-on' );

		container.find( '.dropdown-toggle' ).on( 'click', function( e ) {
			var _this = $( this ),
				screenReaderSpan = _this.find( '.screen-reader-text' );

			e.preventDefault();
			_this.toggleClass( 'toggled-on' );
			_this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );

			_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );

			screenReaderSpan.text( screenReaderSpan.text() === wuchaiwpScreenReaderText.expand ? wuchaiwpScreenReaderText.collapse : wuchaiwpScreenReaderText.expand );
		});
	}

	initMainNavigation( $( '.main-navigation' ) );

	masthead       = $( '#masthead' );
	menuToggle     = masthead.find( '.menu-toggle' );
	siteNavContain = masthead.find( '.main-navigation' );
	siteNavigation = masthead.find( '.main-navigation > div > ul' );

	// Enable menuToggle.
	(function() {

		// Return early if menuToggle is missing.
		if ( ! menuToggle.length ) {
			return;
		}

		// Add an initial value for the attribute.
		menuToggle.attr( 'aria-expanded', 'false' );

		// 检查是否存在侧边栏菜单，如果存在则由 navigation-sidebar.php 处理点击事件
		var sidebarNav = $( '#sidebar-navigation' );
		if ( sidebarNav.length ) {
			// 侧边栏菜单已存在，navigation-sidebar.php 已处理点击事件，跳过此处绑定
			return;
		}

		menuToggle.on( 'click.wuchaiwp', function() {
			// 使用默认下拉菜单
			siteNavContain.toggleClass( 'toggled-on' );

			$( this ).attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
		});
	})();

	// 侧边栏菜单关闭功能
	(function() {
		var sidebarNav = $( '#sidebar-navigation' );
		var sidebarOverlay = $( '#sidebar-overlay' );
		var sidebarClose = $( '#sidebar-close' );
		
		// 点击关闭按钮
		sidebarClose.on( 'click.wuchaiwp', function() {
			sidebarNav.removeClass( 'active' );
			sidebarOverlay.removeClass( 'active' );
			$('body').removeClass('sidebar-open');
			menuToggle.attr( 'aria-expanded', 'false' );
		});
		
		// 点击遮罩层关闭
		sidebarOverlay.on( 'click.wuchaiwp', function() {
			sidebarNav.removeClass( 'active' );
			sidebarOverlay.removeClass( 'active' );
			$('body').removeClass('sidebar-open');
			menuToggle.attr( 'aria-expanded', 'false' );
		});
	})();

	// Fix sub-menus for touch devices and better focus for hidden submenu items for accessibility.
	(function() {
		if ( ! siteNavigation.length || ! siteNavigation.children().length ) {
			return;
		}

		// Toggle `focus` class to allow submenu access on tablets.
		function toggleFocusClassTouchScreen() {
			if ( 'none' === $( '.menu-toggle' ).css( 'display' ) ) {

				$( document.body ).on( 'touchstart.wuchaiwp', function( e ) {
					if ( ! $( e.target ).closest( '.main-navigation li' ).length ) {
						$( '.main-navigation li' ).removeClass( 'focus' );
					}
				});

				siteNavigation.find( '.menu-item-has-children > a, .page_item_has_children > a' )
					.on( 'touchstart.wuchaiwp', function( e ) {
						var el = $( this ).parent( 'li' );

						if ( ! el.hasClass( 'focus' ) ) {
							e.preventDefault();
							el.toggleClass( 'focus' );
							el.siblings( '.focus' ).removeClass( 'focus' );
						}
					});

			} else {
				siteNavigation.find( '.menu-item-has-children > a, .page_item_has_children > a' ).unbind( 'touchstart.wuchaiwp' );
			}
		}

		if ( 'ontouchstart' in window ) {
			$( window ).on( 'resize.wuchaiwp', toggleFocusClassTouchScreen );
			toggleFocusClassTouchScreen();
		}

		siteNavigation.find( 'a' ).on( 'focus.wuchaiwp blur.wuchaiwp', function() {
			$( this ).parents( '.menu-item, .page_item' ).toggleClass( 'focus' );
		});
	})();
})( jQuery );
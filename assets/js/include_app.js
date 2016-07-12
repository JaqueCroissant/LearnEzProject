+function($, window){ 'use strict';
	var app = {
		name: 'Infinity',
		version: '1.0.0'
	};
//
//	app.defaults = {
//		sidebar: {
//			folded: false,
//			theme: 'light',
//			themes: ['light', 'dark']
//		},
//		navbar: {
//			theme: 'primary',
//			themes: ['primary', 'success', 'warning', 'danger', 'pink', 'purple', 'inverse', 'dark']
//		}
//	};

	app.$body = $('body');
//	app.$sidebar = $('#app-aside');
//	app.$navbar = $('#app-navbar');
	app.$main = $('#app-main');

//	app.settings = app.defaults;

//	var appSettings = app.name+"Settings";
//	app.storage = $.localStorage;

//	if (app.storage.isEmpty(appSettings)) {
//		app.storage.set(appSettings, app.settings);
//	} else {
//		app.settings = app.storage.get(appSettings);
//	}
//
//	app.saveSettings = function() {
//		app.storage.set(appSettings, app.settings);
//	};

//	// initialize navbar
//	app.$navbar.removeClass('primary').addClass(app.settings.navbar.theme).addClass('in');
//	app.$body.removeClass('theme-primary').addClass('theme-'+app.settings.navbar.theme);
//
//	// initialize sidebar
//	app.$sidebar.removeClass('light').addClass(app.settings.sidebar.theme).addClass('in');
//	app.settings.sidebar.folded
//		&& app.$sidebar.addClass('folded')
//		&& app.$body.addClass('sb-folded')
//		&& $('#aside-fold').removeClass('is-active');

	// initialize main
	app.$main.addClass('in');
	
	app.init = function() {

		$('[data-plugin]').plugins();
//		$('.scrollable-container').perfectScrollbar();
//		$('.sf-menu').superfish();

		// load some needed libs listed at: LIBS.others => library.js
//		var loadingLibs = loader.load(LIBS["others"]);
		
//		loadingLibs.done(function(){
//
//			$('[data-switchery]').each(function(){
//				var $this = $(this),
//						color = $this.attr('data-color') || '#188ae2',
//						jackColor = $this.attr('data-jackColor') || '#ffffff',
//						size = $this.attr('data-size') || 'default'
//
//				new Switchery(this, {
//					color: color,
//					size: size,
//					jackColor: jackColor
//				});
//			});
//		});
	};

	window.app = app;
}(jQuery, window);

+function($, window) { 'use strict';
	window.app.init();
//	window.app.navbar.init();
//	window.app.sidebar.init();
//	window.app.customizer.init();
}(jQuery, window);
(function ($) {

	"use strict";

	var pluginName = 'siowebGlossar',
		PluginClass;


	/* Enter PluginOptions */
	$[pluginName + 'Default'] = {
		contao: null,
		layerContainer: 'body',
		waitUntilCloseAfterMouseLeave: 300,
		waitUntilOpenAfterMouseEnter: 1500,
		layer: '.glossar_layer',
		controller: '/sioweb/glossar/api/'
	};


	PluginClass = function () {

		var selfObj = this;
		this.requestStarted = null;
		this.item = false;


		this.waitUntilOpenTimeout = false;
		this.openLayerTimeout = false;
		this.waitUntilCloseTimeout = false;
		this.LayerAjaxRequest = false;
		this.termloaded = false;

		this.initOptions = new Object($[pluginName + 'Default']);

		this.init = function (elem) {
			selfObj = this;
			this.item = $(elem);

			if (location.href.indexOf('app_dev') !== -1) {
				this.controller = '/app_dev.php' + this.controller;
			}

			this.loaded();
		};

		this.setupHandler = function (handler) {
			var selector = arguments[1] || selfObj[handler];

			if (selfObj[handler] !== undefined) {
				selfObj['$' + handler] = $(selector);
			} else {
				return;
			}

			if (!selfObj['$' + handler].length) {
				return;
			}

			return true;
		};

		this.loaded = function () {
			selfObj.item.click(selfObj.followTerm);

			selfObj.item.unbind('mouseenter').mouseenter(function () {
				selfObj.termloaded = false;
				clearTimeout(selfObj.waitUntilOpenTimeout);
				selfObj.removeLayer();
				selfObj.waitUntilOpenTimeout = setTimeout(function () {
					selfObj.layerTemplate();

					selfObj.requestStarted = Date.now();
					selfObj.$layer
						.addClass('layer_load')
						.css({
							top: (selfObj.item.offset().top - 20),
							left: (selfObj.item.offset().left - 20),
							'max-width': selfObj.item.data('maxwidth'),
							'max-height': selfObj.item.data('maxheight')
						});

					selfObj.loadLayer();
				}, 200);
			}).mouseout(function () {
				clearTimeout(selfObj.waitUntilOpenTimeout);
				selfObj.waitUntilCloseTimeout = setTimeout(selfObj.removeLayer, selfObj.waitUntilCloseAfterMouseLeave);
			});
		};

		this.layerTemplate = function () {
			$('<div class="glossar_layer">' + selfObj.loadingTemplate() + '</div>').appendTo(selfObj.layerContainer);
			selfObj.setupHandler('layer');
		};

		this.loadingTemplate = function () {
			var template = '';
			template += '<div class="layer_loading">'
				template += '<div class="layer_ring"></div>';
					template += '<div class="layer_content">';
					template += '<span></span>';
				template += '</div>';
			template += '</div>';
			return template;
		};

		this.loadLayer = function () {
			var left = ((selfObj.item.offset().left + selfObj.item.data('maxwidth')) < $(window).width() ? true : false);

			if (selfObj.LayerAjaxRequest) {
				selfObj.LayerAjaxRequest.abort();
			}

			selfObj.LayerAjaxRequest = $.ajax({
				type: "GET",
				method: "GET",
				url: selfObj.controller+'term/'+selfObj.item.data('glossar'),
				data: {
					page: selfObj.contao.page,
					no_ref: (this.href === '' ? 1 : 0),
					REQUEST_TOKEN: selfObj.contao.request_token
				},
				success: function (result) {
					var dateDiff = Math.max(0, selfObj.waitUntilOpenAfterMouseEnter - (Date.now() - selfObj.requestStarted));
					selfObj.openLayerTimeout = setTimeout(function () {
						selfObj.$layer.addClass('layer_loaded').append($($.parseJSON(result).content));
						selfObj.$layer.append('<div class="ce_glossar_close">×</div>').children('.ce_glossar_close')
							.click(function () {
								selfObj.removeLayer();
							});

						selfObj.$layer.find('.glossar_layer_link').click(selfObj.followTerm);

						if (!left) {
							selfObj.$layer.css({ left: 'auto', 'right': 20 });
						}

						if (parseInt(selfObj.$layer.css('top'), 10) + selfObj.$layer.height() > $(window).height() + $(window).scrollTop()) {
							selfObj.$layer.css({ top: 'auto', bottom: 20, position: 'fixed' });
						}

						selfObj.termIsFullyLoaded();

						selfObj.$layer.mouseenter(function () {
							clearTimeout(selfObj.waitUntilCloseTimeout);
						}).mouseleave(function () {
							selfObj.waitUntilCloseTimeout = setTimeout(selfObj.removeLayer, selfObj.waitUntilCloseAfterMouseLeave);
						});
					}, dateDiff);
				}
			});
		};

		this.termIsFullyLoaded = function (e) {
			selfObj.termloaded = true;
			$.ajax({
				type: "POST",
				url: selfObj.controller+'term/load/'+selfObj.item.data('glossar'),
				data: {
					page: selfObj.contao.page,
					no_ref: (this.href === '' ? 1 : 0),
					REQUEST_TOKEN: selfObj.contao.request_token
				}
			});
		};

		this.followTerm = function (e) {
			// e.preventDefault();
			if (this.nodeName.toLowerCase() !== 'a') {
				return;
			}
			selfObj.termloaded = false

			$.ajax({
				type: "POST",
				url: selfObj.controller+'term/follow/'+selfObj.item.data('glossar'),
				data: {
					page: selfObj.contao.page,
					no_ref: (this.href === '' ? 1 : 0),
					REQUEST_TOKEN: selfObj.contao.request_token
				}
			});
		};

		this.removeLayer = function () {
			if (selfObj.$layer !== undefined) {
				selfObj.$layer.remove();
				selfObj.$layer = null;
				delete selfObj.$layer;
			}
			
			clearTimeout(selfObj.waitUntilCloseTimeout);
			clearTimeout(selfObj.openLayerTimeout);

			if (selfObj.termloaded) {
				selfObj.termloaded = false;
				$.ajax({
					type: "POST",
					url: selfObj.controller+'term/close/'+selfObj.item.data('glossar'),
					data: {
						page: selfObj.contao.page,
						no_ref: (this.href === '' ? 1 : 0),
						REQUEST_TOKEN: selfObj.contao.request_token
					}
				});
			}
		}
	};

	$[pluginName] = $.fn[pluginName] = function (settings) {
		var element = typeof this === 'function' ? $('html') : this,
			newData = arguments[1] || {},
			returnElement = [];

		returnElement[0] = element.each(function (k, i) {
			var pluginClass = $.data(this, pluginName);

			if (!settings || typeof settings === 'object' || settings === 'init') {

				if (!pluginClass) {
					if (settings === 'init') {
						settings = arguments[1] || {};
					}
					pluginClass = new PluginClass();

					var newOptions = new Object(pluginClass.initOptions);

					if (settings) {
						newOptions = $.extend(true, {}, newOptions, settings);
					}
					pluginClass = $.extend(newOptions, pluginClass);
					/** Initialisieren. */
					this[pluginName] = pluginClass;
					pluginClass.init(this);
					if (element.prop('tagName').toLowerCase() !== 'html') {
						$.data(this, pluginName, pluginClass);
					}
				} else {
					pluginClass.init(this, 1);
					if (element.prop('tagName').toLowerCase() !== 'html') {
						$.data(this, pluginName, pluginClass);
					}
				}
			} else if (!pluginClass) {
				return;
			} else if (pluginClass[settings]) {
				var method = settings;
				returnElement[1] = pluginClass[method](newData);
			} else {
				return;
			}
		});

		if (returnElement[1] !== undefined) {
			return returnElement[1];
		}

		return returnElement[0];
	};
})(jQuery);
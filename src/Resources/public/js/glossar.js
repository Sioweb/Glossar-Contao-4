(function($){

	var preOpenTimeout= false,
			openLayerTimeout = false,
			glossarTimeout = false,
			LayerAjaxRequest = false,
			layer = false,
			loaded = false,
			glossar_id = false;

	function followTerm() {
		var href = this.href,
				gid = $(this).data('glossar');

		$.ajax({
			type: "POST",
			url:"index.php",
			data: {
				isAjaxRequest :1,
				clicked: 1,
				no_ref: (href === ''?1:0),
				glossar: 1,
				id: gid,
				REQUEST_TOKEN: Contao.request_token
			}
		});
	}


	$(function(){
		layer = $('body')
			.append('<div class="glossar_layer">')
			.find('.glossar_layer');

		$('.tagcloud a').click(function(e){
			$.ajax({
				type: "POST",url:"index.php",
				data: {
					glossar: 1,
					isAjaxRequest: 1,
					cloud: $(this).data('page'),
					REQUEST_TOKEN: Contao.request_token
				}
			});
		});

		$('span.glossar,a.glossar').each(function(key, elem) {
			$(elem).mouseenter(function(e) {
				var glossar = $(this);
				
				clearTimeout(preOpenTimeout);

				removeLayer();
				layer.append('<div class="layer_loading"><div class="layer_ring"></div><div class="layer_content"><span></span></div></div>');
			
				layer
					.addClass('layer_load')
					.css({
						top: (glossar.offset().top - 20),
						left: (glossar.offset().left - Math.round(layer.width() * 1.2)),
						'max-width': glossar.data('maxwidth'),
						'max-height': glossar.data('maxheight')
					});

				preOpenTimeout = setTimeout(function(){loadLayer(glossar);},450);
			}).mouseout(function() {
				clearTimeout(preOpenTimeout);
				glossarTimeout = setTimeout(removeLayer,200);
			});
		});

		$('a.glossar').click(followTerm);
	});

	function loadLayer(glossar) {
		var left = false,
				top = false,
				maxWidth = glossar.data('maxwidth'),
				maxHeight = glossar.data('maxheight');

		left = ((glossar.offset().left + maxWidth) < $(window).width() ? true : false);

	
		if(LayerAjaxRequest) {
			LayerAjaxRequest.abort();
		}

		LayerAjaxRequest = $.ajax({
			type: "POST",
			url:  "index.php",
			data: {
				glossar: 1,
				isAjaxRequest: 1,
				id: glossar.data('glossar'),
				objPageUrl: Contao.objPageUrl,
				REQUEST_TOKEN: Contao.request_token
			},
			success: function(result) {
				openLayerTimeout = setTimeout(function(){
					glossar_id = glossar.data('glossar');
					loaded = true;
					layer.addClass('layer_loaded').append($($.parseJSON(result).content));
					layer.append('<div class="ce_glossar_close">×</div>').children('.ce_glossar_close')
						.click(function(){
							removeLayer();
						});

					layer.find('.glossar_layer_link').click(followTerm);
					
					if(!left) {
						layer.css({left: 'auto','right': 20});
					}

					if(layer.offset().top + layer.height() > $(window).height() + $(window).scrollTop()) {
						layer.css({top: 'auto', bottom: 20, position: 'fixed' });
					}

					$('.ce_glossar_layer').mouseenter(function(){
						clearTimeout(glossarTimeout);
					}).mouseleave(function(){
						glossarTimeout = setTimeout(removeLayer, 750);
					});
				},1000);
			}
		});
	}

	function removeLayer() {
		clearTimeout(glossarTimeout);
		clearTimeout(openLayerTimeout);
		if(loaded) {
			$.ajax({
				type: "POST",
				url:"index.php",
				data: {
					loaded: 1,
					glossar: 1,
					id: glossar_id,
					isAjaxRequest: 1,
					objPageUrl: Contao.objPageUrl,
					REQUEST_TOKEN: Contao.request_token
				}
			});
		}
		$('.layer_loading,.ce_glossar_close').remove();
		layer.css({position: 'absolute'}).removeClass('layer_loaded layer_load').children('.ce_glossar_layer').remove();
		glossar_id = loaded = false;
	}
})(jQuery);

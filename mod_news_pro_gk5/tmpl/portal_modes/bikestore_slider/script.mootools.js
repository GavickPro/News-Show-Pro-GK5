(function($) {
	$(window).load(function(){	
	    $('.gkNspPM-BikestoreSlider').each(function(i, module){
	    	module = $(module);
			var current_offset = 0;
			var arts = module.find('.nspArt');
			var auto_anim = module.attr('data-autoanim');
			var anim_interval = module.attr('data-speed');
			var animation = false;
			var scrollWrap = module.find('.nspArts').first();			
			module.find('.nspArtsScroll').first().css('width', (arts[arts.length-1].outerWidth() * arts.length) + 2);
			var offset = module.find('.nspArt').first().outerWidth();
			var size = module.find('.nspArts').first().outerWidth();
			var scrollSize = (arts[arts.length-1].outerWidth() * arts.length);
			var amountInView = Math.floor(size / offset);
			var totalAmount = module.find('.nspArt').length;
			
			// reset
			current_art = amountInView;
			
			if(totalAmount > amountInView) {
				if(module.find('.nspPrev').length) {
					module.find('.nspPrev').click(function() {
						animation = true;
						if(current_offset <= 0) {
							current_offset = scrollSize - size;
						} else {
							current_offset -= offset;
						}
						
						scrollWrap.animate({"margin-left": -1 * current_offset}, 500);
					});
				}
				
				if(module.find('.nspNext').length) {
					module.find('.nspNext').click(function() {
						animation = true;
						if(current_offset <= scrollSize - size) {
							current_offset += offset;
						} else {
							current_offset = 0;
						}
						
						scrollWrap.animate({"margin-left": -1 * current_offset}, 500);
					});
				}
				
				if(auto_anim){
					function gk_bikestore_slider_autoanim() {
						if(!animation) {
							module.find('.nspNext').trigger("click");
						} else {
							animation = false;
						}
					}
				
					setTimeout(gk_bikestore_slider_autoanim, anim_interval);
				}
			}
		});
	});
})(jQuery);
jQuery(document).ready(function() {
	jQuery('.gkNspPM-NewsSlideshow').each(function(i, module) {
		if(!jQuery(module).hasClass('active')) {
			jQuery(module).addClass('active');
			gkPortalModeNewsSlideshowInit(jQuery(module));
		}
	});
});

var gkPortalModeNewsSlideshowInit = function(module) {
	var current_art = 0;
	var arts = module.find('.gkImage');
	var headline_size = module.find('.nspArtHeadline').first().height();
	var headline_titles = module.find('.nspArtHeadline');
	var auto_anim = module.data('autoanim');
	var anim_speed = module.data('autoanim-speed');
	var anim_interval = module.data('autoanim-time');
	var animation = false;
	var scrollWrap = module.find('.nspArts');
	//var scroller = new Fx.Scroll(scrollWrap, {duration: anim_speed, wheelStops: false});
	var headlines = module.find('.nspTextWrap').first();
	var startItem = 0;

	module.find('.gkImage').first().addClass('active');	
	setTimeout(function() {
		console.log(jQuery(arts[0]).outerWidth(true));
		console.log(arts.length);
		module.find('.gkImagesWrapper').first().css('width', (jQuery(arts[0]).outerWidth(true) * (arts.length+1)) + 2);
	}, 150);
	
	// reset
	scrollWrap.scrollLeft(0);
	//
	if(module.find('.nspBotInterface .gkPrevBtn').length > 0) {
		module.find('.nspBotInterface .gkPrevBtn').click( function() {
			animation = true;
			jQuery(headline_titles[current_art]).animate({opacity : 1}, anim_speed/2);

			if(current_art == 0) {
				current_art = arts.length - 1;
			} else {
				current_art--;
			}
			scrollWrap.animate({scrollLeft : jQuery(arts[current_art]).position().left + 40}, anim_speed);	

			headlines.animate({marginTop: -1*headline_size * current_art}, anim_speed);
			jQuery(headline_titles[current_art]).animate({opacity: 1}, anim_speed * 2);

			arts.each(function(i, art){
				if(i !== current_art && arts[i].hasClass('active')) {
					arts[i].removeClass('active');
				} else if(i == current_art) {
					if(!arts[i].hasClass('active')) arts[i].addClass('active');
				}
			});
		});
	}
	if(module.find('.nspBotInterface .gkNextBtn').length > 0) {
		module.find('.nspBotInterface .gkNextBtn').click( function() {
			animation = true;
			jQuery(headline_titles[current_art]).animate({opacity: 1}, anim_speed/2);
			if(current_art < arts.length - 1) {
				current_art++;
			} else {
				current_art = 0;
			}

			scrollWrap.animate({scrollLeft : jQuery(arts[current_art]).position().left - 40}, anim_speed);	

			headlines.animate({marginTop: -1*headline_size * current_art}, anim_speed);

			jQuery(headline_titles[current_art]).animate({opacity : 1}, anim_speed *2);

			arts.each(function(i, art){
				if(i !== current_art && arts[i].hasClass('active')) {
					arts[i].removeClass('active');
				} else if(i == current_art) {
					if(!arts[i].hasClass('active')) arts[i].addClass('active');
				}
			});
		});
	}

	var arts_pos_start_x = 0;
	var arts_pos_start_y = 0;
	var arts_time_start = 0;
	var arts_swipe = false;
	
	/*module.getElement('.nspImages').addEvent('touchstart', function(e) {
		arts_swipe = true;
		console.log('touchstart');
		if(e.changedTouches.length > 0) {
			arts_pos_start_x = e.changedTouches[0].pageX;
			arts_pos_start_y = e.changedTouches[0].pageY;
			arts_time_start = new Date().getTime();
		}
	});
	
	module.getElement('.nspImages').addEvent('touchmove', function(e) {

		if(e.changedTouches.length > 0 && arts_swipe) {
			if(
				Math.abs(e.changedTouches[0].pageX - arts_pos_start_x) > Math.abs(e.changedTouches[0].pageY - arts_pos_start_y)
			) {
				e.preventDefault();
			} else {
				arts_swipe = false;
			}
		}
	});
	
	module.getElement('.nspImages').addEvent('touchend', function(e) {
		if(e.changedTouches.length > 0 && arts_swipe) {					
			if(
				Math.abs(e.changedTouches[0].pageX - arts_pos_start_x) >= 30 && 
				new Date().getTime() - arts_time_start <= 500
			) {
				if(e.changedTouches[0].pageX - arts_pos_start_x > 0) {
					module.getElement('.nspBotInterface .gkNextBtn').fireEvent("click")
				} else {
					module.getElement('.nspBotInterface .gkPrevBtn').fireEvent("click")
				}
			}
		}
	});
	*/

	if(auto_anim){
		setInterval(function() {
			if(!animation) module.find('.nspBotInterface .gkNextBtn').trigger('click');
			else animation = false;
		}, anim_interval/2);
	}
};

// EOF
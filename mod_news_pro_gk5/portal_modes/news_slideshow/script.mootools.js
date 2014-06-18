window.addEvent('domready', function() {
	document.getElements('.gkNspPM-NewsSlideshow').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeNewsSlideshowInit(module);
		}
	});
});

var gkPortalModeNewsSlideshowInit = function(module) {
	var current_art = 0;
	var arts = module.getElements('.gkImage');
	var headline_size = module.getElement('.nspArtHeadline').getSize().y;
	var headline_titles = module.getElements('.nspArtHeadline');
	var auto_anim = module.getProperty('data-autoanim');
	var anim_speed = module.getProperty('data-autoanim-speed');
	var anim_interval = module.getProperty('data-autoanim-time');
	var animation = false;
	var scrollWrap = module.getElement('.nspArts');
	var scroller = new Fx.Scroll(scrollWrap, {duration: anim_speed, wheelStops: false});
	var headlines = new Fx.Morph(module.getElement('.nspTextWrap'), {duration: anim_speed, wheelStops: false});
	var dimensions = scrollWrap.getSize();
	var startItem = 0;
	var sizeWrap = scrollWrap.getCoordinates();

	module.getElement('.gkImage').addClass('active');	
	setTimeout(function() {
		module.getElement('.gkImagesWrapper').setStyle('width', (arts[0].getSize().x * arts.length) + 2);
	}, 150);
	
	// reset
	scroller.start(0,0);
	//
	if(module.getElement('.nspBotInterface .gkPrevBtn')) {
		module.getElement('.nspBotInterface .gkPrevBtn').addEvent('click', function() {
			animation = true;
			new Fx.Morph(headline_titles[current_art], {duration:anim_speed / 2}).start({'opacity':0});
			if(current_art === 0) {
				current_art = arts.length - 1;
			} else {
				current_art--;
			}
			scroller.toElement(arts[current_art]);
			headlines.start({'margin-top':-1 * headline_size * current_art});
			new Fx.Morph(headline_titles[current_art],{duration:anim_speed * 2}).start({'opacity':1});
			arts.each(function(art,i){
				if(i !== current_art && arts[i].hasClass('active')) {
					arts[i].removeClass('active');
				} else if(i == current_art) {
					if(!arts[i].hasClass('active')) arts[i].addClass('active');
				}
			});
		});
	}
	if(module.getElement('.nspBotInterface .gkNextBtn')) {
		module.getElement('.nspBotInterface .gkNextBtn').addEvent('click', function() {
			animation = true;
			new Fx.Morph(headline_titles[current_art], {duration:anim_speed / 2}).start({'opacity':0});
			if(current_art < arts.length - 1) {
				current_art++;
			} else {
				current_art = 0;
			}

			scroller.toElement(arts[current_art]);				
			headlines.start({'margin-top': -1 * headline_size * current_art});
			new Fx.Morph(headline_titles[current_art],  {duration:anim_speed * 2}).start({'opacity':1});
			arts.each(function(art,i){
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
	
	module.getElement('.nspImages').addEvent('touchstart', function(e) {
		arts_swipe = true;
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
					module.getElement('.nspBotInterface .gkPrevBtn').fireEvent("click");
				} else {
					module.getElement('.nspBotInterface .gkNextBtn').fireEvent("click");
				}
			}
		}
	});


	if(auto_anim){
		setInterval(function() {
			if(!animation) module.getElement('.nspBotInterface .gkNextBtn').fireEvent("click");
			else animation = false;
		}, anim_interval/2);
	}
};

// EOF
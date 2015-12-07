jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-ProductGallery').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeProductGalleryInit(module);
		}
	});
});

var gkPortalModeProductGalleryInit = function(module) {
	module = jQuery(module);
	// set the basic module variables
	module.attr('data-current', 1);
	module.attr('data-blank', 0);
	module.attr('data-stop', 0);
	module.attr('data-all-pages', Math.ceil(module.find('.gkImage').length / module.attr('data-cols')));
	
	// check if buttons exists
	if(module.find('.gkPrevBtn')) {
		module.find('.gkPrevBtn').click(function(e) {
			e.preventDefault();
			module.attr('data-blank', 1);
			gkPortalModeProductGalleryAnim(module, 'prev');
		});
	
		module.find('.gkNextBtn').click(function(e) {
			e.preventDefault();
			module.attr('data-blank', 1);
			gkPortalModeProductGalleryAnim(module, 'next');
		});
		
		var arts_pos_start_x = 0;
		var arts_pos_start_y = 0;
		var arts_time_start = 0;
		var arts_swipe = false;
		
		module.bind('touchstart', function(e) {
			arts_swipe = true;
			var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

			if(touches.length > 0) {
				arts_pos_start_x = touches[0].pageX;
				arts_pos_start_y = touches[0].pageY;
				arts_time_start = new Date().getTime();
			}
		});
		
		module.bind('touchmove', function(e) {
			var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
			
			if(touches.length > 0 && arts_swipe) {
				if(
					Math.abs(touches[0].pageX - arts_pos_start_x) > Math.abs(touches[0].pageY - arts_pos_start_y)
				) {
					e.preventDefault();
				} else {
					arts_swipe = false;
				}
			}
		});
					
		module.bind('touchend', function(e) {
			var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
			
			if(touches.length > 0 && arts_swipe) {									
				if(
					Math.abs(touches[0].pageX - arts_pos_start_x) >= 30 && 
					new Date().getTime() - arts_time_start <= 500
				) {					
					if(touches[0].pageX - arts_pos_start_x > 0) {
						module.attr('data-blank', 1);
						gkPortalModeProductGalleryAnim(module, 'prev');
					} else {
						module.attr('data-blank', 1);
						gkPortalModeProductGalleryAnim(module, 'next');
					}
				}
			}
		});
	}
	
	// check if autoanimation is enabled
	if(module.hasClass('gkAutoAnimation')) {
		setTimeout(function() {
			gkPortalModeProductGalleryAutoAnim(module);
		}, module.attr('data-autoanim-time'));
	}
		
	// add stop event
	module.find('.gkImage').each(function(i, img) {
		img = jQuery(img);
		
		img.mouseenter(function() {
			module.attr('data-stop', 1);
			img.addClass('hover');
		});
		
		img.mouseleave(function() {
			module.attr('data-stop', 0);
			img.removeClass('hover');
		});
	});
	
	module.find('.gkImgOverlay').each(function(i, img) {
		img = jQuery(img);
		
		img.click(function() {
			window.location.href = jQuery(img.parent().find('h4 a').first()).attr('href');
		});
	});
};

var gkPortalModeProductGalleryAutoAnim = function(module) {
	if(module.attr('data-blank') == 1 || module.attr('data-stop') == 1 ) {
		setTimeout(function() {
			module.attr('data-blank', 0);	
			gkPortalModeProductGalleryAutoAnim(module);
		}, module.attr('data-autoanim-time'));
	} else {
		gkPortalModeProductGalleryAnim(module, 'next');
		
		setTimeout(function() {	
			gkPortalModeProductGalleryAutoAnim(module);
		}, module.attr('data-autoanim-time'));
	}
};

var gkPortalModeProductGalleryAnim = function(module, dir) {
	// amount of news per page
	var perPage = module.attr('data-cols');
	var current = module.attr('data-current') * 1.0;
	var allPages = module.attr('data-all-pages');
	var next = 0;
	// select next page
	if(dir == 'next') {
		if(current == allPages) {
			next = 1;
		} else {
			next = current + 1;
		}
	} else if(dir == 'prev') {
		if(current == 1) {
			next = allPages;
		} else {
			next = current - 1;
		}
	}
	// set the current page
	module.attr('data-current', next);
	// hide current elements
	module.find('.gkImage').each(function(i, img) {
		img = jQuery(img);
		
		if(img.hasClass('active')) {
			gkPortalModeProductGalleryImgClass(img, 'active', false, 0);
			gkPortalModeProductGalleryImgClass(img, '', true, 300);
		}
	});
	// show next elements	
	setTimeout(function() {
		module.find('.gkImage').each(function(i, img) {
			img = jQuery(img);
			
			if(i >= (next - 1) * perPage && i < (next * perPage)) {
				gkPortalModeProductGalleryImgClass(img, 'active', false, 0);
				gkPortalModeProductGalleryImgClass(img, 'active show', true, 300);
			}
		});
	}, 300);
};

var gkPortalModeProductGalleryImgClass = function(img, className, delay, time) {
	if(!delay) {
		img.attr('class', 'gkImage ' + className);
	} else {
		setTimeout(function() {
			img.attr('class', 'gkImage ' + className);	
		}, time);
	}
};
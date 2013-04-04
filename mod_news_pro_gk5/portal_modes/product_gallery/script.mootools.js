window.addEvent('domready', function() {
	document.getElements('.gkNspPM-ProductGallery').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeProductGalleryInit(module);
		}
	});
});

var gkPortalModeProductGalleryInit = function(module) {
	// set the basic module variables
	module.setProperty('data-current', 1);
	module.setProperty('data-blank', 0);
	module.setProperty('data-stop', 0);
	module.setProperty('data-all-pages', Math.ceil(module.getElements('.gkImage').length / module.getProperty('data-cols')));
	
	// check if buttons exists
	if(module.getElement('.gkPrevBtn')) {
		module.getElement('.gkPrevBtn').addEvent('click', function(e) {
			e.preventDefault();
			module.setProperty('data-blank', 1);
			gkPortalModeProductGalleryAnim(module, 'prev');
		});
	
		module.getElement('.gkNextBtn').addEvent('click', function(e) {
			e.preventDefault();
			module.setProperty('data-blank', 1);
			gkPortalModeProductGalleryAnim(module, 'next');
		});
		
		var arts_pos_start_x = 0;
		var arts_pos_start_y = 0;
		var arts_time_start = 0;
		var arts_swipe = false;
		
		module.addEvent('touchstart', function(e) {
			arts_swipe = true;
			
			if(e.changedTouches.length > 0) {
				arts_pos_start_x = e.changedTouches[0].pageX;
				arts_pos_start_y = e.changedTouches[0].pageY;
				arts_time_start = new Date().getTime();
			}
		});
		
		module.addEvent('touchmove', function(e) {
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
		
		module.addEvent('touchend', function(e) {
			if(e.changedTouches.length > 0 && arts_swipe) {					
				if(
					Math.abs(e.changedTouches[0].pageX - arts_pos_start_x) >= 30 && 
					new Date().getTime() - arts_time_start <= 500
				) {
					if(e.changedTouches[0].pageX - arts_pos_start_x > 0) {
						module.setProperty('data-blank', 1);
						gkPortalModeProductGalleryAnim(module, 'prev');
					} else {
						module.setProperty('data-blank', 1);
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
		}, module.getProperty('data-autoanim-time'));
	}
	
	// add stop event
	module.getElements('.gkImage').each(function(img) {
		img.addEvent('mouseenter', function() {
			module.setProperty('data-stop', 1);
			img.addClass('hover');
		});
		
		img.addEvent('mouseleave', function() {
			module.setProperty('data-stop', 0);
			img.removeClass('hover');
		});
	});
	
	module.getElements('.gkImgOverlay').each(function(img) {
		img.addEvent('click', function() {
			window.location.href = img.getParent().getElement('h4 a').getProperty('href');
		});
	});
};

var gkPortalModeProductGalleryAutoAnim = function(module) {
	if(module.getProperty('data-blank') == 1 || module.getProperty('data-stop') == 1 ) {
		setTimeout(function() {
			module.setProperty('data-blank', 0);	
			gkPortalModeProductGalleryAutoAnim(module);
		}, module.getProperty('data-autoanim-time'));
	} else {
		gkPortalModeProductGalleryAnim(module, 'next');
		
		setTimeout(function() {	
			gkPortalModeProductGalleryAutoAnim(module);
		}, module.getProperty('data-autoanim-time'));
	}
};

var gkPortalModeProductGalleryAnim = function(module, dir) {
	// amount of news per page
	var perPage = module.getProperty('data-cols');
	var current = module.getProperty('data-current') * 1.0;
	var allPages = module.getProperty('data-all-pages');
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
	module.setProperty('data-current', next);
	// hide current elements
	module.getElements('.gkImage').each(function(img) {
		if(img.hasClass('active')) {
			gkPortalModeProductGalleryImgClass(img, 'active', false, 0);
			gkPortalModeProductGalleryImgClass(img, '', true, 300);
		}
	});
	// show next elements	
	setTimeout(function() {
		module.getElements('.gkImage').each(function(img, i) {
			if(i >= (next - 1) * perPage && i < (next * perPage)) {
				gkPortalModeProductGalleryImgClass(img, 'active', false, 0);
				gkPortalModeProductGalleryImgClass(img, 'active show', true, 300);
			}
		});
	}, 300);
};

var gkPortalModeProductGalleryImgClass = function(img, className, delay, time) {
	if(!delay) {
		img.setProperty('class', 'gkImage ' + className);
	} else {
		setTimeout(function() {
			img.setProperty('class', 'gkImage ' + className);	
		}, time);
	}
};
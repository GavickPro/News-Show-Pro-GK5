window.addEvent('domready', function() {
	document.getElements('.gkNspPM-NewsGallery').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeNewsGalleryInit(module);
		}
	});
});

var gkPortalModeNewsGalleryInit = function(module) {
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
			gkPortalModeNewsGalleryAnim(module, 'prev');
		});
	
		module.getElement('.gkNextBtn').addEvent('click', function(e) {
			e.preventDefault();
			module.setProperty('data-blank', 1);
			gkPortalModeNewsGalleryAnim(module, 'next');
		});
	}
	
	// check if pagination exists
	if(module.getElement('.gkPagination')) {
		module.getElements('.gkPagination li').each(function(el, i) {
			el.addEvent('click', function(e) {
				e.preventDefault();
				
				if(i+1 != module.getProperty('data-current') * 1.0) {
					module.setProperty('data-blank', 1);
					gkPortalModeNewsGalleryAnim(module, i+1);
				}
			});
		});
	}
	
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
					gkPortalModeNewsGalleryAnim(module, 'prev');
				} else {
					module.setProperty('data-blank', 1);
					gkPortalModeNewsGalleryAnim(module, 'next');
				}
			}
		}
	});
	
	// check if autoanimation is enabled
	if(module.hasClass('gkAutoAnimation')) {
		setTimeout(function() {
			gkPortalModeNewsGalleryAutoAnim(module);
		}, module.getProperty('data-autoanim-time'));
	}
	
	// add overlays
	module.getElements('.gkImage').each(function(img) {
		var overlay = new Element('div', { 'class' : 'gkImgOverlay' });
		overlay.innerHTML = '<span></span>';
		overlay.inject(img, 'bottom');
	});
	
	// add stop event
	module.getElements('.gkImage').each(function(img) {
		img.addEvent('mouseenter', function() {
			module.setProperty('data-stop', 1);
			var overlay = img.getElement('.gkImgOverlay');
			var realImg = img.getElement('img');
			overlay.setStyles({
				'margin-left': (-1.0 * (realImg.getSize().x / 2.0)) + "px",
				'width': realImg.getSize().x + "px"
			});
			overlay.setProperty('class', 'gkImgOverlay active');
		});
		
		img.addEvent('mouseleave', function() {
			module.setProperty('data-stop', 0);
			var overlay = img.getElement('.gkImgOverlay');
			overlay.setProperty('class', 'gkImgOverlay');
		});
	});
};

var gkPortalModeNewsGalleryAutoAnim = function(module) {
	if(module.getProperty('data-blank') == 1 || module.getProperty('data-stop') == 1 ) {
		setTimeout(function() {
			module.setProperty('data-blank', 0);	
			gkPortalModeNewsGalleryAutoAnim(module);
		}, module.getProperty('data-autoanim-time'));
	} else {
		gkPortalModeNewsGalleryAnim(module, 'next');
		
		setTimeout(function() {	
			gkPortalModeNewsGalleryAutoAnim(module);
		}, module.getProperty('data-autoanim-time'));
	}
};

var gkPortalModeNewsGalleryAnim = function(module, dir) {
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
	} else {
		if(current != dir) {
			next = dir;
		}
	}
	// set the current page
	module.setProperty('data-current', next);
	// hide current elements
	module.getElements('.gkImage').each(function(img) {
		if(img.hasClass('active')) {
			gkPortalModeNewsGalleryImgClass(img, 'active', false, 0);
			gkPortalModeNewsGalleryImgClass(img, '', true, 300);
		}
	});
	// pagination classes
	if(module.getElement('.gkPagination')) {
		var items = module.getElements('.gkPagination li');
		items.removeClass('active');
		items[next-1].addClass('active');
	}
	// show next elements	
	setTimeout(function() {
		module.getElements('.gkImage').each(function(img, i) {
			if(i >= (next - 1) * perPage && i < (next * perPage)) {
				gkPortalModeNewsGalleryImgClass(img, 'active', false, 0);
				gkPortalModeNewsGalleryImgClass(img, 'active show', true, 300);
			}
		});
	}, 300);
};

var gkPortalModeNewsGalleryImgClass = function(img, className, delay, time) {
	if(!delay) {
		img.setProperty('class', 'gkImage ' + className);
	} else {
		setTimeout(function() {
			img.setProperty('class', 'gkImage ' + className);	
		}, time);
	}
};
window.addEvent('domready', function() {
	document.getElements('.gkNspPM-PortfolioGrid').each(function(module, i) {		
		if(!module.hasClass('active')) {
			module.addClass('active');
			
			module.getElements('.image-resource').each(function(img, i) {				
				setTimeout(function() {
					var new_img = new Element('img', { 'src' : img.getProperty('data-url'), 'class': "image-not-loaded"});
					new_img.inject(img, 'inside');
					new_img.addEvent('load', function() {	
						new_img.removeClass('image-not-loaded');
						new_img.getParent().getElement('.helper-image').remove();
						
						setTimeout(function() {
							new_img.addClass('image-loaded');
							
							if(module.hasClass('with-overlay')) {
								img.getParent().removeClass('loading').addClass('loaded');
							} else {
								img.getParent().getParent().removeClass('loading').addClass('loaded');
							}
						}, 150);
					});
					
					var wrap = img.getParent().getElement('.figcaption');
					
					if(wrap.getProperty('data-popup') == 'false') {
						wrap.addEvent('click', function() {
							window.location.href = wrap.getProperty('data-popup-url');
						});
					}
					
					wrap.getElement('h3 a').addEvent('click', function(e) {
						e.stopPropagation();
					});
				}, i * 200);
			});
		}
		
		var selector = '.gkNspPM-PortfolioGrid .figcaption';
		
		if(module.hasClass('without-overlay')) {
			selector = '.gkNspPM-PortfolioGrid .image-resource';
		}
		
		if(i == 0 && document.getElements(selector + '[data-popup="true"]').length > 0) {
			// Gallery popups
			var photos = document.getElements(selector);
			
			if(photos.length > 0) {
				// photos collection
				var collection = [];
				// create overlay elements
				var overlay = new Element('div', { class: 'gk-photo-overlay' });
				var overlay_prev = new Element('a', { class: 'gk-photo-overlay-prev' });
				var overlay_next = new Element('a', { class: 'gk-photo-overlay-next' });
				// put the element
				overlay.inject(document.body, 'bottom');
				// add events
				overlay.addEvent('click', function() {
					var img = overlay.getElement('img');
					if(img) { img.dispose(); }
					overlay.removeClass('active');
					overlay_prev.removeClass('active');
					overlay_next.removeClass('active');
					setTimeout(function() {
						overlay.setStyle('display', 'none');
					}, 300);
				});
				// prepare links
				photos.each(function(photo, j) {
					collection.push(photo.getProperty('data-popup-url'));
					
					photo.addEvent('click', function(e) {						
						if(module.hasClass('without-overlay')) {
							e.preventDefault();
						}
						
						if(
							(module.hasClass('with-overlay') && e.target.tagName != 'A') ||
							(module.hasClass('without-overlay') && e.target.tagName == 'A')
						) {
							overlay.setStyle('display', 'block');
							
							setTimeout(function() {
								overlay.addClass('active');
								
								setTimeout(function() {
									overlay_prev.addClass('active');
									overlay_next.addClass('active');
								}, 300);
								
								var img = new Element('img', { class: 'loading' });
								img.addEvent('load', function() {
									img.removeClass('loading');
								});
								img.setProperty('src', photo.getProperty('data-popup-url'));
								img.inject(overlay);
							}, 50);
						}
					});
				});
				// if collection is bigger than one photo
				if(collection.length > 1) {
					overlay_prev.inject(overlay, 'top');
					overlay_next.inject(overlay, 'top');
					
					overlay_prev.addEvent('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						var img = overlay.getElement('img');
						if(!img.hasClass('loading')) {
							img.addClass('loading');
						}
						setTimeout(function() {
							var current_img = img.getProperty('src');
							var id = collection.indexOf(current_img);
							var new_img = collection[(id > 0) ? id - 1 : collection.length - 1];
							img.setProperty('src', new_img);
						}, 300);
					});
					
					overlay_next.addEvent('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						var img = overlay.getElement('img');
						if(!img.hasClass('loading')) {
							img.addClass('loading');
						}
						setTimeout(function() {
							var current_img = img.getProperty('src');
							var id = collection.indexOf(current_img);
							var new_img = collection[(id < collection.length - 1) ? id + 1 : 0];
							img.setProperty('src', new_img);
						}, 300);
					});
					
					var photos_pos_start_x = 0;
					var photos_pos_start_y = 0;
					var photos_time_start = 0;
					var photos_swipe = false;
					var swipe_min_move = 50;
					var swipe_max_time = 500;
					
					overlay.addEvent('touchstart', function(e) {
						photos_swipe = true;
						
						if(e.changedTouches.length > 0) {
							photos_pos_start_x = e.changedTouches[0].pageX;
							photos_pos_start_y = e.changedTouches[0].pageY;
							photos_time_start = new Date().getTime();
						}
					});
					
					overlay.addEvent('touchmove', function(e) {
						if(e.changedTouches.length > 0 && photos_swipe) {
							if(
								Math.abs(e.changedTouches[0].pageX - photos_pos_start_x) > Math.abs(e.changedTouches[0].pageY - photos_pos_start_y)
							) {
								e.preventDefault();
							} else {
								photos_swipe = false;
							}
						}
					});
					
					overlay.addEvent('touchend', function(e) {
						if(e.changedTouches.length > 0 && photos_swipe) {					
							if(
								Math.abs(e.changedTouches[0].pageX - photos_pos_start_x) >= $this.swipe_min_move && 
								new Date().getTime() - photos_time_start <= $this.swipe_max_time
							) {
								if(e.changedTouches[0].pageX - photos_pos_start_x > 0) {
									overlay_prev.fireEvent('click');
								} else {
									overlay_next.fireEvent('click');
								}
							}
						}
					});
				}
			}
		}
	});
});

// EOF
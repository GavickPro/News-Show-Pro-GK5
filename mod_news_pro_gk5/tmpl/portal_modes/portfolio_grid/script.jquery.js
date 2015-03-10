jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-PortfolioGrid').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active')) {
			module.addClass('active');
			
			module.find('.image-resource').each(function(i, img) {
				img = jQuery(img);
				
				setTimeout(function() {
					var new_img = jQuery('<img src="' + img.attr('data-url') + '" class="image-not-loaded" />');
					new_img.appendTo(img);
					new_img.load(function() {	
						new_img.removeClass('image-not-loaded');
						new_img.parent().find('.helper-image').remove();
						
						setTimeout(function() {
							new_img.addClass('image-loaded');
							
							if(module.hasClass('with-overlay')) {
								img.parent().removeClass('loading').addClass('loaded');
							} else {
								img.parent().parent().removeClass('loading').addClass('loaded');
							}
						}, 150);
					});
					
					var wrap = img.parent().find('.figcaption');
					
					if(wrap.attr('data-popup') == 'false') {
						wrap.click(function() {
							window.location.href = wrap.attr('data-popup-url');
						});
					}
					
					img.find('h3 a').click(function(e) {
						e.stopPropagation();
					}); 
				}, i * 200);
			});
		}
		
		var selector = '.gkNspPM-PortfolioGrid .figcaption';
		
		if(module.hasClass('without-overlay')) {
			selector = '.gkNspPM-PortfolioGrid .image-resource';
		}
		
		if(i == 0 && jQuery(selector + '[data-popup="true"]').length > 0) {
			// Gallery popups
			var photos = jQuery(selector);
			
			if(photos.length > 0) {
				// photos collection
				var collection = [];
				// create overlay elements
				var overlay = jQuery('<div>', { class: 'gk-photo-overlay' });
				var overlay_prev = jQuery('<a>', { class: 'gk-photo-overlay-prev' });
				var overlay_next = jQuery('<a>', { class: 'gk-photo-overlay-next' });
				// put the element
				overlay.appendTo(jQuery('body'));
				// add events
				overlay.click(function() {
					var img = overlay.find('img');
					if(img) { img.remove(); }
					overlay.removeClass('active');
					overlay_prev.removeClass('active');
					overlay_next.removeClass('active');
					setTimeout(function() {
						overlay.css('display', 'none');
					}, 300);
				});
				// prepare links
				photos.each(function(j, photo) {
					photo = jQuery(photo);
					collection.push(photo.attr('data-popup-url'));
					
					photo.click(function(e) {						
						if(module.hasClass('without-overlay')) {
							e.preventDefault();
						}
						
						if(
							(module.hasClass('with-overlay') && e.target.tagName != 'A') ||
							(module.hasClass('without-overlay') && e.target.tagName == 'A')
						) {
							overlay.css('display', 'block');
							
							setTimeout(function() {
								overlay.addClass('active');
								
								setTimeout(function() {
									overlay_prev.addClass('active');
									overlay_next.addClass('active');
								}, 300);
								
								var img = jQuery('<img>', { class: 'loading' });
								img.load(function() {
									img.removeClass('loading');
								});
								img.attr('src', photo.attr('data-popup-url'));
								img.prependTo(overlay);
							}, 50);
						}
					});
				});
				// if collection is bigger than one photo
				if(collection.length > 1) {
					overlay_prev.appendTo(overlay);
					overlay_next.appendTo(overlay);
					
					overlay_prev.click(function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						var img = overlay.find('img');
						if(!img.hasClass('loading')) {
							img.addClass('loading');
						}
						setTimeout(function() {
							var current_img = img.attr('src');
							var id = collection.indexOf(current_img);
							var new_img = collection[(id > 0) ? id - 1 : collection.length - 1];
							img.attr('src', new_img);
						}, 300);
					});
					
					overlay_next.click(function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						var img = overlay.find('img');
						if(!img.hasClass('loading')) {
							img.addClass('loading');
						}
						setTimeout(function() {
							var current_img = img.attr('src');
							var id = collection.indexOf(current_img);
							var new_img = collection[(id < collection.length - 1) ? id + 1 : 0];
							img.attr('src', new_img);
						}, 300);
					});
					
					var photos_pos_start_x = 0;
					var photos_pos_start_y = 0;
					var photos_time_start = 0;
					var photos_swipe = false;
					var swipe_min_move = 50;
					var swipe_max_time = 500;
					
					overlay.bind('touchstart', function(e) {
						photos_swipe = true;
						var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
			
						if(touches.length > 0) {
							photos_pos_start_x = touches[0].pageX;
							photos_pos_start_y = touches[0].pageY;
							photos_time_start = new Date().getTime();
						}
					});
					
					overlay.bind('touchmove', function(e) {
						var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
						
						if(touches.length > 0 && photos_swipe) {
							if(
								Math.abs(touches[0].pageX - photos_pos_start_x) > Math.abs(touches[0].pageY - photos_pos_start_y)
							) {
								e.preventDefault();
							} else {
								photos_swipe = false;
							}
						}
					});
								
					overlay.bind('touchend', function(e) {
						var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
						
						if(touches.length > 0 && photos_swipe) {									
							if(
								Math.abs(touches[0].pageX - photos_pos_start_x) >= swipe_min_move && 
								new Date().getTime() - photos_time_start <= swipe_max_time
							) {					
								if(touches[0].pageX - photos_pos_start_x > 0) {
									overlay_prev.trigger('click');
								} else {
									overlay_next.trigger('click');
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
jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-Portfolio').each(function(i, module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModePortfolioInit(module);
		}
	});
});

var gkPortalModePortfolioInit = function(module) {
	// button events
	if(jQuery(module).find('.gkLoadMore')) {
		module = jQuery(module);
		var btn = module.find('.gkLoadMore');
		btn = jQuery(btn);
		var preloaded = 0;
		var maxpreloaded = btn.attr('data-max') * 1;
		var pageamount = module.attr('data-cols') * module.attr('data-rows');
		var jsondata = btn.attr('data-toload') != null ? JSON.decode(btn.attr('data-toload').replace(/\\\'/g, '&apos;').replace(/\'/g, '"')) : false; 
		// button
		if(btn.attr('data-text') != 'false') {
			// add the load area
			var loadarea = jQuery('<div class="gkImagesWrapperLoadArea"></div>');
			module.append(loadarea);
			// add the click event
			btn.click(function(e) {
				// if there are thumbnails to load
				if(preloaded < maxpreloaded && !btn.hasClass('inactive')) {
					// prevent the default event
					e.preventDefault();
					e.stopPropagation();
					
					var prevtext = btn.html();
					btn.html('<div class="gkLoader"></div>');
					btn.addClass('inactive');
					
					var start = preloaded;
					var toInject = [];
					for(i = preloaded; i < start + pageamount && i < maxpreloaded; i++) {
						var img = new jQuery('<a></a>');
						img.attr('href', jsondata[i].link);
						img.attr('title', jsondata[i].title);
						img.attr('class', 'gkImage active');
						img.html('<img src="' + jsondata[i].src + '" alt="' + jsondata[i].title + '" />');
						toInject.push(img);
						module.find('.gkImagesWrapperLoadArea').append(img);
						preloaded++;
					}
					
					var imgWrap = module.find('.gkImagesWrapper');
					imgWrap = jQuery(imgWrap);
					imgWrap.css('height', imgWrap.outerHeight(true) + "px");

					var preloaderTimer = setInterval(function() {
						var sum = toInject.length;
						var loaded = 0;

						for(var i = 0; i < sum; i++) {
							if(toInject[i].find('img')[0].complete) {
								loaded++;
							}
						}
						
						if(loaded == sum) {
							
							clearInterval(preloaderTimer);
							for(var i = 0; i < sum; i++) {
								
								module.find('.gkImagesWrapper').append(toInject[i]);
								//toInject[i].inject(module.getElement('.gkImagesWrapper'), 'bottom');
								//toInject[i].addClass('show');
								gkPortalModePortfolioImgClass(toInject[i], 'show', true, 150, i);
								
								
							}
							
							btn.removeClass('inactive');
							
							// height animation
							imgWrap.animate({
								height: imgWrap[0].scrollHeight,
								}, 350, function() {
								   imgWrap.css('height', 'auto');
								 });
							
							// add overlays
							module.find('.gkImage').each(function(i, img) {
								img = jQuery(img);
								if(img.find('.gkImgOverlay').length == 0) {
									// create overlays
									var overlay = new jQuery('<div class="gkImgOverlay"></div>');
									overlay.html('<span></span>');
									img.append(overlay);
									// add overlay events
									img.mouseenter( function() {
										var overlay = img.find('.gkImgOverlay');
										var realImg = img.find('img');
										overlay.css({
											'margin-left': (-1.0 * (realImg.width() / 2.0)) + "px",
											'width': realImg.width() + "px"
										});
										overlay.attr('class', 'gkImgOverlay active');
									});
									
									img.mouseleave( function() {
										var overlay = img.find('.gkImgOverlay');
										overlay.attr('class', 'gkImgOverlay');
									});
								}
							});
							
							if(preloaded < maxpreloaded) {
								btn.html(prevtext);	
							} else {
								btn.html(btn.attr('data-text'));
							}
						}
					}, 1000);
				}
			});
		} 
	}
	
	// add overlays
	module.find('.gkImage').each(function(i, img) {
		img = jQuery(img);
		if(img.find('.gkImgOverlay').length == 0) {
			// create overlays
			var overlay = jQuery('<div class="gkImgOverlay"></div>');
			overlay.html('<span></span>');
			img.append(overlay);
			// add overlay events
			img.mouseenter( function() {
				var overlay = img.find('.gkImgOverlay');
				var realImg = img.find('img');
				overlay.css({
					'margin-left': (-1.0 * (realImg.width() / 2.0)) + "px",
					'width': realImg.width() + "px"
				});
				overlay.attr('class', 'gkImgOverlay active');
			});
			
			img.mouseleave(function() {
				var overlay = img.find('.gkImgOverlay');
				overlay.attr('class', 'gkImgOverlay');
			});
		}
	});
};

var gkPortalModePortfolioImgClass = function(img, className, delay, time, i) {
	i = i || 1;
	
	if(!delay) {
		img.attr('class', 'gkImage ' + className);
	} else {
		setTimeout(function() {
			img.attr('class', 'gkImage ' + className);	
		}, time * i);
	}
};
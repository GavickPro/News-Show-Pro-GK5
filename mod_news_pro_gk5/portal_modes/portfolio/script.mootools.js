window.addEvent('domready', function() {
	document.getElements('.gkNspPM-Portfolio').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModePortfolioInit(module);
		}
	});
});

var gkPortalModePortfolioInit = function(module) {
	// button events
	if(module.getElement('.gkLoadMore')) {
		var btn = module.getElement('.gkLoadMore');
		var preloaded = 0;
		var maxpreloaded = btn.getProperty('data-max') * 1;
		var pageamount = module.getProperty('data-cols') * module.getProperty('data-rows');
		var jsondata = btn.getProperty('data-toload') != null ? JSON.decode(btn.getProperty('data-toload').replace(/\\\'/g, '&apos;').replace(/\'/g, '"')) : false; 
		// button
		if(btn.getProperty('data-text') != 'false') {
			// add the load area
			var loadarea = new Element('div', {
				'class': 'gkImagesWrapperLoadArea'
			});
			loadarea.inject(module, 'bottom');
			// add the click event
			btn.addEvent('click', function(e) {
				// if there are thumbnails to load
				if(preloaded < maxpreloaded && !btn.hasClass('inactive')) {
					// prevent the default event
					e.stop();
					
					var prevtext = btn.innerHTML;
					btn.innerHTML = '<div class="gkLoader"></div>';
					btn.addClass('inactive');
					
					var start = preloaded;
					var toInject = [];
					for(i = preloaded; i < start + pageamount && i < maxpreloaded; i++) {
						var img = new Element('a', {
							'href': jsondata[i].link,
							'title': jsondata[i].title,
							'class': 'gkImage active',
							'html': '<img src="' + jsondata[i].src + '" alt="' + jsondata[i].title + '" />'
						});
						toInject.push(img);
						img.inject(module.getElement('.gkImagesWrapperLoadArea'), 'bottom');
						preloaded++;
					}
					
					var imgWrap = module.getElement('.gkImagesWrapper');
					
					imgWrap.setStyle('height', imgWrap.getSize().y + "px");

					var preloaderTimer = setInterval(function() {
						var sum = toInject.length;
						var loaded = 0;
						
						for(var i = 0; i < sum; i++) {
							if(toInject[i].getElement('img').complete) {
								loaded++;
							}
						}
						
						if(loaded == sum) {
							clearInterval(preloaderTimer);
							for(var i = 0; i < sum; i++) {
								toInject[i].inject(module.getElement('.gkImagesWrapper'), 'bottom');
								//toInject[i].addClass('show');
								gkPortalModePortfolioImgClass(toInject[i], 'show', true, 150, i);
							}
							
							btn.removeClass('inactive');
							
							// height animation
							new Fx.Tween(imgWrap, {
								duration: 350,
								onComplete: function() {
									imgWrap.setStyle('height', 'auto');
								}
							}).start('height', imgWrap.getScrollSize().y);
							
							// add overlays
							module.getElements('.gkImage').each(function(img) {
								if(!img.getElement('.gkImgOverlay')) {
									// create overlays
									var overlay = new Element('div', { 'class' : 'gkImgOverlay' });
									overlay.innerHTML = '<span></span>';
									overlay.inject(img, 'bottom');
									// add overlay events
									img.addEvent('mouseenter', function() {
										var overlay = img.getElement('.gkImgOverlay');
										var realImg = img.getElement('img');
										overlay.setStyles({
											'margin-left': (-1.0 * (realImg.getSize().x / 2.0)) + "px",
											'width': realImg.getSize().x + "px"
										});
										overlay.setProperty('class', 'gkImgOverlay active');
									});
									
									img.addEvent('mouseleave', function() {
										var overlay = img.getElement('.gkImgOverlay');
										overlay.setProperty('class', 'gkImgOverlay');
									});
								}
							});
							
							if(preloaded < maxpreloaded) {
								btn.innerHTML = prevtext;	
							} else {
								btn.innerHTML = btn.getProperty('data-text');
							}
						}
					}, 1000);
				}
			});
		} 
	}
	
	// add overlays
	module.getElements('.gkImage').each(function(img) {
		if(!img.getElement('.gkImgOverlay')) {
			// create overlays
			var overlay = new Element('div', { 'class' : 'gkImgOverlay' });
			overlay.innerHTML = '<span></span>';
			overlay.inject(img, 'bottom');
			// add overlay events
			img.addEvent('mouseenter', function() {
				var overlay = img.getElement('.gkImgOverlay');
				var realImg = img.getElement('img');
				overlay.setStyles({
					'margin-left': (-1.0 * (realImg.getSize().x / 2.0)) + "px",
					'width': realImg.getSize().x + "px"
				});
				overlay.setProperty('class', 'gkImgOverlay active');
			});
			
			img.addEvent('mouseleave', function() {
				var overlay = img.getElement('.gkImgOverlay');
				overlay.setProperty('class', 'gkImgOverlay');
			});
		}
	});
};

var gkPortalModePortfolioImgClass = function(img, className, delay, time, i) {
	i = i || 1;
	
	if(!delay) {
		img.setProperty('class', 'gkImage ' + className);
	} else {
		setTimeout(function() {
			img.setProperty('class', 'gkImage ' + className);	
		}, time * i);
	}
};
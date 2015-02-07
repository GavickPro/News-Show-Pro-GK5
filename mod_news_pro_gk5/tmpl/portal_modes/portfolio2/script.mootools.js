window.addEvent('domready', function() {
	document.getElements('.gkNspPM-Portfolio2').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModePortfolio2Init(module);
		}
	});
});

var gkPortalModePortfolio2Init = function(module) {
	var filter = module.getElement('.gkPortfolioCategories');
	
	if(filter || module.getProperty('data-popup') == '1') {
		var images = module.getElements('.gkImagesWrapper a');
		
		if(filter) {
			var btns = filter.getElements('li');
			
			btns.each(function(btn, i) {
				btn.addEvent('click', function() {
					images.removeClass('active');
					btns.removeClass('active');
					btns[i].addClass('active');
					
					if(i > 0) {
						module.getElements('.gkImagesWrapper a[data-cat="' + btn.innerHTML + '"]').addClass('active');
					} else {
						images.addClass('active');
					}
				});
			});
		}
		// check if popup is enabled
		if(module.getProperty('data-popup') == '1') {
			var popup_content = '<a href="#" class="gkPortfolioPrev">&laquo;</a><a href="#" class="gkPortfolioNext">&raquo;</a><a href="#" class="gkPortfolioClose">&times;</a><div><div class="gkPortfolioImage"></div><div class="gkPortfolioDesc"><h3 class="gkPortfolioTitle"></h3><small class="gkPortfolioCategory"></small><span class="gkPortfolioAuthor"></span><span class="gkPortfolioDate"></span></div></div>';
			var popup = new Element('div', { 'class': 'gkPortfolioPopup', 'html': popup_content });
			popup.inject(module, 'bottom');
			var popup_image_wrap = popup.getElement('.gkPortfolioImage');
			var popup_close = popup.getElement('.gkPortfolioClose');
			var popup_prev = popup.getElement('.gkPortfolioPrev');
			var popup_next = popup.getElement('.gkPortfolioNext');
			var popup_title = popup.getElement('.gkPortfolioTitle');
			var popup_cat = popup.getElement('.gkPortfolioCategory');
			var popup_author = popup.getElement('.gkPortfolioAuthor');
			var popup_date = popup.getElement('.gkPortfolioDate');
			var popup_current_image = 0;
			var blank = false;
			
			popup_close.addEvent('click', function(e) {
				e.preventDefault();
				popup.removeClass('active');
				popup_image_wrap.removeClass('active');
				popup_title.removeClass('active');
				popup_cat.removeClass('active');
				popup_author.removeClass('active');
				popup_date.removeClass('active');
				
				setTimeout(function() {
					popup_image_wrap.innerHTML = '';
					popup.removeClass('activated');
				}, 300);
			});
			
			popup_prev.addEvent('click', function(e) {
				e.preventDefault();
				
				if(!blank) {
					blank = true;
					popup_image_wrap.removeClass('active');
					popup_title.removeClass('active');
					popup_cat.removeClass('active');
					popup_author.removeClass('active');
					popup_date.removeClass('active');
					
					setTimeout(function() {
						var prev = 0;
						
						if(current_popup_image > 0) {
							prev = current_popup_image - 1;
						} else {
							prev = images.length - 1;
						}
						
						showItem(images[prev]);
						current_popup_image = prev;
					}, 350);
				}
			});
			
			popup_next.addEvent('click', function(e) {
				e.preventDefault();
				
				if(!blank) {
					blank = true;
					popup_image_wrap.removeClass('active');
					popup_title.removeClass('active');
					popup_cat.removeClass('active');
					popup_author.removeClass('active');
					popup_date.removeClass('active');
					
					setTimeout(function() {
						var next = 0;
						
						if(current_popup_image < images.length - 1) {
							next = current_popup_image + 1;
						} else {
							next = 0;
						}
						
						showItem(images[next]);
						current_popup_image = next;
					}, 350);
				}
			});
			
			function showItem(img) {
				popup_image_wrap.innerHTML = '<a href="' + img.getProperty('href') + '"><img src="' + img.getProperty('data-img') + '" /></a>';
				popup_title.innerHTML = img.getProperty('title');
				
				if(img.getProperty('data-cat-text') && img.getProperty('data-cat')) {
					popup_cat.innerHTML = '<span>' + img.getProperty('data-cat-text') + '</span>' + img.getProperty('data-cat');
				}
				
				if(img.getProperty('data-author-text') && img.getProperty('data-author')) {
					popup_author.innerHTML = '<span>' + img.getProperty('data-author-text') + '</span>' + img.getProperty('data-author');
				}
				
				if(img.getProperty('data-date-text') && img.getProperty('data-date')) {
					popup_date.innerHTML = '<span>' + img.getProperty('data-date-text') + '</span>' + img.getProperty('data-date');
				}
				
				var image = popup_image_wrap.getElement('img');
				
				var timer = setInterval(function() {
					if(image.complete) {
						clearInterval(timer);
						setTimeout(function() { popup_image_wrap.addClass('active'); }, 100);
						setTimeout(function() { popup_title.addClass('active'); }, 200);
						setTimeout(function() { popup_cat.addClass('active'); }, 300);
						setTimeout(function() { popup_author.addClass('active'); }, 400);
						setTimeout(function() { 
							popup_date.addClass('active'); 
							blank = false;
						}, 500);
					}
				}, 300);
			}
			
			images.each(function(img, i) {
				img.addEvent('click', function(e) {
					if(window.getSize().x > 600) {
						e.preventDefault();
						
						current_popup_image = i;
						popup.addClass('activated');
						showItem(img);
						
						setTimeout(function() {
							popup.addClass('active');
						}, 50);
					}
				});
			});
		}
	}
};

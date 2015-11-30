jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-Portfolio2').each(function(i, module) {
		if(!jQuery(module).hasClass('active')) {
			jQuery(module).addClass('active');
			gkPortalModePortfolio2Init(jQuery(module));
		}
	});
});

var gkPortalModePortfolio2Init = function(module) {
	var filter = module.find('.gkPortfolioCategories');
	
	if(filter.length || module.attr('data-popup') == '1') {
		var images = module.find('.gkImagesWrapper a');
		
		if(filter.length) {
			var btns = filter.find('li');
			
			btns.each(function(i, btn) {
				btn = jQuery(btn);
				btn.click(function() {
					images.removeClass('active');
					btns.removeClass('active');
					jQuery(btns[i]).addClass('active');
					
					if(i > 0) {
						module.find('.gkImagesWrapper a[data-cat="' + btn.html() + '"]').addClass('active');
					} else {
						images.addClass('active');
					}
				});
			});
		}
		// check if popup is enabled
		if(module.attr('data-popup') == '1') {
			var popup_content = '<a href="#" class="gkPortfolioPrev">&laquo;</a><a href="#" class="gkPortfolioNext">&raquo;</a><a href="#" class="gkPortfolioClose">&times;</a><div><div class="gkPortfolioImage"></div><div class="gkPortfolioDesc"><h3 class="gkPortfolioTitle"></h3><small class="gkPortfolioCategory"></small><span class="gkPortfolioAuthor"></span><span class="gkPortfolioDate"></span></div></div>';
			var popup = jQuery('<div class="gkPortfolioPopup">' + popup_content + '</div>');
			module.append(popup);
			var popup_image_wrap = popup.find('.gkPortfolioImage');
			var popup_close = popup.find('.gkPortfolioClose');
			var popup_prev = popup.find('.gkPortfolioPrev');
			var popup_next = popup.find('.gkPortfolioNext');
			var popup_title = popup.find('.gkPortfolioTitle');
			var popup_cat = popup.find('.gkPortfolioCategory');
			var popup_author = popup.find('.gkPortfolioAuthor');
			var popup_date = popup.find('.gkPortfolioDate');
			var popup_current_image = 0;
			var blank = false;
			
			popup_close.click(function(e) {
				e.preventDefault();
				popup.removeClass('active');
				popup_image_wrap.removeClass('active');
				popup_title.removeClass('active');
				popup_cat.removeClass('active');
				popup_author.removeClass('active');
				popup_date.removeClass('active');
				
				setTimeout(function() {
					popup_image_wrap.html('');
					popup.removeClass('activated');
				}, 300);
			});
			
			popup_prev.click(function(e) {
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
			
			popup_next.click(function(e) {
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
				img = jQuery(img);
				popup_image_wrap.html('<a href="' + img.attr('href') + '"><img src="' + img.attr('data-img') + '" /></a>');
				popup_title.html(img.attr('title'));
				
				if(img.attr('data-cat') && img.attr('data-cat-text')) {
					popup_cat.html('<span>' + img.attr('data-cat-text') + '</span>' + img.attr('data-cat'));
				}
				
				if(img.attr('data-author-text') && img.attr('data-author')) {
					popup_author.html('<span>' + img.attr('data-author-text') + '</span>' + img.attr('data-author'));
				}
				
				if(img.attr('data-date-text') && img.attr('data-date')) {
					popup_date.html('<span>' + img.attr('data-date-text') + '</span>' + img.attr('data-date'));
				}
				
				var image = popup_image_wrap.find('img');
				
				var timer = setInterval(function() {
					if(image[0].complete) {
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
			
			images.each(function(i, img) {
				img = jQuery(img);
				img.click(function(e) {
					if(jQuery(window).width() > 600) {
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

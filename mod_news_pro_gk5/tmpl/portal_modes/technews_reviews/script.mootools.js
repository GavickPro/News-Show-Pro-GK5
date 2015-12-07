jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-TechNewsReviews').each(function(i, module) {
		module = jQuery(module);

		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeTechNewsReviewsInit(module);
		}
	});
});

function gkPortalModeTechNewsReviewsInit(module) {
	var list = jQuery('.gk-sidebar-list li');
	var items = jQuery('.gk-content-review');
	var current = 0;
	var circles = [];
	
	list.each(function(i, item) {
		if(ProgressBar && jQuery(items[current]).find('.gk-review-sum-value').length) {
			circles[i] = new ProgressBar.Circle(jQuery(items[i]).find('.gk-review-sum-value')[0], {
			    color: '#07c958',
			    strokeWidth: 4,
			    trailWidth: 4,
			    duration: 1500,
			    easing: 'easeInOut'
			});
		}
	
		jQuery(item).find('a').click(function(e) {
			e.preventDefault();
			list.removeClass('gk-active');
			item.addClass('gk-active');
			current = i;
			items.removeClass('gk-active');
			items[current].addClass('gk-active');
			
			if(ProgressBar) {
				var sum = jQuery(items[i]).find('.gk-review-sum-value').first();
				circles[current].set(0);
				circles[current].animate(jQuery(sum).attr('data-final'));
			}
		});
	});
	
	if(circles[0]) {
		circles[0].animate(jQuery(jQuery(items[0]).find('.gk-review-sum-value')[0]).attr('data-final'));
	}
	
	if(module.attr('data-autoanim') == '1') {
		gkPortalModeTechNewsReviewsAutoanim(module);
	}
}

function gkPortalModeTechNewsReviewsAutoanim(module) {
	setTimeout(function() {
		var list = jQuery('.gk-sidebar-list');
		
		if(list.find('.gk-active').next().length) {
			list.find('.gk-active').next().find('a').trigger('click');
		} else {
			list.find('li').first().find('a').trigger('click');
		}
		
		gkPortalModeTechNewsReviewsAutoanim(module);		
	}, module.attr('data-interval'));
}

// EOF

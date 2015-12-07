jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-TechNewsHeader1').each(function(i, module) {
		module = jQuery(module);

		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeTechNewsHeader1Init(module);
		}
	});
});

var gkPortalModeTechNewsHeader1Init = function(module) {
	module = jQuery(module);
	
	module.find('.gkImage').each(function(i, img) {
		img = jQuery(img);
		
		img.on('mouseenter', function() {
			module.find('.gkImage').removeClass('active');
			img.addClass('active');
		});
		
		img.on('mouseleave', function() {
			img.removeClass('active');
		});
	});
	
	module.on('mouseleave', function() {
		module.find('.gkImage').first().addClass('active');
	});
};

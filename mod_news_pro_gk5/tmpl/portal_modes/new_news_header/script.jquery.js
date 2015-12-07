jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-NewNewsHeader').each(function(i, module) {
		module = jQuery(module);

		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeNewNewsHeaderInit(module);
		}
	});
});

var gkPortalModeNewNewsHeaderInit = function(module) {
	module = jQuery(module);
	
	module.find('.gkImage').each(function(i, img) {
		img = jQuery(img);
		
		img.on('click', function() {
			window.location = img.find('a').attr('href');
		});
	});
};

jQuery(window).load(function() {
	setTimeout(function() {
		jQuery(document).find('.gkNspPM-FrontpageImageOverlay').each(function(i, module) {
			module = jQuery(module);
			
			if(!module.hasClass('active')) {
				module.addClass('active');
			}
		});
	}, 1000);
});

// EOF
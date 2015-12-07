// Centered Title Overlay - Portal Mode

jQuery(window).load(function() {
	setTimeout(function() {
		jQuery(document).find('.gkNspPM-CenteredTitleOverlay').each(function(i, module) {
			module = jQuery(module);
			
			if(!module.hasClass('active')) {
				module.addClass('active');
			}
		});
	}, 1000);
});

// EOF
// Centered Title Overlay - Portal Mode

jQuery(window).load(function() {
	setTimeout(function() {
		jQuery(document).find('.gkNspPM-CenteredTitleOverlay').each(function(i, module) {
			module = jQuery(module);
			
			setTimeout(function() {
				if(!module.hasClass('active')) {
					module.addClass('active');
				}
			}, i * 150);
		});
	}, 1000);
});

// EOF
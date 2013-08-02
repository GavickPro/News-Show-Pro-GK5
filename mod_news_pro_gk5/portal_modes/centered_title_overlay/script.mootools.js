// Centered Title Overlay - Portal Mode

window.addEvent('load', function() {
	setTimeout(function() {
		document.getElements('.gkNspPM-CenteredTitleOverlay').each(function(module, i) {
			module = jQuery(module);
			
			if(!module.hasClass('active')) {
				module.addClass('active');
			}
		});
	}, 1000);
});

// EOF
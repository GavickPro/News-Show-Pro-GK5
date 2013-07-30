// Centered Title Overlay - Portal Mode

window.addEvent('load', function() {
	setTimeout(function() {
		document.getElements('.gkNspPM-CenteredTitleOverlay').each(function(module, i) {
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
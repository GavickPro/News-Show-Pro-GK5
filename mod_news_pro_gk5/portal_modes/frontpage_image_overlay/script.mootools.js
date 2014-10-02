window.addEvent('load', function() {
	setTimeout(function() {
		document.getElements('.gkNspPM-FrontpageImageOverlay').each(function(module) {
			if(!module.hasClass('active')) {
				module.addClass('active');
			}
		});
	}, 1000);
});

// EOF
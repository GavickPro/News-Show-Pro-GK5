window.addEvent('domready', function() {
	document.getElements('.gkNspPM-Highlights').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
		}
	});
});

// EOF
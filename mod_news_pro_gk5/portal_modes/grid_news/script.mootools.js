window.addEvent('domready', function() {
	document.getElements('.gkNspPM-GridNews').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
		}
	});
});

// EOF
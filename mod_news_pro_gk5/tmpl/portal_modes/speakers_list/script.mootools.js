window.addEvent('domready', function() {
	document.getElements('.gkNspPM-SpeakersList').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
		}
	});
});

// EOF
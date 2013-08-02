window.addEvent('domready', function() {
	document.getElements('.gkNspPM-ProductGallery2').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
		}
	});
});
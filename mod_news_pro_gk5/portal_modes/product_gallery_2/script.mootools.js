window.addEvent('domready', function() {
	document.getElements('.gkNspPM-ProductGallery2').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeProductGallery2Init(module);
		}
	});
});

var gkPortalModeProductGallery2Init = function(module) {
	
};
window.addEvent('domready', function() {
	document.getElements('.gkNspPM-GridTitleOverlay').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeGridTitleOverlayInit(module);
		}
	});
});

var gkPortalModeGridTitleOverlayInit = function(module) {
	// add the basic events	
	module.addEvents({
		'mouseenter': function() {
			module.addClass('hover');
		},
		'mouseleave': function() {
			module.removeClass('hover');
		}
	});
};

// EOF
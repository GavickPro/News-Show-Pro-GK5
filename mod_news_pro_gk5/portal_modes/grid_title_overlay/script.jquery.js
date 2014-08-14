jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-GridTitleOverlay').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeGridTitleOverlayInit(module);
		}
	});
});

var gkPortalModeGridTitleOverlayInit = function(module) {
	module = jQuery(module);
	// add the basic events
	module.mouseenter(function() {
		module.addClass('hover');
	});
	
	module.mouseleave(function() {
		module.removeClass('hover');
	});
};

// EOF
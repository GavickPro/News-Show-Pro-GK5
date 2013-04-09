jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-NewsBlocks').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeNewsBlocksInit(module);
		}
	});
});

var gkPortalModeNewsBlocksInit = function(module) {
	module = jQuery(module);
	// add the basic events
	module.find('figure').each(function(i, figure) {
		figure = jQuery(figure);
		var overlay = jQuery('<div class="gkImgOverlay"></div>');
		jQuery(figure.find('figcaption').first()).before(overlay);
		overlay.click(function() {
			window.location.href = jQuery(figure.find('a').first()).attr('href');
		});
	});
};

// EOF
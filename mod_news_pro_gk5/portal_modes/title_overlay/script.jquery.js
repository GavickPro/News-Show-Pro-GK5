jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-TitleOverlay').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeTitleOverlayInit(module);
		}
	});
});

var gkPortalModeTitleOverlayInit = function(module) {
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
	
	module.mouseenter(function() {
		module.addClass('hover');
	});
	
	module.mouseleave(function() {
		module.removeClass('hover');
	});
};

// EOF
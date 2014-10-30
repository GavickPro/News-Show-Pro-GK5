window.addEvent('domready', function() {
	document.getElements('.gkNspPM-TitleOverlay').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeTitleOverlayInit(module);
		}
	});
});

var gkPortalModeTitleOverlayInit = function(module) {
	// add the basic events
	module.getElements('figure').each(function(figure) {
		var overlay = new Element('div', {
			'class': 'gkImgOverlay'
		});
		overlay.inject(figure.getElement('img'), 'after');
		overlay.addEvent('click', function() {
			window.location.href = figure.getElement('a').getProperty('href');
		});
	});
	
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
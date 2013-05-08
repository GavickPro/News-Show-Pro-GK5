window.addEvent('domready', function() {
	document.getElements('.gkNspPM-NewsBlocks').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeNewsBlocksInit(module);
		}
	});
});

var gkPortalModeNewsBlocksInit = function(module) {
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
};

// EOF
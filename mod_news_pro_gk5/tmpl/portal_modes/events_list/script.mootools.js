window.addEvent('domready', function() {
	document.getElements('.gkNspPM-EventsList').each(function(module) {
		if(!module.hasClass('active')) {
			module.addClass('active');
			gkPortalModeEventsListInit(module);
		}
	});
});

var gkPortalModeEventsListInit = function(module) {
	// add the basic events
	var gk_events = module.getElements('time');
	
	if(gk_events.length) {
		gk_events.each(function(event, i) {
			var progress = event.getParent().getElement('.gkEventsListProgress');
			var progress_bar = new Element('div');
			progress_bar.inject(progress)
			var end = event.getProperty('datetime').split('T')[0].split('-');
			var end_date = new Date(end[0], end[1]-1, end[2], 0, 0);
			
			var start = event.getProperty('data-start').split('T')[0].split('-');
			var start_date = new Date(start[0], start[1]-1, start[2], 0, 0);
					
			var diff = end_date - start_date;
			var current = new Date();
			var current_date = new Date(current.getFullYear(), current.getMonth(), current.getDate(), 0, 0);
			var progress = 1 - Math.round(((end_date - current_date) / diff) * 1000) / 1000;
			progress = Math.round(progress * 1000) / 1000;
			
			setTimeout(function() {
				progress_bar.setStyle('width', progress * 100 + "%");
			}, 1000);
		});
	}
};

// EOF
jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-SpeakersList').each(function(i, module) {
		module = jQuery(module);
		if(!module.hasClass('active')) {
			module.addClass('active');
		}
	});
});

// EOF
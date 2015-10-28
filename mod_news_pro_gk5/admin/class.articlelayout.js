// class used with article layouts

function ArticleLayout() {
	this.init();
}

ArticleLayout.prototype.init = function() {
	this.layoutTabs();
}

ArticleLayout.prototype.layoutTabs = function() {
	// add necessary classes
	jQuery(['pagination', 'header', 'image', 'text', 'info', 'layout']).each(function(i, item) {
		jQuery('.gk-group-' + item).each(function(i, el) {
			jQuery(el).parent().parent().addClass('gk-group-' + item).css('display', 'none');
		});		
	});
	// add the toggle effect
	jQuery('.gkFormLine').each(function(i, elm) {
		elm = jQuery(elm);
		if(elm.attr('data-section-toggle') != '') {
			if(i==0) {
				elm.addClass('open');
				var classToToggle = '.gk-group-' + elm.attr('data-section-toggle');
				jQuery(classToToggle).css('display', 'block');
			} else {
				elm.addClass('closed');
			}
			elm.click(function() {
				var classToToggle = '.gk-group-' + elm.attr('data-section-toggle');
				if(jQuery(classToToggle).css('display') == 'none') {
					jQuery(classToToggle).css('display', 'block');
					if(elm.hasClass('closed')) {
						elm.removeClass('closed');
					}
					elm.addClass('open');
				} else {
					jQuery(classToToggle).css('display', 'none');
					if(elm.hasClass('open')) {
						elm.removeClass('open');
					}
					elm.addClass('close');
				}			
			});
		}
	});
}

// class used with article layouts

function ArticleLayout() {
	this.init();
}

ArticleLayout.prototype.init = function() {
	this.layoutTabs();
	this.layoutOrder();
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

ArticleLayout.prototype.layoutOrder = function() {
	var $this = this;
	
	jQuery(['header', 'image', 'text', 'info', 'info2']).each(function(i, item) {
		var el = jQuery('#jform_params_news_' + item + '_order');
		el.change(function() {
			$this.changeOrder(el);
		});
	});
	
	
}

ArticleLayout.prototype.changeOrder = function(current) {
	var unexisting = [false, false, false, false, false];
	var searched = 0;
	
	
	
	var elms = jQuery.map(['header', 'image', 'text', 'info', 'info2'], function(item) {
		return jQuery('#jform_params_news_' + item + '_order');
	});
	
	jQuery(elms).each(function(i, item) {
		item = jQuery(item);;
		unexisting[item.val() - 1] = true;
	});
	
	
	for(var i = 0; i < 5; i++) {
		if(unexisting[i] == false){ searched = i+1;}
	}
	
	jQuery(elms).each(function(i, item) {
		item = jQuery(item);
		if((item.selector != current.selector) && (item.val() == current.val())) {			
			item.val(searched);
		}
	});	
}
// class used with article layouts
var ArticleLayout = new Class({
	initialize: function() {
		this.layoutTabs();
		this.layoutOrder();
	},
	
	layoutTabs: function() {
		// add necessary classes
		['pagination', 'header', 'image', 'text', 'info', 'layout'].each(function(item) {
			if(item == 'pagination') {
				document.getElements('.gk-group-' + item).each(function(el) {
					el.getParent().addClass('gk-group-' + item);
				});	
			} else {
				document.getElements('.gk-group-' + item).each(function(el) {
					el.getParent().addClass('gk-group-' + item).setStyle('display', 'none');
				});	
			}	
		});
		// add the toggle effect
		document.getElements('.gkFormLine').each(function(elm, i) {
			if(elm.getProperty('data-section-toggle') != '') {
				if(i == 0 || i == 5) {
					elm.addClass('open');
				} else {
					elm.addClass('closed');
				}
				
				elm.addEvent('click', function() {
					var classToToggle = '.gk-group-' + elm.getProperty('data-section-toggle');
					if(document.getElement(classToToggle).getStyle('display') == 'none') {
						document.getElements(classToToggle).setStyle('display', 'block');
						if(elm.hasClass('closed')) {
							elm.removeClass('closed');
						}
						elm.addClass('open');
					} else {
						document.getElements(classToToggle).setStyle('display', 'none');
						if(elm.hasClass('open')) {
							elm.removeClass('open');
						}
						elm.addClass('close');
					}			
				});
			}
		});
	},
	
	layoutOrder: function() {
		var $this = this;
		
		['header', 'image', 'text', 'info', 'info2'].each(function(item) {
			var el = document.id('jform_params_news_' + item + '_order');
			el.addEvent("change", function() {
				$this.changeOrder(el);
			});
		});
	},
	
	changeOrder: function(current) {
		var unexisting = [false, false, false, false, false];
		var searched = 0;
		var elms = ['header', 'image', 'text', 'info', 'info2'].map(function(item) {
			return document.id('jform_params_news_' + item + '_order');
		});
		
		elms.each(function(item) {
			unexisting[item.value - 1] = true;
		});
		
		for(var i = 0; i < 5; i++) {
			if(unexisting[i] == false) searched = i+1;
		}
		
		elms.each(function(item) {
			if(item != current && item.value == current.value) {
				item.value = searched;
			}
		});
	}
});
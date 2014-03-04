jQuery(window).load(function() {
	setTimeout(function() {
		jQuery(document).find('.gkNspPM-VideoList').each(function(i, module) {
			module = jQuery(module);
			
			if(!module.hasClass('active')) {
				module.addClass('active');
			}

			var mod = new GKNSPVideoList();
			mod.init(module);
		});
	}, 1000);
});

// module initializer
var GKNSPVideoList = function(module) {
	var API = {
		// basic variables
		currentPage: 0,
		blank: false,
		pagesAmount: 0,
		itemsAmount: 0,
		perPage: 0,
		pages: null,
		pagination: null,
		// instance initialization
		init: function(module) {
			//
			// init variables
			//
			var $this = this;
			this.pagesAmount = module.find('.gkItemsPage').length;
			this.pages = module.find('.gkItemsPage');
			this.itemsAmount = module.find('.gkItem').length;
			this.perPage = Math.ceil(this.itemsAmount / this.pagesAmount);
			this.pagination = module.find('.gkBottomNavPagination li');
			//
			// init UI
			//
			// video popups
			module.find('.gkItem').each(function(i, item) {
				item = jQuery(item);
				item.find('.gkImageWrap').click(function(e) {
					e.preventDefault();
					var target_img = item.find('img');
					var url = target_img.attr('data-url');
					
					if(url != '#') {
						SqueezeBox.open(url, {handler: 'iframe', size: {x: target_img.attr('data-x'),y: target_img.attr('data-y') }});
					} else {
						window.location.href = item.find('h3 a').attr('href');
					}
				});
			});
			// pagination events
			if(this.pagination.length > 0) {
				// next button events
				jQuery(module.find('.gkBottomNavNext')).click(function(e) {
					e.preventDefault();
					$this.next();
				});
				// prev button events
				jQuery(module.find('.gkBottomNavPrev')).click(function(e) {
					e.preventDefault();
					$this.prev();
				});
				// pagination events
				jQuery(this.pagination).each(function(i, item) {
					jQuery(item).click(function(e) {
						e.preventDefault();
						$this.page(i);
					});
				});
			}
		},
		// skip to next slide
		next: function() {
			var num = (this.currentPage < this.pagesAmount - 1) ? this.currentPage + 1 : 0;
			this.page(num);
		},
		// skip to previous slide
		prev: function() {
			var num = (this.currentPage > 0) ? this.currentPage - 1 : this.pagesAmount - 1;
			this.page(num);
		},
		// skip to n-th slide
		page: function(num) {
			var $this = this;
			var prev = jQuery(this.pages[this.currentPage]);
			// hide prev items
			prev.find('.gkItem').each(function(i, item) {
				setTimeout(function() {
					jQuery(item).removeClass('active');
				}, i * 50);
			});
			var prevNum = this.currentPage;
			// hide current page
			prev.addClass('to-hide');
			// get the next page num
			this.currentPage = num;
			// active pagination elements
			if(this.pagination) {
				jQuery(this.pagination[prevNum]).removeClass('active');
				jQuery(this.pagination[this.currentPage]).addClass('active');
			}
			// hide old page
			setTimeout(function() {
				prev.removeClass('active');
				// show new page
				jQuery($this.pages[$this.currentPage]).addClass('active');
				// show next items
				jQuery($this.pages[$this.currentPage]).find('.gkItem').each(function(i, item) {
					setTimeout(function() {
						jQuery(item).addClass('active');
					}, i * 50);
				});
				
				prev.removeClass('to-hide');
			}, 500);
		} 
	};
	
	return API;
};

// EOF
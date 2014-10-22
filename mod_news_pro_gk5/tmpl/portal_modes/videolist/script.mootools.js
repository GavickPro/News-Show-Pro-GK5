window.addEvent('load', function() {
	setTimeout(function() {
		document.getElements('.gkNspPM-VideoList').each(function(module) {
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
			this.pagesAmount = module.getElements('.gkItemsPage').length;
			this.pages = module.getElements('.gkItemsPage');
			this.itemsAmount = module.getElements('.gkItem').length;
			this.perPage = Math.ceil(this.itemsAmount / this.pagesAmount);
			this.pagination = module.getElements('.gkBottomNavPagination li');
			//
			// init UI
			//
			// video popups
			module.getElements('.gkItem').each(function(item, i) {
				item.getElement('.gkImageWrap').addEvent('click', function(e) {
					e.preventDefault();
					var target_img = item.getElement('img');
					var url = target_img.getProperty('data-url');
					
					if(url != '#') {
						SqueezeBox.open(url, {handler: 'iframe', size: {x: target_img.getProperty('data-x'),y: target_img.getProperty('data-y') }});
					} else {
						window.location.href = item.getElement('h3 a').getProperty('href');
					}
				});
			});
			// pagination events
			if(this.pagination) {
				// next button events
				module.getElement('.gkBottomNavNext').addEvent('click', function(e) {
					e.preventDefault();
					$this.next();
				});
				// prev button events
				module.getElement('.gkBottomNavPrev').addEvent('click', function(e) {
					e.preventDefault();
					$this.prev();
				});
				// pagination events
				this.pagination.each(function(item, i) {
					item.addEvent('click', function(e) {
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
			var prev = this.pages[this.currentPage];
			// hide prev items
			prev.getElements('.gkItem').each(function(item, i) {
				setTimeout(function() {
					item.removeClass('active');
				}, i * 50);
			});
			var prevNum = this.currentPage;
			// hide current page
			prev.addClass('to-hide');
			// get the next page num
			this.currentPage = num;
			// active pagination elements
			if(this.pagination) {
				this.pagination[prevNum].removeClass('active');
				this.pagination[this.currentPage].addClass('active');
			}
			// hide old page
			setTimeout(function() {
				prev.removeClass('active');
				// show new page
				$this.pages[$this.currentPage].addClass('active');
				// show next items
				$this.pages[$this.currentPage].getElements('.gkItem').each(function(item, i) {
					setTimeout(function() {
						item.addClass('active');
					}, i * 50);
				});
				
				prev.removeClass('to-hide');
			}, 500);
		} 
	};
	
	return API;
};

// EOF
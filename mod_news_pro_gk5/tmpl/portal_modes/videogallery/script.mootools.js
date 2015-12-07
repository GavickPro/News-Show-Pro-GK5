window.addEvent('load', function() {
	setTimeout(function() {
		document.getElements('.gkNspPM-VideoGallery').each(function(module) {
			if(!module.hasClass('active')) {
				module.addClass('active');
			}
			
			var mod = new GKNSPVideoGallery();
			mod.init(module);
		});
	}, 1000);
});
// module initializer
var GKNSPVideoGallery = function(module) {
	var API = {
		// basic variables
		bigarea: null,
		smallarea: null,
		interval: null,
		current: 0,
		currentPage: 0,
		blank: false,
		
		init: function(wrapper) {
			//
			$this = this;
			this.bigarea = wrapper.getElement('.gkBigBlock');
			this.smallarea = wrapper.getElement('.gkSmallBlock');
			this.interval = wrapper.getProperty('data-autoanim');
			// add video popup event
			this.bigarea.addEvent('click', function(e) {
				if(e.target.get('tag') == 'figure') {
					var target_img = e.target.getElement('img');
					var url = target_img.getProperty('data-url');
					if(url != '#') {
						SqueezeBox.open(url, {handler: 'iframe', size: {x: target_img.getProperty('data-x'),y: target_img.getProperty('data-y') }});
					} else {
						window.location.href = $this.bigarea.getElement('h3 a').getProperty('href');
					}
				}
			});
			// add news resizer event
			this.smallarea.getElements('.gkItem').each(function(item) {
				item.addEvent('click', function(e) {
					if(e) e.stop();
					// replace current big slide with the new one
					$this.bigarea.removeClass('active');
					$this.blank = true;
					new Fx.Tween($this.bigarea, {
						duration: 250,
						property: 'opacity',
						onComplete: function() {
							item.clone().inject($this.bigarea, 'bottom');
							$this.bigarea.getElement('figure').dispose();
							$this.current = item.getProperty('data-num');
							setTimeout(function() {
								new Fx.Tween($this.bigarea, {
									duration: 250,
									property: 'opacity'
								}).start(1);
								$this.bigarea.addClass('active');
							}, 100);
						}
					}).start(0);
				});
			});
			// autoanimation
			setTimeout(function() {
				$this.next();
			}, this.interval);
		},
		
		next: function() {
			if(!this.blank) {
				// check amount of pages and elements
				var pagesAmount = this.smallarea.getElements('.gkItemsPage').length;
				var itemsAmount = this.smallarea.getElements('.gkItem').length;
				var perPage = Math.ceil(itemsAmount / pagesAmount);
				//
				if(this.current > itemsAmount - 2) {
					this.current = 0;
					
					if(this.currentPage != Math.floor(this.current / perPage)) {
						var toHide = this.smallarea.getElements('.gkItemsPage')[this.currentPage];
						var toShow = this.smallarea.getElements('.gkItemsPage')[0];
						
						new Fx.Tween(toHide, { 
							duration: 250, 
							onComplete: function() {
								toHide.removeClass('active');
								$this.currentPage = 0;
								
								toShow.setStyle('opacity', 0);
								toShow.addClass('active');
					
								new Fx.Tween(toShow, {
									duration: 250
								}).start('opacity', 1);
							} 
						}).start('opacity', 0);
					}
	
					this.smallarea.getElements('.gkItem')[0].fireEvent('click');
				} else {
					this.current = this.current * 1 + 1;
					
					if(this.currentPage != Math.floor(this.current / perPage)) {
						var toHide = this.smallarea.getElements('.gkItemsPage')[this.currentPage];
						this.currentPage = Math.floor(this.current / perPage);
						var toShow = this.smallarea.getElements('.gkItemsPage')[this.currentPage];
						
						new Fx.Tween(toHide, { 
							duration: 250, 
							onComplete: function() {
								toHide.removeClass('active');
								toShow.setStyle('opacity', 0);
								toShow.addClass('active');
					
								new Fx.Tween(toShow, {
									duration: 250
								}).start('opacity', 1);
							} 
						}).start('opacity', 0);
					}
					
					this.smallarea.getElements('.gkItem')[this.current].fireEvent('click');
				}
			} else {
				this.blank = false;
			}
			// autoanimation
			setTimeout(function() {
				$this.next();
			}, this.interval);
		}
	};
	
	return API;
};

// EOF
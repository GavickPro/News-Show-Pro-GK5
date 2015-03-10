// module initializer
var GK_NSP_NewsSlider = function(module) {
	var API = {
		// basic variables
		queue: false,
		clone_mode: false,
		current_slide: false,
		autoanim: false,
		module: false,
		// instance initialization
		init: function(module) {
			//
			// init variables
			//
			var $this = this;
			this.queue = module.find('figure');
			this.clone_mode = this.queue.length < 7;
			this.current_slide = 2;
			this.autoanim = module.attr('data-autoanim');
			this.module = module;
			this.readytoanim = false;
			//
			// init UI
			//
			// Clone mode
			if(this.clone_mode) {
				var clones = this.queue.clone();
				for(var i = 0; i < clones.length; i++) {
					if(clones.length == 5 && i == 0) {
						clones.eq(i).attr('class', 'gk-to-hide');
						clones.eq(i).attr('style', '');
					} else if(clones.length == 5 && i == 1) {
						clones.eq(i).attr('class', 'gk-to-show');
						clones.eq(i).attr('style', '');
					} else if(clones.length == 6 && i == 0) {
						clones.eq(i).attr('class', 'gk-to-hide');
						clones.eq(i).attr('style', '');
					} else if(clones.length == 6 && i == 1) {
						clones.eq(i).attr('class', 'gk-to-show');
						clones.eq(i).attr('style', '');
					} else {
						clones.eq(i).attr('class', 'gk-hide');
						clones.eq(i).attr('style', '');
					}
					
					clones.eq(i).attr('data-clone', 'true');
					clones.eq(i).insertAfter(this.queue.last());
					this.queue = this.module.find('figure');
				}
			}
			
			this.queue.eq(5).attr('style', '');
			module.find('.gk-data-category-link').attr('href', this.queue.eq(2).attr('data-cat'));
			module.find('.gk-data-category-link').click(function() {
				window.location.href = this.getAttribute('href');
			});
			// Activate
			var active_figcaption = this.queue.eq(this.current_slide).find('figcaption');
			active_figcaption.css('margin-top', "-300px");
			// Autoanim
			setTimeout(function() {
				$this.anim();
			}, this.autoanim);
		},
		anim: function() {
			if(this.module.hasClass('gk-run-animation') && this.readytoanim) {
				var prev_current = this.current_slide;
				
				if(this.current_slide > 0) {
					this.current_slide--;
				} else {
					this.current_slide = this.queue.length - 1;
				}
				
				var to_show = this.current_slide - 3;
				var prev_2 = this.current_slide - 2;
				var prev_1 = this.current_slide - 1;
				var active = this.current_slide;
				var next_1 = (this.current_slide + 1) % this.queue.length;
				var next_2 = (this.current_slide + 2) % this.queue.length;
				var to_hide = (this.current_slide + 3) % this.queue.length;
				
				var active_figcaption = this.queue.eq(active).find('figcaption');
				var prev_figcaption = this.queue.eq(prev_current).find('figcaption');
				
				active_figcaption.css('margin-top', "-" + (active_figcaption.parent().find('.gk-image-wrap').outerHeight()) + "px");
				prev_figcaption.css('margin-top', '0px');
				
				this.queue.attr('class', 'gk-hide');
				this.queue.eq(to_show).attr('class', 'gk-to-show');
				this.queue.eq(prev_2).attr('class', 'gk-prev-2');
				this.queue.eq(prev_1).attr('class', 'gk-prev-1');
				this.queue.eq(active).attr('class', 'gk-active');
				this.queue.eq(next_1).attr('class', 'gk-next-1');
				this.queue.eq(next_2).attr('class', 'gk-next-2');
				this.queue.eq(to_hide).attr('class', 'gk-to-hide');
				
				this.module.find('.gk-data-category-link').attr('href', this.queue.eq(this.current_slide).attr('data-cat'));
			}
			
			if(this.module.hasClass('gk-run-animation') && !this.readytoanim) {
				this.readytoanim = true;
			}
			
			var $this = this;
			
			setTimeout(function() {
				$this.anim();
			}, this.autoanim);
		},
	};
	
	return API;
};

var gkmod = new GK_NSP_NewsSlider();

jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-NewsSlider').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active')) {
			module.addClass('active');
			setTimeout(function() {
				gkmod.init(module);
			}, 1000);
		}
	});
});

// EOF
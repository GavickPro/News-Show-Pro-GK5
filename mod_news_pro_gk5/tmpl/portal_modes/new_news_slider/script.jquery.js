jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-NewNewsSlider').each(function(i, module) {
		module = jQuery(module);

		if(!module.hasClass('active')) {
			module.addClass('active');
			new InfiniteSlider(module);
		}
	});
});

var InfiniteSlider = function (wrapper) {
    // Set object fields
    this.wrapper = wrapper;
    this.next = this.wrapper.find('.gkNext');
    this.prev = this.wrapper.find('.gkPrev');
    this.items = false;
    this.itemsAmount = this.wrapper.find('.gkItem').length;
    this.newWidth = 0;
    this.itemWidth = 0;
    this.list = this.wrapper.find('.gkList');
    this.currentItem = 0;
    this.blank = false;
    this.hoverStop = false;
    this.autoAnimation = this.list.attr('data-interval');
    // Initialize the UI
    this.initUI();
    // Add swipe gestures support
    this.addTouchEvents();
};

InfiniteSlider.prototype.initUI = function () {
    var self = this;
    var content = this.list.html();
    // Duplicate items in the list
    this.list.html(content + content);
    this.items = this.wrapper.find('.gkItem');
    // Get dimensions
    this.itemWidth = this.items.first().outerWidth();
    this.newWidth = this.items.length * this.itemWidth;
    // Set list dimension
    this.list.css('width', this.newWidth + "px");
    // Add clone classes to items
    this.items.each(function (i, item) {
        if (i >= self.items.length / 2) {
            item = jQuery(item);
            item.addClass('gkClone');
        }
    });

    this.next.on('click', function (e) {
        e.preventDefault();
        self.animate('next');
    });

    this.prev.on('click', function (e) {
        e.preventDefault();
        self.animate('prev');
    });

    this.wrapper.on('mouseenter', function () {
        self.hoverStop = true;
    });

    this.wrapper.on('mouseleave', function () {
        self.hoverStop = false;
    });

    jQuery(window).on('resize', function () {
        self.resize();
    });

    if (this.autoAnimation > 0) {
        setTimeout(function () {
            self.autoAnimate(self.autoAnimation);
        }, this.autoAnimation);
    }
    
    this.resize();
};

InfiniteSlider.prototype.addTouchEvents = function () {
	var self = this;
	var arts_pos_start_x = 0;
	var arts_pos_start_y = 0;
	var arts_time_start = 0;
	var arts_swipe = false;
	var swipe_max_time = 500;
	var swipe_min_move = 100;
	
	this.wrapper.bind('touchstart', function(e) {
		arts_swipe = true;
		var touches = e.originalEvent.changedTouches || e.originalEvent.touches;

		if(touches.length > 0) {
			arts_pos_start_x = touches[0].pageX;
			arts_pos_start_y = touches[0].pageY;
			arts_time_start = new Date().getTime();
		}
	});
	
	this.wrapper.bind('touchmove', function(e) {
		var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
		
		if(touches.length > 0 && arts_swipe) {
			if(
				Math.abs(touches[0].pageX - arts_pos_start_x) > Math.abs(touches[0].pageY - arts_pos_start_y)
			) {
				e.preventDefault();
			} else {
				arts_swipe = false;
			}
		}
	});
				
	this.wrapper.bind('touchend', function(e) {
		var touches = e.originalEvent.changedTouches || e.originalEvent.touches;
		
		if(touches.length > 0 && arts_swipe) {									
			if(
				Math.abs(touches[0].pageX - arts_pos_start_x) >= swipe_min_move && 
				new Date().getTime() - arts_time_start <= swipe_max_time
			) {					
				if(touches[0].pageX - arts_pos_start_x > 0) {
					self.animate('prev');
				} else {
					self.animate('next');
				}
			}
		}
	});
};

InfiniteSlider.prototype.elementToMove = function (direction) {
    if (direction === 'prev') {
        return ((this.itemsAmount * 2) + this.currentItem) % (this.itemsAmount * 2);
    }

    return ((this.itemsAmount * 2) + this.currentItem - 1) % (this.itemsAmount * 2);
};

InfiniteSlider.prototype.elementForMove = function (direction) {
    if (direction === 'prev') {
        return ((this.itemsAmount * 2) + this.currentItem + 1) % (this.itemsAmount * 2);
    }

    return ((this.itemsAmount * 2) + this.currentItem - 2) % (this.itemsAmount * 2);
};

InfiniteSlider.prototype.animate = function (direction) {
    var self = this;
    var forMove, toMove;

    if (this.blank || !this.list.hasClass('gkScrollable')) {
        return false;
    }

    if (direction === 'next') {
        this.currentItem = ++this.currentItem;
    } else {
        this.currentItem = --this.currentItem;
    }

    this.currentItem = this.currentItem % (this.itemsAmount * 2);
    forMove = this.elementForMove(direction);
    toMove = this.elementToMove(direction);
    this.blank = true;

    if (direction === 'next') {
        this.list.addClass('gkAnimate');

        setTimeout(function () {
            self.list.css('margin-left', -self.itemWidth + "px");

            setTimeout(function () {
                self.list.removeClass('gkAnimate');

                setTimeout(function () {
                    self.list.css('margin-left', "0px");
                    jQuery(self.items.get(forMove)).after(jQuery(self.items.get(toMove)));
                    self.blank = false;
                }, 20);
            }, 320);
        }, 20);
    } else {
        this.list.css('margin-left', -1 * this.itemWidth + "px");
        jQuery(this.items.get(forMove)).before(jQuery(this.items.get(toMove)));

        setTimeout(function () {
            self.list.addClass('gkAnimate');

            setTimeout(function () {
                self.list.css('margin-left', "0px");

                setTimeout(function () {
                    self.list.removeClass('gkAnimate');
                    self.blank = false;
                }, 320);
            }, 20);
        }, 20);
    }
};

InfiniteSlider.prototype.autoAnimate = function (interval) {
    var self = this;

    if (this.blank) {
        this.blank = false;
    } else if (!this.hoverStop) {
        this.animate('next');
    }

    setTimeout(function () {
        self.autoAnimate(interval);
    }, interval);
};

InfiniteSlider.prototype.resize = function () {
    var divider = this.list.hasClass('scrollable') ? 2 : 1;
    // Get item new iwdth
    this.itemWidth = this.items.first().outerWidth();

    // Compare the size of the list area
    if ((this.list.outerWidth() / divider) >= this.wrapper.outerWidth()) {
        this.newWidth = this.items.length * this.itemWidth;
        this.list.removeClass('gkNonScrollable');
        this.list.addClass('gkScrollable');
        this.prev.css('display', 'block');
        this.next.css('display', 'block');
    } else {
        this.newWidth = (this.items.length / 2) * this.itemWidth;
        this.list.removeClass('gkScrollable');
        this.list.addClass('gkNonScrollable');
        this.prev.css('display', 'none');
        this.next.css('display', 'none');
    }
    // Set list dimension
    this.list.css('width', this.newWidth + "px");
};

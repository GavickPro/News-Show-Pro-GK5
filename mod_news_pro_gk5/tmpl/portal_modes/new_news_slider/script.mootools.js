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

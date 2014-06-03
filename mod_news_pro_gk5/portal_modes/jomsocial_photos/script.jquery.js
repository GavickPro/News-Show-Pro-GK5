jQuery(document).ready(function() {
	jQuery(document).find('.gkNspPM-JomSocialPhotos').each(function(i, module) {
		module = jQuery(module);
		
		if(!module.hasClass('active') && module.hasClass('animate')) {
			module.addClass('active');
			gkPortalModeJomSocialPhotosInit(module);
		}
	});
});

var gkPortalModeJomSocialPhotosInit = function(module) {
	module = jQuery(module);
	
	var pause = false;
	var current = 0;
	var content = [];
	var items = module.find('figure');
	var count = items.length;
	
	if(count) {
		// prepare the content array
		items.each(function(i, item) {
			item = jQuery(item);
			var img = item.find('img');
			var item_content = {
				"src": img.attr('src'),
				"alt": img.attr('alt'),
				"desc": item.find('figcaption').html() 
			};
			content.push(item_content);
			item.mouseenter(function() {
				pause = true;
			});
			item.mouseleave(function() {
				pause = false;
			});
		});
		// prepare animation 
		var animate = function() {
			if(!pause) {
				// modify the content array
				var first_item = content.pop();
				content.unshift(first_item);
				
				items.each(function(i, item) {	
					animate_slide(item, content, i);
				});
			} else {
				pause = false;
			}
			
			setTimeout(function() {
				animate();
			}, 3000 + (100 * count));
		};
		// helper function
		var animate_slide = function(item, content, i) {
			item = jQuery(item);
			
			setTimeout(function() {
				var img = item.find('img');
				img.addClass('hide');
				setTimeout(function() {
					img.attr('src', content[i].src);
					img.attr('alt', content[i].alt);
					img.removeClass('hide');
				}, 500);
				item.find('figcaption').html(content[i].desc);
			}, 100 * i);
		};
		// run animation
		setTimeout(function() {
			animate();
		}, 3000);
	}
};

// EOF
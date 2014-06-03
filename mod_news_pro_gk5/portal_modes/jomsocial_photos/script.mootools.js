window.addEvent('domready', function() {
	document.getElements('.gkNspPM-JomSocialPhotos').each(function(module) {
		if(!module.hasClass('active') && module.hasClass('animate')) {
			module.addClass('active');
			gkPortalModeJomSocialPhotosInit(module);
		}
	});
});

var gkPortalModeJomSocialPhotosInit = function(module) {
	var pause = false;
	var current = 0;
	var content = [];
	var items = module.getElements('figure');
	var count = items.length;
	
	if(count) {
		// prepare the content array
		items.each(function(item, i) {
			var img = item.getElement('img');
			var item_content = {
				"src": img.getProperty('src'),
				"alt": img.getProperty('alt'),
				"desc": item.getElement('figcaption').get('html') 
			};
			content.push(item_content);
			item.addEvent('mouseenter', function() {
				pause = true;
			});
			item.addEvent('mouseleave', function() {
				pause = false;
			});
		});
		// prepare animation 
		var animate = function() {
			if(!pause) {
				// modify the content array
				var first_item = content.pop();
				content.unshift(first_item);
				
				items.each(function(item, i) {	
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
			setTimeout(function() {
				var img = item.getElement('img');
				img.addClass('hide');
				setTimeout(function() {
					img.setProperty('src', content[i].src);
					img.setProperty('alt', content[i].alt);
					img.removeClass('hide');
				}, 500);
				item.getElement('figcaption').set('html', content[i].desc);
			}, 100 * i);
		};
		// run animation
		setTimeout(function() {
			animate();
		}, 3000);
	}
};

// EOF
// Image crop class
function ImageCrop() {
	this.init();
}

ImageCrop.prototype.init = function() {
	//
	var crops = [];
	var $this = this;
	//
	jQuery.each(['#jform_params_simple_crop_top','#jform_params_simple_crop_bottom','#jform_params_simple_crop_left','#jform_params_simple_crop_right'], function(i, elm) {
		jQuery(elm).parents().eq(1).css('display', 'none');
	});
	//
	jQuery(['top', 'bottom', 'left', 'right']).each(function(i, item) {
		jQuery('#simple_crop_' + item).val(jQuery('#jform_params_simple_crop_' + item).val());
		crops[item] = jQuery('#jform_params_simple_crop_' + item).val();	
	});
	//
	jQuery('#simple_crop_crop').css({
		'margin-top': crops['top'] + "%",
		'margin-left': crops['left'] + "%",
		'margin-right': crops['right'] + "%",
		'margin-bottom': crops['bottom'] + "%",
		'height': (200.0 - ( (200.0 * ( crops['top'] * 1 + crops['bottom'] * 1 ) ) / 100.0 ) ) + "px",
		'width': (200.0 - ( (200.0 * ( crops['left'] * 1 + crops['right'] * 1 ) ) / 100.0 ) ) + "px"  
	});
	//
	jQuery(['top', 'bottom', 'left', 'right']).each(function(i, item) {
		jQuery('#simple_crop_' + item).change ( function() {	
			$this.cropEvent(item);
		});
		
		jQuery('#simple_crop_' + item).blur( function() {	
			$this.cropEvent(item);
		});
	});
	// add the toggling crop options when auto-scale enabled
	jQuery('#jform_params_img_auto_scale').parent().find('div').click( function() {
		var value = jQuery('#jform_params_img_auto_scale').val();
		$this.toggleCropOptions(value);
		
		if(value == 1 && jQuery('#jform_params_img_keep_aspect_ratio').val() == 1) {
			jQuery('#jform_params_img_keep_aspect_ratio').parent().find('div').trigger('click');
		}
		
		if(value == 1 && jQuery('#jform_params_img_stretch').val() == 1) {
			jQuery('#jform_params_img_stretch').parent().find('div').trigger('click');
		}
		
		jQuery('#jform_params_img_keep_aspect_ratio').parent().css('display', value == 1 ? 'none' : 'block');
		jQuery('#jform_params_img_stretch').parent().css('display', value == 1 ? 'none' : 'block');
	});
	// toggle crop options at the start
	var value = jQuery('#jform_params_img_auto_scale').val();
	$this.toggleCropOptions(value);
	
	if(value == 1 && jQuery('#jform_params_img_keep_aspect_ratio').val() == 1) {
		jQuery('#jform_params_img_keep_aspect_ratio').parent().find('div').trigger('click');
	}
	
	if(value == 1 && jQuery('#jform_params_img_stretch').val() == 1) {
		jQuery('#jform_params_img_stretch').parent().find('div').trigger('click');
	}
	
	jQuery('#jform_params_img_keep_aspect_ratio').parent().css('display', value == 1 ? 'none' : 'block');
	jQuery('#jform_params_img_stretch').parent().css('display', value == 1 ? 'none' : 'block');
	
	// code to prepare other available fields
	this.prepareOtherFields();
},

ImageCrop.prototype.cropEvent = function(type) {
	var reverse = (type == 'top') ? 'bottom' : (type == 'bottom') ? 'top' : (type == 'left') ? 'right' : 'left';
	var line = (type == 'top' || type == 'bottom') ? 'height' : 'width';
	var field = jQuery('#jform_params_simple_crop_' + type);
	var fieldr = jQuery('#jform_params_simple_crop_' + reverse);
	
	field.val(jQuery('#simple_crop_' + type).val());
	jQuery('#simple_crop_crop').css('margin-' + type, field.val() + "%");
	jQuery('#simple_crop_crop').css(line, (200.0 - ( (200.0 * ( fieldr.val() * 1 + field.val() * 1 ) ) / 100.0 ) ) + "px" );	
},

ImageCrop.prototype.toggleCropOptions = function(state) {
	// when auto-scale enabled / disabled
	jQuery('#simple_crop').parents().eq(1).css('display', state == 1 ? 'none' : 'block');
	jQuery('#jform_params_crop_rules').parents().eq(1).css('display', state == 1 ? 'none' : 'block');
},

ImageCrop.prototype.prepareOtherFields = function() {
	var hfield = jQuery('#jform_params_img_height');
	var parent = hfield.parent();
	var span = hfield.parent().find('span');
	jQuery('#jform_params_img_width').parent().append(hfield);
	hfield.after(span);
	parent.css('display', 'none');

	var lhfield = jQuery('#jform_params_links_img_height');
	var lparent = lhfield.parent();
	var lspan = lhfield.parent().find('span');
	jQuery('#jform_params_links_img_width').parent().append(lhfield);
	lhfield.after(lspan);
	lparent.css('display', 'none');
}
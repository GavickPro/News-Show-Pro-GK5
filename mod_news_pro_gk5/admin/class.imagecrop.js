// Image crop class
var ImageCrop = new Class({	
	initialize: function() {	
		//
		var crops = [];
		var $this = this;
		//
		[document.id('jform_params_simple_crop_top'), document.id('jform_params_simple_crop_bottom'), document.id('jform_params_simple_crop_left'), document.id('jform_params_simple_crop_right')].each(function(elm) {
			elm.getParent().setStyle('display', 'none');
		});
		//
		['top', 'bottom', 'left', 'right'].each(function(item) {
			document.id('simple_crop_' + item).value = document.id('jform_params_simple_crop_' + item).value;
			crops[item] = document.id('jform_params_simple_crop_' + item).value;	
		});
		//
		document.id('simple_crop_crop').setStyles({
			'margin-top': crops['top'] + "%",
			'margin-left': crops['left'] + "%",
			'margin-right': crops['right'] + "%",
			'margin-bottom': crops['bottom'] + "%",
			'height': (200.0 - ( (200.0 * ( crops['top'] * 1 + crops['bottom'] * 1 ) ) / 100.0 ) ) + "px",
			'width': (200.0 - ( (200.0 * ( crops['left'] * 1 + crops['right'] * 1 ) ) / 100.0 ) ) + "px"  
		});
		//
		['top', 'bottom', 'left', 'right'].each(function(item) {
			document.id('simple_crop_' + item).addEvent('change', function() {	
				$this.cropEvent(item);
			});
			
			document.id('simple_crop_' + item).addEvent('blur', function() {	
				$this.cropEvent(item);
			});
		});
		// add the toggling crop options when auto-scale enabled
		document.id('jform_params_img_auto_scale').getParent().getElement('div').addEvent('click', function() {
			var value = document.id('jform_params_img_auto_scale').get('value');
			$this.toggleCropOptions(value);
			
			if(value == 1 && document.id('jform_params_img_keep_aspect_ratio').get('value') == 1) {
				document.id('jform_params_img_keep_aspect_ratio').getParent().getElement('div').fireEvent('click');
			}
			
			if(value == 1 && document.id('jform_params_img_stretch').get('value') == 1) {
				document.id('jform_params_img_stretch').getParent().getElement('div').fireEvent('click');
			}
			
			document.id('jform_params_img_keep_aspect_ratio').getParent().setStyle('display', value == 1 ? 'none' : 'block');
			document.id('jform_params_img_stretch').getParent().setStyle('display', value == 1 ? 'none' : 'block');
		});
		// toggle crop options at the start
		var value = document.id('jform_params_img_auto_scale').get('value');
		$this.toggleCropOptions(value);
		
		if(value == 1 && document.id('jform_params_img_keep_aspect_ratio').get('value') == 1) {
			document.id('jform_params_img_keep_aspect_ratio').getParent().getElement('div').fireEvent('click');
		}
		
		if(value == 1 && document.id('jform_params_img_stretch').get('value') == 1) {
			document.id('jform_params_img_stretch').getParent().getElement('div').fireEvent('click');
		}
		
		document.id('jform_params_img_keep_aspect_ratio').getParent().setStyle('display', value == 1 ? 'none' : 'block');
		document.id('jform_params_img_stretch').getParent().setStyle('display', value == 1 ? 'none' : 'block');
		
		// code to prepare other available fields
		this.prepareOtherFields();
	},
	
	cropEvent: function(type) {
		var reverse = (type == 'top') ? 'bottom' : (type == 'bottom') ? 'top' : (type == 'left') ? 'right' : 'left';
		var line = (type == 'top' || type == 'bottom') ? 'height' : 'width';
		var field = document.id('jform_params_simple_crop_' + type);
		var fieldr = document.id('jform_params_simple_crop_' + reverse);
		
		field.value = document.id('simple_crop_' + type).value;
		document.id('simple_crop_crop').setStyle('margin-' + type, field.value + "%");
		document.id('simple_crop_crop').setStyle(line, (200.0 - ( (200.0 * ( fieldr.value * 1 + field.value * 1 ) ) / 100.0 ) ) + "px" );
	},
	
	toggleCropOptions: function(state) {
		// when auto-scale enabled / disabled
		document.id('simple_crop').getParent().setStyle('display', state == 1 ? 'none' : 'block');
		document.id('jform_params_crop_rules').getParent().setStyle('display', state == 1 ? 'none' : 'block');
	},
	
	prepareOtherFields: function() {
		var hfield = document.id('jform_params_img_height');
		var parent = hfield.getParent();
		var span = hfield.getParent().getElement('span');
		hfield.inject(document.id('jform_params_img_width').getParent(), 'bottom');
		span.inject(hfield, 'after');
		parent.setStyle('display', 'none');
	}
});
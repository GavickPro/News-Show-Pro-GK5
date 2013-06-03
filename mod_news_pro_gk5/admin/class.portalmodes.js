function PortalModes() {
	this.configs = null;
	this.portalmodes = null;
	this.previous_portal_mode = '';
	this.init();
}


PortalModes.prototype.init = function() {
	// binding
	var $this = this;
	// set the array of configuration
	this.configs = [];
	this.portalmodes = [];
	// get the data sources configuration
	jQuery('.gk-json-config-pm').each(function(i, item) {
		var name = jQuery(item).attr('id').replace('gk-json-config-pm-', '');
		$this.configs[name] = JSON.decode(jQuery(item).html());
		$this.portalmodes.push(name);
	});
	// hide all portal mode tabs
	$this.hideAllPMTabs();
	// init
	$this.changeValue();
	// add events
	jQuery('#jform_params_module_mode').change( function() { $this.changeValue() });	
	jQuery('#jform_params_module_mode').focus( function() { $this.changeValue() });	
	jQuery('#jform_params_module_mode').blur( function() { $this.changeValue() });	
}

PortalModes.prototype.changeValue = function() {
	// binding
	var $this = this;
	// get the data source value ..
	var portal_mode_value = jQuery('#jform_params_module_mode').val();
	// check if the value was changed
	if($this.previous_portal_mode != portal_mode_value) {
		// set new prev value
		$this.previous_portal_mode = portal_mode_value;
		// if portal mode selected
		if(portal_mode_value != 'normal') {
			// hide article format fields
			jQuery('#jform_params_use_own_article_format-lbl').parent().parent().css('display', 'none');
			jQuery('#jform_params_article_format-lbl').parent().parent().css('display', 'none');
			// hide unnecessary tabs
			jQuery('#Joomla_Content_Additional_Settings').parents().eq(2).css('display', 'none');
			jQuery('#Links_layout').parents().eq(2).css('display', 'none');
			jQuery('#Article_layout').parents().eq(2).css('display', 'none');
			// hide thumbnails tab if not used
			if($this.configs[portal_mode_value].thumbnails == false) {
				jQuery('#Thumbnails').parents().eq(2).css('display', 'none');	
			}
			// hide all Portal Mode Tabs
			$this.hideAllPMTabs();
			// show the specific one
			if($this.configs[portal_mode_value].full_name == 'NEWS_GALLERY') {
				jQuery('#News_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PRODUCT_GALLERY') {
				jQuery('#Product_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'NEWS_BLOCKS') {
				jQuery('#News_Blocks_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'TITLE_OVERLAY') {
				jQuery('#Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PORTFOLIO') {
				jQuery('#Portfolio_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
			}
			// check the data source value
			var data_source_value = jQuery('#jform_params_data_source').val();
			var data_source_name = jQuery('#jform_params_data_source').find('option[value="'+data_source_value+'"]').attr('data-source');
			// search for the data source name
			var isSupported = false;
			jQuery($this.configs[portal_mode_value].support).each(function(i, source) {
				if(source == data_source_name) {
					isSupported = true;
				}
			});
			//
			if(!isSupported) {
				alert('Specific data source is not supported by selected Portal Mode. Please change the data source or portal mode');
			}
		} else {
			// show article format fields
			jQuery('#jform_params_use_own_article_format-lbl').parents().eq(2).css('display', 'block');
			jQuery('#jform_params_article_format-lbl').parents().eq(2).css('display', 'block');
			// show necessary tabs
			jQuery('#Joomla_Content_Additional_Settings').parents().eq(2).css('display', 'block');
			jQuery('#Links_layout').parents().eq(2).css('display', 'block');
			jQuery('#Article_layout').parents().eq(2).css('display', 'block');
			jQuery('#Thumbnails').parents().eq(2).css('display', 'block');
			// hide all Portal Mode Tabs
			$this.hideAllPMTabs();
		}
	}
}

PortalModes.prototype.hideAllPMTabs = function() {
	jQuery('#News_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
	jQuery('#Product_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
	jQuery('#News_Blocks_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
	jQuery('#Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
	jQuery('#Portfolio_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
}


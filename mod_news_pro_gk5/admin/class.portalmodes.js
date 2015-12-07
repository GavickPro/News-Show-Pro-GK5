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
	this.tabs = ['NEWS_GALLERY', 'PRODUCT_GALLERY', 'NEWS_BLOCKS', 'TITLE_OVERLAY', 'PORTFOLIO', 'PORTFOLIO2', 'CENTERED_TITLE_OVERLAY', 'PRODUCT_GALLERY_2', 'GRID_TITLE_OVERLAY', 'HIGHLIGHTS', 'VIDEOGALLERY', 'VIDEOLIST', 'JOMSOCIAL_PHOTOS', 'EVENTS_LIST', 'SPEAKERS_LIST', 'GRID_NEWS', 'FRONTPAGE_IMAGE_OVERLAY', 'HIGHLIGHTS', 'PORTFOLIO_GRID', 'NEWS_SLIDER', 'BIKESTORE_SLIDER', 'TECHNEWS_HEADER1', 'TECHNEWS_HEADER2', 'TECHNEWS_RATING', 'TECHNEWS_REVIEWS', 'NEW_NEWS_HEADER', 'NEW_NEWS_SLIDER'];
	// get the data sources configuration
	jQuery('.gk-json-config-pm').each(function(i, item) {
		var name = jQuery(item).attr('id').replace('gk-json-config-pm-', '');
		$this.configs[name] = JSON.parse(jQuery(item).html());
		$this.portalmodes.push(name);
	});
	// hide all portal mode tabs
	$this.hideAllPMTabs();
	// init
	$this.changeValue();
	// field position fix 
	jQuery('#jform_params_module_mode-lbl').parents().eq(1).append(jQuery('#jform_params_module_mode'));
	jQuery('#jform_params_module_mode').wrap('<div class="control"></div>');
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
			jQuery('a[href=#attrib-NSP_LINKS_LAYOUT]').parent().css('display', 'none');
			jQuery('a[href=#attrib-NSP_LINKS_LAYOUT]').parent().css('display', 'none');
			// hide thumbnails tab if not used
			if($this.configs[portal_mode_value].thumbnails == false) {
				jQuery('#Thumbnails').parents().eq(2).css('display', 'none');	
			}
			// hide all Portal Mode Tabs
			$this.hideAllPMTabs();
			// show the specific one
			for(var i = 0; i < $this.tabs.length; i++) {
				if($this.configs[portal_mode_value].full_name == $this.tabs[i]) {
					jQuery('a[href=#attrib-NSP_PORTAL_MODE_'+$this.tabs[i]+']').parent().css('display', 'block');
					break;
				}	
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
			jQuery('#jform_params_use_own_article_format-lbl').parents().eq(1).css('display', 'block');
			jQuery('#jform_params_article_format-lbl').parents().eq(1).css('display', 'block');
			// show necessary tabs
			jQuery('a[href=#attrib-NSP_LINKS_LAYOUT]').parent().css('display', 'block');
			jQuery('a[href=#attrib-NSP_ARTICLE_LAYOUT]').parent().css('display', 'block');
			jQuery('a[href=#attrib-NSP_THUMBNAILS]').parent().css('display', 'block');
			// hide all Portal Mode Tabs
			$this.hideAllPMTabs();
		}
	}
}

PortalModes.prototype.hideAllPMTabs = function() {	
	for(var i = 0; i < this.tabs.length; i++) {
		jQuery('a[href=#attrib-NSP_PORTAL_MODE_'+this.tabs[i]+']').parent().css('display', 'none');
	}
}

// EOF
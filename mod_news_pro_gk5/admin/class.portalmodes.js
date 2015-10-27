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
			if($this.configs[portal_mode_value].full_name == 'NEWS_GALLERY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_NEWS_GALLERY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PRODUCT_GALLERY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_PRODUCT_GALLERY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'NEWS_BLOCKS') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_NEWS_BLOCKS]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'TITLE_OVERLAY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_TITLE_OVERLAY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PORTFOLIO') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_PORTFOLIO]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PORTFOLIO2') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_PORTFOLIO2]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PRODUCT_GALLERY_2') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_PRODUCT_GALLERY_2]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'CENTERED_TITLE_OVERLAY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_CENTERED_TITLE_OVERLAY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'GRID_TITLE_OVERLAY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_GRID_TITLE_OVERLAY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'HIGHLIGHTS') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_HIGHLIGHTS]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'VIDEOGALLERY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_VIDEOGALLERY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'VIDEOLIST') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_VIDEOLIST]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'JOMSOCIAL_PHOTOS') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_JOMSOCIAL_PHOTOS]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'EVENTS_LIST') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_EVENTS_LIST]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'SPEAKERS_LIST') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_SPEAKERS_LIST]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'GRID_NEWS') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_GRID_NEWS]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'FRONTPAGE_IMAGE_OVERLAY') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_FRONTPAGE_IMAGE_OVERLAY]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'HIGHLIGHTS') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_HIGHLIGHTS]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'PORTFOLIO_GRID') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_PORTFOLIO_GRID]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'NEWS_SLIDER') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_NEWS_SLIDER]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'BIKESTORE_SLIDER') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_BIKESTORE_SLIDER]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'TECHNEWS_HEADER1') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_HEADER1]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'TECHNEWS_HEADER2') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_HEADER2]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'TECHNEWS_RATING') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_RATING]').parent().css('display', 'block');
			} else if($this.configs[portal_mode_value].full_name == 'TECHNEWS_REVIEWS') {
				jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_REVIEWS]').parent().css('display', 'block');
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
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_NEWS_GALLERY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_PRODUCT_GALLERY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_NEWS_BLOCKS]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_TITLE_OVERLAY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_PORTFOLIO]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_PORTFOLIO2]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_CENTERED_TITLE_OVERLAY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_PRODUCT_GALLERY_2]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_GRID_TITLE_OVERLAY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_HIGHLIGHTS]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_VIDEOGALLERY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_VIDEOLIST]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_JOMSOCIAL_PHOTOS]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_EVENTS_LIST]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_SPEAKERS_LIST]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_GRID_NEWS]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_FRONTPAGE_IMAGE_OVERLAY]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_HIGHLIGHTS]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_PORTFOLIO_GRID]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_NEWS_SLIDER]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_BIKESTORE_SLIDER]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_HEADER1]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_HEADER2]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_RATING]').parent().css('display', 'none');
	jQuery('a[href=#attrib-NSP_PORTAL_MODE_TECHNEWS_REVIEWS]').parent().css('display', 'none');
}

// EOF
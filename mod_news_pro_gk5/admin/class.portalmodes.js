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
	if(jQuery('#module-form').hasClass('j32')) {
		jQuery('#jform_params_module_mode-lbl').parents().eq(1).append(jQuery('#jform_params_module_mode'));
		jQuery('#jform_params_module_mode').wrap('<div class="control"></div>')
	}
	// add events
	jQuery('#jform_params_module_mode').change( function() { $this.changeValue() });	
	jQuery('#jform_params_module_mode').focus( function() { $this.changeValue() });	
	jQuery('#jform_params_module_mode').blur( function() { $this.changeValue() });	
}

PortalModes.prototype.changeValue = function() {
	// binding
	var $this = this;
	var j32 = false;
	if(jQuery('#module-form').hasClass('j32')) { j32 = true; }
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
			if(!j32) {
				//jQuery('#Joomla_Content_Additional_Settings').parents().eq(2).css('display', 'none');
				jQuery('#Links_layout').parents().eq(2).css('display', 'none');
				jQuery('#Article_layout').parents().eq(2).css('display', 'none');
			} else {
				//jQuery('a[href=#attrib-NSP_DATA_SOURCE_COM_CONTENT]').parent().css('display', 'none');
				jQuery('a[href=#attrib-NSP_LINKS_LAYOUT]').parent().css('display', 'none');
				jQuery('a[href=#attrib-NSP_LINKS_LAYOUT]').parent().css('display', 'none');
			}
			// hide thumbnails tab if not used
			if($this.configs[portal_mode_value].thumbnails == false) {
				jQuery('#Thumbnails').parents().eq(2).css('display', 'none');	
			}
			// hide all Portal Mode Tabs
			$this.hideAllPMTabs();
			// show the specific one
			
			if(!j32) {
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
				} else if($this.configs[portal_mode_value].full_name == 'PORTFOLIO') {
					jQuery('#Portfolio_II_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'PRODUCT_GALLERY_2') {
					jQuery('#Product_Gallery_II_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'CENTERED_TITLE_OVERLAY') {
					jQuery('#Centered_Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'GRID_TITLE_OVERLAY') {
					jQuery('#Grid_Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'HIGHLIGHTS') {
					jQuery('#Highlights_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'VIDEOGALLERY') {
					jQuery('#Video_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'VIDEOLIST') {
					jQuery('#Video_List_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'SPEAKERS_LIST') {
					jQuery('#Speakers_List_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'GRID_NEWS') {
					jQuery('#Grid_News_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'NEWS_SLIDER') {
					jQuery('#News_Slider_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				} else if($this.configs[portal_mode_value].full_name == 'BIKESTORE_SLIDER') {
					jQuery('#Bikestore_Slider_-_Portal_Mode_Settings').parents().eq(2).css('display', 'block');
				}
			} else {
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
			if(!j32) {
				//jQuery('#Joomla_Content_Additional_Settings').parents().eq(2).css('display', 'block');
				jQuery('#Links_layout').parents().eq(2).css('display', 'block');
				jQuery('#Article_layout').parents().eq(2).css('display', 'block');
				jQuery('#Thumbnails').parents().eq(2).css('display', 'block');
			} else {
				//jQuery('a[href=#attrib-NSP_DATA_SOURCE_COM_CONTENT]').parent().css('display', 'block');
				jQuery('a[href=#attrib-NSP_LINKS_LAYOUT]').parent().css('display', 'block');
				jQuery('a[href=#attrib-NSP_ARTICLE_LAYOUT]').parent().css('display', 'block');
				jQuery('a[href=#attrib-NSP_THUMBNAILS]').parent().css('display', 'block');
			}
			// hide all Portal Mode Tabs
			$this.hideAllPMTabs();
		}
	}
}

PortalModes.prototype.hideAllPMTabs = function() {
	var j32 = false;
	if(jQuery('#module-form').hasClass('j32')) { j32 = true; }
	if(!j32) {
		jQuery('#News_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Product_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#News_Blocks_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Portfolio_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Portfolio2_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Centered_Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Product_Gallery_II_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Grid_Title_Overlay_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Highlights_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Video_Gallery_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Video_List_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Speakers_List_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Grid_News_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Grid_News_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Grid_News_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#News_Slider_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
		jQuery('#Bikestore_Slider_-_Portal_Mode_Settings').parents().eq(2).css('display', 'none');
	} else {
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
	}
}

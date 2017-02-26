// class used with data sources
function DataSources() {
	this.configs = null;
	this.datasources = null;
	this.previous_data_source = '';
	
	this.init();
}

DataSources.prototype.init = function() {
	// binding
	var $this = this;
	// set the array of configuration
	this.configs = [];
	this.datasources = [];
	// get the data sources configuration
	jQuery('.gk-json-config').each(function(i, item) {
		item = jQuery(item);
		var name = item.attr('id').replace('gk-json-config-', '');
		$this.configs[name] = JSON.parse(item.html());
		$this.datasources.push(name);
	});
	// field position fix	
	jQuery('#jform_params_data_source-lbl').parents().eq(1).append(jQuery('#jform_params_data_source'));
	jQuery('#jform_params_data_source').wrap('<div class="control"></div>');
	// hide hidden fields
	jQuery('.gk-hidden-field').each(function(i, field) {
		jQuery(field).parent().parent().css('display', 'none');
	});
	
	// init
	this.changeValue();
	// add events
	jQuery('#jform_params_data_source').change( function() { $this.changeValue() });
	jQuery('#jform_params_data_source').focus( function() { $this.changeValue() });
	jQuery('#jform_params_data_source').blur( function() { $this.changeValue() });

}

DataSources.prototype.changeValue = function() {
	// binding
	var $this = this;
	// get the data source value ..
	var data_source_value = jQuery('#jform_params_data_source').val();
	// get the name of data source
	var option_field = jQuery('#jform_params_data_source').find('option[value="'+data_source_value+'"]');
	var data_source_name = option_field.attr('data-source');
	// hide tabs with settings for unused data sources
	jQuery($this.datasources).each(function(i, obj) {
		obj = $this.configs[obj];
		
		if(obj.source != data_source_name) {
			jQuery('#'+obj.tab).parents().eq(2).css('display', 'none');	
			jQuery('a[href="#attrib-'+obj.tabj3+'"]').parent().css('display', 'none');
		} else  {
			jQuery('#'+obj.tab).parents().eq(2).css('display', 'block');
			jQuery('a[href="#attrib-'+obj.tabj3+'"]').parent().css('display', 'block');
		}
	});	
	// hide previously showed field (if exists)
	if(jQuery('.gk-used-option')) {
		if(jQuery('.gk-used-option').attr('id') != 'jform_params_' + data_source_value) {
			if(jQuery('.gk-used-option').parent().hasClass('input-append')) {
				jQuery('.gk-used-option').parents().eq(2).css('display', 'none');
			}
			jQuery('.gk-used-option').parent().parent().css('display', 'none');
			jQuery('.gk-used-option').removeClass('gk-used-option');
		}
	}
	
	// show the field connected with the selected option
	if(jQuery('#jform_params_' + data_source_value)) {
		jQuery('#jform_params_' + data_source_value).addClass('gk-used-option');
		jQuery('#jform_params_' + data_source_value).parent().parent().css('display', 'block');
	}
	
	// change some fields only if the data source was changed
	if(this.previous_data_source != data_source_value) {
		//
		this.previous_data_source = data_source_value;
		//
		if(data_source_value != '' && data_source_value != null) {
			// .. and show the proper fields ..
			var option_field = jQuery('#jform_params_data_source').find('option[value="'+data_source_value+'"]');
			var data_source = jQuery(option_field).attr('data-source');
			jQuery('#jform_params_source_name').val(data_source);
			// read the config for the specific data source
			jQuery(['offset', 'news_since', 'news_in', 'news_featured', 'only_featured', 'news_unauthorized', 'time_offset']).each(function(i, key) {
				if($this.configs[data_source].supported_options[key]) {
					if(jQuery('#jform_params_' + key).parent().hasClass('input-append')) {
						jQuery('#jform_params_' + key).parents().eq(2).css('display', 'block');
					} else {
						jQuery('#jform_params_' + key).parent().parent().css('display', 'block');
					}
				} else {
					if(jQuery('#jform_params_' + key).parent().hasClass('input-append')) {
						jQuery('#jform_params_' + key).parents().eq(2).css('display', 'none');
					} else {
						jQuery('#jform_params_' + key).parent().parent().css('display', 'none');
					}
				}
			});
			// .. add the proper options to data sorting fields
			var news_sort = jQuery('#jform_params_news_sort_value');
			news_sort.empty();
			var news_sort_value = news_sort.attr('data-value');
			
			if($this.configs[data_source].supported_options.sort_values != false) {
				news_sort.parent().parent().css('display', 'block');
				jQuery('#jform_params_news_sort_order').parent().parent().css('display', 'block');
				
				for(val in $this.configs[data_source].supported_options.sort_values) {
					var opt = new jQuery('<option>', {
						'value': $this.configs[data_source].supported_options.sort_values[val],
						'text': val
					});
					
					if(news_sort_value == $this.configs[data_source].supported_options.sort_values[val]) {
						opt.attr('selected', 'selected');
					}
					news_sort.append(opt);
				}
			} else {
				news_sort.parent().parent().css('display', 'none');
				jQuery('#jform_params_news_sort_order').parent().parent().css('display', 'none');
			}
			// .. hide/show proper areas available in the specific data source
			jQuery(['header', 'image', 'text', 'info']).each(function(i, key) {
				var toggler = jQuery('div[data-section-toggle="' + key + '"]');
				
				if($this.configs[data_source].supported_elements[key]) {
					jQuery(toggler).parent().parent().css('display', 'block');
				} else {
					jQuery(toggler).parent().parent().css('display', 'none');
				}
				
				if(toggler.hasClass('open')) {
					jQuery(toggler).trigger('click');
				}
			});
			// .. and also elements available in the layout:
			jQuery(['header', 'image', 'text', 'info', 'info2', 'readmore']).each(function(i, key) {
				var element = jQuery('#jform_params_news_'+key+'_enabled').parent(); // maybe second parent
				
				if($this.configs[data_source].supported_elements[key]) {
					element.css('display', 'block');
				} else {
					element.css('display', 'none');
				}
			});
		}
		
		// check the data source value
		var portal_mode_value = jQuery('#jform_params_module_mode').val();
		// if the portal mode is used
		if(portal_mode_value != 'normal') {
			var portal_mode_config = JSON.parse(jQuery('#gk-json-config-pm-'+portal_mode_value).html());
			// search for the data source name
			var isSupported = false;
			jQuery(portal_mode_config.support).each(function(i, source) {
				if(source == data_source) {
					isSupported = true;
				}
			});
			//
			if(!isSupported) {
				alert('Specific data source is not supported by selected Portal Mode. Please change the data source or portal mode');
			}
		}
	}
}

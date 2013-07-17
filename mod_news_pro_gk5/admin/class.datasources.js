// class used with data sources
var DataSources = new Class({
	// array of the configuration
	configs: null,
	datasources: null,
	previous_data_source: '',
	
	initialize: function() {
		// binding
		var $this = this;
		// set the array of configuration
		this.configs = [];
		this.datasources = [];
		// get the data sources configuration
		document.getElements('.gk-json-config').each(function(item, i) {
			var name = item.getProperty('id').replace('gk-json-config-', '');
			$this.configs[name] = JSON.decode(item.innerHTML);
			$this.datasources.push(name);
		});
		// hide hidden fields
		document.getElements('.gk-hidden-field').each(function(field, i) {
			field.getParent().setStyle('display', 'none');
		});
		
		// init
		this.changeValue();
		
		// add events
		document.id('jform_params_data_source').addEvents({
			'change': function() { $this.changeValue() },		
			'focus': function() { $this.changeValue() },
			'blur': function() { $this.changeValue() }
		});
	},
	
	changeValue: function() {
		// binding
		var $this = this;
		// get the data source value ..
		var data_source_value = document.id('jform_params_data_source').get('value');
		// get the name of data source
		var option_field = document.id('jform_params_data_source').getElement('option[value="'+data_source_value+'"]');
		var data_source_name = option_field.getProperty('data-source');
		// hide tabs with settings for unused data sources
		$this.datasources.each(function(obj) {
			obj = $this.configs[obj];
			
			if(obj.source != data_source_name && document.id(obj.tab)) {
				document.id(obj.tab).getParent().setStyle('display', 'none');	
			} else if(document.id(obj.tab)) {
				document.id(obj.tab).getParent().setStyle('display', 'block');
			}
		});	
		// hide previously showed field (if exists)
		if(document.getElement('.gk-used-option')) {
			if(document.getElement('.gk-used-option').getProperty('id') != 'jform_params_' + data_source_value) {
				document.getElement('.gk-used-option').getParent().setStyle('display', 'none');
				document.getElement('.gk-used-option').removeClass('gk-used-option');
			}
		}
		// show the field connected with the selected option
		if(document.id('jform_params_' + data_source_value)) {
			document.id('jform_params_' + data_source_value).addClass('gk-used-option');
			document.id('jform_params_' + data_source_value).getParent().setStyle('display', 'block');
		}
		
		// change some fields only if the data source was changed
		if(this.previous_data_source != data_source_value) {
			//
			this.previous_data_source = data_source_value;
			//
			if(data_source_value != '' && data_source_value != null) {
				// .. and show the proper fields ..
				var option_field = document.id('jform_params_data_source').getElement('option[value="'+data_source_value+'"]');
				var data_source = option_field.getProperty('data-source');
				document.id('jform_params_source_name').set('value', data_source);
				// read the config for the specific data source
				['offset', 'news_since', 'news_in', 'news_featured', 'only_featured', 'news_unauthorized', 'time_offset'].each(function(key) {
					if($this.configs[data_source].supported_options[key]) {
						document.id('jform_params_' + key).getParent().setStyle('display', 'block');
					} else {
						document.id('jform_params_' + key).getParent().setStyle('display', 'none');
					}
				});
				// .. add the proper options to data sorting fields
				var news_sort = document.id('jform_params_news_sort_value');
				news_sort.empty();
				var news_sort_value = news_sort.getProperty('data-value');
				
				if($this.configs[data_source].supported_options.sort_values != false) {
					news_sort.getParent().setStyle('display', 'block');
					document.id('jform_params_news_sort_order').getParent().setStyle('display', 'block');
					
					for(val in $this.configs[data_source].supported_options.sort_values) {
						var opt = new Element('option', {
							'value': $this.configs[data_source].supported_options.sort_values[val],
							'text': val
						});
						
						if(news_sort_value == $this.configs[data_source].supported_options.sort_values[val]) {
							opt.setProperty('selected', 'selected');
						}
						
						opt.inject(news_sort, 'bottom');
					}
				} else {
					news_sort.getParent().setStyle('display', 'none');
					document.id('jform_params_news_sort_order').getParent().setStyle('display', 'none');
				}
				// .. hide/show proper areas available in the specific data source
				['header', 'image', 'text', 'info'].each(function(key) {
					var toggler = document.getElement('div[data-section-toggle="' + key + '"]');
					
					if($this.configs[data_source].supported_elements[key]) {
						toggler.getParent().setStyle('display', 'block');
					} else {
						toggler.getParent().setStyle('display', 'none');
					}
					
					if(toggler.hasClass('open')) {
						toggler.fire('click');
					}
				});
				// .. and also elements available in the layout:
				['header', 'image', 'text', 'info', 'info2', 'readmore'].each(function(key) {
					var element = document.id('jform_params_news_'+key+'_enabled').getParent();
					
					if($this.configs[data_source].supported_elements[key]) {
						element.setStyle('display', 'block');
					} else {
						element.setStyle('display', 'none');
					}
				});
			}
			// check the data source value
			var portal_mode_value = document.id('jform_params_module_mode').get('value');
			// if the portal mode is used
			if(portal_mode_value != 'normal') {
				var portal_mode_config = JSON.decode(document.id('gk-json-config-pm-'+portal_mode_value).innerHTML);
				// search for the data source name
				var isSupported = false;
				portal_mode_config.support.each(function(source) {
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
});
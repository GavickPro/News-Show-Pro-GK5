// class used with portal modes
var PortalModes = new Class({
	// array of the configuration
	configs: null,
	portalmodes: null,
	previous_portal_mode: '',
	// constructor
	initialize: function() {
		// binding
		var $this = this;
		// set the array of configuration
		this.configs = [];
		this.portalmodes = [];
		// get the data sources configuration
		document.getElements('.gk-json-config-pm').each(function(item, i) {
			var name = item.getProperty('id').replace('gk-json-config-pm-', '');
			$this.configs[name] = JSON.decode(item.innerHTML);
			$this.portalmodes.push(name);
		});
		// hide all portal mode tabs
		$this.hideAllPMTabs();
		// init
		$this.changeValue();
		// add events
		document.id('jform_params_module_mode').addEvents({
			'change': function() { $this.changeValue() },		
			'focus': function() { $this.changeValue() },
			'blut': function() { $this.changeValue() }
		});
	},
	// event of the change
	changeValue: function() {
		// binding
		var $this = this;
		// get the data source value ..
		var portal_mode_value = document.id('jform_params_module_mode').get('value');
		// check if the value was changed
		if($this.previous_portal_mode != portal_mode_value) {
			// set new prev value
			$this.previous_portal_mode = portal_mode_value;
			// if portal mode selected
			if(portal_mode_value != 'normal') {
				// hide article format fields
				document.id('jform_params_use_own_article_format-lbl').getParent().setStyle('display', 'none');
				document.id('jform_params_article_format-lbl').getParent().setStyle('display', 'none');
				// hide unnecessary tabs
				document.id('NSP_ADDITIONAL_SETTINGS-options').getParent().setStyle('display', 'none');
				document.id('NSP_LINKS_LAYOUT-options').getParent().setStyle('display', 'none');
				document.id('NSP_ARTICLE_LAYOUT-options').getParent().setStyle('display', 'none');
				// hide thumbnails tab if not used
				if($this.configs[portal_mode_value].thumbnails == false) {
					document.id('NSP_THUMBNAILS-options').getParent().setStyle('display', 'none');	
				}
				// hide all Portal Mode Tabs
				$this.hideAllPMTabs();
				// show the specific one
				document.id('NSP_PORTAL_MODE_' + $this.configs[portal_mode_value].full_name + '-options').getParent().setStyle('display', 'block');
				// check the data source value
				var data_source_value = document.id('jform_params_data_source').get('value');
				var data_source_name = document.id('jform_params_data_source').getElement('option[value="'+data_source_value+'"]').getProperty('data-source');
				// search for the data source name
				var isSupported = false;
				$this.configs[portal_mode_value].support.each(function(source) {
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
				document.id('jform_params_use_own_article_format-lbl').getParent().setStyle('display', 'block');
				document.id('jform_params_article_format-lbl').getParent().setStyle('display', 'block');
				// show necessary tabs
				document.id('NSP_ADDITIONAL_SETTINGS-options').getParent().setStyle('display', 'block');
				document.id('NSP_LINKS_LAYOUT-options').getParent().setStyle('display', 'block');
				document.id('NSP_ARTICLE_LAYOUT-options').getParent().setStyle('display', 'block');
				document.id('NSP_THUMBNAILS-options').getParent().setStyle('display', 'block');
				// hide all Portal Mode Tabs
				$this.hideAllPMTabs();
			}
		}
	},
	// function used to hide all Portal Mode Tabs
	hideAllPMTabs: function() {
		document.getElements('.panel *[id^="NSP_PORTAL_MODE"]').each(function(item) {
			item.getParent().setStyle('display', 'none');
		});
	}
});
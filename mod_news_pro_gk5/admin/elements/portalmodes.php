<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

if(!function_exists('json_last_error')) {
	define('JSON_ERROR_NONE', 1);
	
	function json_last_error() {
		return JSON_ERROR_NONE;
	}
}

class JFormFieldPortalModes extends JFormField {
	//
	protected $type = 'PortalModes';
	//
	protected function getInput() {
		// output for options
		$output_options = '';
		$output_configs = '';
		// prefix for the language files
		$pre = 'MOD_NEWS_PRO_GK5_';
		// get folders with data sources
		$folders = JFolder::listFolderTree(JPATH_SITE . '/modules/mod_news_pro_gk5/portal_modes/', '', 1);	
		//
		$json_data = null;
		// iterate through data source folders
		foreach($folders as $folder) {
			// check if the data source contains the configuration.json file
			if(JFile::exists($folder['fullname'] . '/configuration.json')) {
				// read JSON from this data source
				$file_content = JFile::read($folder['fullname'] . '/configuration.json');
				// parse JSON
				$json_data = json_decode($file_content);
				// if the JSON file is correct
				if(json_last_error() === JSON_ERROR_NONE) {
					// generate the header option
					$output_options .= '<option value="'.$json_data->name.'" '.(($this->value == $json_data->name) ? ' selected="selected"' : '').'>'.JText::_($pre . $json_data->full_name).'</option>'; 
					// parse file content and put translations
					$json_matches = array();
					preg_match_all('@\"MOD_NEWS_PRO_GK5_.*?\"@mis', $file_content, $json_matches);
					//
					if(isset($json_matches[0]) && count($json_matches[0]) > 0 && strlen($json_matches[0][0]) > 0) {
						foreach($json_matches[0] as $translations) {
							$phrase = str_replace('"', '', $translations);
							$file_content = str_replace($phrase, '\'.JText::_("'.$phrase.'").\'', $file_content);
						}
					}
					//
					$file_content = "'" . $file_content . "'";
					$out_fn = create_function('', 'return ' . $file_content . ';');
					// output the config
					$output_configs .= '<div class="gk-json-config-pm" id="gk-json-config-pm-'.$json_data->name.'">'.$out_fn().'</div>';
				}
			}
		}
		// output the select
		echo '<select id="'.$this->id.'" name="'.$this->name.'"><option value="normal"'.(($this->value == 'normal') ? ' selected="selected"' : '').'>'.JText::_('MOD_NEWS_PRO_GK5_NORMAL_MODE').'</option>'.$output_options.'</select>';
		echo $output_configs;
	}
}

/* EOF */
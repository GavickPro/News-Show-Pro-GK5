<?php

/**
 *
 * This Model is responsible for getting data from the
 * com_content data source
 *
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_json_file_Model {
	// Method to get sources of articles
	static function getSources($config) {
		$content = array();
		// if there are selected files - set the variables
		if($config['json_file'] != -1) {
			// read the file content
			$json_content = file_get_contents(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'external_data' . DS . $config['json_file']);			
			// save the data to the temp array
			$temp = json_decode($json_content);
			//
			if(json_last_error() === JSON_ERROR_NONE) {
				// parse it
				for($i = 0; $i < count($temp); $i++) {
					array_push($content, (array) $temp[$i]);
				}
			} else {
				$content = array();
			}
		}
		
		return $content;
	}
	// Method to get articles in standard mode 
	static function getArticles($items, $config, $amount) {	
		$content = array();
		//
		for($i = 0; $i < $amount; $i++) {
			if(isset($items[$i])) {
				array_push($content, $items[$i]);
			}
		}
		// the content array
		return $content; 
	}
}

// EOF
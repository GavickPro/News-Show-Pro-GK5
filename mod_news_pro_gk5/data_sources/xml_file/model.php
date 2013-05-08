<?php

/**
 *
 * This Model is responsible for getting data from the
 * com_content data source
 *
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_xml_file_Model {
	// Method to get sources of articles
	static function getSources($config) {
		$content = array();
		// if there are selected files - set the variables
		if($config['xml_file'] != -1 && file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
			// loading file content
			$file_content = file_get_contents(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'external_data' . DS . $config['xml_file']);
			//
			$xml = new SimpleXMLElement($file_content);
			//
			if(count($xml->article) > 0) {
				//
				$art = array();
				//
				foreach ($xml->article as $element) {
					//
					foreach($element as $key => $value) {
						$art[$key] = (string) $value;
					}
					//
					array_push($content, (array) $art);
				}
			}
		}
		//
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
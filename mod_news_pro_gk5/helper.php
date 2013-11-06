<?php

/**
* Helper file
* @package News Show Pro GK5
* @Copyright (C) 2009-2012 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

// import com_content route helper
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

// import JString class for UTF-8 problems
jimport('joomla.utilities.string'); 
jimport('joomla.application.component.helper');

// Main class
class NSP_GK5_Helper {
	
	var $config = null; // configuration array
	var $content = array(); // array with generated content
	var $module_id = 0; // module id used in JavaScript
	var $source = null;
	
	// module initialization
	function init($module, $params) {  
		// getting module ID - automatically (from Joomla! database) or manually
		$this->module_id = ($params->get('module_unique_id', '') == '') ? 'nsp-'.$module->id : $params->get('module_unique_id', '');
		$this->config = $params->toArray();
		$this->config['module_id'] = $this->module_id;
		// detect the data source
		$this->source = $this->config["source_name"];
		// if the user set engine mode to Mootools
		if($this->config['engine_mode'] == 'mootools') {
			// load the MooTools framework to use with the module
			JHtml::_('behavior.framework', true);
		} else if($this->config['include_jquery'] == 1) {
			// if the user specify to include the jQuery framework - load newest jQuery 1.7.* 
			$doc = JFactory::getDocument();
			// generate keys of script section
			$headData = $doc->getHeadData();
			$headData_keys = array_keys($headData["scripts"]);
			// set variable for false
			$engine_founded = false;
			// searching phrase mootools in scripts paths
			if(array_search('https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js', $headData_keys) > 0) {
				$engine_founded = true;
			}
			//
			if(!$engine_founded) {
				$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js');
			}
		}
        // small validation
		if($this->config['list_title_limit'] == 0 && $this->config['list_text_limit'] == 0) {
			$this->config['news_short_pages'] = 0;
		}

		if($this->config['news_header_enabled'] == 0) $this->config['news_content_header_pos'] = 'disabled';
		if($this->config['news_image_enabled']  == 0) $this->config['news_content_image_pos'] = 'disabled';
		if($this->config['news_text_enabled']  == 0) $this->config['news_content_text_pos'] = 'disabled';
		if($this->config['news_info_enabled'] == 0) $this->config['news_content_info_pos'] = 'disabled';
		if($this->config['news_info2_enabled'] == 0) $this->config['news_content_info2_pos'] = 'disabled';
		if($this->config['news_readmore_enabled'] == 0) $this->config['news_content_readmore_pos'] = 'disabled';		
		// override old string-based rules with the more readable array structures
		$this->config['crop_rules'] = NSP_GK5_Utils::parseCropRules($this->config);
	}
	// GETTING DATA
	function getDatas(){
		//
		if(!class_exists('NSP_GK5_'.$this->source.'_Model')) {
			require_once (dirname(__FILE__).DS.'data_sources'.DS.$this->source.DS.'model.php');
		}
		//
		$mode = $this->config['module_mode'];
		//
		if($mode != 'normal' && !class_exists('NSP_GK5_'.$mode)) {
			require_once (dirname(__FILE__).DS.'portal_modes'.DS.strtolower($mode).DS.'controller.php');
		}
		//
		$db = JFactory::getDBO();
		// Getting list of categories
		$model_class = 'NSP_GK5_'.$this->source.'_Model';
		// PHP 5.3:
		//$categories = $model_class::getSources($this->config);	
		$categories = call_user_func(array($model_class, "getSources"), $this->config);			
		// getting content
		$amountOfArts = 0;
		// check if the portal mode is used
		if($this->config['module_mode'] == 'normal') {
			$amountOfArts = ($this->config['news_column'] * $this->config['news_rows'] * $this->config['news_full_pages']) + ($this->config['links_amount'] * $this->config['news_short_pages'] * $this->config['links_columns_amount']);
		} else {
			$portal_mode_class = 'NSP_GK5_'.$mode;
			// PHP 5.3:
			//$amountOfArts = $portal_mode_class::amount_of_articles($this);
			$amountOfArts = call_user_func(array($portal_mode_class, "amount_of_articles"), $this);
		}
		// PHP 5.3:
		//$this->content = $model_class::getArticles($categories, $this->config, $amountOfArts);
		$this->content = call_user_func(array($model_class, "getArticles"), $categories, $this->config, $amountOfArts);
	}
	// RENDERING LAYOUT
	function renderLayout() {	
		if($this->config['module_mode'] !== 'normal') {
			$this->render_portal_mode($this->config['module_mode']);
			//
			// Load CSS and JS of Portal Mode
			//
			// create instances of basic Joomla! classes
			$document = JFactory::getDocument();
			$uri = JURI::getInstance();
			// add stylesheets to document header
			if($this->config["useCSS"] == 1) {
				$document->addStyleSheet( $uri->root().'modules/mod_news_pro_gk5/portal_modes/'.strtolower($this->config['module_mode']).'/style.css', 'text/css' );
			}
			// add script to the document header
			if($this->config['useScript'] == 1) {
				$document->addScript($uri->root().'modules/mod_news_pro_gk5/portal_modes/'.strtolower($this->config['module_mode']).'/script.'.($this->config['engine_mode']).'.js');
			}
			// init $headData variable
			$headData = false;
			// add scripts with automatic mode to document header
			if($this->config['useScript'] == 2) {
				// getting module head section datas
				unset($headData);
				$headData = $document->getHeadData();
				// generate keys of script section
				$headData_keys = array_keys($headData["scripts"]);
				// set variable for false
				$engine_founded = false;
				// searching phrase mootools in scripts paths
				if(array_search($uri->root().'modules/mod_news_pro_gk5/portal_modes/'.strtolower($this->config['module_mode']).'/scripts.'.($this->config['engine_mode']).'.js', $headData_keys) > 0) {
					$engine_founded = true;
				}
				// if engine doesn't exists in the head section
				if(!$engine_founded){ 
					// add new script tag connected with mootools from module
					$document->addScript($uri->root().'modules/mod_news_pro_gk5/portal_modes/'.strtolower($this->config['module_mode']).'/script.'.($this->config['engine_mode']).'.js');
				}
			}
		} else {	
			//
			if(!class_exists('NSP_GK5_'.$this->source.'_Controller')) {
				require_once (dirname(__FILE__).DS.'data_sources'.DS.$this->source.DS.'controller.php');
			}
			//
			if(!class_exists('NSP_GK5_'.$this->source.'_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', $this->source.'/view'));
			}
			/** GENERATING FINAL XHTML CODE START **/
			$controller_class = 'NSP_GK5_'.$this->source.'_Controller';
			$controller = new $controller_class();
			$controller_data = $controller->initialize($this->config, $this->content);
			
			$news_html_tab = $controller_data['arts'];
			$news_list_tab = $controller_data['list'];
			$news_featured_tab = $controller_data['featured'];
			
			$news_config_json = '{
				"animation_speed": '.($this->config['animation_speed']).',
				"animation_interval": '.($this->config['animation_interval']).',
				"animation_function": "'.($this->config['animation_function']).'",
				"news_column": '.($this->config['news_column']).',
				"news_rows": '.($this->config['news_rows']).',
				"links_columns_amount": '.($this->config['links_columns_amount']).',
				"links_amount": '.($this->config['links_amount']).'
			}';
			// 
			$news_config_json = str_replace('"', '\'', $news_config_json);
			
			// create instances of basic Joomla! classes
			$document = JFactory::getDocument();
			$uri = JURI::getInstance();
			// add stylesheets to document header
			if($this->config["useCSS"] == 1) {
				$document->addStyleSheet( $uri->root().'modules/mod_news_pro_gk5/interface/css/style.css', 'text/css' );
			}
			// add script to the document header
			if($this->config['useScript'] == 1) {
				$document->addScript($uri->root().'modules/mod_news_pro_gk5/interface/scripts/engine.'.($this->config['engine_mode']).'.js');
			}
			// init $headData variable
			$headData = false;
			// add scripts with automatic mode to document header
			if($this->config['useScript'] == 2) {
				// getting module head section datas
				unset($headData);
				$headData = $document->getHeadData();
				// generate keys of script section
				$headData_keys = array_keys($headData["scripts"]);
				// set variable for false
				$engine_founded = false;
				// searching phrase mootools in scripts paths
				if(array_search($uri->root().'modules/mod_news_pro_gk5/interface/scripts/engine.'.($this->config['engine_mode']).'.js', $headData_keys) > 0) {
					$engine_founded = true;
				}
				// if engine doesn't exists in the head section
				if(!$engine_founded){ 
					// add new script tag connected with mootools from module
					$document->addScript($uri->root().'modules/mod_news_pro_gk5/interface/scripts/engine.'.($this->config['engine_mode']).'.js');
				}
			}
			//
			require(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'default'));
		}
    }
    // RENDER PORTAL MODE LAYOUT
	function render_portal_mode($mode) {
		if(!class_exists('NSP_GK5_'.$mode)) {
			require_once (dirname(__FILE__).DS.'portal_modes'.DS.strtolower($mode).DS.'controller.php');
		}

		$class_name = 'NSP_GK5_'.$mode;
		$renderer = new $class_name($this);
		$renderer->output();
	}
}

// EOF
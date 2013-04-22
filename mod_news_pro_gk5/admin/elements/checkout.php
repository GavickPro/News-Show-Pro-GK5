<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldCheckout extends JFormField {
	protected $type = 'Checkout';
	
	protected function getInput() {
		// get the other fields params
		$params = new GK_Params($this->form->getFieldset());
		// start the output
		$output = '<ul id="nsp-gk5-checkout">';
		// GD library
		if (!(extension_loaded('gd') && function_exists('gd_info'))) {
			$output .= '<li>'.MOD_NEWS_PRO_GK5_PROBLEM_GD_LIB.'</li>';
		}
		// cURL library
		if(!function_exists('curl_init')) {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_PROBLEM_CURL').'</li>';
		}
		// cache directory permissions
		if(!is_writable(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache')) {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_PROBLEM_CACHE_DIR').'</li>';
		}
		// cache time
		if($params->get('cache_time') < 5) {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_PROBLEM_CACHE_TIME').'</li>';
		}
		// if all is OK 
		if($output == '<ul id="nsp-gk5-checkout">') {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_NO_PROBLEMS_DETECTED').'</li>';
		}
		// close the output tags
		$output .= '</ul>';
	
		return $output;
	}
}

/* Class used to get the other form fields values */
class GK_Params {
	private $fields;
	
	public function __construct($fields) {
    	// initialize the fields
    	$this->fields = $fields;
   	}
    // getter
   	public function get($param) {     
    	// iterate through all fields
    	foreach($this->fields as $field) {
    		// if the value is the same as the searched field ...
        	if ( $field->name == 'jform[params]['.$param.']' || $field->name == 'jform[params]['.$param.'][]' ) {
        		// ... return its value
        		return $field->value;
         	}
      	}
   	}
}

// EOF
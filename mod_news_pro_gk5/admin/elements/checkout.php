<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldCheckout extends JFormField {
	protected $type = 'Checkout';
	
	protected function getInput() {
		/*
		
		* brak biblioteki GD 
		* brak biblioteki cURL
		* uprawnienia dla katalogów z cache obrazków!
		
		*/
		
		$output = '<ul id="nsp-gk5-checkout">';
	
		if (!(extension_loaded('gd') && function_exists('gd_info'))) {
			$output .= '<li>'.MOD_NEWS_PRO_GK5_PROBLEM_GD_LIB.'</li>';
		}
		
		if(!function_exists('curl_init')) {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_PROBLEM_CURL').'</li>';
		}
		
		if(!is_writable(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache')) {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_PROBLEM_CACHE_DIR').'</li>';
		}
		
		if($output == '<ul id="nsp-gk5-checkout">') {
			$output .= '<li>'.JText::_('MOD_NEWS_PRO_GK5_NO_PROBLEMS_DETECTED').'</li>';
		}
		
		$output .= '</ul>';
	
		return $output;
	}
}

// EOF
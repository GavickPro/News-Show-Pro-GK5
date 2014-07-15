<?php

class NSP_GK5_View {
	function textPlugins($text, $config) {
		// PARSING PLUGINS
		if($config['parse_plugins'] == TRUE) {
			$text = JHtml::_('content.prepare', $text);
		}	
		// CLEANING PLUGINS
		if($config['clean_plugins'] == TRUE) {
			$text = preg_replace("/(\{.+?\}.+?\{.+?})|(\{.+?\})/", "", $text);
			$text = preg_replace("/(\[.+?\].+?\[.+?])|(\[.+?\])/", "", $text);
		}
		
		return $text; 
	}
}

// EOF
<?php
/**
* Utils class
* @package News Show Pro GK5
* @Copyright (C) 2009-2012 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
class NSP_GK5_Utils {
	// Method to cut text with specified limit value and type (characters/words)
	static function cutText($text, $config, $field, $at_end = FALSE) {
		$limit_value = $config[$field];
		$limit_type = $config[$field . '_type'];
		//
		if($at_end === FALSE) {
			$at_end = $config['more_text_value'];
		}
		// solved problem from: https://www.gavick.com/support/forums/47/12309.html?p=57464#p57464
		$cck_path = JPATH_BASE . DS . 'components' . DS . 'com_cck';
		if (file_exists($cck_path)) {
			if(JComponentHelper::isEnabled('com_cck', true)){
				// Force parsing plugin if SEBLOD is used
				if($config['parse_plugins'] == FALSE) {
					 $text = JHtml::_('content.prepare', $text);
				}
				$text = trim(substr(strip_tags( $text,"<br /><br><strong></strong><p></p><i></i><b></b><span></span><ul></ul><li></li><blockquote></blockquote>"),0));
			}
		}
		if($config['clean_xhtml'] == 1) {
			$allowed_html = '';
			
			if(strlen(trim($config['allowed_tags'])) > 0) {
				$allowed_html = explode(',', $config['allowed_tags']);		
				$allowed_len = count($allowed_html);
				
				for($i = 0; $i < $allowed_len; $i++) {
					$allowed_html[$i] = '<' . $allowed_html[$i] . '>';
				}
				
				$allowed_html = implode('', $allowed_html);
			}
			
			if($limit_type == 'words' && $limit_value > 0){
				$temp = explode(' ', strip_tags($text, $allowed_html));
			
				if(count($temp) > $limit_value){
					for($i=0; $i<$limit_value; $i++) $cutted[$i] = $temp[$i];
					$cutted = implode(' ', $cutted);
					$cutted = rtrim($cutted, '\'"!,.');
					$text = $cutted . $at_end;
				}
			} elseif($limit_type == 'words' && $limit_value == 0) {
				return '';
			} else {
				if(JString::strlen($text) > $limit_value){
					$cutted = JString::substr(strip_tags($text, $allowed_html), 0, $limit_value);
					$cutted = rtrim($cutted, '\'"!,.');
					$text = $cutted . $at_end;
				}
			}
		} else {
			if($limit_type == 'words' && $limit_value > 0) {
				$temp = explode(' ', strip_tags($text));
			
				if(count($temp) > $limit_value){
					$text = NSP_GK5_Utils::cutHTML(str_replace(array('<p>', '</p>'), '', $text), $limit_value, $limit_type);
					$text .= $at_end;
				}
			} elseif ($limit_type == 'words' && $limit_value == 0) {
				return '';
			} else {
				if(JString::strlen(strip_tags($text)) > $limit_value) {
					$text = NSP_GK5_Utils::cutHTML(str_replace(array('<p>', '</p>'), '', $text), $limit_value, $limit_type);
					$text .= $at_end;
				}
			}
		}
		// replace unnecessary entities at end of the cutted text
		$toReplace = array('&&', '&a&', '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot&', '&ap&', '&apo&', '&apos&');
		$text = str_replace($toReplace, '&', $text);
		//
		return $text;
	}
	// Method to get Gravatar avatar
	static function avatarURL($email, $size){
		return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size.'&amp;default='.urlencode(JURI::root().'modules/mod_news_pro_gk5/interface/images/avatar.png');
	}
	//
	static function parseCropRules($config) {
		// parse the crop rules
		$temp_crop_rules = explode(';', $config['crop_rules']);
		$final_crop_rules = array();
		// parse every rule
		foreach($temp_crop_rules as $rule) {
			// divide the rule for the name and data
			$temp_rule = explode('=',$rule);
			// validation of format
			if(count($temp_rule) == 2) {
				// create the structure for rule
				$final_rule = array(
										'type' => $temp_rule[0],
										'top' => 0,
										'right' => 0,
										'bottom' => 0,
										'left' => 0 
									);
				// check the type of the rule - class-based or size-based					
				if(strpos($temp_rule[0], ':') !== FALSE) {
					// if the rule is size-based - divide the size
					$temp_size = explode(':', $temp_rule[0]);
					// validation of format
					if(count($temp_size) == 2) {
						// and put to the array the base size of image
						$final_rule['type'] = array(
														'width' => $temp_size[0],
														'height' => $temp_size[1]
													);
					}
				}
				// get the data about cropping
				$temp_crop_size = explode(':', $temp_rule[1]);
				// validation of format
				if(count($temp_crop_size) == 4) {
					// put the data to the structure
					$final_rule['top'] = $temp_crop_size[0];
					$final_rule['right'] = $temp_crop_size[1];
					$final_rule['bottom'] = $temp_crop_size[2];
					$final_rule['left'] = $temp_crop_size[3];
				}
				// override the old rule string with the array structure
				array_push($final_crop_rules, $final_rule);
			}
		}
		// return the result
		return $final_crop_rules;
	}
	// Method to cut the HTML string
	static function cutHTML($text, $limit, $type){
		$output = new cutHTMLString($text, $limit, $type);
		return $output->cutText();
	}
}

class cutHTMLString{
	function __construct($string, $limit, $type){
		// temporary container for the operation
		$this->tempDiv = new DomDocument;
		$this->tempDiv->loadXML('<div>'.$string.'</div>');
		// counters
		$this->charCount = 0;
		$this->wordCount = 0;
		// limit value
		$this->limit = $limit;
		// limit type
		$this->limitType = $type;
	}

	function cutText(){
		// create empty document to store new html
		$this->newDiv = new DomDocument;
		// cut the string by parsing through each element
		$this->search($this->tempDiv->documentElement, $this->newDiv);
		//
		return $this->newDiv->saveHTML();
	}
  
	function search($parseDiv, $newParent){
		foreach($parseDiv->childNodes as $el){
			// not text node
			if($el->nodeType != 3){
				$newEl = $this->newDiv->importNode($el,true);
				if(count($el->childNodes) === 0){
					$newParent->appendChild($newEl);
					continue;
				}
				
				$this->removeNode($newEl);
				$newParent->appendChild($newEl);
				$res = $this->search($el, $newEl);
			
				if($res) {
					return $res;
				} else {
					continue;
				}
			}
			// the limit of the char count reached
			if($this->limitType == 'words') {
				// create the words array
				$words_array = explode(' ', $el->nodeValue);
				//
				if(count($words_array) + $this->wordCount >= $this->limit){
					$newEl = $this->newDiv->importNode($el);
					$newEl->nodeValue = '';
					
					for($i = 0; $i <= $this->limit - $this->wordCount; $i++) {
						if(isset($words_array[$i])) {
							$newEl->nodeValue .= ' '.$words_array[$i];
						}	
					}
					
					$newParent->appendChild($newEl);
					
					return true;
				}
				//
				$newEl = $this->newDiv->importNode($el);
				$newParent->appendChild($newEl);
				$this->wordCount += count($words_array);
			} else {
				//
				if(JString::strlen($el->nodeValue) + $this->charCount >= $this->limit){
					$newEl = $this->newDiv->importNode($el);
					$newEl->nodeValue = substr($newEl->nodeValue, 0, $this->limit - $this->charCount);
					$newParent->appendChild($newEl);
					
					return true;
				}
				//
				$newEl = $this->newDiv->importNode($el);
				$newParent->appendChild($newEl);
				$this->charCount += JString::strlen($newEl->nodeValue);
			}
		}
		
		return false;
	}
	
	function removeNode($node) {
		while(isset($node->firstChild)) {
			$this->removeNode($node->firstChild);
			$node->removeChild($node->firstChild);
		}
	} 
}

/* EOF */

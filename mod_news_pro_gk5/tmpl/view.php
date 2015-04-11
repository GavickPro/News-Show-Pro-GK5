<?php

/**
* This View is responsible for generating layout parts
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.6.2 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_View {
	// header generator
	static function header($config, $item) {
		if($config['news_content_header_pos'] != 'disabled') {
			$class = ' t'.$config['news_content_header_pos'].' f'.$config['news_content_header_float'];
			
			if(static::image($config, $item, true, true) != '') {
				$class .= ' has-image';
			}
			
			$output = NSP_GK5_Utils::cutText(htmlspecialchars($item['title']), $config, 'title_limit', '&hellip;');
			$output = str_replace('"', "&quot;", $output);
			// first word span wrap
			if($config['news_header_first_word'] == 1) {
				$output_temp = explode(' ', $output);
				$first_word = $output_temp[0];
				$output_temp[0] = '<span>'.$output_temp[0].'</span>';
				$output = preg_replace('/' . $first_word . '/mi', $output_temp[0], $output, 1);
			}
			
	        $link = static::itemLink($item, $config);
			//
			if($config['news_header_link'] == 1) {
				return '<h4 class="nspHeader'.$class.'"><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'" target="'.$config['open_links_window'].'">'.$output.'</a></h4>';	
			} else {
				return '<h4 class="nspHeader'.$class.'" title="'.htmlspecialchars($item['title']).'">'.$output.'</h4>';
			}
		} else {
			return '';
		}
	}
	
	// article text generator
	static function text($config, $item, $readmore) {
		if($config['news_content_text_pos'] != 'disabled') {
			//
			$item['text'] = NSP_GK5_Utils::cutText($item['text'], $config, 'news_limit');
			$item['text'] = static::textPlugins($item['text'], $config);
			$link = static::itemLink($item, $config);
			//
			$item['text'] = ($config['news_text_link'] == 1) ? '<a href="'.$link.'" target="'.$config['open_links_window'].'">'.$item['text'].'</a>' : $item['text']; 
			$class = ' t'.$config['news_content_text_pos'].' f'.$config['news_content_text_float'];
			//
			if($config['news_content_readmore_pos'] == 'after') { 
				return '<p class="nspText'.$class.'">'.$item['text'].' '.$readmore.'</p>';
			} else {
				return '<p class="nspText'.$class.'">'.$item['text'].'</p>';
			}
		} else {
			return '';
		}
	}
	
	// ReadMore button generator
	static function readMore($config, $item) {
		//
		if($config['news_content_readmore_pos'] != 'disabled') {
			$class = ' f'.$config['news_content_readmore_pos'];
			$link = static::itemLink($item, $config); 
			//
			if($config['news_content_readmore_pos'] == 'after') {
				return '<a class="readon inline" href="'.$link.'" target="'.$config['open_links_window'].'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
			} else {
				return '<a class="readon '.$class.'" href="'.$link.'" target="'.$config['open_links_window'].'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
			}
		} else {
			return '';
		}
	}
	
	// rest link list generator	
	static function lists($config, $item, $num) {
		$odd = $num % 2;
		
		if($config['news_short_pages'] > 0) {
	        $text = '';
	        $title = '';
	        $image = '';
	        $readmore = '';
	        $link = static::itemLink($item, $config);
	        
	        if($config['list_text_limit'] > 0) {
	            $item['text'] = static::textPlugins($item['text'], $config);
	            $text = NSP_GK5_Utils::cutText(strip_tags($item['text']), $config, 'list_text_limit', '&hellip;');
	            
	            if(JString::strlen($text) > 0) {
	            	$text = '<p>'.$text.'</p>';
	            }
			}
			
			if($config['list_title_limit'] > 0) {
				$title = htmlspecialchars($item['title']);
				$title = NSP_GK5_Utils::cutText($title, $config, 'list_title_limit', '&hellip;');
				$title = str_replace('"', "&quot;", $title);
			
				if(JString::strlen($title) > 0) {
					$title = '<h4><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'" target="'.$config['open_links_window'].'">'.$title.'</a></h4>';
				}
			}
			
			if($config['links_image'] == 1) {
				$image = static::image($config, $item, false, false, true);
			}
			
			if(isset($config['links_readmore']) && $config['links_readmore'] == 1) {
				$readmore = '<a class="readon" href="'.$link.'" target="'.$config['open_links_window'].'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
			}
			
			// creating rest news list
			return '<li class="'.(($odd == 1) ? 'odd' : 'even').'">' . $image . (($image != '') ? '<div>' . $title . $text . $readmore . '</div>' : ($title . $text . $readmore)) . '</li>';	
		} else {
			return '';
		}
	}
	
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		return '';
	}
	
	// article information generator
	static function info($config, $item, $num = 1) {
		return '';		
	}	
	
	// article link generator
	static function itemLink($item, $config = false) {
		return '';
	}
	
	// category link generator
	static function categoryLink($item) {
		return '';
	}
	
	// user link generator
	static function authorLink($item) {
		return '';
	}
	
	// article image generator
	static function originalImage($config, $item){		
		return '';
	}
	
	// Plugin text cleaner
	static function textPlugins($text, $config) {
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

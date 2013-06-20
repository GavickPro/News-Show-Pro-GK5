<?php

/**
* Article format parser class
* @package News Show Pro GK5
* @Copyright (C) 2009-2012 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Article_Format {
	function generateLayout($config, $data) {
		
		/*
			Available variables
			
			{TITLE} - article title
			{TEXT} - article text
			{URL} - article URL
			{IMAGE_SRC} - article image URL
			{AUTHOR_EMAIL} - article autor e-mail 
			{AUTHOR_NAME} - article author name
			{CATEGORY} - article category name
			{CATEGORY_URL} - article category URL
			{HITS} - article hits
			{DATE} - article date
			{RATING} - article rating
		*/
		
		//
		// Get the values
		//
		
		// Basic data
		$title = NSP_GK5_Utils::cutText($data['title'], $config, 'title_limit');
		$text = NSP_GK5_Utils::cutText($data['text'], $config, 'news_limit');
		// URL
		$url = '';
		if(isset($data['url'])) { 
			$url = $data['url'];
		} else {
			$url = call_user_func(array($viewClass, 'itemLink'), $data, $config);
		}
		// Image
		$viewClass = 'NSP_GK5_'.$config['source_name'].'_View';
		// PHP 5.3:
		//$image_src = $viewClass::image($config, $data, true);
		$image_src = call_user_func(array($viewClass, 'image'), $config, $data, true);
		// Author data
		$author_email = $data['author_email'];
		$author_name = $data['author'];
		// Category data
		$category = $data['catname'];
		$category_url = '';
		if(isset($data['caturl'])) {
			$category_url = $data['caturl'];
		} else {
			$url = call_user_func(array($viewClass, 'categoryLink'), $data);
		}
		// Other data
		$hits = $data['hits'];
		$date = $data['date'];
		$rating = $item['rating_count'] > 0 ? number_format($data['rating_sum'] / $data['rating_count'], 2) : 0;
		
		//
		// Get the layout text
		//
		
		if(is_file(JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'article_formats'.DS.$config['article_format'])) {
			// read the format file
			$format_file = file_get_contents(JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'article_formats'.DS.$config['article_format']);
			// replace values
			$to_replace = array(
				'{TITLE}',
				'{TEXT}',
				'{URL}',
				'{IMAGE_SRC}',
				'{AUTHOR_EMAIL}', 
				'{AUTHOR_NAME}',
				'{CATEGORY}',
				'{CATEGORY_URL}',
				'{HITS}',
				'{DATE}',
				'{RATING}'
			);
			// values for the replacement
			$replacement = array(
				$title,
				$text,
				$url,
				$image_src,
				$author_email,
				$author_name,
				$category,
				$category_url,
				$hits,
				$date,
				$rating
			);
			// replace values in the format file 
			$format_file = str_replace($to_replace, $replacement, $format_file);
			// parse lang rules
			$matches = array();
			preg_match_all('@{{.*?}}@', $format_file, $matches);
			
			if(count($matches) > 0) {
				for($i = 0; $i < count($matches); $i++) {
					$phrase = $matches[$i][0];
					$phrase = JText::_(str_replace(array('{{', '}}'), '', $phrase));
					$format_file = str_replace($matches[$i][0], $phrase, $format_file);
				}
			}
			
			// PARSING PLUGINS
			if($config['parse_plugins'] == TRUE) {
				$format_file = JHtml::_('content.prepare', $format_file);
			}	
			// CLEANING PLUGINS
			if($config['clean_plugins'] == TRUE) {
				$format_file = preg_replace("/(\{.+?\}.+?\{.+?})|(\{.+?\})/", "", $format_file);
			} 	
			
			return $format_file;
		} else {
			return '';
		}
	}
}

// EOF
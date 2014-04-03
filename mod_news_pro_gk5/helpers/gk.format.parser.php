<?php

/**
* Helper class for parsing article formats
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Article_Format {
	function generateLayout($config, $data) {
		
		/*
			
			Available variables:
			
			{TITLE} - article title
			{TEXT} - article text
			{URL} - article URL
			{IMAGE_SRC} - article image URL
			{AUTHOR_EMAIL} - article autor e-mail 
			{AUTHOR_NAME} - article author name
			{CATEGORY} - article category name
			{CATEGORY_URL} - article category URL
			{HITS} - article hits
			{DATE} - article date (gets format from the information block settings)
			{RATING} - article rating
			
			K2 specific variables:
			
			{TAGS} - article tag lists
			{VIDEO_HTML} - HTML of the article video
			{CATEGORY_IMAGE_SRC} - article category image URL
			
		*/
		
		//
		// Get the values
		//

		// Image
		$viewClass = 'NSP_GK5_'.$config['source_name'].'_View';
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
			$category_url = call_user_func(array($viewClass, 'categoryLink'), $data);
		}
		// Other data
		$hits = $data['hits'];
		$date = JHTML::_('date', $data['date'], $config['date_format']);
		$rating = $data['rating_count'] > 0 ? number_format($data['rating_sum'] / $data['rating_count'], 2) : 0;
		
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
			// replacements only for K2
			if(stripos($config['data_source'], 'k2_') !== FALSE) {
				// tags list value
				$tags = '';
				// if tags exists
				if(isset($data['tags']) && count($data['tags']) > 0) {
					$i = 0;
					foreach($data['tags'] as $tag) {
						$link = urldecode(JRoute::_(K2HelperRoute::getTagRoute($tag)));
					
						if($i == 0) {
							$tags .= '<a href="' . $link . '">' . $tag . '</a>';
						} else {
							$tags .= ', <a href="' . $link . '">' . $tag . '</a>';
						}
						//
						$i++;
					}
				}
				// video HTML value
				$video_html = $data['video'];
				// category image URL value
				$category_image_src = '';
				// if the category image exists
				if($data['cat_image'] != '') {
					$category_image_src = JURI::root() . 'media/k2/categories/' . $data['cat_image'];
				}
				// replace values
				$to_replace = array(
					'{TAGS}',
					'{VIDEO_HTML}',
					'{CATEGORY_IMAGE_SRC}'
				);
				// values for the replacement
				$replacement = array(
					$tags,
					$video_html,
					$category_image_src
				);
				// replace values in the format file 
				$format_file = str_replace($to_replace, $replacement, $format_file);
			}
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
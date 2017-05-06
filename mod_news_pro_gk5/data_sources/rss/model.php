<?php

/**
* This Model is responsible for getting data from the com_content data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_rss_Model {
	// Method to get sources of articles
	static function getSources($config) {
		$content = array();
		// if there are selected files - set the variables
		if($config['rss_feed'] != '') {
			jimport('simplepie.simplepie');
			// loading file content
			$rss = new SimplePie();
			$rss->set_feed_url($config['rss_feed']);
			$rss->set_cache_location(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache');
			$rss->set_cache_duration($config['rss_cache_time'] * 60);
			$rss->enable_cache();
			$rss->init();
			$rss->handle_content_type();
			$items = $rss->get_items();
			//
			if(count($items) > 0) {
				//
				$art = array();
				$offset = 0;
				//
				foreach ($items as $item) {
					$art['url'] = $item->get_permalink();
					$art['title'] = $item->get_title();
					$art['text'] = $item->get_content();
					$art['date'] = $item->get_date();
					$art['date_publish'] = $item->get_date();
					$art['author'] = '';
					$art['catname'] = ($item->get_category()) ? $item->get_category()->get_term() : '';
					$art['category_url'] = '';
					$art['hits'] = 0;
					$art['email'] = '';
					$art['rating_sum'] = 0;
					$art['rating_count'] = 0;
					$art['image'] = '';
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
		for($i = $config['offset']; $i < $amount + $config['offset']; $i++) {
			if(isset($items[$i])) {
				array_push($content, $items[$i]);
			}
		}
		// the content array
		return $content; 
	}
}

// EOF

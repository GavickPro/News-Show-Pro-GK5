<?php

/**
 *
 * This Model is responsible for getting data from the
 * com_content data source
 *
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_rss_Model {
	// Method to get sources of articles
	static function getSources($config) {
		$content = array();
		// if there are selected files - set the variables
		if($config['rss_feed'] != '' && file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
			jimport('simplepie.simplepie');
			// loading file content
			$rss = new SimplePie($config['rss_feed'], JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache', $config['rss_cache_time'] * 60);
			$rss->enable_cache();
			$rss->init();
			$rss->handle_content_type();
			$items = $rss->get_items();
			//
			if(count($items) > 0) {
				//
				$art = array();
				//
				foreach ($items as $item) {
					$art['url'] = $item->get_permalink();
					$art['title'] = $item->get_title();
					$art['text'] = $item->get_content();
					$art['date'] = $item->get_date();
					$art['date_publish'] = $item->get_date();
					$art['author'] = '';
					$art['catname'] = $item->get_category()->get_term();
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
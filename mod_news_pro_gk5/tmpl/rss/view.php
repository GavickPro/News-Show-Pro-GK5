<?php

/**
* This View is responsible for generating layout parts for the com_content data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_rss_View extends NSP_GK5_View {
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if(!($config['news_content_image_pos'] != 'disabled' || $pm || $links)) {
			return '';
		}
		
		$item['title'] = str_replace('"', "&quot;", $item['title']);
	    $uri = JURI::getInstance();
	    $IMG_LINK = $item['url'];
	    $IMG_SOURCE = NSP_GK5_rss_View::getImageFromText($item['text']);
		// get image from the text
		$full_size_img = $IMG_SOURCE;
		//
		if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
			// try to override standard image
			if(strpos($IMG_SOURCE,'http://') == FALSE) {
				$img_file = NSP_GK5_Thumbs::createThumbnail($IMG_SOURCE, $config, false, false, '', $links);
				
				if(is_array($img_file)) {
					$uri = JURI::getInstance();
					$IMG_SOURCE = $uri->root().'modules/mod_news_pro_gk5/cache/'.$img_file[1];
				} elseif($config['create_thumbs'] == 1) {
					jimport('joomla.filesystem.file');
			  		
			  		if(is_file(JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS.'default'.DS.'default'.$config['module_id'].'.png')) {
						$IMG_SOURCE = $uri->root().'modules/mod_news_pro_gk5/cache/default/default'.$config['module_id'].'.png';
					}
				} else {
					$IMG_SOURCE = '';
				}
			}	
		} elseif($config['create_thumbs'] == 1) {
			jimport('joomla.filesystem.file');
			
			if(is_file(JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS.'default'.DS.'default'.$config['module_id'].'.png')) {
				$IMG_SOURCE = $uri->root().'modules/mod_news_pro_gk5/cache/default/default'.$config['module_id'].'.png';	
			}
		}
		
		return NSP_GK5_rss_View::getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img);
	}
	// article information generator
	static function info($config, $item, $num = 1) {
		// %AUTHOR %DATE %HITS %CATEGORY
		$news_info = '';
		//
		if($num == 1 && $config['news_content_info_pos'] != 'disabled'){
			$class = 'nspInfo1 t'.$config['news_content_info_pos'].' f'.$config['news_content_info_float'];	
		} elseif($config['news_content_info2_pos'] != 'disabled') {
			$class = 'nspInfo2 t'.$config['news_content_info2_pos'].' f'.$config['news_content_info2_float'];			
		}
		//
		if(
			($config['news_content_info_pos'] != 'disabled' && $num == 1) || 
			($config['news_content_info2_pos'] != 'disabled' && $num == 2)
		) {
	        $news_info = '<p class="nspInfo '.$class.'">'.$config['info'.(($num == 2) ? '2' : '').'_format'].'</p>';
	        //
	        $info_category = $item['catname'];
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);
	        // 
	        $news_info = str_replace('%DATE', $info_date, $news_info);
	        $news_info = str_replace('%CATEGORY', $info_category, $news_info);
	    } else {
	    	return '';
	    }
		//
		return $news_info;		
	}
	// article link generator
	static function itemLink($item, $config = false) {
		return $item['url'];
	}
	// category link generator
	static function categoryLink($item) {
		return '';
	}
	// user link generator
	static function authorLink($item) {
		return '';
	}
}

// EOF
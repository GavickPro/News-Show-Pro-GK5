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

class NSP_GK5_json_file_View extends NSP_GK5_View {
	// article text generator
	static function text($config, $item, $readmore) {
		if($config['news_content_text_pos'] != 'disabled') {
			//
			$item['text'] = NSP_GK5_Utils::cutText($item['text'], $config, 'news_limit');
			$link = $item['url'];
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
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if(!($config['news_content_image_pos'] != 'disabled' || $pm || $links)) {
			return '';
		}
		
		$item['title'] = str_replace('"', "&quot;", $item['title']);
	    $IMG_SOURCE = '';
		$IMG_LINK = $item['url'];	
		//
		$uri = JURI::getInstance();
		// get image from Joomla! Images and Links settings
		$IMG_SOURCE = $item['image'];
		//
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
		
		return NSP_GK5_json_file_View::getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img);
	}
	// article information generator
	static function info($config, $item, $num = 1) {
		// %AUTHOR %DATE %HITS %CATEGORY
		$news_info = '';
		//
		if($num == 1){
			if($config['news_content_info_pos'] != 'disabled') {
				$class = 'nspInfo1 t'.$config['news_content_info_pos'].' f'.$config['news_content_info_float'];	
			}
		} else {
			if($config['news_content_info2_pos'] != 'disabled') {
				$class = 'nspInfo2 t'.$config['news_content_info2_pos'].' f'.$config['news_content_info2_float'];
			}			
		}
		//
		if(
			($config['news_content_info_pos'] != 'disabled' && $num == 1) || 
			($config['news_content_info2_pos'] != 'disabled' && $num == 2)
		) {
	        $news_info = '<p class="nspInfo '.$class.'">'.$config['info'.(($num == 2) ? '2' : '').'_format'].'</p>';
	        //
	        $info_category = ($config['category_link'] == 1) ? '<a href="'. $item["category_url"] .'" target="'.$config['open_links_window'].'">'.$item['catname'].'</a>' : $item['catname'];	        
	        $info_author = ($config['user_avatar'] == 1) ? '<span><img src="'. NSP_GK5_Utils::avatarURL($item['author_email'], $config['avatar_size']).'" alt="'.htmlspecialchars($item['author']).' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$item['author'].'</span>' : $item['author'];
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
	        $info_hits = JText::_('MOD_NEWS_PRO_GK5_NHITS').$item['hits'];
	        
	        // case when there is no rates
	        if($item['rating_count'] == 0) {
	        	$item['rating_count'] = 1;
	        }
	        
	        $info_rate = ($item['rating_count'] > 0) ? '<span class="nspRate">' . JText::_('MOD_NEWS_PRO_GK5_NSP_RATE') .' '. number_format($item['rating_sum'] / $item['rating_count'], 2) . '</span>': '';
	        
	        $info_stars = '<span class="nsp-stars">';
	        $stars_count = floor($item['rating_sum'] / $item['rating_count']);
	        for($i = 0; $i < 5; $i++) {
	        	$info_stars .= $i < $stars_count ? '<span class="nsp-star-1"></span>' : '<span class="nsp-star-0"></span>';
	        }
	        $info_stars .= '</span>'; 
	        
	        // 
	        $news_info = str_replace('%AUTHOR', $info_author, $news_info);
	        $news_info = str_replace('%DATE', $info_date, $news_info);
	        $news_info = str_replace('%HITS', $info_hits, $news_info);
	        $news_info = str_replace('%CATEGORY', $info_category, $news_info);
	        $news_info = str_replace('%RATE', $info_rate, $news_info);
	        $news_info = str_replace('%STARS', $info_stars, $news_info);
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
		return $item["category_url"];
	}
	// user link generator
	static function authorLink($item) {
		return '';
	}
}

// EOF
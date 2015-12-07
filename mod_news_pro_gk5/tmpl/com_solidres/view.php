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

// load necessary K2 Route Helper
require_once (JPATH_SITE.DS.'components'.DS.'com_solidres'.DS.'helpers'.DS.'route.php');
//
class NSP_GK5_com_solidres_View extends NSP_GK5_View {
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if(!($config['news_content_image_pos'] != 'disabled' || $pm || $links)) {
			return '';
		}
		
		$IMG_SOURCE = '';
		$item['title'] = str_replace('"', "&quot;", $item['title']);
		$uri = JURI::getInstance();
		$IMG_SOURCE = static::originalImage($config, $item);
		//
		$full_size_img = $IMG_SOURCE;
		$IMG_LINK = static::itemLink($item, $config);
		//			
		if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
			// try to override standard image
			if(strpos($IMG_SOURCE, 'http://') == FALSE) {					
				
				$img_file = NSP_GK5_Thumbs::createThumbnail(str_replace(JURI::root() . 'media', 'media', $IMG_SOURCE), $config, true, false, '', $links, false, false);
				
				if(is_array($img_file)) {
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
		
		return NSP_GK5_com_solidres_View::getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img);
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
	        $news_info = '<div class="nspInfo '.$class.'"> '.$config['info'.(($num == 2) ? '2' : '').'_format'].' </div>';
	        
	        // Featured label
	        $info_featured = '';
	        
	        if(stripos($news_info, '%FEATURED') !== FALSE && $item['frontpage'] == '1') {
	        	$info_featured = '<strong class="is-featured">'.JText::_('MOD_NEWS_PRO_GK5_FEATURED').'</strong>';
	        }
	        
	        // Price
	        $info_price = '';
	        
	        if(
	        	!(
		        	$config['data_source'] == 'com_solidres_all_hotels' ||
		        	$config['data_source'] == 'com_solidres_hotels' ||
		        	$config['data_source'] == 'com_solidres_hotel_categories'
	        	)
	        ) {
	        	JLoader::register('SRCurrency', SRPATH_LIBRARY . '/currency/currency.php');
	        	$currency = new SRCurrency(0, $item['currency_id']);
	        	
	        	$info_price = NSP_GK5_com_solidres_Model::getPrice($item['id'], date("w"));
	        	$info_price = '<strong class="gk-solidres-price">' . $currency->getCode() . ' ' . $info_price . '</strong>';
	        }

	        $news_info = str_replace('%PRICE', $info_price, $news_info);
	        $news_info = str_replace('%FEATURED', $info_featured, $news_info);
	       
	        //
	        return $news_info;	
	    } else {
	    	return '';
	    }	
	}
	// article link generator
	static function itemLink($item, $config = false) {
		if(isset($item['overrided_url'])) {
			return $item['overrided_url'];
		}
		
		if(
			$config['data_source'] == 'com_solidres_all_hotels' ||
			$config['data_source'] == 'com_solidres_hotels' ||
			$config['data_source'] == 'com_solidres_hotel_categories'
		) {
			$url = SolidresHelperRoute::getReservationAssetRoute($item['asset_id']);
		} else {
			$url = SolidresHelperRoute::getReservationAssetRoute($item['asset_id'], $item['id']);
		}
		
		return urldecode(JRoute::_($url));
	}
	// orginal image
	static function originalImage($config, $item) {
		$IMG_SOURCE = '';
		// search for the article featured image
		if(JFile::exists(JPATH_SITE.DS.'media'.DS.'com_solidres'.DS.'assets'.DS.'images'.DS.'system'.DS.$item['image'])){  
			$IMG_SOURCE = JURI::root().'media/com_solidres/assets/images/system/'.$item['image'];
		}
		
		return $IMG_SOURCE;
	}
}

// EOF

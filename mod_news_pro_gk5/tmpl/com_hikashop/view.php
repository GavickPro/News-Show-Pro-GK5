<?php

/**
* This View is responsible for generating layout parts for the com_hikashop data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
// define directory separator constant
if(!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
// load necessary helper functions
include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php');


class NSP_GK5_com_hikashop_View extends NSP_GK5_View {
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if($config['news_content_image_pos'] != 'disabled' || $pm || $links) {			
			$news_title = str_replace('"', "&quot;", $item['title']);
			$IMG_SOURCE = 'media' . DS . 'com_hikashop' . DS . 'upload' . DS . $item['image'];
			$IMG_LINK = static::itemLink($item, $config);
			//
			$full_size_img = $IMG_SOURCE;
			//
			if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
				// try to override standard image
				if(strpos($IMG_SOURCE,'http://') == FALSE) {
					$img_file = NSP_GK5_Thumbs::createThumbnail($IMG_SOURCE, $config, false, false, '', $links, true);
					
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
			//
			if($IMG_SOURCE != '') {
				if($only_url) {
					return $IMG_SOURCE;
				} else {
					$class = '';
					
					if(!$links) {
						$class = ' t'.$config['news_content_image_pos'].' f'.$config['news_content_image_float'];
					} 
					
					$size = '';
					$margins = '';
					// 
					if(!$links && $config['responsive_images'] == 1) {
						$class .= ' gkResponsive'; 
					}
					//
					if(!$links) {
						if($config['img_width'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'width:'.$config['img_width'].'px;';
						if($config['img_height'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'height:'.$config['img_height'].'px;';
						if($config['img_margin'] != '') $margins = ' style="margin:'.$config['img_margin'].';"';
					} else {
						if($config['links_img_width'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'width:'.$config['links_img_width'].'px;';
						if($config['links_img_height'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'height:'.$config['links_img_height'].'px;';
						if($config['links_img_margin'] != '') $margins = ' style="margin:'.$config['links_img_margin'].';"';
					}
					//
					$size = ($size == '') ? '' : ' style="' . $size . '"';
					//
					if($config['news_image_link'] == 1) {
						if($config['news_image_modal'] == 1) {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>';
						} else {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>';
							
						}
					} else {
						return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" '.$size.' /></span></div>' : '<span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'" /></span>';
					}
				}
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
	// article information generator
	static function info($config, $item, $num = 1) {
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
	        $info_category = ($config['category_link'] == 1) ? '<a href="'.static::categoryLink($item, $config).'" target="'.$config['open_links_window'].'">'.$item['cat_name'].'</a>' : $news_catname;
	        //          
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
	        //          
            if(!isset($item['comments']) || $item['comments'] == 0){
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_NO_COMMENTS');
            } else {
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_COMMENTS').' ('.(isset($item['comments']) ? $item['comments'] : '0' ) . ')';
            }
	        $info_comments = '<a class="nspComments" href="'.static::itemLink($item, $config).'#product-tabs" target="'.$config['open_links_window'].'">'.$comments_amount.'</a>';
	        // Replace the following phrases:
	        // %COMMENTS %DATE %CATEGORY %MANUFACTURER %STORE
            $news_info = str_replace('%DATE', $info_date, $news_info);
            $news_info = str_replace('%CATEGORY', $info_category, $news_info);
            $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
            $news_info = str_replace('%STORE', static::store($config, $item), $news_info);
	    } else {
	    	return '';
	    }
		//
		return $news_info;
	}
	// function used to show the store details
	static function store($config, $item) {
		$html = '<div class="nspHikashopBlock">';
		//
		if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) {
			return true;
		}
		
		$app = JFactory::getApplication();
		$currencyHelper = hikashop_get('class.currency');
		$taxed_price = $currencyHelper->getTaxedPrice($item['price'], hikashop_getZone(), $item['tax_id']);
		$mainCurr = $currencyHelper->mainCurrency();
		$currCurrency = $app->getUserState( HIKASHOP_COMPONENT.'.currency_id', $mainCurr );
		$msrpCurrencied = $currencyHelper->convertUniquePrice($taxed_price, $mainCurr, $currCurrency);
		
		if($msrpCurrencied == $taxed_price) {
			$html .= '<span>' . $currencyHelper->format($taxed_price, $mainCurr) . '</span>';
		} else {
			$html .= '<span>' . $currencyHelper->format($msrpCurrencied, $currCurrency).' ('.$currencyHelper->format($taxed_price, $mainCurr).')' . '</span>';
		}
		
		if($config['hikashop_add_to_cart'] > 0) {
			if(!defined('DS')) {
				define('DS', DIRECTORY_SEPARATOR);
			}
			include_once(rtrim(JPATH_ADMINISTRATOR,DS) . DS . 'components' . DS . 'com_hikashop' . DS . 'helpers' . DS . 'helper.php');
			JHTML::_('behavior.framework');
			$hs_config = hikashop_config();
			$productClass = hikashop_get('class.product');
			$_SESSION['hikashop_product']= $productClass->get($item['id']);
			$params = new JRegistry('');
			// enable quantity field
			if($config['hikashop_add_to_cart'] == 2) {
				$params->set('show_quantity_field', 1);
			} else {
				$params->set('show_quantity_field', 0);
			}
			$params->set('price_with_tax', $hs_config->get('price_with_tax',1));
			$params->set('add_to_cart',1);
			$js = '';
			$html .= hikashop_getLayout('product','add_to_cart_listing',$params,$js);
		}
		
		$html .= '</div>';
		
		return $html;
	}
	
	function itemCart($name, $map, $ajax, $type){		
		$html = '<input type="'.$type.'" class="btn button hikashop_cart_input_button" name="'.$map.'" value="'.$name.'" onclick="'.$ajax.'"/>';
		$html.='<input id="hikashop_product_quantity_field_1" type="hidden" value="1" class="hikashop_product_quantity_field" name="quantity" />';
		return $html;
	}
	
	// article link generator
	static function itemLink($item, $config) {
		if(isset($item['overrided_url'])) {
			return $item['overrided_url'];
		}
		
		return hikashop_completeLink('product&task=show&cid='.$item['id'].'&name='.$item['alias'].'&Itemid=' . $config['hikashop_itemid']);
	}
	// category link generator
	static function categoryLink($item, $config = false) {
		return hikashop_completeLink('category&task=listing&cid='.$item['cid'].'&name='.$item['cat_alias'].'&Itemid=' . $config['hikashop_itemid']);
	}
	// user link generator
	static function authorLink($item) {
		return '';
	}
}

// EOF
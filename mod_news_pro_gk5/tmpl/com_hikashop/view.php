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


class NSP_GK5_com_hikashop_View {
	// header generator
	static function header($config, $item) {
		if($config['news_content_header_pos'] != 'disabled') {
			$class = ' t'.$config['news_content_header_pos'].' f'.$config['news_content_header_float'];
			$output = NSP_GK5_Utils::cutText(htmlspecialchars($item['title']), $config, 'title_limit', '&hellip;');
			$output = str_replace('"', "&quot;", $output);
	        // first word span wrap
	        if($config['news_header_first_word'] == 1) {
	        	$output_temp = explode(' ', $output);
	        	$first_word = $output_temp[0];
	        	$output_temp[0] = '<span>'.$output_temp[0].'</span>';
	        	$output = preg_replace('/' . $first_word . '/mi', $output_temp[0], $output, 1);
	        }
	        
	        $link = NSP_GK5_com_hikashop_View::itemLink($item, $config);
			//
			if($config['news_header_link'] == 1) {
				return '<h4 class="nspHeader'.$class.'"><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'">'.$output.'</a></h4>';	
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
			$link = NSP_GK5_com_hikashop_View::itemLink($item, $config);
			//
			$item['text'] = ($config['news_text_link'] == 1) ? '<a href="'.$link.'">'.$item['text'].'</a>' : $item['text']; 
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
		if($config['news_content_image_pos'] != 'disabled' || $pm || $links) {			
			$news_title = str_replace('"', "&quot;", $item['title']);
			$IMG_SOURCE = 'media' . DS . 'com_hikashop' . DS . 'upload' . DS . $item['image'];
			$IMG_LINK = NSP_GK5_com_hikashop_View::itemLink($item, $config);
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
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>';
						} else {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>';
							
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
	// ReadMore button generator
	static function readMore($config, $item) {
		//
		if($config['news_content_readmore_pos'] != 'disabled') {
			$class = ' f'.$config['news_content_readmore_pos'];
			//
			if($config['news_content_readmore_pos'] == 'after') {
				return '<a class="readon inline"  href="'.NSP_GK5_com_hikashop_View::itemLink($item, $config).'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_COM_HIKASHOP_VIEW_PRODUCT')).'</a>';
			} else {
				return '<a class="readon '.$class.'" href="'.NSP_GK5_com_hikashop_View::itemLink($item, $config).'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_COM_HIKASHOP_VIEW_PRODUCT')).'</a>';
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
	        $info_category = ($config['category_link'] == 1) ? '<a href="'.NSP_GK5_com_hikashop_View::categoryLink($item, $config).'" >'.$item['cat_name'].'</a>' : $news_catname;
	        //          
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
	        //          
            if(!isset($item['comments']) || $item['comments'] == 0){
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_NO_COMMENTS');
            } else {
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_COMMENTS').' ('.(isset($item['comments']) ? $item['comments'] : '0' ) . ')';
            }
	        $info_comments = '<a class="nspComments" href="'.NSP_GK5_com_hikashop_View::itemLink($item, $config).'#product-tabs">'.$comments_amount.'</a>';
	        // Replace the following phrases:
	        // %COMMENTS %DATE %CATEGORY %MANUFACTURER %STORE
            $news_info = str_replace('%DATE', $info_date, $news_info);
            $news_info = str_replace('%CATEGORY', $info_category, $news_info);
            $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
            $news_info = str_replace('%STORE', NSP_GK5_com_hikashop_View::store($config, $item), $news_info);
	    } else {
	    	return '';
	    }
		//
		return $news_info;
	}
	// rest link list generator	
	static function lists($config, $item, $num) {
		$odd = $num % 2;
		
		if($config['news_short_pages'] > 0) {
	        $text = '';
	        $title = '';
	        $image = '';
	        
	        if($config['list_text_limit'] > 0) {
	            $text = NSP_GK5_Utils::cutText(strip_tags(preg_replace("/\{.+?\}/", "", $item['text'])), $config, 'list_text_limit', '&hellip;');
	            $text = preg_replace("/\{.+?\}/", "", $text);
	            
	            if(JString::strlen($text) > 0) {
	            	$text = '<p>'.$text.'</p>';
	            }
			}
			
			if($config['list_title_limit'] > 0) {
				$title = htmlspecialchars($item['title']);
				$title = NSP_GK5_Utils::cutText($title, $config, 'list_text_limit', '&hellip;');
				$title = str_replace('"', "&quot;", $title);
				$link = NSP_GK5_com_hikashop_View::itemLink($item, $config);
			
				if(JString::strlen($title) > 0) {
					$title = '<h4><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'">'.$title.'</a></h4>';
				}
			}
			
			if($config['links_image'] == 1) {
				$image = NSP_GK5_com_hikashop_View::image($config, $item, false, false, true);
			}
			// creating rest news list
			return '<li class="'.(($odd == 1) ? 'odd' : 'even').'">' . $image . (($image != '') ? '<div>' . $title . $text . '</div>' : ($title . $text)) . '</li>';	
		} else {
			return '';
		}
	}
	// function used to show the store details
	static function store($config, $item) {
		$html = '<div class="nspHikashopBlock">';
		//
		$currencyHelper = hikashop_get('class.currency');
		$mainCurr = $currencyHelper->mainCurrency();
		$app = JFactory::getApplication();
		$currCurrency = $app->getUserState( HIKASHOP_COMPONENT.'.currency_id', $mainCurr );
		$msrpCurrencied = $currencyHelper->convertUniquePrice($item['product_msrp'],$mainCurr,$currCurrency);
		
		if($msrpCurrencied == $item['product_msrp']) {
			$html .= '<span>' . $currencyHelper->format($item['product_msrp'], $mainCurr) . '</span>';
		} else {
			$html .= '<span>' . $currencyHelper->format($msrpCurrencied, $currCurrency).' ('.$currencyHelper->format($item['product_msrp'], $mainCurr).')' . '</span>';
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
			
			
			$params->set('price_with_tax',$hs_config->get('price_with_tax',1));
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
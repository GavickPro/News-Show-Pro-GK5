<?php

/**
* This View is responsible for generating layout parts for the com_virtuemart data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_com_virtuemart_View {
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
	        
	        $link = NSP_GK5_com_virtuemart_View::itemLink($item, $config);
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
			$link = NSP_GK5_com_virtuemart_View::itemLink($item, $config);
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
			$IMG_SOURCE = $item['image'];
			$IMG_LINK = NSP_GK5_com_virtuemart_View::itemLink($item, $config);
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
				return '<a class="readon inline"  href="'.NSP_GK5_com_virtuemart_View::itemLink($item, $config).'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
			} else {
				return '<a class="readon '.$class.'" href="'.NSP_GK5_com_virtuemart_View::itemLink($item, $config).'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
			}
		} else {
			return '';
		}
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
	        $info_category = ($config['category_link'] == 1) ? '<a href="'.NSP_GK5_com_virtuemart_View::categoryLink($item).'" >'.$item['cat_name'].'</a>' : $news_catname;
	        //          
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
	        //          
            if($config['no_comments_text'] && (!isset($item['comments']) || $item['comments'] == 0)){
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_NO_COMMENTS');
            } else {
                $comments_amount = JText::_('MOD_NEWS_PRO_GK5_COMMENTS').' ('.(isset($item['comments']) ? $item['comments'] : '0' ) . ')';
            }
	        $info_comments = '<a class="nspComments" href="'.NSP_GK5_com_virtuemart_View::itemLink($item, $config).'#reviewform">'.$comments_amount.'</a>';
	        $info_manufacturer = JText::_('MOD_NEWS_PRO_GK5_MANUFACTURER').$item['manufacturer'];
	        // Replace the following phrases:
	        // %COMMENTS %DATE %CATEGORY %MANUFACTURER %STORE
            $news_info = str_replace('%DATE', $info_date, $news_info);
            $news_info = str_replace('%CATEGORY', $info_category, $news_info);
            $news_info = str_replace('%MANUFACTURER', $info_manufacturer, $news_info);
            $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
            $news_info = str_replace('%STORE', NSP_GK5_com_virtuemart_View::store($config, $item), $news_info);
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
				$link = NSP_GK5_com_virtuemart_View::itemLink($item, $config);
			
				if(JString::strlen($title) > 0) {
					$title = '<h4><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'">'.$title.'</a></h4>';
				}
			}
			
			if($config['links_image'] == 1) {
				$image = NSP_GK5_com_virtuemart_View::image($config, $item, false, false, true);
			}
			// creating rest news list
			return '<li class="'.(($odd == 1) ? 'odd' : 'even').'">' . $image . (($image != '') ? '<div>' . $title . $text . '</div>' : ($title . $text)) . '</li>';	
		} else {
			return '';
		}
	}
	// function used to show the store details
	static function store($config, $item) {
		// if the VM is available
        if (!class_exists( 'VmConfig' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
        }
        VmConfig::loadConfig();
        // Load the language file of com_virtuemart.
        JFactory::getLanguage()->load('com_virtuemart');
        // load necessary classes
        if (!class_exists( 'calculationHelper' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');
        }
        if (!class_exists( 'CurrencyDisplay' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
        }
        if (!class_exists( 'VirtueMartModelVendor' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'vendor.php');
        }
        if (!class_exists( 'VmImage' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
        }
        if (!class_exists( 'shopFunctionsF' )) {
        	require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
        }
        if (!class_exists( 'calculationHelper' )) {
        	require(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'cart.php');
        }
        if (!class_exists( 'VirtueMartModelProduct' )){
           JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
        }
        // load the base
        $mainframe = JFactory::getApplication();
        $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id',0) );
        $currency = CurrencyDisplay::getInstance();
        $cSymbol = $currency->getSymbol();
        $cDecimals = $currency->getNbrDecimals();
        $cDecSymbol = $currency->getDecimalSymbol();

        $productModel = new VirtueMartModelProduct();
	    $product = $productModel->getProduct($item['id'], 100, true, true, true);
	    // prepare price - apply correct format and decimal separator
	    $price = str_replace('.',$cDecSymbol,number_format($product->prices[$config['vm_show_price_type']],$cDecimals));
	    if($config['vm_currency_position'] == 'before') { 
	    	$price = $cSymbol.' '.$price;
	    } else {
	    	$price = $price.' '.$cSymbol;
	    }
	    //
        if($config['vm_add_to_cart'] == 1) {
            vmJsApi::jQuery();
            vmJsApi::jPrice();
        }
        $news_price = '<div>';
        //
        if($config['vm_show_price_type'] != 'none') {
            if($config['vm_display_type'] == 'text_price') {
            	$news_price .=  '<span>'.JText::_('MOD_NEWS_PRO_GK5_PRODUCT_PRICE').' '.$price.'</span>';
            } else {
            	$news_price .= '<span>'.$price.'</span>';
            }
        } 
        // 'Add to cart' button
        if($config['vm_add_to_cart'] == 1) {
            $code = '<form method="post" class="product" action="index.php">';
            $code .= '<div class="addtocart-bar">';
            $code .= '<span class="quantity-box" style="display: none"><input type="text" class="quantity-input" name="quantity[]" value="1" /></span>';
            
            $button_lbl = JText::_('MOD_NEWS_PRO_GK5_COM_VIRTUEMART_CART_ADD_TO');
			$button_cls = '';
            $stockhandle = VmConfig::get('stockhandle','none');
            
            $code .= '<span class="addtocart-button"><input type="submit" name="addtocart" class="addtocart-button" value="'.$button_lbl.'" title="'.$button_lbl.'" /></span>';
                
            $code .= '</div>
                    <input type="hidden" class="pname" value="'.$product->product_name.'"/>
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="view" value="cart" />
                    <noscript><input type="hidden" name="task" value="add" /></noscript>
                    <input type="hidden" name="virtuemart_product_id[]" value="'.$product->virtuemart_product_id.'" />
                    <input type="hidden" name="virtuemart_category_id[]" value="'.$product->virtuemart_category_id.'" />
                </form>';     
            $news_price .= $code;
		} 
       	// display discount
        if($config['vm_show_discount_amount'] == 1) {
            $disc_amount = str_replace('.',$cDecSymbol,number_format($product->prices['discountAmount'],$cDecimals));
            if($config['vm_currency_position'] == 'before') {  
            	$disc_amount = $cSymbol.' '.$disc_amount;
            } else {
            	$disc_amount = $disc_amount.' '.$cSymbol;
            }
            $news_price.= JText::_('MOD_NEWS_PRO_GK5_PRODUCT_DISCOUNT_AMOUNT'). $disc_amount;
        }
		// display tax
        if($config['vm_show_tax'] == 1) {
          	$taxAmount = str_replace('.',$cDecSymbol,number_format($product->prices['taxAmount'],$cDecimals));
            if($config['vm_currency_position'] == 'before') {  
            	$taxAmount = $cSymbol.' '.$taxAmount;
            } else {
            	$taxAmount = $taxAmount.' '.$cSymbol;
            }
            $news_price.= JText::_('MOD_NEWS_PRO_GK5_PRODUCT_TAX_AMOUNT'). $taxAmount;  
        }
  		// results
        return ($news_price != '<div>') ? $news_price.'</div>' : '';
	}
	// article link generator
	static function itemLink($item, $config) {
		$itemid = $config['vm_itemid'];
		$link = 'index.php?option=com_virtuemart&amp;view=productdetails&amp;virtuemart_product_id='.$item['id'].'&amp;virtuemart_category_id='.$item['cid'].'&amp;Itemid='.$itemid;
		return $link;
	}
	// category link generator
	static function categoryLink($item) {
		return 'index.php?option=com_virtuemart&amp;view=category&amp;virtuemart_category_id='.$item['cid'];
	}
}

// EOF
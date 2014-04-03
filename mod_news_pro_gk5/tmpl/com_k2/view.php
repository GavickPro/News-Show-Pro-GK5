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
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
//
class NSP_GK5_com_k2_View {
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
	        
	        $link = NSP_GK5_com_k2_View::itemLink($item);
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
			$link = NSP_GK5_com_k2_View::itemLink($item);
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
			$IMG_SOURCE = '';
			$item['title'] = str_replace('"', "&quot;", $item['title']);
			$uri = JURI::getInstance();
			//
			if(!$config['k2_image_size']) {
				$config['k2_image_size'] = 'Generic';
			}
			//
			if(JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item['id']).'_'.$config['k2_image_size'].'.jpg')){  
				$IMG_SOURCE = JURI::root().'media/k2/items/cache/'.md5("Image".$item['id']).'_'.$config['k2_image_size'].'.jpg';
	        } else {
				// set image to first in article content
				if(preg_match('/\<img.*src=.*?\>/',$item['text'])){
					$imgStartPos = JString::strpos($item['text'], 'src="');
					if($imgStartPos) {
						$imgEndPos = JString::strpos($item['text'], '"', $imgStartPos + 5);
					}	
					if($imgStartPos > 0) {
						$IMG_SOURCE = JString::substr($item['text'], ($imgStartPos + 5), ($imgEndPos - ($imgStartPos + 5)));
					}
				}
			}
			//
			$full_size_img = $IMG_SOURCE;
			//
			if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
				// try to override standard image
				if(strpos($IMG_SOURCE, 'http://') == FALSE) {					
					
					$img_file = NSP_GK5_Thumbs::createThumbnail(str_replace(JURI::root() . 'media', 'media', $IMG_SOURCE), $config, true, false, '', $links);
					
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
			
			if($only_url) {
				return $IMG_SOURCE;
			} else {
				//
				if($IMG_SOURCE != '') {
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
					if($config['news_image_link'] == 1 || $links) {
						if($config['news_image_modal'] == 1) {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a></div>' : '<a href="'.$full_size_img.'" class="modal nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a>';
						} else {
							$link = NSP_GK5_com_k2_View::itemLink($item);	
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$link.'" class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a></div>' : '<a href="'.$link.'" class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a>';
						}
					} else {
						return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" '.$size.' /></span></div>' : '<span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'" /></span>';
					}
				} else {
					return '';
				}
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
			$link = NSP_GK5_com_k2_View::itemLink($item);
			//
			if($config['news_content_readmore_pos'] == 'after') {
				return '<a class="readon inline" href="'.$link.'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
			} else {
				return '<a class="readon '.$class.'" href="'.$link.'">'.((trim($config['readmore_text']) != '') ? $config['readmore_text'] : JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE')).'</a>';
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
			$news_info_tag = stripos($config['info'.(($num == 2) ? '2' : '').'_format'], '%CART') !== FALSE ? 'div' : 'p';
	        $news_info = '<'.$news_info_tag.' class="nspInfo '.$class.'">'.$config['info'.(($num == 2) ? '2' : '').'_format'].'</'.$news_info_tag.'>';
	        //
	        $author = (trim(htmlspecialchars($item['author_alias'])) != '') ? htmlspecialchars($item['author_alias']) : htmlspecialchars($item['author_username']);
	        $info_author = ($config['user_avatar'] == 1) ? '<span><img src="'.K2HelperUtilities::getAvatar($item['author_id'], $item['author_email'], $config['avatar_size']).'" alt="'.$author.' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$author.'</span>' : $author;
	        //
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);
	        //
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
	        
	        $info_category = ($config['category_link'] == 1) ? '<a href="'.NSP_GK5_com_k2_View::categoryLink($item).'" >'.$item['catname'].'</a>' : $item['catname'];
	        $info_comments = JText::_('MOD_NEWS_PRO_GK5_NO_COMMENTS');
	       	//
	        if(isset($item['comments'])) { 
	        	if($item['comments'] == 1) {
	            	$info_comments = JText::_('MOD_NEWS_PRO_GK5_1COMMENT');
	            } else if($item['comments'] > 1 && $item['comments'] < 5) {
	            	$info_comments = $item['comments'] . ' ' . JText::_('MOD_NEWS_PRO_GK5_MORECOMMENTS');
	            } else if($item['comments'] >= 5) {
	            	$info_comments = $item['comments'] . ' ' . JText::_('MOD_NEWS_PRO_GK5_MUCHMORECOMMENTS');
	            }
	        }
	        //
	        $info_comments_short = '0';
	        $link = NSP_GK5_com_k2_View::itemLink($item);
            //
            if(isset($item['comments'])) { 
            	$info_comments_short = $item['comments'];
            }
            
            $info_comments_short = '<a href="'.$link.'">'.$info_comments_short.'</a>';
	        //
	        $info_tags = '';
	        if(isset($item['tags']) && count($item['tags']) > 0) {
	        	$i = 0;
	        	foreach($item['tags'] as $tag) {
	        		$link = urldecode(JRoute::_(K2HelperRoute::getTagRoute($tag)));
	        	
	        		if($i == 0) {
	        			$info_tags .= '<a href="' . $link . '">' . $tag . '</a>';
	        		} else {
	        			$info_tags .= ', <a href="' . $link . '">' . $tag . '</a>';
	        		}
	        		//
	        		$i++;
	        	}
	        }
	        // get k2 store data
	        $k2store_data = array(
		        				'cart' => '',
		        				'price' => ''
	        				);
	       	// get K2Store data if necessary
	        if(
	        	stripos($news_info, '%CART') !== FALSE || 
	        	stripos($news_info, '%PRICE') !== FALSE
	        ) {
	        	$k2store_data = NSP_GK5_com_k2_View::k2Store($item);
	        	
	        	if(stripos($news_info, '%CART') !== FALSE) {
		        	// load K2Store scripts
		       		$uri = JURI::getInstance();
		        	$document = JFactory::getDocument();
		        	$headData = $document->getHeadData();
		        	// generate keys of script section
		        	$headData_keys = array_keys($headData["scripts"]);
		        	// set variable for false
		        	$engine_founded = false;
		        	// searching phrase mootools in scripts paths
		        	if(array_search($uri->root().'media/k2store/js/k2store.js', $headData_keys) > 0) {
		        		$engine_founded = true;
		        	}
		        	// if engine doesn't exists in the head section
		        	if(!$engine_founded){ 
		        		// add new script tag connected with mootools from module
		        		$document->addScript($uri->root().'media/k2store/js/k2store.noconflict.js');
		        		$document->addScript($uri->root().'media/k2store/js/k2store.js');
		        	}
	        	}
	        }
	        // 
	        $news_info = str_replace('%AUTHOR', $info_author, $news_info);
	        $news_info = str_replace('%DATE', $info_date, $news_info);
	        $news_info = str_replace('%HITS', $info_hits, $news_info);
	        $news_info = str_replace('%CATEGORY', $info_category, $news_info);
	        $news_info = str_replace('%STARS', $info_stars, $news_info);
	        $news_info = str_replace('%RATE', $info_rate, $news_info);
	        $news_info = str_replace('%COMMENTS_SHORT', $info_comments_short, $news_info);
	        $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
	        $news_info = str_replace('%TAGS', $info_tags, $news_info);
	        $news_info = str_replace('%CART', $k2store_data['cart'], $news_info);
	        $news_info = str_replace('%PRICE', $k2store_data['price'], $news_info);
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
				$title = NSP_GK5_Utils::cutText($title, $config, 'list_title_limit', '&hellip;');
				$title = str_replace('"', "&quot;", $title);
				$link = NSP_GK5_com_k2_View::itemLink($item);
			
				if(JString::strlen($title) > 0) {
					$title = '<h4><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'">'.$title.'</a></h4>';
				}
			}
			
			if($config['links_image'] == 1) {
				$image = NSP_GK5_com_k2_View::image($config, $item, false, false, true);
			}
			// creating rest news list
			return '<li class="'.(($odd == 1) ? 'odd' : 'even').'">' . $image . (($image != '') ? '<div>' . $title . $text . '</div>' : ($title . $text)) . '</li>';	
		} else {
			return '';
		}
	}
	// article link generator
	static function itemLink($item, $config = false) {
		return urldecode(JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))));
	}
	// category link generator
	static function categoryLink($item) {
		return urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item['cid'].':'.urlencode($item['cat_alias']))));
	}
	// K2 Store data generator
	static function k2Store($item) {
		// preparing the output array
		$output = array(
						'cart' => '',
						'price' => ''
						);
		// if the settings exists
		if(is_file(JPATH_SITE.'/components/com_k2store/helpers/cart.php')) {
			require_once (JPATH_SITE.'/components/com_k2store/helpers/cart.php');
			// get product data
			$item_plugins_data = json_decode($item['plugins']);
			
			// output for the cart
			$output['cart'] = '<form action="index.php?option=com_k2store&amp;view=mycart" method="post" class="k2storeCartForm1 nspK2StoreCartForm" id="k2storeadminForm_'.$item['id'].'" name="k2storeadminForm_'.$item['id'].'" enctype="multipart/form-data">							
				<div id="add_to_cart_12" class="k2store_add_to_cart">
			        <input type="hidden" id="k2store_product_id" name="product_id" value="'.$item['id'].'">
		
			        '.JHTML::_( 'form.token' ).'				        
			        <input type="hidden" name="return" value="'.base64_encode(JUri::getInstance()->toString()).'">
			        <input value="Add to cart" type="submit" class="k2store_cart_button btn btn-primary">
			    </div>
			
				<div class="k2store-notification" style="display: none;">
						<div class="message"></div>
						<div class="cart_link"><a class="btn btn-success" href="index.php?option=com_k2store&amp;view=mycart">View Cart</a></div>
						<div class="cart_dialogue_close" onclick="jQuery(this).parent().slideUp().hide();">x</div>
				</div>
				
				<div class="error_container">
					<div class="k2product"></div>
					<div class="k2stock"></div>
				</div>
			
				<input type="hidden" name="product_qty" value="1">
				<input type="hidden" name="option" value="com_k2store">
				<input type="hidden" name="view" value="mycart">
				<input type="hidden" id="task" name="task" value="add">
			</form>';
			// output for the price
			$output['price'] = '';
			// getting the necessary data
			$price = $item_plugins_data->k2storeitem_price;
			$tax = $item_plugins_data->k2storeitem_tax;
			$special_price = 0;
			// getting the special price if exists
			if(isset($item_plugins_data->k2storespecial_price)) {
				$special_price = $item_plugins_data->k2storespecial_price;
			}
			// generate the basic price
			$base_price = K2StoreHelperCart::dispayPriceWithTax($price, $tax, 1);
			// check if the special price exists
			if($special_price > 0.0000) {
				$base_price = '<strike>' . $base_price . '</strike> ' . K2StoreHelperCart::dispayPriceWithTax($special_price, $tax, 1);
			} 
			// set the final output of the price
			$output['price'] = '<span class="nspK2StorePrice">' . $base_price . '</span>';
		}
		// return the output array
		return $output;
	}
}

// EOF

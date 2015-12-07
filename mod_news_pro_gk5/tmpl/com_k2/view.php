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
class NSP_GK5_com_k2_View extends NSP_GK5_View {
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if(!($config['news_content_image_pos'] != 'disabled' || $pm || $links)) {
			return '';
		}
		
		$IMG_SOURCE = '';
		$IMG_LINK = static::itemLink($item, $config);
		$item['title'] = str_replace('"', "&quot;", $item['title']);
		$uri = JURI::getInstance();
		$IMG_SOURCE = static::originalImage($config, $item);
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
		
		return NSP_GK5_com_k2_View::getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img);
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
	        $author_name = (trim(htmlspecialchars($item['author_alias'])) != '') ? htmlspecialchars($item['author_alias']) : htmlspecialchars($item['author_username']);
	        $author_html = '<a href="'.urldecode(JRoute::_(K2HelperRoute::getUserRoute($item['author_id']))).'" target="'.$config['open_links_window'].'">';
	        $author_html .= $author_name;
	        $author_html .= '</a>';
	        $info_author = ($config['user_avatar'] == 1) ? '<span><img src="'.K2HelperUtilities::getAvatar($item['author_id'], $item['author_email'], $config['avatar_size']).'" alt="'.$author_name.' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$author_html.'</span>' : $author_html;
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
	        
	        $info_category = ($config['category_link'] == 1) ? '<a href="'.static::categoryLink($item).'" target="'.$config['open_links_window'].'">'.$item['catname'].'</a>' : $item['catname'];
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
	        $link = static::itemLink($item);
            //
            if(isset($item['comments'])) { 
            	$info_comments_short = $item['comments'];
            }
            
            $info_comments_short = '<a href="'.$link.'" target="'.$config['open_links_window'].'">'.$info_comments_short.'</a>';
	        //
	        $info_tags = '';
	        if(isset($item['tags']) && count($item['tags']) > 0) {
	        	$i = 0;
	        	foreach($item['tags'] as $tag) {
	        		$link = urldecode(JRoute::_(K2HelperRoute::getTagRoute($tag)));
	        	
	        		if($i == 0) {
	        			$info_tags .= '<a href="' . $link . '" target="'.$config['open_links_window'].'">' . $tag . '</a>';
	        		} else {
	        			$info_tags .= ', <a href="' . $link . '" target="'.$config['open_links_window'].'">' . $tag . '</a>';
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
	        	$k2store_data = static::k2Store($item);
	        	
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
	        // Featured label
	        $info_featured = '';
	        
	        if(stripos($news_info, '%FEATURED') !== FALSE && $item['frontpage'] == '1') {
	        	$info_featured = '<strong class="is-featured">'.JText::_('MOD_NEWS_PRO_GK5_FEATURED').'</strong>';
	        }
	        // 
	        $news_info = str_replace('%AUTHOR_NAME', $author_name, $news_info);
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
	        $news_info = str_replace('%FEATURED', $info_featured, $news_info);
	    } else {
	    	return '';
	    }
		//
		return $news_info;		
	}
	// article link generator
	static function itemLink($item, $config = false) {
		if(isset($item['overrided_url'])) {
			return $item['overrided_url'];
		}
		
		return urldecode(JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))));
	}
	// category link generator
	static function categoryLink($item) {
		return urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item['cid'].':'.urlencode($item['cat_alias']))));
	}
	// user link generator
	static function authorLink($item) {
		return urldecode(JRoute::_(K2HelperRoute::getUserRoute($item['author_id'])));
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
	// orginal image
	static function originalImage($config, $item) {
		$IMG_SOURCE = '';
		
		if(!$config['k2_image_size']) {
			$config['k2_image_size'] = 'Generic';
		}
		
		if($config['k2_image_size'] == 'first') {
			// set image to first in article content
			$IMG_SOURCE = NSP_GK5_com_k2_View::getImageFromText($item['text']);	
		} else {
			// search for the article featured image
			if(JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item['id']).'_'.$config['k2_image_size'].'.jpg')){  
				$IMG_SOURCE = JURI::root().'media/k2/items/cache/'.md5("Image".$item['id']).'_'.$config['k2_image_size'].'.jpg';
			} elseif(JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item['id']).'_'.$config['k2_image_size'].'.png')){  
	            $IMG_SOURCE = JURI::root().'media/k2/items/cache/'.md5("Image".$item['id']).'_'.$config['k2_image_size'].'.png';
			}
		}
		
		return $IMG_SOURCE;
	}
}

// EOF

<?php

/**
 *
 * This View is responsible for generating layout parts for the
 * com_content data source
 *
 **/

// no direct access
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
			$output = str_replace('"', "&quot;", $item['title']);
	        $link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))));
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
			if($config['clean_xhtml'] == 1) {
				$item['text'] = strip_tags($item['text']);
			}
			//
			$item['text'] = NSP_GK5_Utils::cutText($item['text'], $config, 'news_limit');
			$link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))));
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
	static function image($config, $item, $only_url = false, $pm = false){		
		if($config['news_content_image_pos'] != 'disabled' || $pm) {
			$IMG_SOURCE = '';
			$item['title'] = str_replace('"', "&quot;", $item['title']);
			$uri = JURI::getInstance();
			//
			if(JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item['id']).'_L.jpg')){  
				$IMG_SOURCE = JURI::root().'media/k2/items/cache/'.md5("Image".$item['id']).'_L.jpg';
	        }elseif(JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'items'.DS.'cache'.DS.md5("Image".$item['id']).'_S.jpg')){  
				$IMG_SOURCE = JURI::root().'media/k2/items/cache/'.md5("Image".$item['id']).'_S.jpg';
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
			if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
				// try to override standard image
				if(strpos($IMG_SOURCE, 'http://') == FALSE) {					
					
					$img_file = NSP_GK5_Thumbs::createThumbnail(str_replace(JURI::root() . 'media', 'media', $IMG_SOURCE), $config, true);
					
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
					$class = ' t'.$config['news_content_image_pos'].' f'.$config['news_content_image_float']; 
					$size = '';
					$margins = '';
					// 
					if($config['responsive_images'] == 1) {
						$class .= ' gkResponsive'; 
					}
					//
					if($config['img_width'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'width:'.$config['img_width'].'px;';
					if($config['img_height'] != 0 && !$config['img_keep_aspect_ratio'] && $config['responsive_images'] == 0) $size .= 'height:'.$config['img_height'].'px;';
					if($config['img_margin'] != '') $margins = ' style="margin:'.$config['img_margin'].';"';
					//
					if($config['news_image_link'] == 1) {
						$link = urldecode( JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))) );	
						return ($config['news_content_image_pos'] == 'center') ? '<div class="center'.$class.'"><a href="'.$link.'" class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a></div>' : '<a href="'.$link.'" class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'"  /></a>';
					} else {
						return ($config['news_content_image_pos'] == 'center') ? '<div class="center'.$class.'"><span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" '.$size.' /></span></div>' : '<span class="nspImageWrapper'.$class.'"'.$margins.'><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($news_title).'" style="'.$size.'" /></span>';
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
			$link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))));
			//
			if($config['news_content_readmore_pos'] == 'after') {
				return '<a class="readon inline" href="'.$link.'">'.JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE').'</a>';
			} else {
				return '<a class="readon '.$class.'" href="'.$link.'">'.JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE').'</a>';
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
	        $news_info = '<p class="nspInfo '.$class.'">'.$config['info'.(($num == 2) ? '2' : '').'_format'].'</p>';
	        //
	        $info_author = ($config['user_avatar'] == 1) ? '<span><img src="'.K2HelperUtilities::getAvatar($item['author_id'], $item['author_email'], $config['avatar_size']).'" alt="'.htmlspecialchars($item['author']).' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$item['author'].'</span>' : $item['author'];
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
	        $info_hits = JText::_('MOD_NEWS_PRO_GK5_NHITS').$item['hits'];
	        $info_rate = ($item['rating_count'] > 0) ? '<span class="nspRate">' . JText::_('MOD_NEWS_PRO_GK5_NSP_RATE') .' '. number_format($item['rating_sum'] / $item['rating_count'], 2) . '</span>': '';
	        $info_category = ($config['category_link'] == 1) ? '<a href="'. urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item['cid'].':'.urlencode($item['cat_alias'])))) .'" >'.$item['catname'].'</a>' : $item['catname'];
	        $info_comments = JText::_('MOD_NEWS_PRO_GK5_NO_COMMENTS');
	       	//
	        if(!(!isset($item['comments']) || $item['comments'] == 0)) {
	            $info_comments = JText::_('MOD_NEWS_PRO_GK5_COMMENTS').' ('.(isset($item['comments']) ? $item['comments'] : '0' ) . ')';
	        }
	        // 
	        $news_info = str_replace('%AUTHOR', $info_author, $news_info);
	        $news_info = str_replace('%DATE', $info_date, $news_info);
	        $news_info = str_replace('%HITS', $info_hits, $news_info);
	        $news_info = str_replace('%CATEGORY', $info_category, $news_info);
	        $news_info = str_replace('%RATE', $info_rate, $news_info);
	        $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
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
				$link = urldecode( JRoute::_(K2HelperRoute::getItemRoute($item['id'].':'.urlencode($item['alias']), $item['cid'].':'.urlencode($item['cat_alias']))) );
			
				if(JString::strlen($title) > 0) {
					$title = '<h4><a href="'.$link.'" title="'.htmlspecialchars($item['title']).'">'.$title.'</a></h4>';
				}
			}
			// creating rest news list
			return '<li class="'.(($odd == 1) ? 'odd' : 'even').'">' . $title . $text . '</li>';	
		} else {
			return '';
		}
	}
}

// EOF

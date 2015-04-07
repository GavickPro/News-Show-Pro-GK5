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

class NSP_GK5_com_content_View extends NSP_GK5_View {
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if($config['news_content_image_pos'] != 'disabled' || $pm || $links) {
			$item['title'] = str_replace('"', "&quot;", $item['title']);
		    $IMG_SOURCE = '';
		    $IMG_LINK = static::itemLink($item);
			$uri = JURI::getInstance();
			// get image from Joomla! Images and Links settings
			$IMG_SOURCE = static::originalImage($config, $item);
			//
			$full_size_img = $IMG_SOURCE;
			//
			if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
				// try to override standard image
				if(strpos($IMG_SOURCE,'http://') == FALSE) {
					$img_file = NSP_GK5_Thumbs::createThumbnail($IMG_SOURCE, $config, false, false, '', $links);
					
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
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$full_size_img .'" class="modal nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a></div>' : '<a href="'.$full_size_img .'" class="modal nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a>';
						} else {
							return ($config['news_content_image_pos'] == 'center' && !$links) ? '<div class="center'.$class.'"><a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a></div>' : '<a href="'.$IMG_LINK.'" class="nspImageWrapper'.$class.'"'.$margins.' target="'.$config['open_links_window'].'"><img class="nspImage'.$class.'" src="'.$IMG_SOURCE.'" alt="'.htmlspecialchars($item['title']).'" style="'.$size.'"  /></a>';
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
	        $info_category = ($config['category_link'] == 1) ? '<a href="'. static::categoryLink($item) .'" target="'.$config['open_links_window'].'">'.$item['catname'].'</a>' : $item['catname'];		        
	        //
	        $author = (trim(htmlspecialchars($item['author_alias'])) != '') ? htmlspecialchars($item['author_alias']) : htmlspecialchars($item['author_username']);
	        $info_author = ($config['user_avatar'] == 1) ? '<span><img src="'. NSP_GK5_Utils::avatarURL($item['author_email'], $config['avatar_size']).'" alt="'.$author.' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$author.'</span>' : $author;
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
	        
	        $info_comments = '';
	        
	        if($config['com_content_comments_source'] != 'none') {
		    	$link = static::itemLink($item); 
		    	
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
		        
		        $info_comments = '<a href="'.$link.'" target="'.$config['open_links_window'].'">'.$info_comments.'</a>';
	        }
	        //
	        $info_comments_short = '';
	        
	        if($config['com_content_comments_source'] != 'none') {
	        	$link = static::itemLink($item);  
	        	
	        	$info_comments_short = 0;
	            //
	            if(isset($item['comments'])) { 
	            	$info_comments_short = $item['comments'];
	            }
	            
	            $info_comments_short = '<a href="'.$link.'" target="'.$config['open_links_window'].'">'.$info_comments_short.'</a>';
	        }
	        // Featured label
	        $info_featured = '';
	        	        
	        if(stripos($news_info, '%FEATURED') !== FALSE && $item['frontpage'] == '1') {
	        	$info_featured = '<strong class="is-featured">'.JText::_('MOD_NEWS_PRO_GK5_FEATURED').'</strong>';
	        }
	        // Tags
	        $info_tags = '';
	        if(isset($item['tags']) && count($item['tags']) > 0) {
	        	if (!class_exists( 'TagsHelperRoute' )) {
        			require(JPATH_SITE.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'route.php');
        		}
	        	
	        	$i = 0;
	        	foreach($item['tags'] as $tag_name => $tag_id) {
	        		$link = urldecode(JRoute::_(TagsHelperRoute::getTagRoute($tag_id)));
	        	
	        		if($i == 0) {
	        			$info_tags .= '<a href="' . $link . '" target="'.$config['open_links_window'].'">' . $tag_name . '</a>';
	        		} else {
	        			$info_tags .= ', <a href="' . $link . '" target="'.$config['open_links_window'].'">' . $tag_name . '</a>';
	        		}
	        		//
	        		$i++;
	        	}
	        }
	        // 
	        $news_info = str_replace('%AUTHOR', $info_author, $news_info);
	        $news_info = str_replace('%DATE', $info_date, $news_info);
	        $news_info = str_replace('%HITS', $info_hits, $news_info);
	        $news_info = str_replace('%CATEGORY', $info_category, $news_info);
	        $news_info = str_replace('%STARS', $info_stars, $news_info);
	        $news_info = str_replace('%RATE', $info_rate, $news_info);
	        $news_info = str_replace('%TAGS', $info_tags, $news_info);
	        $news_info = str_replace('%FEATURED', $info_featured, $news_info);
	        // only if comments used
	       	if($config['com_content_comments_source'] != 'none') {
	        	$news_info = str_replace('%COMMENTS_SHORT', $info_comments_short, $news_info);
	        	$news_info = str_replace('%COMMENTS', $info_comments, $news_info);
	        }
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
		
		return ($item['id'] != 0) ? JRoute::_(ContentHelperRoute::getArticleRoute($item['id'], $item['cid'], $item['lang'])) : JRoute::_('index.php?option=com_users&view=login');
	}
	// category link generator
	static function categoryLink($item) {
		return JRoute::_(ContentHelperRoute::getCategoryRoute($item['cid']));
	}
	// user link generator
	static function authorLink($item) {
		return '';
	}
	// original image
	static function originalImage($config, $item) {
		$images = json_decode($item['images']);
		$IMG_SOURCE = '';
		
		if($config['thumb_image_type'] == 'full' && (isset($images) && $images->image_fulltext!= '')) {
			$IMG_SOURCE = $images->image_fulltext;
		} elseif($config['thumb_image_type'] == 'intro' && (isset($images) && $images->image_intro!='')) {
			$IMG_SOURCE = $images->image_intro;
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
		
		return $IMG_SOURCE;
	}
}

// EOF

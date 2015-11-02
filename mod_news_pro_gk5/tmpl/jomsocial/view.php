<?php

/**
* This View is responsible for generating layout parts for the jomsocial data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

if(file_exists(JPATH_BASE . '/components/com_community/defines.community.php')) {
	include_once(JPATH_BASE . '/components/com_community/defines.community.php');
	require_once(JPATH_BASE . '/components/com_community/libraries/core.php');
	
	class NSP_GK5_jomsocial_View extends NSP_GK5_View {
		// header generator
		static function header($config, $item) {
			return '';		
		}
		// article image generator
		static function image($config, $item, $only_url = false, $pm = false, $links = false){		
			if(!($item['type'] == 'photo' && ($config['news_content_image_pos'] != 'disabled' || $pm || $links))) {
				return '';
			}
			
			$IMG_SOURCE = '';
			$IMG_LINK = static::itemLink($item, $config);
			$uri = JURI::getInstance();
			//
			if(JFile::exists(JPATH_SITE.DS . $item['image'])) {  
				$IMG_SOURCE = $item['image'];
	        }
			//
			$full_size_img = $IMG_SOURCE;
			//
			if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
				// try to override standard image
				if(strpos($IMG_SOURCE, 'http://') == FALSE) {					
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
			
			return NSP_GK5_jomsocial_View::getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img);
		}
		// article information generator
		static function info($config, $item, $num = 1) {
			// %AUTHOR %DATE %COMMENTS %LIKES
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
		        // Author
		        $author = $item['author_username'];
		        
		        $user = CFactory::getUser($item['user_id']);
				$avatar = false;
	
				if($config['user_avatar'] == 1) {
					$avatar = $user->getAvatar();
				}
		        
		        $info_author = ($avatar) ? '<span><img src="'. $avatar .'" alt="'.$author.' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$author.'</span>' : $author;
		        // Date
		        $info_date = JHTML::_('date', $item['date'], $config['date_format']);			
		        // Comments
		        $info_comments = '';	
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
		        
			    $info_comments = '<span>'.$info_comments.'</span>';
			    // Likes
			    $info_likes = '';	
			    $info_likes = JText::_('MOD_NEWS_PRO_GK5_NO_LIKES');
			    //
			    if(isset($item['likes'])) { 
			    	if($item['likes'] == 1) {
			        	$info_likes = JText::_('MOD_NEWS_PRO_GK5_1LIKE');
			        } else if($item['likes'] > 1 && $item['likes'] < 5) {
			        	$info_likes = $item['likes'] . ' ' . JText::_('MOD_NEWS_PRO_GK5_MORELIKES');
			        } else if($item['likes'] >= 5) {
			        	$info_likes = $item['likes'] . ' ' . JText::_('MOD_NEWS_PRO_GK5_MUCHMORELIKES');
			        }
			    }
			    
			    $info_likes = '<span>'.$info_likes.'</span>';
		        // 
		        $news_info = str_replace('%AUTHOR', $info_author, $news_info);
		        $news_info = str_replace('%DATE', $info_date, $news_info);
		        $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
		        $news_info = str_replace('%LIKES', $info_likes, $news_info);
		    } else {
		    	return '';
		    }
			//
			return $news_info;
		}
		// article link generator
		static function itemLink($item, $config = false) {
			if($item['app'] == 'profile') {
				return CRoute::_('index.php?option=com_community&view=profile&userid='.$item['user_id']);
			} else {
				return CRoute::_('index.php?option=com_community&view=photos&task=photo&albumid=' . $item['album_id'] . '&photoid=' . $item['photo_id'] . '&userid=' . $item['user_id']);	
			}
		}
		// category link generator
		static function categoryLink($item) {
			return '';
		}
		// user link generator
		static function authorLink($item) {
			return CRoute::_('index.php?option=com_community&view=profile&userid='.$item['user_id']);
		}
	}
} else {
	echo 'Please install JomSocial in order to use this data source';
}

// EOF

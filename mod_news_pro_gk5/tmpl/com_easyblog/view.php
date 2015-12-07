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

//
class NSP_GK5_com_easyblog_View extends NSP_GK5_View {
	// article text generator
	static function text($config, $item, $readmore) {
		if($config['news_content_text_pos'] == 'disabled') {
			return '';
		}
		//
		if($item['text'] === '') {
			$item['text'] = $item['text_alt'];
		}
		//
		$item['text'] = NSP_GK5_Utils::cutText($item['text'], $config, 'news_limit');
		$item['text'] = static::textPlugins($item['text'], $config);
		$link = static::itemLink($item, $config);
		//
		if($config['news_text_link'] == 1) {
			$item['text'] = '<a href="'.$link.'" target="'.$config['open_links_window'].'">'.$item['text'].'</a>';
		} 
		
		$class = ' t'.$config['news_content_text_pos'].' f'.$config['news_content_text_float'];
		//
		$output_html = '<p class="nspText'.$class.'">';
		$output_html .= $item['text'];
		if($config['news_content_readmore_pos'] == 'after') { 
			$output_html .= ' ' . $readmore;
		}
		$output_html .= '</p>';
		
		return $output_html;
	}
	
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		if(!($config['news_content_image_pos'] != 'disabled' || $pm || $links)) {
			return '';
		}
		
		$IMG_SOURCE = '';
		$item['title'] = str_replace('"', "&quot;", $item['title']);
		$uri = JURI::getInstance();
		$IMG_LINK = static::itemLink($item, $config);
		//
		if(trim($item['image']) != ''){  
			$image_path = str_replace(array('user:', 'post:'), '', $item['image']);

			if(stripos($item['image'], 'user:') !== FALSE) {
				$IMG_SOURCE = 'images/easyblog_images/' . $image_path;
			} else {
				$IMG_SOURCE = 'images/easyblog_articles/' . $image_path;
			}
	        } else {
			// set image to first in article content
			$IMG_SOURCE = NSP_GK5_com_easyblog_View::getImageFromText($item['text']);					
		}
		//
		$full_size_img = $IMG_SOURCE;
		//
		if($config['create_thumbs'] == 1 && $IMG_SOURCE != ''){
			// try to override standard image
			if(strpos($IMG_SOURCE, 'http://') == FALSE) {					
				
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
		
		return NSP_GK5_com_easyblog_View::getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img);
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
 			$info_author = '';
 			// load author data only if necessary
 			if(stripos($news_info, '%AUTHOR') !== FALSE){
 				$author = htmlspecialchars($item['author_username']);
 				$author_html = '<a href="'.urldecode(JRoute::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $item['author_id'])).'" target="'.$config['open_links_window'].'">'.$author.'</a>';
 				
	 			if($config['user_avatar'] == 1) {
		 			// load necessary Easy Blog Route Helper and avatar helper
		 			require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_easyblog'.DS.'includes'.DS.'utils.php');
		 			require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_easyblog'.DS.'includes'.DS.'easyblog.php');
		 			require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_easyblog'.DS.'includes'.DS.'avatar'.DS.'avatar.php');
		 			
		 			$post_data = EB::post($item['id']);
		 			$blogger = $post_data->getAuthor();
		 			$info_author = '<span><img src="'.$blogger->getAvatar().'" alt="'.$author.' - avatar" class="nspAvatar" width="'.$config['avatar_size'].'" height="'.$config['avatar_size'].'" /> '.$author_html.'</span>';
	 			} else {
	 				$info_author = $author_html;
	 			}
			}
	        //
	        $info_date = JHTML::_('date', $item['date'], $config['date_format']);
	        //
	        $info_hits = JText::_('MOD_NEWS_PRO_GK5_NHITS').$item['hits'];
	        $info_rate = '';
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
        	$info_comments_short = 0;
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
	        	foreach($item['tags'] as $tag => $id) {
	        		$link = urldecode(JRoute::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id));
	        	
	        		if($i == 0) {
	        			$info_tags .= '<a href="' . $link . '" target="'.$config['open_links_window'].'">' . $tag . '</a>';
	        		} else {
	        			$info_tags .= ', <a href="' . $link . '" target="'.$config['open_links_window'].'">' . $tag . '</a>';
	        		}
	        		//
	        		$i++;
	        	}
	        }
	        // Featured label
	        $info_featured = '';
	                
	        if(stripos($news_info, '%FEATURED') !== FALSE && $item['frontpage'] == '1') {
	        	$info_featured = '<strong class="is-featured">'.JText::_('MOD_NEWS_PRO_GK5_FEATURED').'</strong>';
	        }
	        // 
	        $news_info = str_replace('%AUTHOR_NAME', $author, $news_info);
	        $news_info = str_replace('%AUTHOR', $info_author, $news_info);
	        $news_info = str_replace('%DATE', $info_date, $news_info);
	        $news_info = str_replace('%HITS', $info_hits, $news_info);
	        $news_info = str_replace('%CATEGORY', $info_category, $news_info);
	        $news_info = str_replace('%RATE', $info_rate, $news_info);
	        $news_info = str_replace('%COMMENTS_SHORT', $info_comments_short, $news_info);
	        $news_info = str_replace('%COMMENTS', $info_comments, $news_info);
	        $news_info = str_replace('%TAGS', $info_tags, $news_info);
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
		
		return urldecode(JRoute::_('index.php?option=com_easyblog&view=entry&id=' . $item['id']));
	}
	// category link generator
	static function categoryLink($item) {
		return urldecode(JRoute::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $item['cid']));
	}
	// user link generator
	static function authorLink($item) {
		return urldecode(JRoute::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $item['author_id']));
	}
}

// EOF

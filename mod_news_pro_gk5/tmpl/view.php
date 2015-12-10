<?php

/**
* This View is responsible for generating layout parts
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.6.2 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_View {
	// header generator
	static function header($config, $item) {
		if($config['news_content_header_pos'] == 'disabled') {
			return '';
		}
		
		$class = ' t'.$config['news_content_header_pos'].' f'.$config['news_content_header_float'];
		
		if(static::image($config, $item, true, true) != '') {
			$class .= ' has-image';
		}
		
		$item_title = htmlspecialchars(html_entity_decode(strip_tags($item['title'])));
		$output = NSP_GK5_Utils::cutText($item_title, $config, 'title_limit', '&hellip;');
		$output = str_replace('"', "&quot;", $output);
		// first word span wrap
		if($config['news_header_first_word'] == 1) {
			$output_temp = explode(' ', $output);
			$first_word = $output_temp[0];
			$output_temp[0] = '<span>'.$output_temp[0].'</span>';
			$output = preg_replace('/' . $first_word . '/mi', $output_temp[0], $output, 1);
		}

        $link = static::itemLink($item, $config);
		//
		if($config['news_header_link'] == 1) {
			$link_attrs = ' title="'.htmlspecialchars($item['title']).'"';
			$link_attrs .= ' target="'.$config['open_links_window'].'"';
			
			$output_html = '<h4 class="nspHeader'.$class.'">';
			$output_html .= '<a href="'.$link.'" '.$link_attrs.'>';
			$output_html .=  $output;
			$output_html .= '</a></h4>';	
			
			return $output_html;
		} else {
			$output_html = '<h4 class="nspHeader'.$class.'" title="'.htmlspecialchars($item['title']).'">';
			$output_html .= $output;
			$output_html .= '</h4>';
			
			return $output_html;
		}
	}
	
	// article text generator
	static function text($config, $item, $readmore) {
		if($config['news_content_text_pos'] == 'disabled') {
			return '';
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
	
	// ReadMore button generator
	static function readMore($config, $item) {
		//
		if($config['news_content_readmore_pos'] == 'disabled') {
			return '';
		}
		
		$class = 'readon f'.$config['news_content_readmore_pos'];
		
		if($config['news_content_readmore_pos'] == 'after') {
			$class = 'readon inline';
		}
		
		$link = static::itemLink($item, $config); 
		//
		$output_html = '<a class="'.$class.'" href="'.$link.'" target="'.$config['open_links_window'].'">';
		
		if(trim($config['readmore_text']) != '') {
			$output_html .= $config['readmore_text'];
		} else {
			$output_html .= JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE');
		}
		
		$output_html .= '</a>';
		
		return $output_html;
	}
	
	// rest link list generator	
	static function lists($config, $item, $num) {
		$odd = $num % 2;
		
		if($config['news_short_pages'] == 0) {
			return '';
		}
		
        $text = '';
        $title = '';
        $image = '';
        $readmore = '';
        $link = static::itemLink($item, $config);
        
        if($config['list_text_limit'] > 0) {
            $item['text'] = static::textPlugins($item['text'], $config);
            $text = NSP_GK5_Utils::cutText(strip_tags($item['text']), $config, 'list_text_limit', '&hellip;');
            
            if(JString::strlen($text) > 0) {
            	$text = '<p>'.$text.'</p>';
            }
		}
		
		if($config['list_title_limit'] > 0) {
			$title = htmlspecialchars($item['title']);
			$title = NSP_GK5_Utils::cutText($title, $config, 'list_title_limit', '&hellip;');
			$title = str_replace('"', "&quot;", $title);
		
			if(JString::strlen($title) > 0) {
				$title_attrs  = 'title="'.htmlspecialchars($item['title']).'"';
				$title_attrs .= ' target="'.$config['open_links_window'].'"';
			
				$title_html  = '<h4>';
				$title_html .= '<a href="'.$link.'" '.$title_attrs.'>';
				$title_html .= $title;
				$title_html .= '</a>';
				$title_html .= '</h4>';
				
				$title = $title_html;
			}
		}
		
		if($config['links_image'] == 1) {
			$image = static::image($config, $item, false, false, true);
		}
		
		if(isset($config['links_readmore']) && $config['links_readmore'] == 1) {
			$readmore = '<a class="readon" href="'.$link.'" target="'.$config['open_links_window'].'">';
			
			if(trim($config['readmore_text']) != '') {
				$readmore .= $config['readmore_text'];
			} else {
				$readmore .= JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE');
			}
			
			$readmore .= '</a>';
		}
		
		// creating rest news list
		$output_html = '<li class="'.(($odd == 1) ? 'odd' : 'even').'">';
		$output_html .= $image;
		
		if($image != '') {
			$output_html .= '<div>';
		}
		
		$output_html .= $title . $text . $readmore;
		
		if($image != '') {
			$output_html .= '</div>';
		}
		
		$output_html .= '</li>';
		
		return $output_html;
	}
	
	// article image generator
	static function image($config, $item, $only_url = false, $pm = false, $links = false){		
		return '';
	}
	
	// article information generator
	static function info($config, $item, $num = 1) {
		return '';		
	}	
	
	// article link generator
	static function itemLink($item, $config = false) {
		return '';
	}
	
	// category link generator
	static function categoryLink($item) {
		return '';
	}
	
	// user link generator
	static function authorLink($item) {
		return '';
	}
	
	// article image generator
	static function originalImage($config, $item){		
		return '';
	}
	
	// Plugin text cleaner
	static function textPlugins($text, $config) {
		// Parsing plugins
		if($config['parse_plugins'] == TRUE) {
			$text = JHtml::_('content.prepare', $text);
		}	
		// Cleaning plugins
		if($config['clean_plugins'] == TRUE) {
			$text = preg_replace("/(\{.+?\}.+?\{.+?})|(\{.+?\})/", "", $text);
			$text = preg_replace("/(\[.+?\].+?\[.+?])|(\[.+?\])/", "", $text);
		}
		
		return $text; 
	}
	
	// Get image tag from the specified string
	static function getImageFromText($text) {
		if(preg_match('/\<img.*src=.*?\>/', $text)){
			$imgStartPos = JString::strpos($text, 'src="');
			
			if($imgStartPos) {
				$imgEndPos = JString::strpos($text, '"', $imgStartPos + 5);
			}
				
			if($imgStartPos > 0) {
				return JString::substr($text, ($imgStartPos + 5), ($imgEndPos - ($imgStartPos + 5)));
			}
		}
		
		return '';
	}
	
	// Get image HTML code
	static function getImageHTML($only_url, $IMG_SOURCE, $links, $config, $IMG_LINK, $full_size_img, $alt_text = '', $featured_state = false) {
		if($only_url) {
			return $IMG_SOURCE;
		} else {
			//
			if($IMG_SOURCE == '') {
				return '';
			}
	
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
				if(
					$config['img_width'] != 0 && 
					!$config['img_keep_aspect_ratio'] && 
					$config['responsive_images'] == 0
				) {
					$size .= 'width:'.$config['img_width'].'px;';
				}
				
				if(
					$config['img_height'] != 0 && 
					!$config['img_keep_aspect_ratio'] && 
					$config['responsive_images'] == 0
				) {
					$size .= 'height:'.$config['img_height'].'px;';
				}
				
				if($config['img_margin'] != '') {
					$margins = ' style="margin:'.$config['img_margin'].';"';
				}
			} else {
				if(
					$config['links_img_width'] != 0 && 
					!$config['img_keep_aspect_ratio'] && 
					$config['responsive_images'] == 0
				) {
					$size .= 'width:'.$config['links_img_width'].'px;';
				}
				
				if(
					$config['links_img_height'] != 0 && 
					!$config['img_keep_aspect_ratio'] && 
					$config['responsive_images'] == 0
				) {
					$size .= 'height:'.$config['links_img_height'].'px;';
				}
				
				if($config['links_img_margin'] != '') {
					$margins = ' style="margin:'.$config['links_img_margin'].';"';
				}
			}
			//
			$img_link = $IMG_LINK;
			$img_class = 'nspImageWrapper' . $class;

			if($links && $config['links_image_position'] == 'right') {
				$img_class .= ' nspImageWrapperRight';
			}

			$img_output = '';
			
			if($size != '') {
				$size = ' style="'.$size.'"';
			}
			
			if($config['news_image_modal'] == 1) {
				$img_link = $full_size_img;
				$img_class .= ' modal';
			}
			
			if($config['news_content_image_pos'] == 'center' && !$links) {
				$img_output .= '<div class="center'.$class.'">';
			}
				
			$img_link_attrs = 'class="'.$img_class.'" ' . $margins;
		
			if($config['news_image_link'] == 1 || $links) {
				$img_link_attrs .= ' target="'.$config['open_links_window'].'"';
			}
			
			$img_attrs = ' class="nspImage" src="'.$IMG_SOURCE.'"';
			$img_attrs .= ' alt="'.$alt_text.'" '.$size;
			
			if(
				($config['news_image_link'] == 1 && !$links) || 
				($links && $config['links_image_link'] == 1)
			) {
				$img_output .= '<a href="'.$img_link.'" '.$img_link_attrs.'>';
			} else {
				$img_output .= '<span '.$img_link_attrs.'>';
			}
			
			$img_output .= '<img '.$img_attrs.' />';
			$badge = '';

			if(
				$featured_state && 
				(
					$config['data_source'] === 'com_virtuemart' ||
					$config['data_source'] === 'com_virtuemart_categories'
				) &&
				$config['vm_show_featured_badge']
			) {
            	$badge = '<sup class="nspBadge">'.JText::_('MOD_NEWS_PRO_GK5_NSP_FEATURED').'</sup>';
        	}

        	$img_output = $img_output . $badge;

			if(
				($config['news_image_link'] == 1 && !$links) || 
				($links && $config['links_image_link'] == 1)
			) {
				$img_output .= '</a>';
			} else {
				$img_output .= '</span>';
			}
				
			if($config['news_content_image_pos'] == 'center' && !$links) {
				$img_output .= '</div>';
			}
			
			return $img_output;
		}
	}
}

// EOF
<?php

/**
* Video List
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.4 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_VideoList {
	// necessary class fields
	private $parent;
	private $mode;
	private $videos;
	// constructor
	function __construct($parent) {
		$this->parent = $parent;
		// detect the supported Data Sources
		if(stripos($this->parent->config['data_source'], 'com_content_') !== FALSE) {
			$this->mode = 'com_content';
		} else if(stripos($this->parent->config['data_source'], 'k2_') !== FALSE) { 
			$this->mode = 'com_k2';
		} else {
			$this->mode = false;
		}
		// get videos
		$this->videos = $this->get_videos();
	}
	// static function which returns amount of articles to render - VERY IMPORTANT!!
	static function amount_of_articles($parent) {
		return $parent->config['portal_mode_video_list_pages'] * $parent->config['portal_mode_video_list_per_page'];
	}
	// output generator	
	function output() {	
		// check if any article to display exists
		if(count($this->parent->content)) {
			// output the HTML code
			echo '<div class="gkNspPM gkNspPM-VideoList">';
			
			if(trim($this->parent->config['nsp_pre_text'])) {
				echo $this->parent->config['nsp_pre_text'];
			}
			
			$rows = 1;
			
			if($this->parent->config['portal_mode_video_list_rows'] > 1) {
				$rows = $this->parent->config['portal_mode_video_list_rows'];
			}
			
			echo '<div>';
			// render blocks
			for($i = 0; $i < count($this->parent->content); $i++) {
				$title = NSP_GK5_Utils::cutText(strip_tags($this->parent->content[$i]['title']), $this->parent->config, 'portal_mode_video_list_title_limit', '&hellip;');
				//
				if($i == 0) {
					echo '<div class="gkItemsPage active" data-cols="'.ceil($this->parent->config['portal_mode_video_list_per_page'] / $rows).'">';
				}
				
				$info_date = JHTML::_('date', $this->parent->content[$i]['date'], 'M j, Y');
				//
				echo '<figure class="gkItem'.($this->get_video($i) != '#' ? ' video' : '').($i < $this->parent->config['portal_mode_video_list_per_page'] ? ' active' : '').'">';
					echo '<span class="gkImageWrap"><img src="'.$this->get_image($i).'" alt="'.htmlspecialchars(strip_tags($this->parent->content[$i]['title'])).'" data-url="'.$this->get_video($i).'" data-x="'.$this->parent->config['portal_mode_video_list_popup_x'].'" data-y="'.$this->parent->config['portal_mode_video_list_popup_y'].'" /></span>';
					
					echo '<figcaption>';
						if($this->parent->config['portal_mode_video_list_title'] == 1) {
							echo '<h3><a href="'.$this->get_link($i).'" title="'.htmlspecialchars(strip_tags($this->parent->content[$i]['title'])).'">'.$title.'</a></h3>';
						}
						
						if($this->parent->config['portal_mode_video_list_date'] == 1) {
							echo '<strong>' . $info_date . '</strong>';
						}
					echo '</figcaption>';
				echo '</figure>';
				
				if(($i > 0 && (($i+1) % $this->parent->config['portal_mode_video_list_per_page'] == 0) && $i != count($this->parent->content) - 1) || ($this->parent->config['portal_mode_video_list_per_page'] == 1 && $i != count($this->parent->content) - 1)) {
					echo '</div>';
					echo '<div class="gkItemsPage" data-cols="'.$this->parent->config['portal_mode_video_list_per_page'].'">';
				} elseif($i == count($this->parent->content) - 1) {
					echo '</div>';
				}
			}
			echo '</div>';
			
			// pagination
			if(count($this->parent->content) > $this->parent->config['portal_mode_video_list_per_page']) {
				echo '<div class="gkBottomNav">';
				echo '<a href="#" class="gkBottomNavPrev">&laquo;</a>';
				
				echo '<ul class="gkBottomNavPagination">';
				for($i = 0; $i < ceil(count($this->parent->content) / $this->parent->config['portal_mode_video_list_per_page']); $i++) {
					echo '<li'.($i == 0 ? ' class="active"': '').'>'.($i+1).'</li>';
				}
				echo '</ul>';
				
				echo '<a href="#" class="gkBottomNavNext">&raquo;</a>';
				echo '</div>';
			}
			
			if(trim($this->parent->config['nsp_post_text'])) {
				echo $this->parent->config['nsp_post_text'];
			}
			
			echo '</div>';
		} else {
			echo '<strong>Error:</strong> No articles to display';
		}
	}
	// function used to retrieve all items videos
	function get_videos() {
		if($this->mode == 'com_k2') {
			// prepare the array with IDs
			$IDs = array();
			
			for($i = 0; $i < count($this->parent->content); $i++) {
				array_push($IDs, $this->parent->content[$i]['id']);
			}
			
			$IDs = implode(',', $IDs);
			// use the array in the query
			$db = JFactory::getDBO();
			$query_videos = '
				SELECT 
					c.id AS id,
					c.video AS video
				FROM 
					#__k2_items AS c
				WHERE 
					c.id IN ('.$IDs.')
		        ';	
			// Executing SQL Query
			$db->setQuery($query_videos);
	
			// check if some categories was detected
			$results = array();
			if($videos = $db->loadObjectList()) {
				// prepare the results
				foreach($videos as $v) {
					$vid = $v->video;
					$vid = JHTML::_('content.prepare', $vid);
					if(trim($vid) != '') {
						$vid_matches = array();
						preg_match('@src="(.*?)"@mi', $vid, $vid_matches);
						if(count($vid_matches) >= 1) {
							$vid = $vid_matches[1];
						} else {
							$vid = '#';
						}
					} else {
						$vid = '#';
					}
					$results[$v->id] = $vid;
				}
			}
			// return the results
			return $results;
		} else {
			return false;
		}
	}
	// function used to retrieve the item video
	function get_video($num) {
		if($this->mode == 'com_k2' && $this->parent->config['portal_mode_video_list_popup'] == 1) {
			if($this->videos[$this->parent->content[$num]['id']] != '#') {
				return str_replace('&', '&amp;', $this->videos[$this->parent->content[$num]['id']]);
			} else {
				$video_matches = array();
				preg_match('@<iframe.*?src="(.*?)".*?</iframe>@mis', $this->parent->content[$num]['text'], $video_matches);
				
				if(count($video_matches)) {
					return $video_matches[1];
				}
			}
		}
		
		return '#';
	}
	// function used to retrieve the item URL
	function get_link($num) {
		if($this->mode == 'com_content') {
			// load necessary com_content View class
			if(!class_exists('NSP_GK5_com_content_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_content/view'));
			}
			return NSP_GK5_com_content_View::itemLink($this->parent->content[$num], $this->parent->config);
		} else if($this->mode == 'com_k2') {
			// load necessary k2 View class
			if(!class_exists('NSP_GK5_com_k2_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_k2/view'));
			}
			return NSP_GK5_com_k2_View::itemLink($this->parent->content[$num], $this->parent->config);
		} else {
			return false;
		}
	}
	// image generator
	function get_image($num) {		
		// used variables
		$url = false;
		$output = '';
		// select the proper image function
		if($this->mode == 'com_content') {
			// load necessary com_content View class
			if(!class_exists('NSP_GK5_com_content_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_content/view'));
			}
			// generate the com_content image URL only
			$url = NSP_GK5_com_content_View::image($this->parent->config, $this->parent->content[$num], true, true);
		} else if($this->mode == 'com_k2') {
			// load necessary k2 View class
			if(!class_exists('NSP_GK5_com_k2_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_k2/view'));
			}
			// generate the K2 image URL only
			$url = NSP_GK5_com_k2_View::image($this->parent->config, $this->parent->content[$num], true, true);
		}
		// check if the URL exists
		if($url === FALSE) {
			return false;
		} else {
			// if URL isn't blank - return it!
			if($url != '') {
				return $url;
			} else {
				return false;
			}
		}
	}
	// function to generate blank transparent PNG images
	public function generateBlankImage($width, $height){ 
		$image = imagecreatetruecolor($width, $height);
		imagesavealpha($image, true);
		$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefill($image, 0, 0, $transparent);
		// cache the output
		ob_start();
		imagepng($image);
		$img =  ob_get_contents();
		ob_end_clean();
		// return the string
		return base64_encode($img);
	}
}

// EOF
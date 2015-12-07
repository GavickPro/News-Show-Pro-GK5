<?php

/**
* JomSocial Photos Portal Mode
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.6.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

if(file_exists(JPATH_BASE . '/components/com_community/defines.community.php')) {

	class NSP_GK5_JomSocial_Photos {
		// necessary class fields
		private $parent;
		private $mode;
		// constructor
		function __construct($parent) {
		
			$this->parent = $parent;
			// detect the supported Data Sources
			if(
				$this->parent->config['data_source'] !== 'jomsocial_latest_photo' ||
				$this->parent->config['data_source'] !== 'jomsocial_user_photo'
			) {
				$this->mode = 'jomsocial';
			} else {
				$this->mode = false;
			}
		}
		// static function which returns amount of articles to render - VERY IMPORTANT!!
		static function amount_of_articles($parent) {
			return $parent->config['portal_mode_jomsocial_photos_total'];
		}
		// output generator	
		function output() {	
			// main wrapper
			echo '<div class="gkNspPM gkNspPM-JomSocialPhotos'.($this->parent->config['portal_mode_jomsocial_photos_animation'] == 1 ? ' animate' : '').'" data-cols="'.$this->parent->config['portal_mode_jomsocial_photos_amount'].'">';
			
			if(trim($this->parent->config['nsp_pre_text'])) {
				echo $this->parent->config['nsp_pre_text'];
			}
			
			$width = 3000;
			$new_width = $this->parent->config['portal_mode_jomsocial_photos_total'] * $this->parent->config['portal_mode_jomsocial_photos_width'];
			
			if($new_width > 3000) {
				$width = $new_width;
			}
			
			echo '<div style="width: '.$width.'px">';
			// generate indexes array like:
			// [6,4,2,0,1,3,5,7]
			// [6,4,2,0,1,3,5]
			$indexes = array();
			$amount = count($this->parent->content);
			
			if($amount % 2 == 0) {
				for($i = $amount - 2; $i > 0; $i -= 2) {
					array_push($indexes, $i);
				}
				
				array_push($indexes, 0);
				
				for($i = 1; $i < $amount; $i += 2) {
					array_push($indexes, $i);
				}
			} else {
				for($i = $amount - 1; $i > 0; $i -= 2) {
					array_push($indexes, $i);
				}
				
				array_push($indexes, 0);
				
				for($i = 1; $i < $amount; $i += 2) {
					array_push($indexes, $i);
				}
			}
			
			// render images
			for($i = 0; $i < count($this->parent->content); $i++) {	
				$id = $indexes[$i];
					
				$text = trim($this->parent->content[$id]['text']);
				// output the HTML code
				echo '<figure style="width: '.$this->parent->config['portal_mode_jomsocial_photos_width'].'px;">';
				if($this->get_image($id)) {
					echo '<img src="'.strip_tags($this->get_image($id)).'" alt="'.strip_tags(htmlspecialchars($this->parent->content[$id]['text'])).'" />';
				}
				echo '<figcaption>';
				echo '<small>'.$this->parent->content[$id]['author_username'].'</small>';
				echo '<p>'.$text.'</p>';
				echo '<a href="'.$this->get_link($id).'">';
				echo JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_JOMSOCIAL_PHOTOS_MORE');
				echo '</a>';
				echo '</figcaption>';
				echo '</figure>';
			}
			// closing main wrappers
			echo '</div>';
			
			if(trim($this->parent->config['nsp_post_text'])) {
				echo $this->parent->config['nsp_post_text'];
			}
			
			echo '</div>';
		}
		// function used to retrieve the item URL
		function get_link($num) {
			if($this->mode == 'jomsocial') {
				// load necessary com_content View class
				if(!class_exists('NSP_GK5_jomsocial_View')) {
					require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'jomsocial/view'));
				}
				// generate the com_content image URL only
				return NSP_GK5_jomsocial_View::itemLink($this->parent->content[$num]);
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
			if($this->mode == 'jomsocial') {
				// load necessary com_content View class
				if(!class_exists('NSP_GK5_jomsocial_View')) {
					require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'jomsocial/view'));
				}
				// generate the com_content image URL only
				$url = NSP_GK5_jomsocial_View::image($this->parent->config, $this->parent->content[$num], true, true);
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
	}
} else {
	echo '<p style="text-align: center; padding: 30px 0;">Please install JomSocial in order to use this Portal Mode</p>';
	
	class NSP_GK5_JomSocial_Photos {
		// constructor
		function __construct($parent) {
		}
		// static function which returns amount of articles to render - VERY IMPORTANT!!
		static function amount_of_articles($parent) {
			return 0;
		}
		// output generator	
		function output() {	
			
		}
	}
}

// EOF
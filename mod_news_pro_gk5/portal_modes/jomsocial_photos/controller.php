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
		echo '<div class="gkNspPM gkNspPM-JomSocialPhotos" data-cols="'.$this->parent->config['portal_mode_jomsocial_photos_amount'].'">';
		
		$width = 3000;
		$new_width = $this->parent->config['portal_mode_jomsocial_photos_total'] * $this->parent->config['portal_mode_jomsocial_photos_width'];
		
		if($new_width > 3000) {
			$width = $new_width;
		}
		
		echo '<div style="width: '.$width.'px">';
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			$text = trim($this->parent->content[$i]['text']);
			// output the HTML code
			echo '<figure style="width: '.$this->parent->config['portal_mode_jomsocial_photos_width'].'px;">';
			if($this->get_image($i)) {
				echo '<img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags(htmlspecialchars($this->parent->content[$i]['text'])).'" />';
			}
			echo '<figcaption>';
			echo '<small>'.$this->parent->content[$i]['author_username'].'</small>';
			echo '<p>'.$text.'</p>';
			echo '<a href="'.$this->get_link($i).'">';
			echo JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_JOMSOCIAL_PHOTOS_MORE');
			echo '</a>';
			echo '</figcaption>';
			echo '</figure>';
		}
		// closing main wrappers
		echo '</div>';
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

// EOF
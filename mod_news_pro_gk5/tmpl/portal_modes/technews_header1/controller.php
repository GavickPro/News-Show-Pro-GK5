<?php

/**
* TechNews Header I
* @package News Show Pro GK5
* @Copyright (C) 2009-2015 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.9.1 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_TechNews_Header1 {
	// necessary class fields
	private $parent;
	private $mode;
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
	}
	// static function which returns amount of articles to render - VERY IMPORTANT!!
	static function amount_of_articles($parent) {
		return $parent->config['portal_mode_technews_header1_cols'];
	}
	// output generator	
	function output() {
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-TechNewsHeader1" id="'.$this->parent->config['module_id'].'" data-cols="'.$this->parent->config['portal_mode_technews_header1_cols'].'">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		// images wrapper
		echo '<div class="gkImagesWrapper">';
		// render images
		$j = 0;
		for($i = 0; $i < count($this->parent->content); $i++) {			
			if($this->get_image($i)) {
				echo '<div class="gkImage'.(($j == 0) ? ' active' : '').'">';
				echo '	<div class="gkImageArea" style="background-image: url(\''.strip_tags($this->get_image($i)).'\');"></div>';
				echo '	<p class="gkTitle">'.strip_tags($this->parent->content[$i]['title']).'</p>';
				echo '	<a href="'.strip_tags($this->get_link($i)).'"><i class="micon">arrow_forward</i></a>';
				echo '</div>';
				$j = 1;
			}		
		}
		// blank image for the proper proportions
		echo '	<div class="gkBlankImage">';
		echo '		<img src="data:image/png;base64,'.$this->generateBlankImage($this->parent->config['img_width'], $this->parent->config['img_height']).'" alt="" />';
		echo '	</div>';
		// closing images wrapper
		echo '</div>';
		
		if(trim($this->parent->config['nsp_post_text'])) {
			echo $this->parent->config['nsp_post_text'];
		}
		// closing main wrapper
		echo '</div>';
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
				if(stripos($url, 'http://') === FALSE && stripos($url, 'https://') === FALSE) {
					$uri = JUri::getInstance();
					return $uri->root() . $url;
				}
				
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

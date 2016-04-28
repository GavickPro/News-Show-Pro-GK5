<?php

/**
* New News Header
* @package News Show Pro GK5
* @Copyright (C) 2009-2015 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.9.2 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_New_News_Slider {
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
		return $parent->config['portal_mode_new_news_slider_amount'];
	}
	// output generator	
	function output() {
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-NewNewsSlider" id="'.$this->parent->config['module_id'].'">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		// images wrapper
		echo '<div class="gkListWrapper" data-arrows="'.$this->parent->config['portal_mode_new_news_slider_arrows'].'">';
		echo '<div class="gkList" data-interval="'.$this->parent->config['portal_mode_new_news_slider_interval'].'">';
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			echo '<div class="gkItem">';
			echo '<div class="gkItemWrap">';
			
			if($this->parent->config['portal_mode_new_news_slider_images'] == '1') {
				echo '<a href="'.strip_tags($this->get_link($i)).'" class="gkImageWrapper">';
				echo '<img class="gkImage" src="'.$this->get_image($i).'" alt="" width="'.$this->parent->config['img_width'].'" height="'.$this->parent->config['img_height'].'" />';
				echo '</a>';
			}
			
			echo '<h2 class="gkTitle"><a href="'.strip_tags($this->get_link($i)).'">'.strip_tags($this->parent->content[$i]['title']).'</a></h2>';
			echo '</div>';
			echo '</div>';	
		}
		// closing images wrapper
		echo '</div>';
		echo '</div>';
		
		if($this->parent->config['portal_mode_new_news_slider_arrows'] == '1') {
			echo '<a href="#" class="gkPrev"><i class="fa fa-chevron-left"></i></a>';
			echo '<a href="#" class="gkNext"><i class="fa fa-chevron-right"></i></a>';
		}
		
		if(trim($this->parent->config['nsp_post_text'])) {
			echo $this->parent->config['nsp_post_text'];
		}
		// closing main wrapper
		echo '</div>';
		
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration('
			#'.$this->parent->config['module_id'].' .gkItem { width: '.$this->parent->config['portal_mode_new_news_slider_item_width'].'px; }
			@media (max-width: 1240px) {#'.$this->parent->config['module_id'].' .gkItem { width: '.$this->parent->config['portal_mode_new_news_slider_item_width_small_desktop'].'px; }}
			@media (max-width: 960px) {#'.$this->parent->config['module_id'].' .gkItem { width: '.$this->parent->config['portal_mode_new_news_slider_item_width_tablet'].'px; }}
			@media (max-width: 600px) {#'.$this->parent->config['module_id'].' .gkItem { width: '.$this->parent->config['portal_mode_new_news_slider_item_width_mobile'].'px; }}
		');
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
}

// EOF

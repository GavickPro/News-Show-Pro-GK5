<?php

/**
* News Blocks Portal Mode
* @package News Show Pro GK5
* @Copyright (C) 2009-2015 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.8.1 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_News_Slider {
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
		return $parent->config['portal_mode_news_slider_amount'];
	}
	// output generator	
	function output() {	
		if(count($this->parent->content) < 5) {
			echo 'This module needs at least 5 articles to display.';
			return;
		}
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-NewsSlider" data-autoanim="'.$this->parent->config['portal_mode_news_slider_autoanimation_time'].'" style="min-height: '.intval($this->parent->config['img_height'] + 200.0).'px;">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		if($this->parent->config['portal_mode_news_slider_label'] != '') {
			echo '<h2>' . $this->parent->config['portal_mode_news_slider_label'] . '</h2>';
		}
		
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			$title = $this->parent->content[$i]['title'];
			$text = $this->parent->content[$i]['text'];
			$element_classname = '';
			
			switch($i) {
				
				case 0:
					$element_classname = ' class="gk-prev-2"';
					break;
				case 1: 
					$element_classname = ' class="gk-prev-1"';
					break;
				case 2:
					$element_classname = ' class="gk-active"';
					break;
				case 3:
					$element_classname = ' class="gk-next-1"';
					break;
				case 4:
					$element_classname = ' class="gk-next-2"';
					break;
				case 5:
					$element_classname = ' class="gk-to-hide"';
					break;
				case 6:
					$element_classname = ' class="gk-to-show"';
					break;
				default:
					$element_classname = ' class="gk-hide"';
					break;
			}
			
			$element_sr = '';
			
			if($i == 0) {
				$element_sr = ' data-sr="enter right and move 200px and wait .6s"';
			}
			
			if($i == 1) {
				$element_sr = ' data-sr="enter right and move 100px and wait .4s"';
			}
			
			if($i == 2) {
				$element_sr = ' data-sr="enter bottom and move 100px"';
			}
			
			if($i == 3) {
				$element_sr = ' data-sr="enter left and move 100px and wait .4s"';
			}
			
			if($i == 4) {
				$element_sr = ' data-sr="enter left and move 200px and wait .6s"';
			}
			
			// output the HTML code
			echo '<figure'.$element_classname.$element_sr.' data-cat="'.$this->get_category_link($i).'">';
			
			if($this->get_image($i)) {
				echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'" class="gk-image-wrap">';
				echo '<img src="'.strip_tags($this->get_image($i)).'" style="margin: '.$this->parent->config['portal_mode_news_slider_image_margin'].';" alt="'.strip_tags($this->parent->content[$i]['title']).'" />';
				echo '</a>';
			}
			
			echo '<figcaption>';
			
			if($this->parent->config['portal_mode_news_slider_date_format'] != '') {
				echo '<small>' . JHTML::_('date', $this->parent->content[$i]['date'], $this->parent->config['portal_mode_news_slider_date_format']) . '</small>';
			}
			
			echo '<h3>';
			echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'">';
			echo $title;
			echo '</a>';
			echo '</h3>';
			echo '<p>' . NSP_GK5_Utils::cutText($text, $this->parent->config, 'news_limit') . '</p>';
			echo '</figcaption>';
			echo '</figure>';
		}
		
		$to_hide_link = '';
		
		if($this->parent->config['portal_mode_news_slider_category_label'] == '') {
			$to_hide_link = ' style="display: none;"';
		}
		
		echo '<a href="#" class="gk-data-category-link"'.$to_hide_link.'>'.$this->parent->config['portal_mode_news_slider_category_label'].'</a>';
		
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
	// function used to retrieve the category item URL
	function get_category_link($num) {
		if($this->mode == 'com_content') {
			return JRoute::_(ContentHelperRoute::getCategoryRoute($this->parent->content[$num]['cid']));
		} else if($this->mode == 'com_k2') {
			//
			require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
			//
			return urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($this->parent->content[$num]['cid'].':'.urlencode($this->parent->content[$num]['cat_alias']))));
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
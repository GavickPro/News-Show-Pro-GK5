<?php

/**
* News Blocks Portal Mode
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Bikestore_Slider {
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
		} else if(stripos($this->parent->config['data_source'], 'com_virtuemart_') !== FALSE) { 
			$this->mode = 'com_virtuemart';
		} else {
			$this->mode = false;
		}
	}
	// static function which returns amount of articles to render - VERY IMPORTANT!!
	static function amount_of_articles($parent) {
		return $parent->config['portal_mode_bikestore_slider_amount'];
	}
	// output generator	
	function output() {	
		$animation_speed = ' data-speed="' . $this->parent->config['portal_mode_bikestore_slider_speed'] . '"';
		$auto_animation = $this->parent->config['portal_mode_bikestore_slider_autoanim'] == '1' ? ' data-autoanim="true"': '';
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-BikestoreSlider"'.$auto_animation.$animation_speed.'>';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		echo '<div class="nspImages">';
		echo '<div class="nspArts">';
		echo '<div class="nspArtsScroll">';
		
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			$title = trim($this->parent->content[$i]['title']);
			// output the HTML code
			if($this->get_image($i)) {
				echo '<div class="nspArt" style="padding: '. $this->parent->config['img_margin'] .';width: '. ($this->parent->config['img_width'] + 2) . 'px;">';
				echo '<div style="width: '. ($this->parent->config['img_width'] + 2) .'px;">';
				echo '<img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags($title).'" />';
				echo '</div>';
				echo '<div class="nspHeadline"><h4 class="nspHeader tcenter fnone"><a href="'.$this->get_link($i).'">' . $title . '</a></h4></div>';
				echo '</div>';
			}
		}
		// closing main wrapper
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '<a class="nspPrev">'. JText::_('MOD_NEWS_PRO_GK5_NSP_PREV') .'</a>';
		echo '<a class="nspNext">'. JText::_('MOD_NEWS_PRO_GK5_NSP_NEXT') .'</a>';
		
		if(trim($this->parent->config['nsp_post_text'])) {
			echo $this->parent->config['nsp_post_text'];
		}
		
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
		} else if($this->mode == 'com_virtuemart') {
			$itemid = $this->parent->config['vm_itemid'];
			$link = 'index.php?option=com_virtuemart&amp;view=productdetails&amp;virtuemart_product_id='.$this->parent->content[$num]['id'].'&amp;virtuemart_category_id='.$this->parent->content[$num]['cid'].'&amp;Itemid='.$itemid;
			//
			return $link;
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
		} else if($this->mode == 'com_virtuemart') {
			// load necessary EasyBlog View class
			if(!class_exists('NSP_GK5_com_virtuemart_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_virtuemart/view'));
			}
			// generate the VirtueMart image URL only
			$url = NSP_GK5_com_virtuemart_View::image($this->parent->config, $this->parent->content[$num], true, true);
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
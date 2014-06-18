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

class NSP_GK5_News_Slideshow {
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
		} else if(stripos($this->parent->config['data_source'], 'easyblog_') !== FALSE) { 
			$this->mode = 'com_easyblog';
		} else {
			$this->mode = false;
		}
	}
	// static function which returns amount of articles to render - VERY IMPORTANT!!
	static function amount_of_articles($parent) {
		return $parent->config['portal_mode_news_slideshow_amount'];
	}
	// output generator	
	function output() {	
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-NewsSlideshow"' . 'data-autoanim="'.$this->parent->config['portal_mode_news_slideshow_autoanimation'] . '"  data-autoanim-time="'.$this->parent->config['portal_mode_news_slideshow_animation_interval'] .'"  data-autoanim-speed="'.$this->parent->config['portal_mode_news_slideshow_animation_speed'].'">';
		// render images
		echo '<div class="nspImages">';
		echo '<div class="nspArts">';
			echo '<div class="gkImagesWrapper">';
			// render images
			for($i = 0; $i < count($this->parent->content); $i++) {			
				if($this->get_image($i)) {
					echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'" class="gkImage">';
					echo '<img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags($this->parent->content[$i]['title']).'" />';
					echo '</a>';
				}		
			}
			echo '</div>';
		// closing images wrapper
		echo '</div>';
		echo '</div>';

		echo '<div class="nspBotInterface">';
			echo '<a href="#prev" class="gkPrevBtn">'.JText::_('MOD_NEWS_PRO_GK5_NSP_PREV').'</a>';
			echo '<div class="nspTextBlock">';
			echo '<div class="nspTextWrap">';
				for($i = 0; $i < count($this->parent->content); $i++) {
					echo '<div class="nspArtHeadline">';
					echo '<div class="nspInfo">'.JHTML::_('date', $this->parent->content[$i]['date'], 'j-m-Y').'</div>';
					echo '<h4><a href="'.$this->get_link($i).'" title="'.htmlspecialchars(strip_tags($this->parent->content[$i]['title'])).'">'.NSP_GK5_Utils::cutText(htmlspecialchars($this->parent->content[$i]['title']), $this->parent->config, 'portal_mode_highlights_title_limit', '&hellip;').'</a></h4>';
					echo '</div>';
				}
			echo '</div>';
			echo '</div>';
		//closing interface wrapper 
		echo '<a href="#next" class="gkNextBtn">'.JText::_('MOD_NEWS_PRO_GK5_NSP_NEXT').'</a>';
		echo '</div>';
		// closing main wrapper
		echo '</div>';
	}
	// function used to retrieve the item URL
	function get_link($num) {
		if($this->mode == 'com_content') {
			return ($this->parent->content[$num]['id'] != 0) ? JRoute::_(ContentHelperRoute::getArticleRoute($this->parent->content[$num]['id'], $this->parent->content[$num]['cid'])) : JRoute::_('index.php?option=com_users&view=login');
		} else if($this->mode == 'com_k2') {
			//
			require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
			//
			return urldecode(JRoute::_(K2HelperRoute::getItemRoute($this->parent->content[$num]['id'].':'.urlencode($this->parent->content[$num]['alias']), $this->parent->content[$num]['cid'].':'.urlencode($this->parent->content[$num]['cat_alias']))));
		} else if($this->mode == 'com_easyblog') {
			//
			require_once (JPATH_SITE.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'router.php');
			//
			return urldecode(JRoute::_(EasyBlogRouter::getEntryRoute($this->parent->content[$num]['id'])));
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
			// generate the EasyBlog image URL only
			$url = NSP_GK5_com_k2_View::image($this->parent->config, $this->parent->content[$num], true, true);
		} else if($this->mode == 'com_easyblog') {
			// load necessary EasyBlog View class
			if(!class_exists('NSP_GK5_com_easyblog_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_easyblog/view'));
			}
			// generate the EasyBlog image URL only
			$url = NSP_GK5_com_easyblog_View::image($this->parent->config, $this->parent->content[$num], true, true);
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
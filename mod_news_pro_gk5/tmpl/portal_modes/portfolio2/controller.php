<?php

/**
* Portfolio2 Portal Mode
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Portfolio2 {
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
		return $parent->config['portal_mode_portfolio2_cols'] * $parent->config['portal_mode_portfolio2_rows'];
	}
	// output generator	
	function output() {
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-Portfolio2" data-cols="'.$this->parent->config['portal_mode_portfolio2_cols'].'" data-popup="'.$this->parent->config['portal_mode_portfolio2_popup'].'" data-rows="'.$this->parent->config['portal_mode_portfolio2_rows'].'">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		if($this->parent->config['portal_mode_portfolio2_filter'] == 1) {
			$categories = array();
			
			echo '<ul class="gkPortfolioCategories">';
				echo '<li class="active">' . JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_PORTFOLIO2_ALL') . '</li>';
				for($i = 0; $i < count($this->parent->content); $i++) {
					if(!in_array($this->parent->content[$i]['catname'], $categories)) {
						echo '<li>'.$this->parent->content[$i]['catname'].'</li>';
						array_push($categories, $this->parent->content[$i]['catname']);
					}
				}
			echo '</ul>';
		}
		
		// images wrapper
		echo '<div class="gkImagesWrapper gkImagesCols'.$this->parent->config['portal_mode_portfolio2_cols'].'">';
		// render images		
		for($i = 0; $i < count($this->parent->content); $i++) {			
			if($this->get_image($i)) {
				echo '<a 
						href="'.$this->get_link($i).'" 
						title="'.strip_tags($this->parent->content[$i]['title']).'" 
						class="gkImage nspArt nspCol'.$this->parent->config['portal_mode_portfolio2_cols'].' active" 
						data-cat="'.$this->parent->content[$i]['catname'].'" ';
					
			 	if($this->parent->config['portal_mode_portfolio2_popup'] == '1') {	
					echo   'data-cat-text="'.JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_PORTFOLIO2_CAT_TEXT').'"
							data-date="'.JHTML::_('date', $this->parent->content[$i]['date'], 'F j, Y').'"
							data-date-text="'.JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_PORTFOLIO2_DATE_TEXT').'"
							data-author="'.$this->parent->content[$i]['author_username'].'"
							data-author-text="'.JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_PORTFOLIO2_AUTHOR_TEXT').'"
							data-img="'.strip_tags($this->get_image($i)).'"';
				}
				
				echo '>';
				echo '<img src="'.strip_tags($this->get_image($i, true)).'" alt="'.strip_tags($this->parent->content[$i]['title']).'" />';
				echo '</a>';
			}		
		}
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
		} else if($this->mode == 'com_easyblog') {
			return urldecode(JRoute::_('index.php?option=com_easyblog&view=entry&id=' . $this->parent->content[$num]['id']));
		} else {
			return false;
		}
	}
	// image generator
	function get_image($num, $small = false) {		
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
			$url = NSP_GK5_com_content_View::image($this->parent->config, $this->parent->content[$num], true, true, $small);
		} else if($this->mode == 'com_k2') {
			// load necessary k2 View class
			if(!class_exists('NSP_GK5_com_k2_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_k2/view'));
			}
			// generate the K2 image URL only
			$url = NSP_GK5_com_k2_View::image($this->parent->config, $this->parent->content[$num], true, true, $small);
			
		} else if($this->mode == 'com_easyblog') {
			// load necessary EasyBlog View class
			if(!class_exists('NSP_GK5_com_easyblog_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_easyblog/view'));
			}
			// generate the EasyBlog image URL only
			$url = NSP_GK5_com_easyblog_View::image($this->parent->config, $this->parent->content[$num], true, true, $small);
		}
		// check if the URL exists
		if($url === FALSE) {
			return false;
		} else {
			// if URL isn't blank - return it!
			if($url != '') {
				if(stripos($url, JURI::base()) === FALSE) {
					$url = JURI::base() . $url;
				}
				
				return $url;
			} else {
				return false;
			}
		}
	}
}

// EOF

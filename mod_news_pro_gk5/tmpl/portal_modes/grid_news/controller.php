<?php

/**
* Grid News Portal Mode
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.6.2 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Grid_News {
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
		return $parent->config['portal_mode_grid_news_cols'] * $parent->config['portal_mode_grid_news_rows'];
	}
	// output generator	
	function output() {	
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-GridNews" data-cols="'.$this->parent->config['portal_mode_grid_news_cols'].'">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			$this->parent->content[$i]['title'] = explode('__', $this->parent->content[$i]['title']);
			$this->parent->content[$i]['title'] = $this->parent->content[$i]['title'][0];
			// calculate the inverse class
			$inverse_class = '';
			$rows = $this->parent->config['portal_mode_grid_news_cols'] * 2;
			//
			if($i % $rows >= $this->parent->config['portal_mode_grid_news_cols']) {
				$inverse_class = ' class="inverse"';
			}
			// output the HTML code
			echo '<figure'.$inverse_class.'>';
			if($this->get_image($i)) {
				echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'">';
				echo '<img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags($this->parent->content[$i]['title']).'" />';
				echo '</a>';
			}
			echo '<figcaption>';
			echo '<div>';
			// Title
			if($this->parent->config['portal_mode_grid_news_title_length'] > 0) {
				echo '<h3>';
				echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'">';
				$content = NSP_GK5_Utils::cutText(strip_tags($this->parent->content[$i]['title']), $this->parent->config, 'portal_mode_grid_news_title_length', '&hellip;');
				$content = NSP_GK5_View::textPlugins($content, $config);
				echo $content;
				echo '</a>';
				echo '</h3>';
			}
			// Date
			if($this->parent->config['portal_mode_grid_news_date_format'] != '') {
				echo '<time datetime="'.JHTML::_('date', $this->parent->content[$i]['date'], DATE_W3C).'">'.JHTML::_('date', $this->parent->content[$i]['date'], $this->parent->config['portal_mode_grid_news_date_format']).'</time>';
			}
			// Separator under the title/date
			if(
				$this->parent->config['portal_mode_grid_news_title_length'] > 0 || 
				$this->parent->config['portal_mode_grid_news_date_format'] != ''
			) {
				echo '<hr class="separator" />';
			}
			// Text
			if($this->parent->config['portal_mode_grid_news_text_length'] > 0) {
				echo '<p>'.NSP_GK5_Utils::cutText(strip_tags($this->parent->content[$i]['text']), $this->parent->config, 'portal_mode_grid_news_text_length', '&hellip;').'</p>';
			}
			echo '</div>';
			echo '</figcaption>';
			echo '</figure>';
		}
		// Link at the end
		if($this->parent->config['portal_mode_grid_news_url'] != '') {
			echo '<a href="'.$this->parent->config['portal_mode_grid_news_url'].'">'.$this->parent->config['portal_mode_grid_news_link_text'].'</a>';
		}
		
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
			// generate the EasyBlog image URL only
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

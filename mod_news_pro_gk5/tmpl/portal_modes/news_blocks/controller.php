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

class NSP_GK5_News_Blocks {
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
		} else if(stripos($this->parent->config['data_source'], 'easyblog_') !== FALSE) { 
			$this->mode = 'com_easyblog';
		} else {
			$this->mode = false;
		}
	}
	// static function which returns amount of articles to render - VERY IMPORTANT!!
	static function amount_of_articles($parent) {
		return $parent->config['portal_mode_news_blocks_cols'] * $parent->config['portal_mode_news_blocks_rows'];
	}
	// output generator	
	function output() {	
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-NewsBlocks" data-cols="'.$this->parent->config['portal_mode_news_blocks_cols'].'">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			$title_parts = explode(' ', trim($this->parent->content[$i]['title']));
			$title_part_one = '';
			$title_part_two = '';
			//
			if(count($title_parts) > 0) {
				$title_part_one = $title_parts[0];
				$title_parts[0] = '';
				$title_part_two = implode(' ', $title_parts);
			}
			// calculate the inverse class
			$inverse_class = '';
			$row = floor($i / $this->parent->config['portal_mode_news_blocks_cols']) + 1;
			$offset = 0;
			//
			if($row % 2 == 0) {
				$offset = 1;
			}
			//
			if(($i % $this->parent->config['portal_mode_news_blocks_cols']) % 2 == $offset) {
				$inverse_class = ' class="inverse"';
			}
			// output the HTML code
			echo '<figure'.$inverse_class.'>';
			if($this->get_image($i)) {
				echo '<img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags($this->parent->content[$i]['title']).'" />';
			}
			echo '<figcaption>';
			echo '<h3><strong>'.$title_part_one.'</strong>'.$title_part_two.'</h3>';
			echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'">';
			echo JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_NEWS_BLOCKS_MORE');
			echo '</a>';
			echo '</figcaption>';
			echo '</figure>';
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
		} else if($this->mode == 'com_easyblog') {
			return urldecode(JRoute::_('index.php?option=com_easyblog&view=entry&id=' . $this->parent->content[$num]['id']));
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
		} else if($this->mode == 'com_easyblog') {
			// load necessary EasyBlog View class
			if(!class_exists('NSP_GK5_com_easyblog_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_easyblog/view'));
			}
			// generate the EasyBlog image URL only
			
			$url = NSP_GK5_com_easyblog_View::image($this->parent->config, $this->parent->content[$num], true, true);
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
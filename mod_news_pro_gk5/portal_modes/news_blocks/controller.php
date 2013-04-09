<?php

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
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

class NSP_GK5_Portfolio_Grid {
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
		return $parent->config['portal_mode_portfolio_grid_amount'];
	}
	// output generator	
	function output() {	
		// main wrapper
		echo '<div 
		class="gkNspPM gkNspPM-PortfolioGrid'.($this->parent->config['portal_mode_portfolio_grid_overlay'] ? ' with-overlay' : ' without-overlay').'" 
		data-cols="'.$this->parent->config['portal_mode_portfolio_grid_cols'].'"
		data-small-desktop-cols="'.$this->parent->config['portal_mode_portfolio_grid_cols_small_desktop'].'"
		data-tablet-cols="'.$this->parent->config['portal_mode_portfolio_grid_cols_tablet'].'"
		data-small-tablet-cols="'.$this->parent->config['portal_mode_portfolio_grid_cols_small_tablet'].'"
		data-mobile-cols="'.$this->parent->config['portal_mode_portfolio_grid_cols_mobile'].'"
		>';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			if($this->get_image($i)) {
				// output the HTML code
				echo '<div class="figure loading">';
					if($this->parent->config['portal_mode_portfolio_grid_overlay'] == '0') {
						echo '<div>';
					}
					
					$data_popup = 'false';
					$data_url = $this->get_link($i);
						
					if($this->parent->config['portal_mode_portfolio_grid_popup'] != 0) {
						$data_url = $this->get_image($i, true);
						
						if(stripos($data_url, JUri::base()) === FALSE) {
							$data_url = JUri::base() . '/' . $data_url;
						}
						
						$data_popup= 'true';
					}
					
					$data_attrs = ' data-popup="'.$data_popup.'" data-popup-url="'.$data_url.'"';
					$figcaption_data_attrs = $data_attrs;
					$link_data_attrs = '';
					
					if($this->parent->config['portal_mode_portfolio_grid_overlay'] == '0') {
						$figcaption_data_attrs = '';
						$link_data_attrs = $data_attrs;	
					}
					
					$img_url = $this->get_image($i);
					
					if(stripos($img_url, JUri::base()) === FALSE) {
						$img_url = JUri::base() . '/' . $img_url;
					}
					
					echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'" data-url="'.strip_tags($img_url).'" class="image-resource"'.$link_data_attrs.'><img class="helper-image" src="data:image/png;base64,'.$this->generateBlankImage($this->parent->config).'" alt="" />
						</a>';
					
					echo '<div class="figcaption" '.$figcaption_data_attrs.'>';
					echo '<div>';
					// Title
					if($this->parent->config['portal_mode_portfolio_grid_title_length'] > 0) {
						echo '<h3>';
						
						if($this->parent->config['portal_mode_portfolio_grid_title_link'] != 0) {
							echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'">';
						}
						
						echo NSP_GK5_Utils::cutText(strip_tags($this->parent->content[$i]['title']), $this->parent->config, 'portal_mode_portfolio_grid_title_length', '&hellip;');
						
						if($this->parent->config['portal_mode_portfolio_grid_title_link'] != 0) {
							echo '</a>';
						}
						
						echo '</h3>';
					}
					// Date & Author
					if(
						$this->parent->config['portal_mode_portfolio_grid_date_format'] != '' &&
						$this->parent->config['portal_mode_portfolio_grid_author'] != 0
					) {
						echo '<span>';
						// Date
						if($this->parent->config['portal_mode_portfolio_grid_date_format'] != '') {
							echo '<time datetime="'.JHTML::_('date', $this->parent->content[$i]['date'], DATE_W3C).'">'.JHTML::_('date', $this->parent->content[$i]['date'], $this->parent->config['portal_mode_portfolio_grid_date_format']).'</time>';
						}
						// Author
						if($this->parent->config['portal_mode_portfolio_grid_author'] != 0) {
							echo '<span>'.$this->parent->content[$i]['author_username'].'</span>';
						}
						echo '</span>';
					}
					echo '</div>';
					echo '</div>';
					
					if($this->parent->config['portal_mode_portfolio_grid_overlay'] == '0') {
						echo '</div>';
					}
				echo '</div>';
			}
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
	function get_image($num, $original = false) {		
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
			if(!$original) {
				$url = NSP_GK5_com_content_View::image($this->parent->config, $this->parent->content[$num], true, true);
			} else {
				$url = NSP_GK5_com_content_View::originalImage($this->parent->config, $this->parent->content[$num]);
			}
		} else if($this->mode == 'com_k2') {
			// load necessary k2 View class
			if(!class_exists('NSP_GK5_com_k2_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_k2/view'));
			}
			// generate the K2 image URL only
			if(!$original) {
				$url = NSP_GK5_com_k2_View::image($this->parent->config, $this->parent->content[$num], true, true);
			} else {
				$url = NSP_GK5_com_k2_View::originalImage($this->parent->config, $this->parent->content[$num]);
			}
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
	// function to generate blank transparent PNG images
	public function generateBlankImage($config){ 
		$image = imagecreatetruecolor($config['img_width'], $config['img_height']);
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

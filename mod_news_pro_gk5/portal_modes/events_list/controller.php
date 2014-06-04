<?php

/**
* Events List Portal Mode
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.6.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Events_List {
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
		return $parent->config['portal_mode_events_list_cols'] * $parent->config['portal_mode_events_list_rows'];
	}
	// output generator	
	function output() {	
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-EventsList" data-cols="'.$this->parent->config['portal_mode_events_list_cols'].'">';
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			$title = $this->parent->content[$i]['title'];
			$text = $this->parent->content[$i]['text'];
			// parse event data
			$event_info = array(
				"date" => '',
				"when" => '',
				"timestamp" => '',
				"counter_timestamp" => ''
			);
			$event_data = array();
			$event_data_count = preg_match('@<div class="gkEvent">.*?</div>@mis', $text, $event_data);
			
			if($event_data_count > 0) {
				$event_datetimes = array();
				$event_datetimes_count = preg_match_all('@<time.*?datetime="(.*?)".*?>(.*?)</time>@', $event_data[0], $event_datetimes);
			
				if($event_datetimes_count > 0) {
					$event_timestamp = strtotime($event_datetimes[1][0]);
					$event_info['timestamp'] = date(DATE_W3C, $event_timestamp);
					$event_info['counter_timestamp'] = date(DATE_W3C, strtotime($event_datetimes[1][4]));
					$event_info['date'] = strftime('%a', $event_timestamp) . '<small>' . strftime('%b %e', $event_timestamp) . '</small>';
					$event_info['when'] = $event_datetimes[2][0] . ' @ ' . $event_datetimes[2][2];
				}
			}
			
			// output the HTML code
			echo '<div>';
			
			if($event_info['date'] != '') {
				echo '<time datetime="'.$event_info['timestamp'].'" data-start="'.$event_info['counter_timestamp'].'">'.$event_info['date'].'</time>';
			}
			
			echo '<div>';
			echo '<h3>';
			echo '<a href="'.$this->get_link($i).'" title="'.strip_tags($this->parent->content[$i]['title']).'">';
			echo $title;
			echo '</a>';
			echo '</h3>';
			
			if($event_info['when'] != '') {
				echo '<span>';
				echo JText::_('MOD_NEWS_PRO_GK5_PORTAL_MODE_EVENTS_LIST_WHEN') . $event_info['when'];
				echo '<span class="gkEventsListProgress"></span>';
				echo '</span>';
			}
			
			echo '</div>';
			echo '</div>';
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
		} else if($this->mode == 'com_easyblog') {
			//
			require_once (JPATH_SITE.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'router.php');
			//
			return urldecode(JRoute::_(EasyBlogRouter::getEntryRoute($this->parent->content[$num]['id'])));
		} else {
			return false;
		}
	}
}

// EOF
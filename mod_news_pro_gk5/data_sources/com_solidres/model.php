<?php

/**
* This Model is responsible for getting data from the com_solidres data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');


class NSP_GK5_com_solidres_Model {
	// Method to get sources of articles
	static function getSources($config) {
		if($config['data_source'] !== 'com_solidres_hotel_categories') {
			return null;
		} 
		//
		$db = JFactory::getDBO();
		$where1 = '';
		$where2 = '';
		//
		$source = $config['com_solidres_hotel_categories'];
		$where1 = ' c.id = ';
		$where2 = ' OR c.id = ';
		//	
		$where = ''; // initialize WHERE condition
		// generating WHERE condition
		for($i = 0;$i < count($source);$i++){
			if(count($source) == 1) $where .= (is_array($source)) ? $where1.$source[0] : $where1.$source;
			else $where .= ($i == 0) ? $where1.$source[$i] : $where2.$source[$i];		
		}
		
		if($where != '') {
			$where = ' AND (' . $where . ') ';
		}
		//
		$query_name = '
		SELECT 
			c.id AS CID
		FROM 
			#__categories AS c
		WHERE 
			1=1 
			'.$where.'
			AND 
			c.extension = '.$db->quote('com_solidres').
			' AND 
			c.published = 1
        ';	
	        
		// Executing SQL Query
		$db->setQuery($query_name);
		// check if some categories was detected
		if($categories = $db->loadObjectList()) {
			$categories_array = array();
			// iterate through all items 
			foreach($categories as $item) {
				if(!in_array($item->CID, $categories_array)) {
					array_push($categories_array, $item->CID);
				}
			}
			//
			return $categories_array;
		} else {
			// when no categories detected
			return null;
		}
	}
	// Method to get articles in standard mode 
	static function getArticles($categories, $config, $amount) {	
		//
		$sql_where = '';
		$db = JFactory::getDBO();
		//
		if($categories) {		
			// getting categories ItemIDs
			for($j = 0; $j < count($categories); $j++) {
				$sql_where .= ($j != 0) ? ' OR content.category_id = ' . $categories[$j] : ' content.category_id = '. $categories[$j];
			}	
		}
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'com_solidres_hotels' && $config['com_solidres_hotels'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['com_solidres_hotels']);
			//
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.id = '.$ids[$i] : ' content.id = '.$ids[$i];
			}
		}
		
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'com_solidres_rooms' && $config['com_solidres_rooms'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['com_solidres_rooms']);
			//
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.id = '.$ids[$i] : ' content.id = '.$ids[$i];
			}
		}
		
		// Arrays for content
		$content = array();
		$news_amount = 0;
		
		// check if the timezone offset is set
		if($config['time_offset'] == 0) {
			$date = JFactory::getDate(date('Y-m-d H:i:s', strtotime('now')));
		} else {
			$date = JFactory::getDate($config['time_offset'].' hour '.date('Y-m-d H:i:s', strtotime('now')));
		}
		$now  = $date->toSql(true);
		$nullDate = $db->getNullDate();
		// if some data are available
		// when showing only frontpage articles is disabled
		$frontpage_con = '';
		
		if($config['only_featured'] == 0 && $config['news_featured'] == 0) {
		 	$frontpage_con = ' AND content.featured = 0 ';
		} else if($config['only_featured'] == 1) {
			$frontpage_con = ' AND content.featured = 1';
		}
		
		$since_con = '';
		if($config['news_since'] !== '') {
			$since_con = ' AND content.created_date >= ' . $db->Quote($config['news_since']);
		}
		//
		if($config['news_since'] == '' && $config['news_in'] != '') {
			$since_con = ' AND content.created_date >= ' . $db->Quote(strftime('%Y-%m-%d 00:00:00', time() - ($config['news_in'] * 24 * 60 * 60)));
		}
		
		// Ordering string
		$order_options = '';
		// When sort value is random
		if($config['news_sort_value'] == 'random' || $config['news_sort_value'] == 'user') {
			$order_options = ' RAND() ';
		} else{ // when sort value is different than random
			$sort_value = $config['news_sort_value'];
			
			if($sort_value == 'created' || $sort_value == 'modified') {
				$sort_value .= '_date';
			}
			
			if($sort_value == 'title') {
				$sort_value = 'name';
			}
			
			$order_options = ' content.'.$sort_value.' '.$config['news_sort_order'].' ';
		}	
		
		if($config['data_source'] != 'com_solidres_all' && $sql_where != '') {
			$sql_where = ' AND ( ' . $sql_where . ' ) ';
		}
		
		// one article per page - helper variables
		$article_id_query = 'content.id AS iid';
		// get table name
		$type = 'room';
		$table_name = 'room_types';
		$media_table_name = 'roomtype';
		$media_column_name = 'room_type';
		$room_type_join = 'LEFT JOIN #__sr_reservation_assets AS ra ON ra.id = content.reservation_asset_id';
		$currency_id = 'ra.currency_id AS currency_id,';
		
		if(
			$config['data_source'] == 'com_solidres_all_hotels' ||
			$config['data_source'] == 'com_solidres_hotels' ||
			$config['data_source'] == 'com_solidres_hotel_categories'
		) {
			$type = 'hotel';
			$table_name = 'reservation_assets';
			$media_table_name = 'reservation_assets';
			$media_column_name = 'reservation_asset';
			$room_type_join = '';
			$currency_id = '';
		}
		// creating SQL query			
		$query_news = '
		SELECT
			'.$article_id_query.'				
		FROM 
			#__sr_'.$table_name.' AS content
		WHERE 
			content.state = 1
			'.$sql_where.'
			'.$frontpage_con.' 
			'.$since_con.'
		ORDER BY 
			'.$order_options.'
		LIMIT
			'.($config['offset']).','.$amount.';
		';
		// run SQL query
		$db->setQuery($query_news);
		// when exist some results
		if($news = $db->loadAssocList()) {			
			// generating tables of news data
			foreach($news as $item) {	
				$content[] = $item; // store item in array
				$news_amount++;	// news amount
			}
		}
		// generate SQL WHERE condition
		$second_sql_where = '';
		for($i = 0; $i < count($content); $i++) {
			$second_sql_where .= (($i != 0) ? ' OR ' : '') . ' content.id = ' . $content[$i]['iid'];
		}
		if($second_sql_where != '') {
			$second_sql_where = ' AND ('.$second_sql_where.')';
		}
		// second SQL query to get rest of the data and avoid the DISTINCT
		$second_query_news = '
		SELECT
			content.id AS iid,
			content.id as id,
			content.'.($type == 'hotel' ? 'id' : 'reservation_asset_id').' as asset_id,
			'.($config['use_title_alias'] ? 'content.alias' : 'content.name').' AS title, 
			content.description AS text,
			content.created_date AS date,  
			content.featured AS frontpage,
			content.language AS lang,
			'.$currency_id.'
			media.name AS image
		FROM 
			#__sr_'.$table_name.' AS content 
			LEFT JOIN 
				#__sr_media_'.$media_table_name.'_xref AS media_xref 
				ON media_xref.'.$media_column_name.'_id = content.id
			LEFT JOIN 
				#__sr_media AS media 
				ON media.id = media_xref.media_id
			'.$room_type_join.'
		WHERE 
			1=1
			'.$second_sql_where.'
			AND
			(media_xref.weight IS NULL OR media_xref.weight = 0)
		ORDER BY 
			'.$order_options.'
		';
		// run the query
		$db->setQuery($second_query_news);
		// when exist some results
		if($news2 = $db->loadAssocList()) {
			// load URL overrides
			$url_overrides = false;
			
			if(isset($config['url_overrides']) && $config['url_overrides'] == '1') {
				$override_file = JPATH_SITE . '/modules/mod_news_pro_gk5/url_overrides.json';
				
				if(JFile::exists($override_file)) {
					$override_content = file_get_contents($override_file);
								
					if($override_content && $override_content != '') {
						$url_overrides = json_decode($override_content, true);
					}
				}
			}
			// create the iid array
			$content_iid = array();
			// create the content IDs array
			foreach($content as $item) {
				array_push($content_iid, $item['iid']);
			}
			// generating tables of news data
			foreach($news2 as $item) {						
			   	$pos = array_search($item['iid'], $content_iid);
				
				if(
				    $url_overrides && 
				    is_array($url_overrides) &&
				    count($url_overrides) > 0 && 
				    isset($url_overrides['com_solidres'])
				) {
					if(isset($url_overrides['com_solidres'][$item['iid']])) {
						$item['overrided_url'] = $url_overrides['com_solidres'][$item['iid']];
					}
				}
				
				if( $item['lang'] == '*' ){
					$lang = JFactory::getLanguage();
					$item['lang'] = $lang->getTag();
				}
				// merge the new data to the array of items data
				if(isset($content[$pos]) && is_array($content[$pos])) {
					$content[$pos] = array_merge($content[$pos], (array) $item);
				}
			}
		}
		// Reorder items if necessary
		if(
			$config['news_sort_value'] == 'user' &&
			(
				$config['data_source'] == 'com_solidres_hotels' && 
				$config['com_solidres_hotels'] != ''
			) ||
			(
				$config['data_source'] == 'com_solidres_rooms' && 
				$config['com_solidres_rooms'] != ''
			)
		) {
			$new_content = array();
			$type = 'rooms';
			
			if($config['data_source'] == 'com_solidres_hotels') {
				$type = 'hotels';
			}
			
			$ids = explode(',', $config['com_solidres_' . $type]);
			$query_ids = array();
			
			if(count($content)) {
				foreach($content as $key => $item) {
					$query_ids[$item['iid']] = $key;
				}
				
				foreach($ids as $id) {
					if(isset($query_ids[$id])) {
						array_push($new_content, $content[$query_ids[$id]]);
					}
				}
				
				$content = $new_content;
			}
		}
		// the content array
		return $content; 
	}
	
	static function getPrice($room_id, $day) {
		$db = JFactory::getDBO();
		// creating SQL query			
		$query_news = '
		SELECT
			tariff_details.price AS price			
		FROM 
			#__sr_tariffs AS tariffs
		LEFT JOIN
			#__sr_tariff_details AS tariff_details
			ON
			tariffs.id = tariff_details.tariff_id
		WHERE 
			tariffs.room_type_id = '.$room_id.'
			AND
			tariff_details.w_day = '.$day.'
		LIMIT 1;
		';
		// run SQL query
		$db->setQuery($query_news);
		// when exist some results
		if($news = $db->loadAssocList()) {			
			// generating tables of news data
			foreach($news as $item) {	
				return $item['price'];
			}
		}
	}
}

// EOF
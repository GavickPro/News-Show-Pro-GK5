<?php

/**
* This Model is responsible for getting data from the jomsocial data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_jomsocial_Model {
	// Method to get sources of articles
	static function getSources($config) {
		return null;
	}
	// Method to get articles in standard mode 
	static function getArticles($categories, $config, $amount) {	
		//
		if(file_exists(JPATH_BASE . '/components/com_community/defines.community.php')) {
			$sql_where = '';
			// Overwrite SQL query when user set IDs manually
			if(
				$config['data_source'] == 'jomsocial_user_status' && 
				$config['jomsocial_user_status'] != ''
			){						
				$ids = explode(',', $config['jomsocial_user_status']);
				//
				for($i = 0; $i < count($ids); $i++ ){	
					// linking string with content IDs
					$sql_where .= ($i != 0) ? ' OR activity.actor = '.$ids[$i] : ' activity.actor = '.$ids[$i];
				}
			}
			
			if(
				$config['data_source'] == 'jomsocial_user_photo' && 
				$config['jomsocial_user_photo'] != ''
			){						
				$ids = explode(',', $config['jomsocial_user_photo']);
				//
				for($i = 0; $i < count($ids); $i++ ){	
					// linking string with content IDs
					$sql_where .= ($i != 0) ? ' OR activity.actor = '.$ids[$i] : ' activity.actor = '.$ids[$i];
				}
			}
			// Arrays for content
			$content = array();
			// Initializing standard Joomla classes and SQL necessary variables
			$db = JFactory::getDBO();
			// check if the timezone offset is set
			if($config['time_offset'] == 0) {
				$date = JFactory::getDate("now");
			} else {
				$date = JFactory::getDate("now", $config['time_offset']);
			}
			$now  = $date->toSql(true);
			$nullDate = $db->getNullDate();
			//
			$since_con = '';
			//
			if($config['news_since'] !== '') {
				$since_con = ' AND activity.created >= ' . $db->Quote($config['news_since']);
			}
			// current article hiding
			$current_con = '';
			// Ordering string
			$order_options = '';
			// When sort value is random
			if($config['news_sort_value'] == 'random') {
				$order_options = ' RAND() '; 
			} else { // when sort value is different than random
				$order_options = ' activity.'.$config['news_sort_value'].' '.$config['news_sort_order'].' ';
			}	
			//
			if(
				$config['data_source'] != 'jomsocial_latest_status' && 
				$config['data_source'] != 'jomsocial_latest_photo' &&
				$sql_where != ''
			) {
				$sql_where = ' AND ( ' . $sql_where . ' ) ';
			}
			//
			if(
				$config['data_source'] == 'jomsocial_latest_status' || 
				$config['data_source'] == 'jomsocial_user_status'
			) {
				// creating SQL query			
				$query_news = '
				SELECT
					activity.id	AS ID,	
					activity.actor AS user_id,
					activity.title AS text,
					activity.created AS date,
					activity.app AS app,
					activity.like_type AS type,
					user.thumb AS avatar,
					'.$config['username'].' AS author_username
				FROM 
					#__community_activities AS activity
					
					LEFT JOIN
						#__community_users AS user
						ON
						user.userid = activity.actor
						
						LEFT JOIN
						
							#__users AS users
							ON
							user.userid = users.id
						
				WHERE 
					(
						activity.access = 0 
						OR
						activity.access = 10 
					)
				 	AND 
				 	( 
				 		activity.created = '.$db->Quote($nullDate).' 
				 		OR 
				 		activity.created <= '.$db->Quote($now).' 
				 	)
				 	AND
				 		activity.like_type = "profile.status"
					'.$sql_where.'
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
					}
				}
			} else {
				// creating SQL query			
				$query_news = '
				SELECT
					activity.id	AS ID,
					activity.params AS params,
					activity.actor AS user_id,
					activity.title AS text,
					activity.created AS date,
					activity.app AS app,
					activity.like_type AS type,
					user.thumb AS avatar,
					'.$config['username'].' AS author_username
				FROM 
					#__community_activities AS activity
		
					LEFT JOIN
						#__community_users AS user
						ON
						user.userid = activity.actor
						
						LEFT JOIN
						
							#__users AS users
							ON
							user.userid = users.id
					
				WHERE 
					(
						activity.access = 0 
						OR
						activity.access = 10 
					)
				 	AND 
				 	( 
				 		activity.created = '.$db->Quote($nullDate).' 
				 		OR 
				 		activity.created <= '.$db->Quote($now).' 
				 	)
				 	AND
				 		activity.like_type = "photo"
					'.$sql_where.'
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
					}
				}
				
				// generate SQL WHERE condition
				$second_sql_where = '';
				for($i = 0; $i < count($content); $i++) {
					$content[$i]['params'] = json_decode($content[$i]['params']);
					$second_sql_where .= (($i != 0) ? ' OR ' : '') . ' photo.id = ' . $content[$i]['params']->photoid;
				}
				
				if($second_sql_where != '') {
					$second_sql_where = ' AND (' . $second_sql_where . ') ';
				}
				
				// second SQL query to get rest of the data and avoid the DISTINCT
				$second_query_news = '
				SELECT
					photo.id AS photo_id,
					photo.albumid AS album_id,
					photo.image AS image	
				FROM 
					#__community_photos AS photo 
				WHERE 
					1=1 
					'.$second_sql_where.'
				ORDER BY
					photo.id DESC
				';
				// run the query
				$db->setQuery($second_query_news);
				// when exist some results
				if($news2 = $db->loadAssocList()) {
					// create the iid array
					$content_id = array();
					// create the content IDs array
					foreach($content as $item) {
						array_push($content_id, $item['params']->photoid);
					}
					// generating tables of news data
					foreach($news2 as $item) {						
					    $pos = array_search($item['photo_id'], $content_id);
						// merge the new data to the array of items data
						if(isset($content[$pos]) && is_array($content[$pos])) {
							$content[$pos] = array_merge($content[$pos], (array) $item);
						}
					}
				}
			}
			// load comments
			if(	
				stripos($config['info_format'], '%COMMENTS') !== FALSE || 
				stripos($config['info2_format'], '%COMMENTS') !== FALSE ||
				stripos($config['info_format'], '%LIKES') !== FALSE || 
				stripos($config['info2_format'], '%LIKES') !== FALSE
			) {
				$content = NSP_GK5_jomsocial_Model::getComments($content, $config);
			}
			// the content array
			return $content; 
			
			} else {
				return null;
			}
		}
		// method to get comments/likes amount
		static function getComments($content, $config) {
			// 
			$db = JFactory::getDBO();
			$counters_tab = array();
			$likes_tab = array();
			// 
			if(count($content) > 0) {
				// initializing variables
				$sql_where = '';
				//
				for($i = 0; $i < count($content); $i++ ) {	
					// linking string with content IDs
					$sql_where .= ($i != 0) ? ' OR activity.id = '.$content[$i]['ID'] : ' activity.id = '.$content[$i]['ID'];
				}
				
				if($sql_where != '') {
					$sql_where = ' AND (' . $sql_where . ') ';
				}
				// creating SQL query for comments
				$query_news = '
				SELECT 
					activity.id AS ID,
					COUNT(comments.contentid) AS count			
				FROM 
					#__community_activities AS activity 
					LEFT JOIN 
						#__community_wall AS comments
						ON 
						comments.contentid = activity.id 		
				WHERE 
					comments.published = 1
					'.$sql_where.' 
					AND
					comments.type = "'.$content[0]['type'].'"  
				GROUP BY 
					comments.contentid
				;';
				// run SQL query
				$db->setQuery($query_news);
				// when exist some results
				if($counters = $db->loadObjectList()) {
					// generating tables of news data
					foreach($counters as $item) {						
						$counters_tab[$item->ID] = $item->count;
					}
				}
				
				// creating SQL query for likes
				$query_news = '
				SELECT 
					activity.id AS ID,
					COUNT(likes.uid) AS count			
				FROM 
					#__community_activities AS activity 
					LEFT JOIN 
						#__community_likes AS likes
						ON 
						likes.uid = activity.id 		
				WHERE 
					1=1 
					'.$sql_where.'
					AND
					likes.element = "'.$content[0]['type'].'"  
				GROUP BY 
					likes.uid
				;';
				// run SQL query
				$db->setQuery($query_news);
				// when exist some results
				if($counters = $db->loadObjectList()) {
					// generating tables of news data
					foreach($counters as $item) {						
						$likes_tab[$item->ID] = $item->count;
					}
				}
			}
			//
			for($i = 0; $i < count($content); $i++ ) {	
				$content[$i]['likes'] = isset($likes_tab[$content[$i]['ID']]) ? $likes_tab[$content[$i]['ID']] : 0;
				$content[$i]['comments'] = isset($counters_tab[$content[$i]['ID']]) ? $counters_tab[$content[$i]['ID']] : 0;
			}
			
			return $content;
	}
}

// EOF

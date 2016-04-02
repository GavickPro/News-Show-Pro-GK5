<?php

/**
* This Model is responsible for getting data from the com_k2 data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_com_k2_Model {
	// Method to get sources of articles
	static function getSources($config) {
		if($config['data_source'] != 'k2_all' && $config['data_source'] != 'k2_authors') {
			//
			$db = JFactory::getDBO();
			// if source type is section / sections
			$source = false;
			$where1 = '';
			$where2 = '';
			$tag_join= '';
			//
			if( $config['data_source'] == 'k2_tags' ) {
				$tag_join = ' LEFT JOIN #__k2_tags_xref AS tx ON content.id = tx.itemID LEFT JOIN #__k2_tags AS t ON t.id = tx.tagID ';
			}
			//
			if($config['data_source'] == 'k2_categories'){
				$source = $config['k2_categories'];
				$where1 = ' c.id = ';
				$where2 = ' OR c.id = ';
			} else if($config['data_source'] == 'k2_tags') {
	           	$where1 = ' t.id = ';
	           	// adding quotes to tag name
	          	$source = $config['k2_tags'];
	         
	        	if(!is_array($source)) {
	           		$source = array($source);
	        	}
			} else {
				$source = strpos($config['k2_articles'],',') !== false ? explode(',', $config['k2_articles']) : $config['k2_articles'];
				$where1 = ' content.id = ';
				$where2 = ' OR content.id = ';	
			}
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
					#__k2_categories AS c
				LEFT JOIN 
					#__k2_items AS content 
					ON 
					c.id = content.catid 	
				'.$tag_join.' 
				WHERE 
					1=1 
					'.$where.'
					AND 
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
		} else {
			return null;
		}
	}
	// Method to get articles in standard mode 
	static function getArticles($categories, $config, $amount) {			
		//
		$sql_where = '';
		$tag_join = '';
		//
		if($categories) {		
			// getting categories ItemIDs
			for($j = 0; $j < count($categories); $j++) {
				$sql_where .= ($j != 0) ? ' OR content.catid = ' . $categories[$j] : ' content.catid = '. $categories[$j];
			}	
		}
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'k2_articles' && $config['k2_articles'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['k2_articles']);
			//
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.id = '.$ids[$i] : ' content.id = '.$ids[$i];
			}
		}
		// Overwrite SQL query when user specified tags
		if($config['data_source'] == 'k2_tags' && $config['k2_tags'] != ''){
			// initializing variables
			$sql_where = '';
			$tag_join = ' LEFT JOIN #__k2_tags_xref AS tx ON content.id = tx.itemID LEFT JOIN #__k2_tags AS t ON t.id = tx.tagID ';
			// getting tag
			$sql_where .= ' t.id = '. $config['k2_tags'];
		}
		// Overwrite SQL query when user specified authors
		if($config['data_source'] == 'k2_authors' && $config['k2_authors'] != ''){
			// initializing variables
			$sql_where = '';
			if(!is_array($config['k2_authors'])) {
				$ids = explode(',', $config['k2_authors']);
			} else {
				$ids = $config['k2_authors'];
			}
			//
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.created_by = '.$ids[$i] : ' content.created_by = '.$ids[$i];
			}
		}
		// Arrays for content
		$content = array();
		$news_amount = 0;
		// Initializing standard Joomla classes and SQL necessary variables
		$db = JFactory::getDBO();
		$access_con = '';
		
		$user = JFactory::getUser();	
		if($config['news_unauthorized'] == '0') {
			$access_con = ' AND content.access IN ('. implode(',', $user->getAuthorisedViewLevels()) .') ';
		}
		if($config['time_offset'] == 0) {
			$date = JFactory::getDate(date('Y-m-d H:i:s', strtotime('now')));
		} else {
			$date = JFactory::getDate($config['time_offset'].' hour '.date('Y-m-d H:i:s', strtotime('now')));
		}
		//$date = JFactory::getDate("now", $config['time_offset']);
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
		//
		if($config['news_since'] !== '') {
			$since_con = ' AND content.created >= ' . $db->Quote($config['news_since']);
		}
		//
		if($config['news_since'] == '' && $config['news_in'] != '') {
			$since_con = ' AND content.created >= ' . $db->Quote(strftime('%Y-%m-%d 00:00:00', time() - ($config['news_in'] * 24 * 60 * 60)));
		}
		// current article hiding
		$current_con = '';
		
		if(
			$config['hide_current_k2_article'] == '1' && 
			JRequest::getCmd('option') == 'com_k2' &&
			JRequest::getCmd('view') == 'item' &&
			JRequest::getVar('id') != ''
		) {
			$id = (int) JRequest::getVar('id');
			// filter the alias from ID
			if(stripos($id, ':') !== FALSE) {
				$id = explode(':', $id);
				$id = $id[0];
			}
			// create the condition
			$current_con = ' AND (content.id != '.$id.') ';
		}
		// Ordering string
		$order_options = '';
		$rating_join = '';
		// When sort value is random
		if(
			$config['news_sort_value'] == 'random' || 
			$config['news_sort_value'] == 'user'
		) {
			$order_options = ' RAND() '; 
		}else if($config['news_sort_value'] == 'rating') {
			$order_options = ' (content_rating.rating_sum / content_rating.rating_count) '.$config['news_sort_order'];
			$rating_join = 'LEFT JOIN #__k2_rating AS content_rating ON content_rating.itemID = content.id';
		}
		else{ // when sort value is different than random
			$order_options = ' content.'.$config['news_sort_value'].' '.$config['news_sort_order'].' ';
		}	
		// language filters
		$lang_filter = '';
		if (JFactory::getApplication()->getLanguageFilter()) {
			$lang_filter = ' AND content.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') ';
		}
		
		if($config['data_source'] != 'k2_all' && $sql_where != '') {
			$sql_where = ' AND ( ' . $sql_where . ' ) ';
		}
		// one article per page - helper variables
		$article_id_query = 'content.id AS id';
		$one_article_query = '';
	
		if($config['one_article_per_category'] && $config['data_source'] == 'k2_authors') {
			$article_id_query = 'MAX(content.id) AS id, content.created_by AS author';
			$one_article_query = ' GROUP BY content.created_by ';
		} elseif($config['one_article_per_category']) {
			$article_id_query = 'MAX(content.id) AS id, content.catid AS cid';
			$one_article_query = ' GROUP BY content.catid ';
		}
		// creating SQL query			
		$query_news = '
		SELECT
			'.$article_id_query.'				
		FROM 
			#__k2_items AS content 
			'.$rating_join.'
			'.$tag_join.'
		WHERE 
			content.published = 1 AND content.trash = 0
                '. $access_con .'   
		 		AND ( content.publish_up = '.$db->Quote($nullDate).' OR content.publish_up <= '.$db->Quote($now).' )
				AND ( content.publish_down = '.$db->Quote($nullDate).' OR content.publish_down >= '.$db->Quote($now).' )
			'.$sql_where.'
			'.$lang_filter.'
			'.$frontpage_con.' 
			'.$since_con.'
			'.$current_con.'
		
		'.$one_article_query.'	
		
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
			$second_sql_where .= (($i != 0) ? ' OR ' : '') . ' content.id = ' . $content[$i]['id'];
		}
		if($second_sql_where != '') {
			$second_sql_where = ' AND ('.$second_sql_where.')';
		}
		// second SQL query to get rest of the data and avoid the DISTINCT
		$second_query_news = '
		SELECT
			content.id AS id,
			content.alias AS alias,
			'.($config['use_title_alias'] ? 'content.alias' : 'content.title').' AS title, 
			content.'.$config['com_k2_text_type'].' AS text,
			content.'.($config['date_publish'] == 0 ? 'created' : ($config['date_publish'] == 1 ? 'publish_up' : 'publish_down')).' AS date, 
			content.publish_up AS date_publish,
			content.hits AS hits,
			content.featured AS frontpage,
			content.access AS access,
			content.catid AS cid,
			content.video AS video,
			content.plugins AS plugins,
			categories.name AS catname, 
			categories.image AS cat_image,
			categories.alias AS cat_alias,
			users.email AS author_email,
			content.created_by_alias AS author_alias,
			'.$config['username'].' AS author_username,
			content.created_by AS author_id,
			content_rating.rating_sum AS rating_sum,
			content_rating.rating_count AS rating_count		
		FROM 
			#__k2_items AS content 
			LEFT JOIN 
				#__k2_categories AS categories 
				ON categories.id = content.catid 
			LEFT JOIN 
				#__users AS users 
				ON users.id = content.created_by 			
			LEFT JOIN 
				#__k2_rating AS content_rating 
				ON content_rating.itemID = content.id
		WHERE 
			1=1
			'.$second_sql_where.'
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
			$content_id = array();
			// create the content IDs array
			foreach($content as $item) {
				array_push($content_id, $item['id']);
			}
			// generating tables of news data
			foreach($news2 as $item) {						
			    $pos = array_search($item['id'], $content_id);
			    
			    if(
				    $url_overrides && 
				    is_array($url_overrides) &&
				    count($url_overrides) > 0 && 
				    isset($url_overrides['com_k2'])
			    ) {
			    	if(isset($url_overrides['com_k2'][$item['id']])) {
			    		$item['overrided_url'] = $url_overrides['com_k2'][$item['id']];
			    	}
			   	}
				// merge the new data to the array of items data
				if(isset($content[$pos]) && is_array($content[$pos])) {
					$content[$pos] = array_merge($content[$pos], (array) $item);
				}
			}
		}
		// load comments
		if(stripos($config['info_format'], '%COMMENTS') !== FALSE || stripos($config['info2_format'], '%COMMENTS') !== FALSE) {
			$content = NSP_GK5_com_k2_Model::getComments($content, $config);
		}
		// load extra fields
		if(isset($config['k2_get_extra_fields']) && $config['k2_get_extra_fields'] == 1) {
			$content = NSP_GK5_com_k2_Model::getExtraFields($content, $config);
		}
		// load tags
		if(stripos($config['info_format'], '%TAGS') !== FALSE || stripos($config['info2_format'], '%TAGS') !== FALSE) {
			$content = NSP_GK5_com_k2_Model::getTags($content, $config);
		}
		// Reorder items if necessary
		if(
			$config['news_sort_value'] == 'user' &&
			$config['data_source'] == 'k2_articles' && 
			$config['k2_articles'] != ''
		) {
			$new_content = array();
			$ids = explode(',', $config['k2_articles']);
			$query_ids = array();
			
			if(count($content)) {
				foreach($content as $key => $item) {
					$query_ids[$item['id']] = $key;
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
	// method to get the extra fields
	static function getExtraFields($content, $config) {
		// 
		$db = JFactory::getDBO();
		$schema_tab = array();
		$preparation_tab = array();
		$results_tab = array();
		// 
		if(count($content) > 0) {
			// initializing variables
			$sql_where = '';
			//
			for($i = 0; $i < count($content); $i++ ) {	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.id = '.$content[$i]['id'] : ' content.id = '.$content[$i]['id'];
			}
			
			if($sql_where != '') {
				$sql_where = ' AND (' . $sql_where . ') ';
			}
			// getting extra fields data
			$query_news = '
			SELECT 
				content.id AS id,
				content.extra_fields AS extra_fields
			FROM 
				#__k2_items AS content		
			WHERE 
				1=1 
				'.$sql_where.'
			ORDER BY
				content.id ASC
			;';
			// run SQL query
			$db->setQuery($query_news);
			// when exist some results
			if($extra_fields_data = $db->loadObjectList()) {
				// generating tables of news data
				foreach($extra_fields_data as $item) {					
					$preparation_tab[$item->id] = json_decode($item->extra_fields);
				}
			}
			// getting extra fields schema
			$query_news2 = '
			SELECT 
				extra_fields.id AS id,
				extra_fields.name AS name,
				extra_fields.value AS value
			FROM 
				#__k2_extra_fields AS extra_fields	
			ORDER BY
				extra_fields.id ASC
			;';
			// run SQL query
			$db->setQuery($query_news2);
			// when exist some results
			if($extra_fields_schema = $db->loadObjectList()) {
				// generating tables of news data
				foreach($extra_fields_schema as $item) {					
					$schema_tab[$item->id] = $item->name;
				}
			}
			// merge the retrieved data
			foreach($preparation_tab as $key => $extra_fields_tab) {
				$extra_fields_data = array();
				
				foreach($extra_fields_tab as $extra_field) {					
					$extra_fields_data[$schema_tab[$extra_field->id]] = $extra_field->value;
				}
				
				$results_tab[$key] = $extra_fields_data;
			}
		}
		//
		for($i = 0; $i < count($content); $i++ ) {	
			$content[$i]['extra_fields'] = $results_tab[$content[$i]['id']];
		}
		
		return $content;
	}
	// method to get comments amount
	static function getComments($content, $config) {
		// 
		$db = JFactory::getDBO();
		$counters_tab = array();
		// 
		if(count($content) > 0) {
			// initializing variables
			$sql_where = '';
			//
			for($i = 0; $i < count($content); $i++ ) {	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.id = '.$content[$i]['id'] : ' content.id = '.$content[$i]['id'];
			}
			
			if($sql_where != '') {
				$sql_where = ' AND (' . $sql_where . ') ';
			}
			// check the comments source
			if($config['k2_comments_source'] == 'k2') {
				// creating SQL query
				$query_news = '
				SELECT 
					content.id AS id,
					COUNT(comments.itemID) AS count			
				FROM 
					#__k2_items AS content 
					LEFT JOIN 
						#__k2_comments AS comments
						ON comments.itemID = content.id 		
				WHERE 
					comments.published
					'.$sql_where.'  
				GROUP BY 
					comments.itemID
				;';
			} elseif($config['k2_comments_source'] == 'jcomments') {
				// creating SQL query
				$query_news = '
				SELECT 
					content.id AS id,
					COUNT(comments.object_id) AS count			
				FROM 
					#__k2_items AS content 
					LEFT JOIN 
						#__jcomments AS comments
						ON comments.object_id = content.id 		
				WHERE 
					comments.published = 1
					'.$sql_where.' 
					AND
					comments.object_group = \'com_k2\'  
				GROUP BY 
					comments.object_id
				;';
			} elseif($config['k2_comments_source'] == 'komento') {
				// creating SQL query
				$query_news = '
				SELECT 
					content.id AS id,
					COUNT(comments.cid) AS count			
				FROM 
					#__k2_items AS content 
					LEFT JOIN 
						#__komento_comments AS comments
						ON comments.cid = content.id 		
				WHERE 
					comments.published = 1
					'.$sql_where.' 
					AND
					comments.component = \'com_k2\'  
				GROUP BY 
					comments.cid
				;';
			}
			// run SQL query
			$db->setQuery($query_news);
			// when exist some results
			if($counters = $db->loadObjectList()) {
				// generating tables of news data
				foreach($counters as $item) {						
					$counters_tab[$item->id] = $item->count;
				}
			}
		}
		//
		for($i = 0; $i < count($content); $i++ ) {	
			if(isset($counters_tab[$content[$i]['id']])) {
				$content[$i]['comments'] = $counters_tab[$content[$i]['id']];
			}
		}
		
		return $content;
	}	
	//
	static function getTags($content, $config) {
		// 
		$db = JFactory::getDBO();
		$counters_tab = array();
		// 
		if(count($content) > 0) {
			// initializing variables
			$sql_where = '';
			//
			for($i = 0; $i < count($content); $i++ ) {	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.id = '.$content[$i]['id'] : ' content.id = '.$content[$i]['id'];
			}
			
			if($sql_where != '') {
				$sql_where = ' AND (' . $sql_where . ') ';
			}
			// creating SQL query
			$query_news = '
			SELECT 
				content.id AS id,
				tags.name AS tag		
			FROM 
				#__k2_items AS content 
				LEFT JOIN 
					#__k2_tags_xref AS xref
					ON xref.itemID = content.id
				LEFT JOIN
					#__k2_tags AS tags
					ON xref.tagID = tags.id 		
			WHERE 
				tags.published
				'.$sql_where.' 
			ORDER BY
				content.id ASC
			;';
			// run SQL query
			$db->setQuery($query_news);
			// when exist some results
			if($counters = $db->loadObjectList()) {
				// generating tables of news data
				foreach($counters as $item) {			
					if(isset($counters_tab[$item->id])) {			
						array_push($counters_tab[$item->id], $item->tag);
					} else {
						$counters_tab[$item->id] = array($item->tag);
					}
				}
			}
		}
		//
		for($i = 0; $i < count($content); $i++ ) {	
			if(isset($counters_tab[$content[$i]['id']])) {
				$content[$i]['tags'] = $counters_tab[$content[$i]['id']];
			}
		}

		return $content;
	}	
	
}

// EOF

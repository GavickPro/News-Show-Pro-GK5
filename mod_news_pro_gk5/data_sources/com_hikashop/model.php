<?php

/**
* This Model is responsible for getting data from the com_hikashop data source
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_com_hikashop_Model {
	// Method to get sources of articles
	static function getSources($config) {
		//
		$db = JFactory::getDBO();
		// if source type is section / sections
		$source = false;
		$where1 = '';
		$where2 = '';
		//
		if($config['data_source'] == 'com_hikashop_categories'){
			$source = $config['com_hikashop_categories'];
			$where1 = ' c.category_id = ';
			$where2 = ' OR c.category_id = ';
		} else {
			$source = strpos($config['com_hikashop_products'],',') !== false ? explode(',', $config['com_hikashop_products']) : $config['com_hikashop_products'];
			$where1 = ' content.product_id = ';
			$where2 = ' OR content.product_id = ';	
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
		SELECT DISTINCT 
			c.category_id AS CID
		FROM 
			#__hikashop_product_category AS cx
		LEFT JOIN 
            #__hikashop_category AS c
            ON
            cx.category_id = c.category_id
		LEFT JOIN 
			#__hikashop_product AS content 
			ON 
			cx.product_id = content.product_id 
        WHERE 
			1=1 
			'.$where.'
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
		//
		if($categories) {		
			// getting categories ItemIDs
			for($j = 0; $j < count($categories); $j++) {
				$sql_where .= ($j != 0) ? ' OR category.category_id = '.$categories[$j] : ' category.category_id = ' . $categories[$j];
			}	
		}
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'com_hikashop_products' && $config['com_hikashop_products'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['com_hikashop_products']);
			//			
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.product_id = '.$ids[$i] : ' content.product_id = '.$ids[$i];
			}
		}
		// Arrays for content
		$content = array();
		$news_amount = 0;
		// Initializing standard Joomla classes and SQL necessary variables
		$db = JFactory::getDBO();
		$access_con = '';
		
		//if($config['news_unauthorized'] == '0') {
		//	$access_con = ' AND content.access IN ('. implode(',', JFactory::getUser()->authorisedLevels()) .') ';
		//}
		// check if the timezone offset is set
		if($config['time_offset'] == 0) {
			$date = JFactory::getDate("now");
		} else {
			$date = JFactory::getDate("now", $config['time_offset']);
		}
		$now  = $date->toSql(true);
		$nullDate = $db->getNullDate();
		// if some data are available
		// when showing only frontpage articles is disabled
		$frontpage_con = '';
		$since_con = '';
		//
		if($config['news_since'] !== '') {
			$since_con = ' AND contentR.created_on >= ' . $db->Quote($config['news_since']);
		}
		//
		if($config['news_since'] == '' && $config['news_in'] != '') {
			$since_con = ' AND contentR.product_created >= ' . $db->Quote(strftime('%Y-%m-%d 00:00:00', time() - ($config['news_in'] * 24 * 60 * 60)));
		}
		// Ordering string
		$order_options = '';
		// When sort value is random
		if(
			$config['news_sort_value'] == 'random'|| 
			$config['news_sort_value'] == 'user'
		) {
			$order_options = ' RAND() '; 
		} else { // when sort value is different than random
			$sort_value = $config['news_sort_value'];
			//
			if ($config['news_sort_value'] == 'created') {
				$sort_value = 'product_created';
			} elseif($config['news_sort_value'] == 'title') {
				$sort_value = 'product_name';
			} else {
				$sort_value = 'product_id';
			}
			// exception for the title
			if($config['news_sort_value'] == 'title') { 
				$order_options = ' content.'.$sort_value.' '.$config['news_sort_order'].' '; 
			} else { 
				$order_options = ' contentR.'.$sort_value.' '.$config['news_sort_order'].' '; 
			}
		}	
		//
		$shopper_group_con = '';
		//
		$out_of_stock_con = '';
		//
		if($config['hikashop_out_of_stock'] != 1) {
            $out_of_stock_con = ' AND contentR.product_quantity > 0 ';
		}
		
		if($sql_where != '') {
			$sql_where = ' AND (' . $sql_where . ') ';
		}
		// creating SQL query
		$query_news = '
		SELECT DISTINCT
            content.product_id AS id,
            content.product_name AS title,
            content.product_description AS text,
            contentR.product_modified AS date,
            contentR.product_created AS date_publish
		FROM 
			#__hikashop_product AS content 
            LEFT JOIN
                #__hikashop_product_category AS category
                ON
                category.product_id = content.product_id
            LEFT JOIN
                #__hikashop_product AS contentR	
                ON
                contentR.product_id = content.product_id
        WHERE
            contentR.product_parent_id = 0
            AND contentR.product_published = 1  
			'.$sql_where.'  
			'.$frontpage_con.' 
			'.$since_con.'
			'.$shopper_group_con.'
			'.$out_of_stock_con.'
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
		// second query start
		$sql_where2 = '';
		// generating IDs			
		for($i = 0; $i < count($content); $i++ ){	
			// linking string with content IDs
			$sql_where2 .= ($i != 0) ? ' OR content.product_id = '.$content[$i]['id'] : ' content.product_id = '.$content[$i]['id'];
		}
		
		if($sql_where2 != '') {
			$sql_where2 = ' AND (' . $sql_where2 . ') ';
		}
		// creating SQL query
		$query_news2 = '
		SELECT DISTINCT
		    content.product_id AS id,
		    content.product_alias AS alias,
		    content.product_tax_id AS tax_id,
			category.category_id AS cid,
			category.category_name AS cat_name,
			category.category_alias AS cat_alias
		FROM 
			#__hikashop_product AS content 
			LEFT JOIN 
				#__hikashop_product_category AS category_xref
				ON 
		        category_xref.product_id = content.product_id 
			LEFT JOIN 
				#__hikashop_category AS category 
				ON 
		        category_xref.category_id = category.category_id 	
        WHERE
			1=1 
			'.$sql_where2.' 
			AND category.category_published = \'1\' 
		ORDER BY 
			content.product_id ASC
		';
		// run second SQL query
		$db->setQuery($query_news2);
		// create the id array
		$content_id = array();
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
				    isset($url_overrides['com_hikashop'])
				) {
					if(isset($url_overrides['com_hikashop'][$item['id']])) {
						$item['overrided_url'] = $url_overrides['com_hikashop'][$item['id']];
					}
				}
				// merge the new data to the array of items data
				if(isset($content[$pos]) && is_array($content[$pos])) {
					$content[$pos] = array_merge($content[$pos], (array) $item);
				}
			}
		}
		// third query
		$query_news3 = "SELECT
			`m`.`file_path` AS `image`, 
			`m`.`file_ordering` AS `ordering`, 
			`content`.`product_id` AS `id`
		FROM
			#__hikashop_product AS content
		LEFT JOIN
			#__hikashop_file AS `m` 
			ON 
			`m`.`file_ref_id` = `content`.`product_id`
		WHERE
			1=1 
			".$sql_where2."
			AND
			`m`.`file_type` LIKE 'product'
		ORDER BY
			`content`.`product_id` ASC, 
			`m`.`file_ordering` DESC
		";
		
		$db->setQuery($query_news3);
	    $pimages = $db->loadAssocList();
	    $images = array();
	    // get the first products images
	    if ($pimages) {
	       foreach($pimages as $item) {
	           $pos = array_search($item['id'], $content_id);
	           // merge the new data to the array of items data
	           $temp_array = array('image' => $item['image']);
	           if(isset($content[$pos]) && is_array($content[$pos])) {
	           		$content[$pos] = array_merge($content[$pos], (array) $temp_array);
	           }
	       }
	    }
	    // fourth query
	    $query_news4 = "SELECT
	    	`content`.`product_id` AS `product_id`,
	    	`price`.`price_value` AS `price`
	    FROM
	    	#__hikashop_product AS content
	    LEFT JOIN
	    	#__hikashop_price AS price 
	    	ON 
	    	`price`.`price_product_id` = `content`.`product_id`
	    WHERE
	    	1=1 
	    	".$sql_where2."
	    	AND
	    	`price`.`price_min_quantity` = 0
	    ORDER BY
	    	`content`.`product_id` ASC
	    ";
	    
	    $db->setQuery($query_news4);
	    $product_prices = $db->loadAssocList();
	    $prices = array();
	    // get the first products images
	    if ($product_prices) {
	       foreach($product_prices as $item) {
	           $pos = array_search($item['product_id'], $content_id);
	           // merge the new data to the array of items data
	           $temp_array = array('price' => $item['price']);
	           if(isset($content[$pos]) && is_array($content[$pos])) {
	           		$content[$pos] = array_merge($content[$pos], (array) $temp_array);
	           }
	       }
	    }
	    // load comments
	    if(stripos($config['info_format'], '%COMMENTS') !== FALSE || stripos($config['info2_format'], '%COMMENTS') !== FALSE) {
	    	$content = NSP_GK5_com_hikashop_Model::getComments($content, $config);
	    }
	    // load variants
	    $content = NSP_GK5_com_hikashop_Model::getVariants($content);
	    // Reorder items if necessary
	    if(
	    	$config['news_sort_value'] == 'user' &&
	    	$config['data_source'] == 'com_hikashop_products' && 
	    	$config['com_hikashop_products'] != ''
	    ) {
	    	$new_content = array();
	    	$ids = explode(',', $config['com_hikashop_products']);
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
	
	// Method to get amount of the product comments 
	function getComments($content, $config) {
		// 
		$db =& JFactory::getDBO();
		$counters_tab = array();
		// 
		if(count($content) > 0) {
			// initializing variables
			$sql_where = '';
			//
			for($i = 0; $i < count($content); $i++ ) {	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.product_id = '.$content[$i]['id'] : ' content.product_id = '.$content[$i]['id'];
			}
			
			if($sql_where != '') {
				$sql_where = ' AND (' . $sql_where . ') ';
			}
			// creating SQL query
			$query_news = "
			SELECT 
				content.product_id AS id,
				COUNT(comments.vote_ref_id) AS count			
			FROM 
				#__hikashop_product AS content 
				LEFT JOIN 
					#__hikashop_vote AS comments
					ON 
                    comments.vote_ref_id = content.product_id 		
			WHERE 
				comments.vote_published = '1'
				AND comments.vote_type = 'product'
				".$sql_where."
			GROUP BY 
				comments.vote_ref_id
			;";
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
		//
		return $content;
	}
	
	// Method to get amount of the product variants 
	function getVariants($content) {
		// 
		$db =& JFactory::getDBO();
		$counters_tab = array();
		// 
		if(count($content) > 0) {
			// initializing variables
			$sql_where = '';
			//
			for($i = 0; $i < count($content); $i++ ) {	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.product_id = '.$content[$i]['id'] : ' content.product_id = '.$content[$i]['id'];
			}
			
			if($sql_where != '') {
				$sql_where = ' AND (' . $sql_where . ') ';
			}
			// creating SQL query
			$query_news = "
			SELECT 
				content.product_id AS id,
				COUNT(variants.variant_product_id) AS count			
			FROM 
				#__hikashop_product AS content 
				LEFT JOIN 
					#__hikashop_variant AS variants
					ON 
	                variants.variant_product_id = content.product_id 		
			WHERE 
				1=1 
				".$sql_where."
			GROUP BY 
				variants.variant_product_id
			;";
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
				$content[$i]['variants'] = $counters_tab[$content[$i]['id']];
			}
		}
		//
		return $content;
	}
}

// EOF
<?php

/**
 *
 * This Model is responsible for getting data from the
 * com_virtuemart data source
 *
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_com_virtuemart_Model {
	// Method to get sources of articles
	static function getSources($config) {
		//
		$db = JFactory::getDBO();
		// if source type is section / sections
		$source = false;
		$where1 = '';
		$where2 = '';
		// VirtueMart uses language detection
		$lang = '';
		// get front-end language
        jimport('joomla.language.helper');
        $languages = JLanguageHelper::getLanguages('lang_code');
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
        // small validation 
       	if($lang == '') { 
       		$lang = 'en_gb';
       	}
		//
		if($config['data_source'] == 'com_virtuemart_categories'){
			$source = $config['com_virtuemart_categories'];
			$where1 = ' c.virtuemart_category_id = ';
			$where2 = ' OR c.virtuemart_category_id = ';
		} else {
			$source = strpos($config['com_virtuemart_products'],',') !== false ? explode(',', $config['com_virtuemart_products']) : $config['com_virtuemart_products'];
			$where1 = ' content.virtuemart_product_id = ';
			$where2 = ' OR content.virtuemart_product_id = ';	
		}
		//	
		$where = ''; // initialize WHERE condition
		// generating WHERE condition
		for($i = 0;$i < count($source);$i++){
			if(count($source) == 1) $where .= (is_array($source)) ? $where1.$source[0] : $where1.$source;
			else $where .= ($i == 0) ? $where1.$source[$i] : $where2.$source[$i];		
		}
		//
		$query_name = '
		SELECT DISTINCT 
			c.virtuemart_category_id AS CID
		FROM 
			#__virtuemart_product_categories AS cx
		LEFT JOIN 
            #__virtuemart_categories_'.$lang.' AS c
            ON
            cx.virtuemart_category_id = c.virtuemart_category_id
		LEFT JOIN 
			#__virtuemart_products_'.$lang.' AS content 
			ON 
			cx.virtuemart_product_id = content.virtuemart_product_id 
        LEFT JOIN
            #__virtuemart_categories AS cat
            ON
            c.virtuemart_category_id = cat.virtuemart_category_id
		WHERE 
			( '.$where.' ) 
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
		// get front-end language
        $languages = JLanguageHelper::getLanguages('lang_code');
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
        // small validation 
        if($lang == '') {
        	$lang = 'en_gb';
        }
		//
		$sql_where = '';
		//
		if($categories) {		
			// getting categories ItemIDs
			for($j = 0; $j < count($categories); $j++) {
				$sql_where .= ($j != 0) ? ' OR category.virtuemart_category_id = '.$categories[$j] : ' category.virtuemart_category_id = ' . $categories[$j];
			}	
		}
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'com_virtuemart_products' && $config['com_virtuemart_products'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['com_virtuemart_products']);
			//			
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.virtuemart_product_id = '.$ids[$i] : ' content.virtuemart_product_id = '.$ids[$i];
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
		$app = JFactory::getApplication();
		$timezone = $app->getCfg('offset') + $config['time_offset'];
		$date = JFactory::getDate("now", $timezone);
		$now  = $date->toSql(true);
		$nullDate = $db->getNullDate();
		// if some data are available
		// when showing only frontpage articles is disabled
		$frontpage_con = '';
		
		if($config['only_featured'] == 0 && $config['news_featured'] == 0) {
		 	$frontpage_con = ' AND contentR.product_special = 0 ';
		} else if($config['only_featured'] == 1) {
			$frontpage_con = ' AND contentR.product_special = 1 ';
		}
		
		$since_con = '';
		//
		if($config['news_since'] !== '') {
			$since_con = ' AND contentR.created_on >= ' . $db->Quote($config['news_since']);
		}
		//
		if($config['news_since'] == '' && $config['news_in'] != '') {
			$since_con = ' AND contentR.created >= ' . $db->Quote(strftime('%Y-%m-%d 00:00:00', time() - ($config['news_in'] * 24 * 60 * 60)));
		}
		// Ordering string
		$order_options = '';
		// When sort value is random
		if($config['news_sort_value'] == 'random') {
			$order_options = ' RAND() '; 
		} else { // when sort value is different than random
			$sort_value = $config['news_sort_value'];
			//
			if ($config['news_sort_value'] == 'created') {
				$sort_value = 'created_on';
			} elseif($config['news_sort_value'] == 'title') {
				$sort_value = 'product_name';
			} else {
				$sort_value = 'virtuemart_product_id';
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
        if($config['vm_shopper_group'] != -1) {
            $shopper_group_con = ' AND sgroup.virtuemart_shoppergroup_id = ' . $config['vm_shopper_group'] . ' ';
		}
		//
		$out_of_stock_con = '';
		//
		if($config['vm_out_of_stock'] != 1) {
            $out_of_stock_con = ' AND contentR.product_in_stock > 0 ';
		}
		// creating SQL query
		$query_news = '
		SELECT DISTINCT
            content.virtuemart_product_id AS id,
            content.product_name AS title,
            content.product_desc AS text,
            contentR.modified_on AS date,
            contentR.created_on AS date_publish,
			manufacturer.mf_name AS manufacturer,
			manufacturer.virtuemart_manufacturer_id AS manufacturer_id
		FROM 
			#__virtuemart_products_'.$lang.' AS content 
            LEFT JOIN
                #__virtuemart_product_categories AS category
                ON
                category.virtuemart_product_id = content.virtuemart_product_id
            
            LEFT JOIN
                #__virtuemart_product_manufacturers AS manufacturer_x
                ON
                content.virtuemart_product_id = manufacturer_x.virtuemart_product_id
            LEFT JOIN
                #__virtuemart_manufacturers_'.$lang.' AS manufacturer
                ON
                manufacturer_x.virtuemart_manufacturer_id = manufacturer.virtuemart_manufacturer_id
            LEFT JOIN
                #__virtuemart_products AS contentR	
                ON
                contentR.virtuemart_product_id = content.virtuemart_product_id
            LEFT JOIN
                #__virtuemart_product_shoppergroups AS psgroup
                ON 
                psgroup.virtuemart_product_id = content.virtuemart_product_id
            LEFT JOIN
                #__virtuemart_shoppergroups AS sgroup
                ON 
                sgroup.virtuemart_shoppergroup_id = psgroup.virtuemart_shoppergroup_id
		WHERE
            contentR.product_parent_id = 0
            AND contentR.published = 1  
			AND ( '.$sql_where.' ) 
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
			$sql_where2 .= ($i != 0) ? ' OR content.virtuemart_product_id = '.$content[$i]['id'] : ' content.virtuemart_product_id = '.$content[$i]['id'];
		}
		// creating SQL query
		$query_news2 = '
		SELECT DISTINCT
		    content.virtuemart_product_id AS id,
			cat.virtuemart_category_id AS cid,
			cat.category_name AS cat_name
		FROM 
			#__virtuemart_products AS content 
			LEFT JOIN 
				#__virtuemart_product_categories AS category_xref
				ON 
		        category_xref.virtuemart_product_id = content.virtuemart_product_id 
			LEFT JOIN 
				#__virtuemart_categories AS category 
				ON 
		        category_xref.virtuemart_category_id = category.virtuemart_category_id 	
            LEFT JOIN
                #__virtuemart_categories_'.$lang.' AS cat
                ON
                category_xref.virtuemart_category_id = cat.virtuemart_category_id
		WHERE
			('.$sql_where2.')
			AND category.published = \'1\' 
		ORDER BY 
			content.virtuemart_product_id ASC
		';
		// run second SQL query
		$db->setQuery($query_news2);
		// create the id array
		$content_id = array();
		// when exist some results
		if($news2 = $db->loadAssocList()) {
			// create the content IDs array
			foreach($content as $item) {
				array_push($content_id, $item['id']);
			}
			// generating tables of news data
			foreach($news2 as $item) {						
			    $pos = array_search($item['id'], $content_id);
				// merge the new data to the array of items data
				$content[$pos] = array_merge($content[$pos], (array) $item);
			}
		}
		// third query
		$query3 = $db->getQuery(true);
		$query3->select('`m`.`file_url` AS `image`, `pm`.`ordering` AS `ordering`, `content`.`virtuemart_product_id` AS `id`');
		$query3->from('#__virtuemart_products AS content');
		$query3->leftJoin('#__virtuemart_product_medias AS `pm` ON `pm`.`virtuemart_product_id` = `content`.`virtuemart_product_id`');
		$query3->leftJoin('#__virtuemart_medias AS `m` ON `m`.`virtuemart_media_id` = `pm`.`virtuemart_media_id`');
		$query3->where($sql_where2);
		$query3->order('`content`.`virtuemart_product_id` ASC, `pm`.`ordering` DESC');
		$db->setQuery((string) $query3);
	    $pimages = $db->loadAssocList();
	    $images = array();
	    // get the first products images
	    if ($pimages) {
	       foreach($pimages as $item) {
	           $pos = array_search($item['id'], $content_id);
	           // merge the new data to the array of items data
	           $temp_array = array('image' => $item['image']);
	           $content[$pos] = array_merge($content[$pos], (array) $temp_array);
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
			// creating SQL query
			$query_news = '
			SELECT 
				content.product_id AS id,
				COUNT(comments.product_id) AS count			
			FROM 
				#__vm_product AS content 
				LEFT JOIN 
					#__vm_product_reviews AS comments
					ON 
                    comments.product_id = content.product_id 		
			WHERE 
				comments.published
				AND ( '.$sql_where.' ) 
			GROUP BY 
				comments.product_id
			;';
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
}

// EOF
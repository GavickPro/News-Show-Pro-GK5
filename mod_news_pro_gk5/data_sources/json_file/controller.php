<?php

/**
 *
 * This Controller is responsible for generating results for the
 * com_content data source
 *
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_json_file_Controller {
	// constructor
	function initialize($config, $content) {
		// tables which will be used in generated content
		$output = array(
			'arts' => array(),
			'list' => array()
		);
		// Generating content 
		$counter = 0;
		//
		for($i = 0; $i < count($content); $i++) {	
			if($i < ($config['news_column'] * $config['news_rows'] * $config['news_full_pages'])) {
				// GENERATING NEWS CONTENT
		        if($config['use_own_article_format'] == 1) {
		        	$generated_content = NSP_GK5_Article_Format::generateLayout($config, $content[$i]);
		        } else {
					// GENERATING HEADER
					$news_header = NSP_GK5_json_file_View::header($config, $content[$i]);
					// GENERATING IMAGE
					$news_image = NSP_GK5_json_file_View::image($config, $content[$i]);
					// GENERATING READMORE
					$news_readmore = NSP_GK5_json_file_View::readMore($config, $content[$i]);
					// GENERATING TEXT
					$news_text = NSP_GK5_json_file_View::text($config, $content[$i], $news_readmore);	
					// GENERATE NEWS INFO
					$news_info = NSP_GK5_json_file_View::info($config, $content[$i]);
					// GENERATE NEWS INFO2
					$news_info2 = NSP_GK5_json_file_View::info($config, $content[$i], 2);		
		           
					// PARSING PLUGINS
					if($config['parse_plugins'] == TRUE) {
						$news_text = JHtml::_('content.prepare', $news_text);
					}	
					// CLEANING PLUGINS
					if($config['clean_plugins'] == TRUE) {
						$news_text = preg_replace("/(\{.+?\}.+?\{.+?})|(\{.+?\})/", "", $news_text);
					} 		
					// GENERATE CONTENT FOR TAB	
					$generated_content = ''; // initialize variable
					//
					for($j = 1; $j < 10; $j++) {
						//
						if(
							$config['wrap_content'] == 1 && 
							$config['news_image_order'] == 1							
						) {
							if($j == 2) {
								$generated_content .= '<div class="gkArtContentWrap">';
							}
						}
						//
						if($config['news_header_order'] == $j) $generated_content .= $news_header;
						if($config['news_image_order'] == $j)  $generated_content .= $news_image;
						if($config['news_text_order'] == $j)   $generated_content .= $news_text;
						if($config['news_info_order'] == $j)   $generated_content .= $news_info;
						if($config['news_info2_order'] == $j)  $generated_content .= $news_info2;
					}		
					//
					if($config['news_content_readmore_pos'] != 'after') {
						$generated_content .= $news_readmore;
					}
					//
					if(
						$config['wrap_content'] == 1 && 
						$config['news_image_order'] == 1							
					) {
						$generated_content .= '</div>';
					}
				} 
				// creating table with news content
				array_push($output['arts'], $generated_content);
			} else { 
				array_push($output['list'], NSP_GK5_json_file_View::lists($config, $content[$i], $counter));
				//
				$counter++;
			}                    
		}
		// return the results array
		return $output;
	}
}

// EOF
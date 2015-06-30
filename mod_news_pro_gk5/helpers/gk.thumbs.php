<?php

/**
* Helper class for generating thumbnails
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.5 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

/*
	This class uses options of module:
	- cache time
	- quality
	- image width
	- image height
	- background color
	- image stretch
*/

class NSP_GK5_Thumbs {
	/*
		function to change file path to filename.
		For example:
		./images/stories/demo.jpg
		will be translated to:
		stories.demo.jpg
		(in this situation mirror of ./images/ directory isn't necessary)
	*/
	static function translateName($name,$mod_id, $k2_mode = false, $vm_mode = false, $image_type = '', $downloaded = false, $filename = null, $links = false, $hikashop_mode = false, $solidres_mode = false) {
		// check the mode
		if($downloaded || stripos($name, 'http://') !== FALSE || stripos($name, 'https://') !== FALSE) {
			if($downloaded) {
				$name = 'downloaded.' . $filename;
				$ext = substr($name, -4);
				$name = substr($name, 0, -4);
				return $name . $mod_id . ($links ? '_links' : '') . $ext; 
			} else {
				$slashpos = strrpos($name, '/');
				$filename = substr($name, $slashpos + 1);
				$name = 'downloaded.' . $filename;
				$ext = substr($name, -4);
				$name = substr($name, 0, -4);
				return $name . $mod_id . ($links ? '_links' : '') . $ext;
			}
		} else {
			$name = NSP_GK5_Thumbs::getRealPath($name, $k2_mode, $vm_mode, $hikashop_mode);
			$start = ($k2_mode || $vm_mode || $hikashop_mode || $solidres_mode) ? (($k2_mode || $hikashop_mode || $solidres_mode) ? strpos($name, DS.'media'.DS) : strpos($name, DS.'components'.DS)) : strpos($name, DS.'images'.DS);
			$name = ($k2_mode || $vm_mode) ? (($k2_mode) ? substr($name, $start+7) : substr($name, $start+12)) : substr($name, $start+8);
			$ext = substr($name, -4);
			$name = substr($name, 0, -4);
			$name = str_replace(DS,'.',$name);
			$name .= $mod_id . $image_type . ($links ? '_links' : '') . $ext;
			return $name;
		}
	}
	// the same function for the output
	/*function translateNameOutput($name,$mod_id, $k2_mode = false, $vm_mode = false, $image_type = '') {
		$name = NSP_GK5_Thumbs::getRealPath($name, $k2_mode, $vm_mode);
		$start = ($k2_mode || $vm_mode) ? (($k2_mode) ? strpos($name, DS.'media'.DS) : strpos($name, DS.'components'.DS)) : strpos($name, DS.'images'.DS);
		$name = ($k2_mode || $vm_mode) ? (($k2_mode) ? substr($name, $start+7) : substr($name, $start+12)) : substr($name, $start+8);
		$ext = substr($name, -4);
		$name = substr($name, 0, -4);
		$name = str_replace(DS,'.',$name);
		$name .= $mod_id.$image_type.$ext;
		$special = NSP_GK5_Thumbs::checkSpecialImages($name);
	
		if($special > 0) {
			if($special == 1) {
				$name = substr($name, 0, -4);
				$name = str_replace(DS,'.',$name);
				$name .= '_cropped'.$ext;
			} 
			
			if($special == 2) {
				$name = substr($name, 0, -4);
				$name = str_replace(DS,'.',$name);
				$name .= '_noscale'.$ext;
			}
		}
		
		return $name;
	}*/

	// function used to get the custom media path
	static function getMediaPath() {
        $imagemanager = JComponentHelper::getParams('com_media');
  		$imagepath = $imagemanager->get('image_path', '');
  		return $imagepath;
    }

	// function to change file path to  real path.
	static function getRealPath($path, $k2_mode = false, $vm_mode = false, $hikashop_mode = false, $solidres_mode = false) {		
		$start = ($k2_mode || $vm_mode || $hikashop_mode || $solidres_mode) ? (($k2_mode || $hikashop_mode || $solidres_mode) ? strpos($path, 'media/') : strpos($path, 'components/')) : strpos($path, self::getMediaPath());
		$path = './'.substr($path, $start);

		return realpath($path);
	}
	/*
		function to check cache
		this function checks if file exists in cache directory
		and checks if time of file life isn't too long
	*/
	static function checkCache($filename, $cache_time) {
		if($cache_time === FALSE) {
			$cache_time = 100 * 365 * 24 * 60 * 60;
		}

		$cache_dir = JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS;
		$file = $cache_dir.$filename;
		return (!is_file($file) || $cache_time == 0) ? FALSE : (filemtime($file) + 60 * $cache_time > time());
	}
	/*
        function to check if _cropped / _noscale images exists
        
        return: 
            0 - when any images exist
            1 - when _cropped image exists
            2 - when _noscale image exists
    */
    static function checkSpecialImages($path) {
        $cache_dir = JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS;
        // generate the names
        $ext = substr($path, -4);
        $path = substr($path, 0, -4);
        $path = str_replace(DS,'.',$path); 
        $cropped_path = $path . '_cropped' . $ext;
        $noscale_path = $path . '_noscale' . $ext;
        // check if the specified files exists
        if(is_file($cache_dir . $cropped_path)) {
        	return 1;
        } else if(is_file($cache_dir . $noscale_path)) {
        	return 2;
        } else {
        	return 0;
        }
    }
	// Creating thumbnails
	static function createThumbnail(
		$path, 
		$config, 
		$k2_mode = false, 
		$vm_mode = false, 
		$img_rel = '', 
		$links = false, 
		$hikashop_mode = false,
		$solidres_mode = false
	) {
		if($config['use_curl_download'] == 0 && (stripos($path, 'http://') || stripos($path, 'https://'))) {
			return false;
		}
		// importing classes
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		//script configuration - increase memory limit to selected value
		ini_set('memory_limit', $config['memory_limit']);
		// cache dir
		$cache_dir = JPATH_ROOT.DS.'modules'.DS.'mod_news_pro_gk5'.DS.'cache'.DS;
		// downloaded and filename variables 
		$downloaded = false;
		$imgname = '';
		// check if the file is external
		if(strpos($path,'http://') !== FALSE || strpos($path,'https://') !== FALSE) {
			// check if the file was downloaded
			$imgname = '';
			$slashpos = strrpos($path, '/');
			$imgname = substr($path, $slashpos + 1);
			// check if the file exists
			if(!file_exists(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache' . DS . 'downloaded' . DS . $imgname)) {
				// if not - download the file
				if(function_exists('curl_init')) {
					// initializing connection
					$curl = curl_init();
					// saves us before putting directly results of request
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
					// check the source of request
				    curl_setopt($curl, CURLOPT_URL, $path);
					// timeout in seconds
					curl_setopt($curl, CURLOPT_TIMEOUT, 20);
					// useragent
					curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
					// reading content
					$img_content = curl_exec($curl);
					// closing connection
					curl_close($curl);
					// check the results (curl_exec return FALSE on failure) and its length
					if($img_content !== FALSE && strlen($img_content) > 0) {
						// save the downloaded image
						$fp = fopen(JPATH_ROOT . DS . 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache' . DS . 'downloaded' . DS . $imgname, 'x');
						fwrite($fp, $img_content);
						fclose($fp);
						// change the path to the downloaded file	
						$path = 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache' . DS . 'downloaded' . DS . $imgname;
						$downloaded = true;
					} else {
						return false;
					}
				} else {
					return false;
				} 
			} else {
				$path = 'modules' . DS . 'mod_news_pro_gk5' . DS . 'cache' . DS . 'downloaded' . DS . $imgname;
				$downloaded = true;
			}
		}
		// checking the special images
		$check_result = NSP_GK5_Thumbs::checkSpecialImages(NSP_GK5_Thumbs::translateName($path,$config['module_id'], $k2_mode, $vm_mode, '', $downloaded, $imgname, $links, $hikashop_mode, $solidres_mode));
		// preparing an array with the image class values
		$img_rels = array();
		// check if any classes exists in the image
		if($img_rel !== '') {
			$img_rels = explode(' ', $img_rel);
		}
		// no scale images
		if($check_result == 2) { // NOSCALE      
			if(NSP_GK5_Thumbs::checkCache(NSP_GK5_Thumbs::translateName($path,$config['module_id'], $k2_mode, $vm_mode, '_noscale', $downloaded, $imgname, $links), false, $config['module_id'], $hikashop_mode, $solidres_mode)){  
				return array(TRUE, NSP_GK5_Thumbs::translateName($path,$config['module_id'], $k2_mode, $vm_mode, '_noscale', $downloaded, $imgname, $links, $hikashop_mode, $solidres_mode));	
			} else {
				// file path
				$file = NSP_GK5_Thumbs::getRealPath($path, $k2_mode, $vm_mode, '_noscale', $hikashop_mode, $solidres_mode);
				// filename
				$filename = NSP_GK5_Thumbs::translateName($path,$config['module_id'], $k2_mode, $vm_mode, '_noscale', $downloaded, $imgname, $links, $hikashop_mode, $solidres_mode);
				// Getting informations about image
				if(is_file($file)){
					$imageData = getimagesize($file);
					$img_w = str_replace('px','',str_replace('%','', $links ? $config['links_img_width'] : $config['img_width']));
					$img_h = str_replace('px','',str_replace('%','', $links ? $config['links_img_height'] : $config['img_height']));
					// loading image depends from type of image		
					if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') $imageSource = @imagecreatefromjpeg($file);
					elseif($imageData['mime'] == 'image/gif') $imageSource = @imagecreatefromgif($file);
					else $imageSource = @imagecreatefrompng($file); 
					// here can be exist an error when image is to big - then class return blank page	
					// setting image size in variables
					$imageSourceWidth = imagesx($imageSource);
					$imageSourceHeight = imagesy($imageSource);
				    $imageBG = imagecreatetruecolor($img_w, $img_h);
					// If image is JPG or GIF
					if($imageData['mime'] == 'image/png') {	
				        $imageBG = imagecreatetruecolor($img_w, $img_h);
						// enable transparent background 
						if($config['img_bg'] == 'transparent'){
							// create transparent color
							$rgb = imagecolorallocatealpha($imageBG, 0, 0, 0, 127);
						} else {// create normal color
							$bg = $config['img_bg'];
							// translate hex to RGB
							$hex_color = strtolower(trim($bg,'#;&Hh'));
				  			$bg = array_map('hexdec',explode('.',wordwrap($hex_color, ceil(strlen($hex_color)/3),'.',1)));
							// creating color
							$rgb = imagecolorallocate($imageBG, $bg[0], $bg[1], $bg[2]);
						}
						// filling the canvas
						imagefill($imageBG, 0, 0, $rgb);
						// enabling transparent settings for better quality
						imagealphablending($imageBG, false);
						imagesavealpha($imageBG, true);
					}
					//
					$imageSourceNWidth = $img_w;
					$imageSourceNHeight = $img_h;
					$base_x = 0;
					$base_y = 0;
					// copy image	
					imagecopyresampled($imageBG, $imageSource, $base_x, $base_y, 0, 0, $imageSourceNWidth, $imageSourceNHeight, $imageSourceWidth, $imageSourceHeight);
					// save image depends from MIME type	
					if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') imagejpeg($imageBG,$cache_dir.$filename, $config['img_quality']);
					elseif($imageData['mime'] == 'image/gif') imagegif($imageBG, $cache_dir.$filename); 
					else imagepng($imageBG, $cache_dir.$filename);
					// result
					return array(TRUE, $filename);
				} else {
					return FALSE;
				}	
			}
		} else { // cropped or normal image
			if(
				NSP_GK5_Thumbs::checkCache(
					NSP_GK5_Thumbs::translateName(
						$path,
						$config['module_id'], 
						$k2_mode, 
						$vm_mode, 
						($check_result == 1) ? '_cropped' : '', 
						$downloaded, 
						$imgname,
						$links,
						$hikashop_mode,
						$solidres_mode
					), 
					isset($config['thumbs_cache_time']) ? $config['thumbs_cache_time'] : '60', 
					$config['module_id']
				)){
				return array(TRUE, NSP_GK5_Thumbs::translateName($path, $config['module_id'], $k2_mode, $vm_mode, ($check_result == 1) ? '_cropped' : '', $downloaded, $imgname, $links, $hikashop_mode, $solidres_mode));	
			} else {
				// file path
				$file = NSP_GK5_Thumbs::getRealPath($path, $k2_mode, $vm_mode, ($check_result == 1) ? '_cropped' : '', $hikashop_mode, $solidres_mode);
				
				// filename
				$filename = NSP_GK5_Thumbs::translateName($path,$config['module_id'], $k2_mode, $vm_mode, ($check_result == 1) ? '_cropped' : '', $downloaded, $imgname, $links, $hikashop_mode, $solidres_mode);
				
				// Getting informations about image
				if(is_file($file)){					
					$imageData = getimagesize($file);
					$img_w = str_replace('px','',str_replace('%','', $links ? $config['links_img_width'] : $config['img_width']));
					$img_h = str_replace('px','',str_replace('%','',$links ? $config['links_img_height'] : $config['img_height']));
					// loading image depends from type of image		
					if(
						$imageData['mime'] == 'image/jpeg' || 
						$imageData['mime'] == 'image/pjpeg' || 
						$imageData['mime'] == 'image/jpg'
					) {
						$imageSource = @imagecreatefromjpeg($file);
					} elseif ($imageData['mime'] == 'image/gif') {
						$imageSource = @imagecreatefromgif($file);
					} else {
						$imageSource = @imagecreatefrompng($file); 
					}
					
					// check if the proper image resource was created
					if(!$imageSource) return FALSE;
					
					// here can be exist an error when image is to big - then class return blank page	
					// setting image size in variables
					$imageSourceWidth = imagesx($imageSource);
					$imageSourceHeight = imagesy($imageSource);
					// variables for cropping
					$top_crop = floor(($imageSourceHeight * $config['simple_crop_top']) / 100);
					$bottom_crop = floor(($imageSourceHeight * $config['simple_crop_bottom']) / 100);
					$left_crop = floor(($imageSourceWidth * $config['simple_crop_left']) / 100);
					$right_crop = floor(($imageSourceWidth * $config['simple_crop_right']) / 100);
					// get the cropping rules
					foreach($config['crop_rules'] as $rule) {
						if(is_string($rule['type']) && count($img_rels)) {
							foreach($img_rels as $rel) {
								if($rule['type'] == $rel) {
									$top_crop = $rule['top'];
									$bottom_crop = $rule['bottom'];
									$left_crop = $rule['left'];
									$right_crop = $rule['right'];
									break;
								}
							}
						} else {
							if( $rule['type']['width'] == $imageSourceWidth && 
								$rule['type']['height'] == $imageSourceHeight) {
								$top_crop = $rule['top'];
								$bottom_crop = $rule['bottom'];
								$left_crop = $rule['left'];
								$right_crop = $rule['right'];
								break;
							}
						}
					}
                    // Creating blank canvas
					if($config['img_keep_aspect_ratio']) {
			            // calculate ratio for first scaling
						$ratio = ($imageSourceWidth > $imageSourceHeight) ? $img_w / $imageSourceWidth : $img_h / $imageSourceHeight;
						// calculate new image size
						$imageSourceNWidth = ($imageSourceWidth - ($left_crop + $right_crop)) * $ratio;
						$imageSourceNHeight = ($imageSourceHeight - ($top_crop + $bottom_crop)) * $ratio;
						// calculate ratio for second scaling
						if($img_w > $img_h){					
							if($imageSourceNHeight > $img_h){
								$ratio2 = $img_h / $imageSourceNHeight;
								$imageSourceNHeight *= $ratio2;
								$imageSourceNWidth *= $ratio2;
							}
						} else {
							if($imageSourceNWidth > $img_w){
								$ratio2 = $img_w / $imageSourceNWidth;
								$imageSourceNHeight *= $ratio2;
								$imageSourceNWidth *= $ratio2;
							}
						}

						$img_w = $imageSourceNWidth;
						$img_h = $imageSourceNHeight;
					}
			        $imageBG = imagecreatetruecolor($img_w, $img_h);
					// If image is JPG or GIF
					if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg' || $imageData['mime'] == 'image/gif') {
						// when bg is set to transparent - use black background
						if($config['img_bg'] == 'transparent'){
							$bgColorR = 0;
							$bgColorG = 0;
							$bgColorB = 0;				
						} else { // in other situation - translate hex to RGB
							$bg = $config['img_bg'];
							if(strlen($bg) == 4) $bg = $bg[0].$bg[1].$bg[1].$bg[2].$bg[2].$bg[3].$bg[3];
							$hex_color = strtolower(trim($bg,'#;&Hh'));
				  			$bg = array_map('hexdec',explode('.',wordwrap($hex_color, ceil(strlen($hex_color)/3),'.',1)));
							$bgColorR = $bg[0];
							$bgColorG = $bg[1];
							$bgColorB = $bg[2];
						}
						// Creating color
						$rgb = imagecolorallocate($imageBG, $bgColorR, $bgColorG, $bgColorB);
						// filling canvas with new color
						imagefill($imageBG, 0, 0, $rgb);	
					} else {// for PNG images	
						if($config['img_keep_aspect_ratio']) {
			                // calculate ratio for first scaling
							$ratio = ($imageSourceWidth > $imageSourceHeight) ? $img_w / $imageSourceWidth : $img_h / $imageSourceHeight;
							// calculate new image size
							$imageSourceNWidth = ($imageSourceWidth - ($left_crop + $right_crop)) * $ratio;
							$imageSourceNHeight = ($imageSourceHeight - ($top_crop + $bottom_crop)) * $ratio;
							// calculate ratio for second scaling
							if($img_w > $img_h){					
								if($imageSourceNHeight > $img_h){
									$ratio2 = $img_h / $imageSourceNHeight;
									$imageSourceNHeight *= $ratio2;
									$imageSourceNWidth *= $ratio2;
								}
							} else {
								if($imageSourceNWidth > $img_w){
									$ratio2 = $img_w / $imageSourceNWidth;
									$imageSourceNHeight *= $ratio2;
									$imageSourceNWidth *= $ratio2;
								}
							}
							$img_w = $imageSourceNWidth;
							$img_h = $imageSourceNHeight;
			            }

			            $imageBG = imagecreatetruecolor($img_w, $img_h);
						// enable transparent background 
						if($config['img_bg'] == 'transparent'){
							// create transparent color
							$rgb = imagecolorallocatealpha($imageBG, 0, 0, 0, 127);
						} else {// create normal color
							$bg = $config['img_bg'];
							// translate hex to RGB
							$hex_color = strtolower(trim($bg,'#;&Hh'));
				  			$bg = array_map('hexdec',explode('.',wordwrap($hex_color, ceil(strlen($hex_color)/3),'.',1)));
							// creating color
							$rgb = imagecolorallocate($imageBG, $bg[0], $bg[1], $bg[2]);
						}
						// filling the canvas
						imagefill($imageBG, 0, 0, $rgb);
						// enabling transparent settings for better quality
						imagealphablending($imageBG, false);
						imagesavealpha($imageBG, true);
					}
					// when stretching is disabled		
					if(!$config['img_stretch']){
						if($config['img_keep_aspect_ratio']) {
						   $base_x = 0;
						   $base_y = 0;
						   $imageSourceNWidth = $img_w;
						   $imageSourceNHeight = $img_h;
						} else {
							// if image auto-scale is enabled
							if($config['img_auto_scale'] && !$config['img_keep_aspect_ratio']) {
								$img_ratio = $imageSourceWidth / $imageSourceHeight;
								$thumb_ratio = $img_w / $img_h;
								// base variables
								$imageSourceNWidth = $img_w;
								$imageSourceNHeight = $img_h;
								$base_x = 0;
								$base_y = 0;
								// if img_ratio == thumb_ratio - no scale and cuts
								$left_crop = 0;
								$top_crop = 0;
								// two other cases
								if($img_ratio > $thumb_ratio) { // when source is wider than destination
									$ratio_h = $imageSourceHeight / $img_h;
									$new_w = $img_w * $ratio_h;
									$left_crop = floor(($imageSourceWidth - $new_w) / 2);
									// top_crop still 0
								} else if($img_ratio < $thumb_ratio) { // when source is higher than destination
									$ratio_w = $imageSourceWidth / $img_w;
									$new_h = $img_h * $ratio_w;
									$top_crop = floor(($imageSourceHeight - $new_h) / 2);
									// left_crop still 
								}
								// equal two other crop params
								$right_crop = $left_crop;
								$bottom_crop = $top_crop;
							} else { // if not - left unused areas of the image
				                // calculate ratio for first scaling
								$ratio = (($imageSourceWidth - ($left_crop + $right_crop)) > ($imageSourceHeight - ($top_crop + $bottom_crop))) ? $img_w/($imageSourceWidth - ($left_crop + $right_crop)) : $img_h/($imageSourceHeight - ($top_crop + $bottom_crop));
								// calculate new image size
								$imageSourceNWidth = ($imageSourceWidth - ($left_crop + $right_crop)) * $ratio;
								$imageSourceNHeight = ($imageSourceHeight - ($top_crop + $bottom_crop)) * $ratio;
								// calculate ratio for second scaling
								if($img_w > $img_h){					
									if($imageSourceNHeight > $img_h){
										$ratio2 = $img_h / $imageSourceNHeight;
										$imageSourceNHeight *= $ratio2;
										$imageSourceNWidth *= $ratio2;
									}
								}else{
									if($imageSourceNWidth > $img_w){
										$ratio2 = $img_w / $imageSourceNWidth;
										$imageSourceNHeight *= $ratio2;
										$imageSourceNWidth *= $ratio2;
								    }
								}
								// setting position of putting thumbnail on canvas
								$base_x = floor(($img_w - $imageSourceNWidth) / 2);
								$base_y = floor(($img_h - $imageSourceNHeight) / 2);
                        	}						
                    	}
					} else { // when stretching is enable
						$imageSourceNWidth = $img_w;
						$imageSourceNHeight = $img_h;
						$base_x = 0;
						$base_y = 0;
					}
					// copy image	
					imagecopyresampled(
										$imageBG, 
										$imageSource, 
										$base_x, 
										$base_y, 
										$left_crop, 
										$top_crop, 
										$imageSourceNWidth, 
										$imageSourceNHeight, 
										$imageSourceWidth - ($left_crop + $right_crop), 
										$imageSourceHeight - ($top_crop + $bottom_crop)
									);
					//
					// applying filters
					//

					// grayscale
					if($config['grayscale_filter'] || $config['sepia_filter']) {
						imagefilter($imageBG, IMG_FILTER_GRAYSCALE); 
					}
					// sepia
					if($config['sepia_filter']) {
						imagefilter($imageBG, IMG_FILTER_COLORIZE, 90, 60, 40); 
					}
					// blur
					if($config['blur_filter']) {
						imagefilter($imageBG, IMG_FILTER_GAUSSIAN_BLUR, $config['filter_arg']); 
					}
					// brightness
					if($config['brightness_filter']) {
						imagefilter($imageBG, IMG_FILTER_BRIGHTNESS, $config['filter_arg']); 
					}
					// smooth
					if($config['smooth_filter']) {
						imagefilter($imageBG, IMG_FILTER_SMOOTH, $config['filter_arg']); 
					}
					// pixelate
					if($config['pixelate_filter']) {
						imagefilter($imageBG, IMG_FILTER_PIXELATE, $config['filter_arg'], true); 
					}
					// contrast
					if($config['contrast_filter']) {
						imagefilter($imageBG, IMG_FILTER_CONTRAST, $config['filter_arg']); 
					}

					// save image depends from MIME type	
					if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') imagejpeg($imageBG,$cache_dir.$filename, $config['img_quality']);
					elseif($imageData['mime'] == 'image/gif') imagegif($imageBG, $cache_dir.$filename); 
					else imagepng($imageBG, $cache_dir.$filename);
					// result
					return array(TRUE, $filename);
				} else {
					return FALSE;
				}	
			}
		}
	}	
}

// EOF

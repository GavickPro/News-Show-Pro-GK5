<?php

/**
* Product Gallery
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

class NSP_GK5_Product_Gallery {
	// necessary class fields
	private $parent;
	private $mode;
	static $instances = 0;
	// constructor
	function __construct($parent) {
		$this->parent = $parent;
		NSP_GK5_Product_Gallery::$instances += 1;	
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
		
		// Load path constant
        	if(!defined('VMPATH_ADMIN')) {
        		define('VMPATH_ADMIN', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart');
        	}
        	
		// Load VM configuration if necessary
        	if (!class_exists( 'VmConfig' )) {
        		require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
        		VmConfig::loadConfig();
        	}
		
		//
		if(NSP_GK5_Product_Gallery::$instances == 1) {
			$closeimage = JURI::root(TRUE) .'/components/com_virtuemart/assets/images/fancybox/fancy_close.png';
			$vmLangVar = '';
			
			if (VmConfig::get ('vmlang_js', 1))  {
				$vmLangVar .= "vmLang = '&lang=" . substr (VmConfig::$vmlang, 0, 2) . "' ;\n";
			} else {
				$vmLangVar .= 'vmLang = "";' . "\n";		
			}
			
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration(
				$vmLangVar . '
				vmSiteurl = \''. JURI::root() .'\' ;
				window.Itemid = \'\';
				Virtuemart.addtocart_popup = \''.VmConfig::get('addtocart_popup',1).'\' ; 
				vmCartText = \''.addslashes(vmText::_('COM_VIRTUEMART_CART_PRODUCT_ADDED')).'\' ;
				vmCartError = \''.addslashes(vmText::_('COM_VIRTUEMART_MINICART_ERROR_JS')).'\' ;
				loadingImage = \''.JURI::root(TRUE) .'/components/com_virtuemart/assets/images/facebox/loading.gif\' ;
				closeImage = \''.$closeimage.'\' ; 
				usefancy = true;
				jQuery(document).ready(function() { Virtuemart.product(jQuery("form.product")); });'
			);
		}
	}
	// static function which returns amount of articles to render - VERY IMPORTANT!!
	static function amount_of_articles($parent) {
		return $parent->config['portal_mode_product_gallery_amount'];
	}
	// output generator	
	function output() {
		// amount
		$amount = 0;
		// count
		for($i = 0; $i < count($this->parent->content); $i++) {			
			if($this->get_image($i)) {
				$amount++;
			}
		}
		// pagination
		$pagination = 0;
		
		if($amount > $this->parent->config['portal_mode_product_gallery_cols'] && $this->parent->config['portal_mode_product_gallery_nav'] == '1') {
			$pagination = 1;
		}	
		// main wrapper
		echo '<div class="gkNspPM gkNspPM-ProductGallery'.(($this->parent->config['portal_mode_product_gallery_autoanimation'] == 1) ? ' gkAutoAnimation' : '') . (($pagination) ? ' gkPagination' : '') . '" data-cols="'.$this->parent->config['portal_mode_product_gallery_cols'].'" data-autoanim-time="'.$this->parent->config['portal_mode_product_gallery_autoanimation_time'].'">';
		
		if(trim($this->parent->config['nsp_pre_text'])) {
			echo $this->parent->config['nsp_pre_text'];
		}
		
		// images wrapper
		echo '<div class="gkImagesWrapper gkImagesCols'.$this->parent->config['portal_mode_product_gallery_cols'].'">';
		// render images
		$img_iterator = 0;
		
		for($i = 0; $i < count($this->parent->content); $i++) {			
			if($this->get_image($i)) {
				echo '<div class="gkImage show '.(($img_iterator + 1 <= $this->parent->config['portal_mode_product_gallery_cols']) ? ' active' : ''). '">';
				echo '<a href="' . $this->get_link($i) . '"><img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags($this->parent->content[$i]['title']).'" /></a>';
				echo '<h4><a href="' . $this->get_link($i) . '">' . $this->parent->content[$i]['title'] . '</a></h4>';
				
				$store_output = $this->get_store($this->parent->config, $this->parent->content[$i]['id'], $i);

				if($this->mode != 'com_virtuemart') {
					echo '<div class="gkAddToCart"><a class="addtocart-button" href="' . $this->get_link($i) . '">' . JText::_('MOD_NEWS_PRO_GK5_NSP_READMORE') . '</a></div>';
				} else {
					echo '<div class="gkPrice '.($store_output['price'] == '' ? 'gkPriceEmpty' : '').'">' . $store_output['price'] . '</div>';
					echo '<div class="gkAddToCart">' . $store_output['cart'] . '</div>';
					echo '<div class="gkImgOverlay '.($store_output['price'] == '' ? 'gkImgOverlayEmpty' : '').'">' . $store_output['price'] . '</div>';
				}

				if($this->parent->content[$i]['featured'] && $this->parent->config['vm_show_featured_badge']) {
					echo '<sup class="nspBadge">'.JText::_('MOD_NEWS_PRO_GK5_NSP_FEATURED').'</sup>';
				}

				$img_iterator++;

				echo '</div>';
			}		
		}
		// closing images wrapper
		echo '</div>';
		// pagination buttons
		if($amount > $this->parent->config['portal_mode_product_gallery_cols'] && $this->parent->config['portal_mode_product_gallery_nav'] == '1') {
			echo '<a href="#prev" class="gkPrevBtn">&laquo;</a>';
			echo '<a href="#next" class="gkNextBtn">&raquo;</a>';
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
		} else if($this->mode == 'com_virtuemart') {
			$itemid = $this->parent->config['vm_itemid'];
			$link = 'index.php?option=com_virtuemart&amp;view=productdetails&amp;virtuemart_product_id='.$this->parent->content[$num]['id'].'&amp;virtuemart_category_id='.$this->parent->content[$num]['cid'].'&amp;Itemid='.$itemid;
			
			return $link;
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
		} else if($this->mode == 'com_virtuemart') {
			// load necessary com_content View class
			if(!class_exists('NSP_GK5_com_virtuemart_View')) {
				require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'com_virtuemart/view'));
			}
			// generate the com_content image URL only
			$url = NSP_GK5_com_virtuemart_View::image($this->parent->config, $this->parent->content[$num], true, true);
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
	// store generator
	// function used to show the store details
	function get_store($config, $id, $num) {
	    if($this->mode != 'com_virtuemart') {
	    	return array(
	    		"price" => '',
	    		"cart" => ''
	    	);
		} else {
			// Load the language file of com_virtuemart.
	        JFactory::getLanguage()->load('com_virtuemart');
	       
	        // Load path constant
	        if(!defined('VMPATH_ADMIN')) {
	        	define('VMPATH_ADMIN', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart');
	        }
	        // Load VM configuration if necessary
	        if (!class_exists( 'VmConfig' )) {
	        	require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
	        	VmConfig::loadConfig();
	        }
	        
	        // load necessary classes
	        if (!class_exists( 'calculationHelper' )) {
	        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');
	        }
	        if (!class_exists( 'CurrencyDisplay' )) {
	        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
	        }
	        if (!class_exists( 'VirtueMartModelVendor' )) {
	        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'vendor.php');
	        }
	        if (!class_exists( 'VmImage' )) {
	        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
	        }
	        if (!class_exists( 'shopFunctionsF' )) {
	        	require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
	        }
	        if (!class_exists( 'calculationHelper' )) {
	        	require(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'cart.php');
	        }
	        if (!class_exists( 'VirtueMartModelProduct' )){
	        	JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
	        }
	        // load the base
	        $productModel = new VirtueMartModelProduct();
		    $product = $productModel->getProduct($id, 100, true, true, true);
		    $currency = CurrencyDisplay::getInstance();
		    
		    $price = '<strong>'.$currency->createPriceDiv($config['vm_show_price_type'], '', $product->prices, true).'</strong>';
	       
	        if($config['vm_add_to_cart'] == 1 && JRequest::getCmd('option') != 'com_virtuemart') {
	            vmJsApi::jPrice();
	            vmJsApi::addJScript( 'facebox' );
				vmJsApi::css( 'facebox' );
	            vmJsApi::writeJS();
	        }
	        $news_price = '';
	        //
	        if($config['vm_show_price_type'] != 'none') {	            
	            if($config['vm_display_type'] == 'text_price') {
	            	$news_price .=  '<div class="PricebasePriceWithTax">'.JText::_('MOD_NEWS_PRO_GK5_PRODUCT_PRICE').' '.$price.'</div>';
	            } else {
	            	$news_price .= '<div class="PricebasePriceWithTax">'.$price.'</div>';
	            }
	            
	            $news_price = str_replace('<strong>', '<span class="PricebasePriceWithTax">', $news_price);
	            $news_price = str_replace('</strong>', '</span>', $news_price);
	        }
	        // display discount
	        if($config['vm_show_discount_amount'] == 1) {
	            $disc_amount = $currency->priceDisplay($product->prices['discountAmount'], $currency->getId());
	            $news_price.= '<div class="PricetaxAmount">' . JText::_('MOD_NEWS_PRO_GK5_PRODUCT_DISCOUNT_AMOUNT'). $disc_amount . '</div>';
	        }
			// display tax
	        if($config['vm_show_tax'] == 1) {
	          	$taxAmount = $currency->priceDisplay($product->prices['taxAmount'], $currency->getId());
	            $news_price.= '<div class="PricetaxAmount">' . JText::_('MOD_NEWS_PRO_GK5_PRODUCT_TAX_AMOUNT'). $taxAmount . '</div>';  
	        }
	        // detect empty price
	        if(count($product->prices) === 0) {
	        	$news_price = '';
	        }
	        // 'Add to cart' button
	        if($config['vm_add_to_cart'] == 1) {
	            if(isset($product->customfields) &&count($product->customfields)) {
	            	foreach($product->customfields as $field) {
	            		if(
	            			(isset($field->is_cart_attribute) && $field->is_cart_attribute == 1) ||
	            			(isset($field->layout_pos) && $field->layout_pos == 'addtocart')
	            		) {
	            			$product->orderable = 0;
	            			break;
	            		}
	            	}
	            }
	            
	            $code = '<div class="addtocart-area">';
	            
	            if($product->orderable != 0) {
	            	$code .= '<form method="post" class="product" action="index.php">';
	            } else {
	            	$code .= '<form method="post" class="product-variant" action="'.$this->get_link($num).'">';
	            }
	            
	            $code .= '<div class="addtocart-bar">';
	            $code .= '<span class="quantity-box" style="display: none"><input type="text" class="quantity-input" name="quantity[]" value="1" /></span>';
	            $addtoCartButton = '';
				if($product->addToCartButton){
					$addtoCartButton = $product->addToCartButton;
				} else {
					$addtoCartButton = shopFunctionsF::getAddToCartButton($product->orderable);
				}
	            $btn = str_replace('addtocart-button-disabled"', 'addtocart-button" type="submit"', $addtoCartButton);
	
	            if(stripos($btn, '<span') !== FALSE) {
	            	$btn = str_replace('title=', 'value=', $btn);
	            	$btn = str_replace('<span', '<input', $btn);
	            	$btn = preg_replace('@>.*?</span>@mis', '/>', $btn);
	            }
	
	            $code .= $btn;
	
	            if($product->orderable != 0) { 
		            $code .= '</div>
		                    <input type="hidden" name="pname" value="'.$product->product_name.'"/>
		                    <input type="hidden" name="option" value="com_virtuemart" />
		                    <input type="hidden" name="view" value="cart" />
		                    <noscript><input type="hidden" name="task" value="add" /></noscript>
		                    <input type="hidden" name="virtuemart_product_id[]" value="'.$product->virtuemart_product_id.'" />
		                    <input type="hidden" name="pid" value="'.$product->virtuemart_product_id.'" />
		                    <input type="hidden" name="virtuemart_category_id[]" value="'.$product->virtuemart_category_id.'" />
		                </form>';   
	            } else {
	            	$code .= '</div></form>';  
	            } 
	            $code .= '</div>'; 
			}
	        
			// results
			return array(
				"price" => $news_price,
				"cart" => $code
			);
		}
	}
}

// EOF

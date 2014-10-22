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
	// constructor
	function __construct($parent) {
		$this->parent = $parent;
		// detect the supported Data Sources
		if(stripos($this->parent->config['data_source'], 'com_virtuemart_') !== FALSE) {
			$this->mode = 'com_virtuemart';
		} else {
			$this->mode = false;
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
		// images wrapper
		echo '<div class="gkImagesWrapper gkImagesCols'.$this->parent->config['portal_mode_product_gallery_cols'].'">';
		// render images
		for($i = 0; $i < count($this->parent->content); $i++) {			
			if($this->get_image($i)) {
				echo '<div class="gkImage show '.(($i+1 <= $this->parent->config['portal_mode_product_gallery_cols']) ? ' active' : ''). '">';
				echo '<a href="' . $this->get_link($i) . '"><img src="'.strip_tags($this->get_image($i)).'" alt="'.strip_tags($this->parent->content[$i]['title']).'" /></a>';
				echo '<h4><a href="' . $this->get_link($i) . '">' . $this->parent->content[$i]['title'] . '</a></h4>';
				
				$store_output = $this->get_store($this->parent->config, $this->parent->content[$i]['id']);
				echo '<div class="gkPrice">' . $store_output['price'] . '</div>';
				echo '<div class="gkAddToCart">' . $store_output['cart'] . '</div>';
				echo '<div class="gkImgOverlay">' . $store_output['price'] . '</div>';
				if($this->parent->content[$i]['featured'] && $this->parent->config['vm_show_featured_badge']) {
					echo '<sup class="nspBadge">'.JText::_('MOD_NEWS_PRO_GK5_NSP_FEATURED').'</sup>';
				}
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
		// IE8 fix
		echo '<!--[if IE 8]><div class="ie8clear"></div><![endif]-->';
		// closing main wrapper
		echo '</div>';
	}
	// function used to retrieve the item URL
	function get_link($num) {
		if($this->mode == 'com_virtuemart') {
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
		if($this->mode == 'com_virtuemart') {
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
	function get_store($config, $id) {
		// if the VM is available
        if (!class_exists( 'VmConfig' )) {
        	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
        }
        VmConfig::loadConfig();
        // Load the language file of com_virtuemart.
        JFactory::getLanguage()->load('com_virtuemart');
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
        $mainframe = JFactory::getApplication();
        $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id',0) );
        $currency = CurrencyDisplay::getInstance();
        $cSymbol = $currency->getSymbol();
        $cDecimals = $currency->getNbrDecimals();
        $cDecSymbol = $currency->getDecimalSymbol();
        //
        $productModel = new VirtueMartModelProduct();
	    $product = $productModel->getProduct($id, 100, true, true, true);
	    $price = strip_tags($currency->createPriceDiv ($config['vm_show_price_type'], '', $product->prices));
	    // remove currency 
	    $price = str_replace($cSymbol, '', $price);
	    // prepare price - apply correct format and decimal separator
	    $price = str_replace('.',$cDecSymbol, $price);
	    if($config['vm_currency_position'] == 'before') { 
	    	$price = $cSymbol.' '.$price;
	    } else {
	    	$price = $price.' '.$cSymbol;
	    }
	    //
        if($config['vm_add_to_cart'] == 1) {
            vmJsApi::jQuery();
            vmJsApi::jPrice();
        }
        $news_price = '<div class="PricebasePriceWithTax">';
        //
        if($config['vm_show_price_type'] != 'none') {
            if($config['vm_display_type'] == 'text_price') {
            	$news_price .=  '<span class="PricebasePriceWithTax">'.JText::_('MOD_NEWS_PRO_GK5_PRODUCT_PRICE').' '.$price.'</span>';
            } else {
            	$news_price .= '<span class="PricebasePriceWithTax">'.$price.'</span>';
            }
        } 
        $news_price .= '</div>';
       	// display discount
        if($config['vm_show_discount_amount'] == 1) {
        	$news_price .= '<div class="PricetaxAmount">';
            $disc_amount = str_replace('.',$cDecSymbol,number_format($product->prices['discountAmount'],$cDecimals));
            if($config['vm_currency_position'] == 'before') {  
            	$disc_amount = $cSymbol.' '.$disc_amount;
            } else {
            	$disc_amount = $disc_amount.' '.$cSymbol;
            }
            $news_price .= JText::_('MOD_NEWS_PRO_GK5_PRODUCT_DISCOUNT_AMOUNT'). $disc_amount;
            $news_price .= '</div>'; 
        }
		// display tax
        if($config['vm_show_tax'] == 1) {
        	$news_price .= '<div class="PricetaxAmount">';
          	$taxAmount = str_replace('.',$cDecSymbol,number_format($product->prices['taxAmount'],$cDecimals));
            if($config['vm_currency_position'] == 'before') {  
            	$taxAmount = $cSymbol.' '.$taxAmount;
            } else {
            	$taxAmount = $taxAmount.' '.$cSymbol;
            }
            $news_price.= JText::_('MOD_NEWS_PRO_GK5_PRODUCT_TAX_AMOUNT'). $taxAmount;  
            $news_price .= '</div>'; 
        }
	    // 'Add to cart' button
	    $news_cart = '';
	    //if($config['vm_add_to_cart'] == 1) {
	        $code = '<form method="post" class="product" action="index.php">';
	        $code .= '<div class="addtocart-bar">';
	        $code .= '<span class="quantity-box" style="display: none"><input type="text" class="quantity-input" name="quantity[]" value="1" /></span>';
	        
	        $button_lbl = JText::_('MOD_NEWS_PRO_GK5_COM_VIRTUEMART_CART_ADD_TO');
			$button_cls = '';
	        $stockhandle = VmConfig::get('stockhandle','none');
	        
	        $code .= '<span class="addtocart-button"><input type="submit" name="addtocart" class="addtocart-button" value="'.$button_lbl.'" title="'.$button_lbl.'" /></span>';
	            
	        $code .= '</div>
	                <input type="hidden" class="pname" value="'.$product->product_name.'"/>
	                <input type="hidden" name="option" value="com_virtuemart" />
	                <input type="hidden" name="view" value="cart" />
	                <noscript><input type="hidden" name="task" value="add" /></noscript>
	                <input type="hidden" name="virtuemart_product_id[]" value="'.$product->virtuemart_product_id.'" />
	                <input type="hidden" name="virtuemart_category_id[]" value="'.$product->virtuemart_category_id.'" />
	            </form>';    
	            
	        $news_cart .= $code;
		//} 
		// restults
	    return array(
	    	"price" => $news_price,
	    	"cart" => $news_cart
	    );
	}
}

// EOF

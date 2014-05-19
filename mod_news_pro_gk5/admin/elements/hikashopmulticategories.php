<?php

/**
* HikaShop Multicategories fields
* @package News Show Pro GK5
* @Copyright (C) 2009-2014 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.5.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
 
class JFormFieldHikaShopMulticategories extends JFormFieldList {
	// name of element
	protected $type = 'HikaShopMultiCategories';
    var $options = array();
    
	protected function getInput() {
		// Initialize variables.
		$html = array();
		$attr = '';
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		// Get the field options.
		$this->getOptions();
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $this->options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {		
		    if(isset($this->options[0]) && $this->options[0] != ''){
				$html[] = JHtml::_('select.genericlist', $this->options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
            } else {	        
               	return '<select id="jform_params_hikashop_categories" style="display:none" class="gk-hidden-field"></select><strong style="line-height: 2.6em">HikaShop is not installed or any HikaShop categories are available.</strong>';
            }
		}

		return implode($html);
	}

    protected function getOptions() {
        // Initialize variables.
        $session = JFactory::getSession();
        $attr = '';
        $lang = '';
        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';
        
        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
        $db = JFactory::getDBO();
        // generating the query
        $tables = $db->getTableList();
        $dbprefix = $db->getPrefix();        
        if(in_array($dbprefix . 'hikashop_category', $tables)) {            
            $db->setQuery("
            SELECT 
            	c.category_name AS name, 
            	c.category_id AS id, 
            	c.category_parent_id AS parent 
            FROM 
            	#__hikashop_category AS c 
            WHERE 
            	c.category_published = '1' AND
            	c.category_type = 'product'
            ORDER BY c.category_name, c.category_parent_id ASC");
            $results = $db->loadObjectList();
        } else {
            $results = array();
        }
   		
		if(count($results)){
  	     	// iterating
			$temp_options = array();

			foreach ($results as $item) {
				array_push($temp_options, array($item->id, $item->name, $item->parent));	
			}

			foreach ($temp_options as $option) {
        		if($option[2] == 1) {
        	    	$this->options[] = JHtml::_('select.option', $option[0], $option[1]);
        	    	$this->recursive_options($temp_options, 1, $option[0]);
        	    }
        	}		
		}
	}
 	// bind function to save
    function bind( $array, $ignore = '' ) {
        if (key_exists( 'field-name', $array ) && is_array( $array['field-name'] )) {
        	$array['field-name'] = implode( ',', $array['field-name'] );
        }
        
        return parent::bind( $array, $ignore );
    }

    function recursive_options($temp_options, $level, $parent){
		foreach ($temp_options as $option) {
      		if($option[2] == $parent) {
		  		$level_string = '';
		  		for($i = 0; $i < $level; $i++) $level_string .= '- - ';
        	    $this->options[] = JHtml::_('select.option',  $option[0], $level_string . $option[1]);
       	    	$this->recursive_options($temp_options, $level+1, $option[0]);
			}
       	}
    }
}

// EOF
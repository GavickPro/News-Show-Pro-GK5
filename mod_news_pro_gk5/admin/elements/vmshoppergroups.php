<?php

/**
* VM Shopper Groups list
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
 
class JFormFieldVMShoppergroups extends JFormFieldList {
	// name of element
	protected $type = 'VMShopperGroups';
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
		$options = (array) $this->getOptions();
           // Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
		    if($options[0]!=''){
				$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
            } else {
               return '<input id="jform_params_vm_shoppergroups" type="hidden" /><strong style="line-height: 2.6em">VirtueMart is not installed or any VirtueMart shopper groups are available.</strong>';
            }
		}

		return implode($html);
	}

    protected function getOptions() {
        if(JFile::exists(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php')) {
	        // Initialize variables.
	        $session = JFactory::getSession();
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
	        $db = JFactory::getDBO();
	        // generating query
			$db->setQuery("SELECT sg.shopper_group_name AS name, sg.virtuemart_shoppergroup_id AS id FROM #__virtuemart_shoppergroups AS sg ORDER BY sg.shopper_group_name ASC");
	 		// getting results
	   		$results = $db->loadObjectList();
	   		
			if(count($results)){
	  	     	// iterating
				$temp_options = array();
				array_push($temp_options, array(-1, 'none', 0));
				foreach ($results as $item) {
					array_push($temp_options, array($item->id, $item->name, 0));	
				}
	
				foreach ($temp_options as $option) {
	        		if($option[2] == 0) {
	        	    	$this->options[] = JHtml::_('select.option', $option[0], $option[1]);
	        	    	$this->recursive_options($temp_options, 1, $option[0]);
	        	    }
	        	}		
	
	            return $this->options;
			} else {	
	            return $this->options;
			}
		} else {
			return array('');
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
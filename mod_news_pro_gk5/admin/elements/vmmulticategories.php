<?php

/**
* JElementVMMultiCategories - additional element for module XML file
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0 $
**/
 
// access denied
defined('JPATH_BASE') or die();
 
class JFormFieldVMMulticategories extends JFormFieldList {
	// name of element
	protected $type = 'VMMultiCategories';
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
	        	jimport('joomla.language.helper');
	        	$languages = JLanguageHelper::getLanguages('lang_code');
	        	$siteLang = JFactory::getLanguage()->getTag();
	        	$lang = strtolower(strtr($siteLang,'-','_'));
	        
               	return '<select id="jform_params_vm_categories" style="display:none" class="gk-hidden-field"></select><strong style="line-height: 2.6em">VirtueMart is not installed or any VirtueMart categories are available (current language: ' . $lang . ').</strong>';
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
        // get default VM language
        
       // get front-end language
        jimport('joomla.language.helper');
        $languages = JLanguageHelper::getLanguages('lang_code');
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));

        // generating the query
        $tables = $db->getTableList();
        $dbprefix = $db->getPrefix();        
        if(in_array($dbprefix . 'virtuemart_category_categories', $tables)) {            
            $db->setQuery("SELECT c.category_name AS name, c.virtuemart_category_id AS id, x.category_parent_id AS parent FROM #__virtuemart_categories_".$lang." AS c LEFT JOIN #__virtuemart_category_categories AS x ON x.category_child_id = c.virtuemart_category_id LEFT JOIN #__virtuemart_categories AS cr ON cr.virtuemart_category_id = c.virtuemart_category_id WHERE cr.published = '1' ORDER BY c.category_name, x.category_parent_id ASC");
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
        		if($option[2] == 0) {
        	    	$this->options[] = JHtml::_('select.option', $option[0], $option[1]);
        	    	$this->recursive_options($temp_options, 1, $option[0]);
        	    }
        	}		

            return $this->options;
		} else {	
            return $this->options;
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
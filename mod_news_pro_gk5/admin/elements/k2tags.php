<?php
/**
* JElementK2Tags - additional element for module XML file
* @package Highlighter GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 5.0.0 $
**/ 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield
class JFormFieldK2Tags extends JFormFieldList {
    public $type = 'K2Tags';
    
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
               return '<select id="jform_params_k2_tags" style="display:none"></select><strong style="line-height: 2.6em" class="gk-hidden-field">K2 is not installed or any K2 tags are available.</strong>';
            }
		}
		
		return implode($html);
	}
	// function to create an element    
	protected function getOptions() {
        $session = JFactory::getSession();
        $attr = '';
        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';        
        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
           $attr .= ' disabled="disabled"';
        }
        //
        $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';       
        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
        $db = JFactory::getDBO();
        // generating the query
        $tables = $db->getTableList();
        $dbprefix = $db->getPrefix();        
        if(in_array($dbprefix . 'k2_tags', $tables)) {
            $db->setQuery("SELECT t.name AS name, t.id AS id FROM #__k2_tags AS t WHERE published = 1 ORDER BY t.name ASC"); 
            $results = $db->loadObjectList();
        } else {
            $results = array();
        }

        $tags=array();
		if(count($results)) {
			foreach ($results as $tag) {
				$tags[] = JHtml::_('select.option', $tag->id, $tag->name);	
			}		
  	     $tags = array_merge(parent::getOptions(), $tags);
	     return $tags;
    } else {
            $tags=array();
            return $tags;
		}
    }
}

/* EOF */
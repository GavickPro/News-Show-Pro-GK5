<?php
/**
* Easy Blog Authors list
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield
class JFormFieldEasyblogAuthors extends JFormFieldList {
    public $type = 'EasyblogAuthors';
    
    protected function getInput() {
		// Initialize variables.
		$html = array();
		$attr = '';
		$options = array();
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
		    if(count($options) > 0){
				$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
            } else {
               return '<select id="jform_params_easyblog_authors" style="display:none"></select><strong style="line-height: 2.6em" class="gk-hidden-field">Easy Blog is not installed or any EasyBlog authors are available.</strong>';
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
		
		// generating query
		$tables = $db->getTableList();
		$dbprefix = $db->getPrefix();        
		if(in_array($dbprefix . 'easyblog_post', $tables)) {
		    $db->setQuery("SELECT created_by FROM  `#__easyblog_post` GROUP BY created_by;");   
		    $results = $db->loadObjectList();
		} else {
		    $results = array();
		}
		
        $authors = array();
        $authors_arr = array();
		if(count($results)) {
			foreach ($results as $ID) {
				array_push($authors_arr, $ID->created_by);	
			}		
			
			$db->setQuery("SELECT id, name FROM  `#__users` WHERE id IN (".implode(',', $authors_arr).") ORDER BY name ASC;");
			$res = $db->loadObjectList();
			
			if(count($res)) {
				foreach ($res as $author) {
					$authors[] = JHtml::_('select.option', $author->id, $author->name);
  	    			$authors = array_merge(parent::getOptions(), $authors);
  	    		}
  	    	}
  	    	
	    	return $authors;
    	} else {
            $authors = array();
            return $authors;
		}
    }
}

/* EOF */
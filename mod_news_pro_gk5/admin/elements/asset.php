<?php
/**
* Asset element
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField {
    protected $type = 'Asset';
    protected function getInput() {
        $doc = JFactory::getDocument();
        // scripts
        $doc->addScript(JURI::root().$this->element['path'].'class.articlelayout.js');
        $doc->addScript(JURI::root().$this->element['path'].'class.configmanager.js');
        $doc->addScript(JURI::root().$this->element['path'].'class.datasources.js');
        $doc->addScript(JURI::root().$this->element['path'].'class.portalmodes.js');
        $doc->addScript(JURI::root().$this->element['path'].'class.imagecrop.js');
        $doc->addScript(JURI::root().$this->element['path'].'main.js');
  		// stylesheets
        $doc->addStyleSheet(JURI::root().$this->element['path'].'style.css');        
        return null;
    }
}
/* eof */
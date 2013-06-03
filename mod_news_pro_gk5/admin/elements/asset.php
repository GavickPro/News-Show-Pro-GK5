<?php
/**
* Asset Element
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

defined('JPATH_BASE') or die;

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
<?php

/**
* Main file
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK5 1.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
// Solves problem with loading mootools-more in selected templates
JHtml::_('behavior.framework', true);
// Loading helper class
require_once (dirname(__FILE__).DS.'helper.php');
// loading module classes
if(!class_exists('NSP_GK5_Thumbs')) require_once (dirname(__FILE__).DS.'helpers'.DS.'gk.thumbs.php');
if(!class_exists('NSP_GK5_Utils')) require_once (dirname(__FILE__).DS.'helpers'.DS.'gk.utils.php');
if(!class_exists('NSP_GK5_Article_Format')) require_once(dirname(__FILE__).DS.'helpers'.DS.'gk.format.parser.php');
// Add MooTools loading for non-Gavick templates
JHtml::_('behavior.framework', true);
// start the module code
$helper = new NSP_GK5_Helper();
$helper->init($module, $params);
$helper->getDatas();
$helper->renderLayout();

// EOF
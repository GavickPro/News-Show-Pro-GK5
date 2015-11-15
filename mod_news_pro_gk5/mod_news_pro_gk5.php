<?php

/**
* Main file
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){ define('DS',DIRECTORY_SEPARATOR); }
// Loading helper class
require_once (dirname(__FILE__).DS.'helper.php');
// loading module classes
if(!class_exists('NSP_GK5_Thumbs')) require_once (dirname(__FILE__).DS.'helpers'.DS.'gk.thumbs.php');
if(!class_exists('NSP_GK5_Utils')) require_once (dirname(__FILE__).DS.'helpers'.DS.'gk.utils.php');
if(!class_exists('NSP_GK5_Article_Format')) require_once(dirname(__FILE__).DS.'helpers'.DS.'gk.format.parser.php');
if(!class_exists('NSP_GK5_View')) require_once(JModuleHelper::getLayoutPath('mod_news_pro_gk5', 'view'));

// start the module code
$helper = new NSP_GK5_Helper();
$helper->init($module, $params);
$helper->getDatas();
$helper->renderLayout();

// EOF

<?php
/**
* About Us Field
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
jimport('joomla.version');
jimport('joomla.form.formfield');

class JFormFieldAbout extends JFormField {

	protected $type = 'About';

	protected function getInput() {
		$version = new JVersion;
		$ver = $version->getShortVersion();
		
		return '<div id="gk_about_us" data-jversion="'.$ver.'">'. JText::_('MOD_NEWS_PRO_GK5_ABOUT_US_CONTENT') . '</div></div>';
	}
}

// EOF
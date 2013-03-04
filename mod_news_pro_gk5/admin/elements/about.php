<?php
/**
* About block Element
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldAbout extends JFormField {

	protected $type = 'About';

	protected function getInput() {
		return '<div id="gk_about_us">' . JText::_('MOD_NEWS_PRO_GK5_ABOUT_US_CONTENT') . '</div>';
	}
}

// EOF
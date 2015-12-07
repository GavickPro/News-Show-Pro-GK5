<?php
/**
* Line element
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
class JFormFieldLine extends JFormField {
	protected $type = 'Line';
	protected function getInput() {
		$text  	= (string) $this->element['text'];
		$class  = (string) $this->element['class'];
		$toggle = (string) $this->element['toggle'];
		return '<div class="gkFormLine'.(($text != '') ? ' hasText hasTip' : '').' '.$class.'" title="'. JText::_($this->element['desc']) .'" data-section-toggle="'.$toggle.'"><span>' . JText::_($text) . '</span></div>';
	}
}
/* EOF */
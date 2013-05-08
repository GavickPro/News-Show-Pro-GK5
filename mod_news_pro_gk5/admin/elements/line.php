<?php
defined('JPATH_BASE') or die;
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
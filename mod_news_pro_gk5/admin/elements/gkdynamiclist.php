<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldGkDynamicList extends JFormField
{
	protected $type = 'GkDynamicList';

	protected function getInput()
	{
		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		$attr .= ' data-value="' . $this->value . '"';
		$attr .= ' id="' . $this->id . '"';
		$attr .= ' name="' . $this->name . '"';
		
		$html = '<select ' . $attr . '></select>';

		return $html;
	}
}

// EOF
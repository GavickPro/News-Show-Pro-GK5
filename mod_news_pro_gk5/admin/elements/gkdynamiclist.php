<?php
/**
* Dynamic list field
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.2 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
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
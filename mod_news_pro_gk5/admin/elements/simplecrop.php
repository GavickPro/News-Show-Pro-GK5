<?php
/**
* Image Crop interface element
* @package News Show Pro GK5
* @Copyright (C) 2009-2013 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @version $Revision: GK5 1.3.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield
class JFormFieldSimpleCrop extends JFormField {
	// name of element
	var $_name = 'SimpleCrop';
    protected $type = 'SimpleCrop';
	// function to create an element
	protected function getInput() {
        // render the element
		return '<div id="simple_crop">
			<div>
				<div id="simple_crop_top_wrap" class="input-prepend"><input type="text" id="simple_crop_top" value="0" /><span class="add-on">%</span></div>
				<div id="simple_crop_main_wrap">
					<div id="simple_crop_left_wrap" class="input-prepend"><input type="text" id="simple_crop_left" value="0" /><span class="add-on">%</span></div>
					<div id="simple_crop_bg">
						<div id="simple_crop_crop"></div>
					</div>
					<div id="simple_crop_right_wrap" class="input-prepend"><input type="text" id="simple_crop_right" value="0" /><span class="add-on">%</span></div>	
				</div>
				<div id="simple_crop_bottom_wrap" class="input-prepend"><input type="text" id="simple_crop_bottom" value="0" /><span class="add-on">%</span></div>
			</div>
		</div>';	 
	}
}

/* EOF */
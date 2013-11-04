<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Provides and input field for e-mail addresses
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.email.html#input.email
 * @see         JFormRuleEmail
 * @since       11.1
 */
class JFormFieldDate extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Date';

	/**
	 * Method to get the field input markup for e-mail addresses.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{	
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$class = $this->element['class'] ? ' ' . (string) $this->element['class'] : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$required = $this->required ? ' required="required" aria-required="true"' : '';
		
		$jsparams =	($this->element['format']?'dateFormat: "'.$this->element['format'].'", ':'').
					($this->element['startDate']?'minDate: "'.$this->element['startDate'].'", ':'').
					($this->element['endDate']?'maxDate: "'.$this->element['endDate'].'", ':'');

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		
		$doc = JFactory::getDocument();
		//	$doc =& JFactory::getDocument();
			$doc->addScript("http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js");
		//$doc->addScript('http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/js/bootstrap-datepicker.js');
		//$doc->addStylesheet('http://eternicode.github.io/bootstrap-datepicker/bootstrap-datepicker/css/datepicker.css');
		$js = 'jQuery(document).ready( function() { jQuery("#' . $this->id . '").datepicker({'.$jsparams.'}); });';
		$doc->addScriptDeclaration($js);	
		
		return '
<div class="input-append date" id="date_' . $this->id . '"><input type="text" name="' . $this->name . '" class="' . $class . '" id="' . $this->id . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $size . $disabled . $readonly . $onchange . $maxLength . $required . '/>
			<i class="icon-calendar" style="position:absolute; margin-left: -30px;"></i>
</div>';
	}
}

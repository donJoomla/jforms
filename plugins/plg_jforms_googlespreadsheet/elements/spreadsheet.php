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
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldSpreadsheet extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Spreadsheet';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->required ? ' required="required" aria-required="true"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get the field options.
		JPluginHelper::importPlugin('jforms', 'googlespreadsheet');
		$dispatcher =& JDispatcher::getInstance();
		$spreadsheets = $dispatcher->trigger('listSheets');
		if(!$spreadsheets) return '<div class="alert alert-error">Before using Google Spreadsheets, please publish &amp configure the <a href="index.php?option=com_plugins&amp;view=plugins&amp;filter_folder=jforms&amp;filter_search=Google%20Spreadsheet%20Export" target="_blank">jForms Google Spreadsheets plugin</a>.</div>
';
		elseif(!is_array($spreadsheets[0])) return '<div class="alert alert-error"><b>'.$spreadsheets[0].'.</b> Please check your <a href="index.php?option=com_plugins&amp;view=plugins&amp;filter_folder=jforms&amp;filter_search=Google%20Spreadsheet%20Export" target="_blank">jForms Google Spreadsheets plugin</a> configuration.</div>
';
		$spreadsheet[] = JHtml::_('select.option', '', 'Please select a Spreadsheet', 'value', 'text');
		foreach($spreadsheets[0] as $option) {
			$spreadsheet[] = JHtml::_('select.option', $option->id, JText::alt(trim($option->title), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text');
			$spreadsheetJS[] = $option->sheets;
		}
		$html[] = JHtml::_('select.genericlist', $spreadsheet, $this->name.'[0]', trim($attr), 'value', 'text', $this->value[0], $this->id.'0');
		
		if($this->value[0]) 
		{
			$worksheets = $dispatcher->trigger('listSheets', $this->value[0]);
			if($worksheets[0]) foreach($worksheets[0] as $option) {
				$worksheet[] = JHtml::_('select.option', $option->id, JText::alt(trim($option->title), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text');
			}

		} 
		else $worksheet[] = JHtml::_('select.option', '', 'Select a Spreadsheet first', 'value', 'text');
		$html[] = '<span id="'.$this->id.'1_1">'.JHtml::_('select.genericlist', $worksheet, $this->name.'[1]', trim($attr), 'value', 'text', $this->value[1], $this->id.'1').'</span>';
			
		$js = '
			jQuery(document).ready(function() {
				var sheets = jQuery.parseJSON(\''.json_encode($spreadsheetJS).'\');
				function changeSheet() {
					current = jQuery("#'.$this->id.'0 option:selected").index();
					var listItems = "";
					if(current > 0) {
						current--;
						for (var i = 0; i < sheets[current].length; i++){
							listItems+= "<option value=\'" + sheets[current][i].id + "\'>" + sheets[current][i].title + "</option>";
						}
						jQuery("#'.$this->id.'1_1").show();
					} else {
						jQuery("#'.$this->id.'1_1").hide();
					}
					jQuery("#'.$this->id.'1").html(listItems).trigger("liszt:updated");
				}
				jQuery("#'.$this->id.'0").change( function() {
					changeSheet();
				});
				if(jQuery("#'.$this->id.'0 option:selected").index()==0) jQuery("#'.$this->id.'1_1").hide();
			});';
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration( $js );
		//return print_r($this->value);
		return implode($html);
	}
}

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
class JFormFieldYmlplist extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Ymlplist';

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

		// Get the field options.
		JPluginHelper::importPlugin('jforms', 'ymlp');
		$dispatcher =& JDispatcher::getInstance();
		$groups = $dispatcher->trigger('GroupsGetList');
		$fields = $dispatcher->trigger('FieldsGetList');
		//die(print_r($groups[0]) . print_r($fields[0]));
		if(!is_array($groups[0]) || !is_array($fields[0])) return '<div class="alert alert-error">Before using YMPL, please publish &amp configure the <a href="index.php?option=com_plugins&amp;view=plugins&amp;filter_folder=jforms" target="_blank">jForms YMPL plugin</a>.</div>';
		if(isset($groups[0]['Code'])) return '<div class="alert alert-error">'.$groups[0]['Output'].'</div>';
		if(isset($fields[0]['Code'])) return '<div class="alert alert-error">'.$fields[0]['Output'].'</div>';
		$mailinglist[] = JHtml::_('select.option', '', 'Please select a Mailing List', 'value', 'text');
		foreach($groups[0] as $option) {
			$mailinglist[] = JHtml::_('select.option', $option['ID'], JText::alt(trim($option['GroupName']), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text');
		}
		$html[] = JHtml::_('select.genericlist', $mailinglist, $this->name.'[group]', trim($attr), 'value', 'text', $this->value[group], $this->id.'_group');
		
		$html[]='</div></div>'; // close form control group to start on a new row
		
		$html[] = '<div class="control-group"><legend>Map YMLP Fields</legend>Map your form fields to the user fields eg. "{email}". To pass a static value to, for example, track where they signed up from, just enter standard text eg. "Website Visiter".</div>';
		foreach($fields[0] as $field) {
			if($field['ID']==0) {
				$id = $this->id.'_email';
				$name = $this->name.'[email]';
				$value = (empty($this->value['email'])?'{email}':$this->value['email']);
			}
			else {
				$id = $this->id.'_field'.$field['ID'];
				$name = $this->name.'[fields][field'.$field['ID'].']';
				$value = (is_array($this->value['fields'])?$this->value['fields']:json_decode($this->value['fields']));
				$value = $value['field'.$field['ID']];
			}
			$html[] = '<div class="control-group"><div class="control-label"><label for="'.$id.'">'.$field['FieldName'].':</label></div><div class="controls"><input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" /></div></div>';
		}
		
		$html[]='<div><div>'; // open new divs as joomla wants to close them
		
		return implode($html);
	}
}

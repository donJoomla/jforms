<?php
/**
 * @version     1.1.2
 * @package     plg_jforms_mailchimp
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <info@donjoomla.com> - http://donjoomla.com
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
class JFormFieldMailchimplist extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Mailchimplist';

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
		JPluginHelper::importPlugin('jforms', 'mailchimp');
		$dispatcher =& JDispatcher::getInstance();
		$lists = $dispatcher->trigger('getList');
		if(!is_array($lists)) return '<div class="alert alert-error">'.JText::_('PLG_JFORMS_MAILCHIMP_ERROR_PUBLISH_CONFIGURE').'</div>';
		//if($lists->status == 'error') return '<div class="alert alert-error">'.$lists->error.'</div>';
		$mailinglist[] = JHtml::_('select.option', '', JText::_('PLG_JFORMS_MAILCHIMP_SELECT_LIST_DESC'), 'value', 'text');
		foreach($lists[0]['data'] as $option) {
			$mailinglist[] = JHtml::_('select.option', $option['id'], JText::alt(trim($option['name']), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text');
		}
		$html[] = JHtml::_('select.genericlist', $mailinglist, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		
		$fields = $dispatcher->trigger('getFields');
		$html[] = '<input type="hidden" value="'.implode(',', $fields[0]).'" name="'.substr($this->name, 0, -1).'_fields]" />';
		
		return implode($html);
	}
}

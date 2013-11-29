<?php
/**
 * @version     1.1.2
 * @package     plg_jforms_ymlp
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
		if(!is_array($groups[0])) return '<div class="alert alert-error">'.JText::_('PLG_JFORMS_YMLP_ERROR_PUBLISH_CONFIGURE').'</div>';
		if(isset($groups[0]['Code'])) return '<div class="alert alert-error">'.$groups[0]['Output'].'</div>';
		$mailinglist[] = JHtml::_('select.option', '', JText::_('PLG_JFORMS_YMLP_SELECT_LIST_DESC'), 'value', 'text');
		foreach($groups[0] as $option) {
			$mailinglist[] = JHtml::_('select.option', $option['ID'], JText::alt(trim($option['GroupName']), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text');
		}
		$html[] = JHtml::_('select.genericlist', $mailinglist, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		
		return implode($html);
	}
}

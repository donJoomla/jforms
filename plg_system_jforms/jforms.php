<?php
/**
* @copyright Copyright (C) 2013 Adam Bouqdib. All rights reserved.
* @license GNU/GPL
*
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class plgSystemJForms extends JPlugin
{
	function plgSystemJForms( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}
	
	/**
	* Change forms before they are shown to the user
	*
	* @param   JForm  $form  JForm object
	* @param   array  $data  Data array
	*
	* @return boolean
	*/
	public function onContentPrepareForm($form, $data)
	{
		// Check we have a form
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		// Extra parameters for mod_jforms edit
		if ($form->getName() == 'com_modules.module' && $data->module == 'mod_jforms')
		{
			print_r($form->getGroup('params'));
			$form->removeGroup('advanced');
			
			$plugins = JFolder::folders(JPATH_ROOT.'/plugins/jforms');
			
			foreach($plugins as $plugin) {
				$xmlPath = JPATH_ROOT.'/plugins/jforms/'.$plugin.'/config.xml';
				if(JFile::exists($xmlPath)) $form->loadFile($xmlPath);
			}
		}
		return true;
	}
	
	/**
	* Add the new form data to the tables
	*
	* @param   JForm  $form  JForm object
	* @param   array  $data  Data array
	*
	* @return boolean
	*/
	public function onExtensionBeforeSave($form, &$data, $basa)
	{
		// Extra parameters for mod_jforms edit
		if ($form == 'com_modules.module' && $data->module == 'mod_jforms')
		{
			$input = JFactory::getApplication()->input;
			$params = $input->post->get('jform', array(), 'array');
			$data->params = json_encode($params['params']);
		}
		return true;
	}
}

<?php

/**
 * @version     1.6.2
 * @package     mod_jforms
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <adam@abemedia.co.uk> - http://www.abemedia.co.uk
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).'/helper.php');

$message 			= '<div class="jfMessage">'.$params->get('introtext', '').'</div>'; 
$thanks 			= $params->get('thanks', ''); 
$submit_label 		= $params->get('submit_label', 'Submit'); 
$layout 			= $params->get('layout', 'default');
$moduleclass_sfx 	= $params->get('moduleclass_sfx', '');

$form = new modJformsHelper;
$form->params = $params;
$form->id = $module->id;

$input 		= JFactory::getApplication()->input;
$form->data	= new JRegistry($input->get('jform'.$module->id, '', 'array'));

if($form->data->get('jFormAction', false)) {
	$form->processForm();
} 
$fields = $form->getForm();

require(JModuleHelper::getLayoutPath('mod_jforms', $layout));
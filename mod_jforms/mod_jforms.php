<?php

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
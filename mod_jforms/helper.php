<?php

/**
 * @version     1.6.2
 * @package     mod_jforms
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Bouqdib <adam@abemedia.co.uk> - http://www.abemedia.co.uk
 */
 
defined('_JEXEC') or die('Restricted access');

/**
 * jForms Module Helper
 *
 * @since       1.0
 */
class modJformsHelper
{
	public $id;
	public $params;
	public $data;
	
	
	/**
	 * Method to get the form and load JavaScript.
	 *
	 * The form is loaded from XML 
     * 
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.0
	 */
	function getForm() 
	{
		$module_id = $this->id;
		$formname = 'jform'.$module_id;
		$xmlPath = dirname(__FILE__) .'/forms/'. $this->params->get('fields_xml', 'default') . '.xml';
		jimport( 'joomla.filesystem.file' );
		
		if(JFile::exists($xmlPath)) {
			$ajax = $this->params->get('use_ajax', true);
			$form =& JForm::getInstance('jform'.$module_id, $xmlPath,  array('control' => 'jform'.$module_id, 'load_data' => true));
			$fields = $form->getGroup(null);
			
			$jinput 	= JFactory::getApplication()->input;
			$formdata 	= new stdClass();
			
			//pre-populate form with user data
			if($params->get('populate_form', 1) {
				$user = JFactory::getUser();
				if(!$user->guest) {
					$profile = JUserHelper::getProfile($user->id)->profile;
					
					//split name into first name & last name
					if($params->get('split_name', 0) {
						$namearray = explode(' ', $user->name);
						if(count($namearray)>1) {
							$lastname = array_pop($namearray);
							$firstname = implode(' ', $namearray);
						} else {
							$firstname = $user->name;
							$lastname = null;
						}
						if(!$profile['firstname']) $profile['firstname'] = $firstname;
						if(!$profile['lastname']) $profile['lastname'] = $lastname;
					}
					
					if(!$this->data->get('name')) $this->data->set('name', $user->name);
					if(!$this->data->get('email')) $this->data->set('email', $user->email);
					foreach($profile as $key=>$data) {
						if(!$this->data->get($key)) $this->data->set($key, $data);
					}
				}
				$form->bind($this->data);
			}
			
			//add neccesary form fields
			$element = new SimpleXMLElement('<field name="jFormAction" type="hidden" default="1" />');
			$form->setField($element);
			$element = new SimpleXMLElement('<field name="'.JSession::getFormToken().'" type="hidden" default="1" />');
			$form->setField($element);
			$fields = $form->getGroup(null);
			
			//run ajax functions
			if($ajax) {
				$doc =& JFactory::getDocument();
				$jsFunctions = null;
				if($this->params->get('ajax_functions')) {
					foreach ($this->params->get('ajax_functions') as $filename) {
						$doc->addScript(JURI::base().'modules/mod_jforms/functions/'.$filename.'.js');
					}
					$jsFunctions = '"' . implode('", "', $this->params->get('ajax_functions')) . '"';
				} $jsFunctions = '['.$jsFunctions.']';
				$js = 'jQuery(document).ready(function(){mod_jforms('.$module_id.','.$jsFunctions.')});';
				$doc->addScriptDeclaration($js);
				$doc->addScript(JUri::root().'modules/mod_jforms/js/jforms.js');
			}

			return ($fields);

		} 
		else die('Form not found');
	}
	
	/**
	 * Method to process the form data.
	 *
	 * The base form is loaded from XML 
     * 
	 * @return	boolean	True on success, false on failure
	 * @since	1.0
	 */
	function processForm() 
	{
		$xmlPath = dirname(__FILE__) .'/forms/'. $this->params->get('fields_xml', 'default') . '.xml';
		$form =& JForm::getInstance('jform'.$module_id, $xmlPath);			
		$fields = $form->getGroup(null);
		foreach($fields as $key=>$field) {
			$post_fields[] = $key;
		}
		
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$post 	= $this->data->toArray();
		if(!$form->validate($post)) {
			$app->enqueueMessage("Please check your entries and try again.", "error");
			return false;
		}
		if(!$post[JSession::getFormToken()]) {
			$app->enqueueMessage("Your session has expired. Refresh the page and try again.", "error");
			return false;
		}
		
		foreach($post_fields as $key) {
			$data[$key] = new stdClass();
			$data[$key]->title = $fields[$key]->title;
			$data[$key]->value = $post[$key];
		}
		
		//split name into first name & last name
		if($params->get('split_name', 0) {
			if($post['name']) {
				$namearray = explode(' ', $post['name']);
				$data['lastname'] = new stdClass();
				$data['lastname']->title = 'First Name';
				$data['firstname'] = new stdClass();
				$data['firstname']->title = 'Last Name';
				if(count($namearray)>1) {
					$data['lastname']->value = array_pop($namearray);
					$data['firstname']->value = implode(' ', $namearray);
				} else {
					$data['lastname']->value = null;
					$data['firstname']->value = $post['name'];
				}
			}
		}
		
		//run jforms plugins
		JPluginHelper::importPlugin('jforms');
		$dispatcher =& JDispatcher::getInstance();
		$results = $dispatcher->trigger('OnJFormsSubmit', array($this->params, &$data) );
		
		//send form data by email
		if ($this->params->get('send_email', true)) {
			modJformsHelper::sendForm($data);
		}
		else {
			$app->enqueueMessage($this->params->get('thanks', ''));
			return true;
		}
	}
	
	
	/**
	 * Method to send the form data via email.
     * 
	 * @param	array	$data		An array of data to send.
	 * @return	boolean	True on success, false on failure
	 * @since	1.0
	 */
	function sendForm($data) 
	{
		$app 	= JFactory::getApplication();
			
		$fromname 	= $this->data->get('name', $app->getCfg('fromname'), 'string');
		$mailfrom 	= $app->getCfg('mailfrom');
		$from 	 	= array($mailfrom, $fromname);
		$reply 	 	= array($this->data->get('email', $mailfrom, 'email'), $fromname);
		
		// set up the email body
		foreach($data as $key=>$row) {
			if($row->title) $field_html .= $row->title.': '.$row->value . "<br>";
			$search[] = '{'.$key.'}';
			$replace[] = $row->value;
		}
		$search[] = '{fields}';
		$replace[] = $field_html;
		
		$body = str_replace($search, $replace, $this->params->get('email_body'));
		
		$mailer = JFactory::getMailer();
		$mailer->isHTML(true);
		//$mailer->setSender($from);
		$mailer->addReplyTo(str_replace($search, $replace, $reply));
		$mailer->addRecipient(str_replace($search, $replace, $this->params->get('receipt_email')));
		$mailer->setSubject(str_replace($search, $replace, $this->params->get('subject')));
		$mailer->setBody(str_replace($search, $replace, $body));
		
		if ($mailer->Send() !== true) {
			$app->enqueueMessage("Error. Please try again.", "error");
			return false;
		}
		else {
			$app->enqueueMessage($this->params->get('thanks', ''));
			return true;
		}
	}
}
?>
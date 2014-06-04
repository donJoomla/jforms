<?php
class plgJformsMailchimp extends JPlugin
{
	var $mailchimp = null;
	
	function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	
	function mailchimp()
	{
		if(!$this->mailchimp) {
			require_once(dirname(__file__)."/helper.php");
			$api_key = $this->params->get('api_key', '');
			$this->mailchimp = new MailChimp($api_key);
		}
		return $this->mailchimp; 
	}
 
	function getList()
	{
		$mailchimp = $this->mailchimp();
		$result = $mailchimp->call('lists/list');
		return $result;
	}
 
	function getFields()
	{
		$mailchimp = $this->mailchimp();
		$lists = $mailchimp->call('lists/list');
		if(!is_array($lists['data'])) return false;
		
		foreach($lists['data'] as $option) {
			$ids[] = $option['id'];
		}
		$fields = $mailchimp->call('lists/merge-vars', array('id'=>$ids));
		
		foreach($fields['data'] as $group) {
			foreach($group['merge_vars'] as $i=>$field) {
				if($i) $merge_vars[] = $field['tag'];
			}
		}
		return array_unique($merge_vars);
	}
 
	function OnJFormsAdminPrepareForm( $form, $data )
	{
		$mailchimp = $this->mailchimp();
		$lists = $mailchimp->call('lists/list');
		$lists = $mailchimp->call('lists/list');
		/*
		$input = new JInput;
		$mod_row = & JTable::getInstance ( 'Module', 'JTable' );
		$id = $input->get('id');
		if (!$mod_row->load($id)) {
			JError::raiseError (500, $mod_row->getError());
		
		}
		$params = json_decode($mod_row->params);
		*/
		if(!is_array($lists['data'])) return false;
		
		foreach($lists['data'] as $option) {
			$ids[] = $option['id'];
		}
		$fields = $mailchimp->call('lists/merge-vars', array('id'=>$ids));
		
		$xml = array();
		$xml[] = '<field type="spacer" label="&lt;legend&gt;'.JText::_('PLG_JFORMS_MAILCHIMP_MAP_FIELDS_LABEL').'&lt;/legend&gt; '.JText::_('PLG_JFORMS_MAILCHIMP_MAP_FIELDS_DESC').'" />';
		foreach($fields['data'] as $group) {
			foreach($group['merge_vars'] as $i=>$field) {
				$fieldname = 'mailchimp_'.(!$i?'email':'field_'.strtolower($field['tag']));
				$xml[] = '<field name="'.$fieldname.'" type="text" default="{'.strtolower($field['tag']).'}" label="'.$field['name'].'" description="'.$field['helptext'].'"'.($field['req']?' required="true"':'').' />';
			}
		}
        return new SimpleXMLElement('<fields name="params"><fieldset name="mailchimp">'.implode($xml).'</fieldset></fields>');
	}
 
	function OnJFormsSubmit( $params, $data )
	{
		if($params->get('use_mailchimp', '')) {
			$mailchimp = $this->mailchimp();
			$list_id = $params->get('mailchimp');
			$merge_vars = explode(',', $params->get('mailchimp_fields'));
			foreach($data as $key=>$row) {
				$search[] = '{'.$key.'}';
				$replace[] = $row->value;
			}
			$email = str_replace($search, $replace, $params->get('mailchimp_email', '{email}'));
			foreach($merge_vars as $merge_var) {
				$var =  str_replace($search, $replace, $params->get('mailchimp_field_'.strtolower($merge_var)));
				if($var[0] !== '{') $fields[$merge_var] = $var;
			}
			$mailchimp->subscribe($list_id, $email, $fields);
		}
		return true;
	}
}

?>
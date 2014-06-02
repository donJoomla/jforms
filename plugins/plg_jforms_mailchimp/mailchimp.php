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
 
	function OnJFormsAdminPrepareForm( $form, $data )
	{
		$mailchimp = $this->mailchimp();
		$lists = $mailchimp->call('lists/list');
		
		if(!is_array($lists['data'])) return false;
		
		foreach($lists['data'] as $option) {
			$ids[] = $option['id'];
		}
		$fields = $mailchimp->call('lists/merge-vars', array('id'=>$ids));
		
		$xml = array();
		$xml[] = '<field type="spacer" label="&lt;legend&gt;'.JText::_('PLG_JFORMS_MAILCHIMP_MAP_FIELDS_LABEL').'&lt;/legend&gt; '.JText::_('PLG_JFORMS_MAILCHIMP_MAP_FIELDS_DESC').'" />';
		foreach($fields['data'] as $group) {
			$xml[] = '<field type="spacer" label="&lt;b&gt;'.$group['name'].'&lt;/b&gt;" />';
			foreach($group['merge_vars'] as $field) {
				$xml[] = '<field name="mailchimp_'.$group['id'].'_'.$field['id'].'" type="text" default="{'.strtolower($field['tag']).'}" label="'.$field['name'].'" description="'.$field['helptext'].'"'.($field['req']?' required="true"':'').' />';
			}
			}
		$xml[] = '<field name="mailchimp_fieldcount" type="hidden" default="'.count($xml).'" />';
        return new SimpleXMLElement('<fields name="params"><fieldset name="mailchimp">'.implode($xml).'</fieldset></fields>');
	}
 
	function OnJFormsSubmit( $params, $data )
	{
		if($params->get('use_mailchimp', '')) {
			$mailchimp = $this->mailchimp();
			$fieldcount		= $params->get('mailchimp_fieldcount');
			$settings 		= $params->get('mailchimp');
			$group_id		= $settings->list;
			
			foreach($data as $key=>$row) {
				$search[] = '{'.$key.'}';
				$replace[] = $row->value;
			}
			$email = str_replace($search, $replace, $params->get('mailchimp_email', '{email}'));
			for ($i = 0; $i<=$fieldcount; $i++) {
				if($params->get('mailchimp_field'.$i)) $fields['Field'.$i] = str_replace($search, $replace, $params->get('mailchimp_field'.$i));
			}
			
			$mailchimp->subscribe($list_id, $email, $fields);
		}
		return true;
	}
}

?>
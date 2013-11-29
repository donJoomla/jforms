<?php
class plgJformsYmlp extends JPlugin
{
	var $ymlp = null;
	
	function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	
	function ympl()
	{
		if(!$this->ymlp) {
			require_once(dirname(__file__)."/helper.php");
			$api_key		= $this->params->get('api_key', '');
			$username		= $this->params->get('username', '');
			$this->ymlp 	= new YMLP($api_key, $username);
		}
		return $this->ymlp; 
	}
 
	function GroupsGetList()
	{
		$ymlp = $this->ympl();
		return $ymlp->GroupsGetList();
	}
 
	function FieldsGetList()
	{
		$ymlp = $this->ympl();
		return $ymlp->FieldsGetList();
	}
 
	function OnJFormsAdminPrepareForm( $form, $data )
	{
		JPluginHelper::importPlugin('jforms', 'ymlp');
		$dispatcher =& JDispatcher::getInstance();
		$fields = $dispatcher->trigger('FieldsGetList');
		$xml = array();
		$xml[] = '<field type="spacer" label="&lt;legend&gt;'.JText::_('PLG_JFORMS_YMLP_MAP_FIELDS_Label').'&lt;/legend&gt; '.JText::_('PLG_JFORMS_YMLP_MAP_FIELDS_DESC').'" />';
		foreach($fields[0] as $field) {
			if($field['ID']==0) {
				$name = 'ymlp_email';
				$value = '{email}';
			}
			else {
				$name = 'ymlp_field'.$field['ID'].'';
				$value = '';
			}
			$xml[] = '<field name="'.$name.'" type="text" default="'.$value.'" label="'.$field['FieldName'].'" description="" />';
		}
		$xml[] = '<field name="ymlp_fieldcount" type="hidden" default="'.count($xml).'" />';
        return new SimpleXMLElement('<fields name="params"><fieldset name="ymlp">'.implode($xml).'</fieldset></fields>');
	}
 
	function OnJFormsSubmit( $params, $data )
	{
		if($params->get('use_ymlp', '')) {
			$ymlp = $this->ympl();
			$fieldcount		= $params->get('ymlp_fieldcount');
			$settings 		= $params->get('ymlp');
			$group_id		= $settings->group;
			$blacklist 		= $params->get('overrule_blacklist', '0');
			
			foreach($data as $key=>$row) {
				$search[] = '{'.$key.'}';
				$replace[] = $row->value;
			}
			$email = str_replace($search, $replace, $params->get('ymlp_email', '{email}'));
			for ($i = 1; $i<=$fieldcount; $i++) {
				if($params->get('ymlp_field'.$i)) $fields['Field'.$i] = str_replace($search, $replace, $params->get('ymlp_field'.$i));
			}

			$ymlp->ContactsAdd($email, $fields, $group_id, $blacklist);
		}
		return true;
	}
}

?>
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
 
	function OnJFormsSubmit( $params, $data )
	{
		if($params->get('use_ymlp', '')) {
			$ymlp = $this->ympl();
			$settings 		= $params->get('ymlp');
			$group_id		= $settings->group;
			$blacklist 		= $params->get('overrule_blacklist', '0');
			
			foreach($data as $key=>$row) {
				$search[] = '{'.$key.'}';
				$replace[] = $row->value;
			}
			$email = str_replace($search, $replace, $settings->email);
			foreach ($settings->fields as $key=>$field) {
				if($field) $fields[$key] = str_replace($search, $replace, $field);
			}

			$ymlp->ContactsAdd($email, $fields, $group_id, $blacklist);
		}
		return true;
	}
}

?>
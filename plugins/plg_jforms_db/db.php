<?php

class plgJformsDB extends JPlugin
{
	function plgJformsDB( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}

	function OnJFormsSubmit( $params, $data )
	{
		if($params->get('use_db', '')) {
			$table = $params->get('table', '');
			foreach($data as $key=>$row) {
				$data[$key] = $row->value;
			}
			$data = (object) $data;
			return JFactory::getDbo()->insertObject($table, $data);
		}
	}
}

<?php

class plgJformsGooglespreadsheet extends JPlugin
{
	function plgJformsGooglespreadsheet( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
 
	function listSheets($spreadsheet = null)
	{
		set_include_path(dirname(__file__));
		include_once("helper.php");
		$google_user		= $this->params->get('google_user', '');
		$google_pass		= $this->params->get('google_pass', '');
		 
		try {
			$spread = new Google_Spreadsheet($google_user,$google_pass);
		} catch (Zend_Gdata_App_AuthException $e) {
			return $e->getMessage();
		}
		if(!$spreadsheet) $data = $spread->listSpreadsheets();
		else {
			$data = $spread->listWorksheets($spreadsheet);
		}
		return $data;
	}
 
	function OnJFormsSubmit( $params, $data )
	{
		if($params->get('use_google', '')) {
			set_include_path(dirname(__file__));
			include_once("helper.php");
			$google_user		= $this->params->get('google_user', '');
			$google_pass		= $this->params->get('google_pass', '');
			$google_spreadsheet	= $params->get('google_spreadsheet', '');
			 
			$spread = new Google_Spreadsheet($google_user, $google_pass, $google_spreadsheet[0], $google_spreadsheet[1]);
			 
			$row = array();
			$row['date'] = JHtml::date('now', 'd/m/Y H:i:s', false);
			foreach($data as $key=>$fields) {
				$row[$key] = $fields->value;
			}
			if ($spread->addRow($row)) return true;
			else { 
				$app->enqueueMessage("Error, unable to store spreadsheet data", "error");
				return false;
			}
		}
	}
}

?>
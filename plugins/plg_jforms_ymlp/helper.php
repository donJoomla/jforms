<?php

class YMLP
{
    var $ErrorMessage;
    var $ApiUrl = "www.ymlp.com/api/";
    var $ApiUsername;
    var $ApiKey;
    var $Secure = false;
    var $Curl = true;
    var $CurlAvailable = true;

    function __construct($ApiKey=null,$ApiUsername=null,$secure=false) {
        $this->ApiKey = $ApiKey;
        $this->ApiUsername = $ApiUsername;
        $this->Secure = $secure;
        $this->CurlAvailable = function_exists( 'curl_init' ) && function_exists( 'curl_setopt' );
    }

    function useSecure($val) {
        if ($val===true){
            $this->Secure = true;
        } else {
            $this->Secure = false;
        }
    }

    function doCall($method = '',$params = array()) {

        $params["key"] = $this->ApiKey;
        $params["username"] = $this->ApiUsername;
        $params["output"] = "PHP";
        $this->ErrorMessage = "";

        foreach ( $params as $k => $v )
            $postdata .= '&' . $k . '=' .rawurlencode(utf8_encode($v));

        if ( $this->Curl && $this->CurlAvailable )  {
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            if ($this->Secure){
                curl_setopt( $ch, CURLOPT_URL, "https://" .$this->ApiUrl . $method );
            } else {
                curl_setopt( $ch, CURLOPT_URL, "http://" .$this->ApiUrl . $method );
            }
            $response = curl_exec( $ch );
            if(curl_errno($ch)) {
                $this->ErrorMessage = curl_error($ch);
                return false;
                }
            }
        else {
            $this->ApiUrl = parse_url( "http://" .$this->ApiUrl . $method);
            $payload = "POST " . $this->ApiUrl["path"] . "?" . $this->ApiUrl["query"] . " HTTP/1.0\r\n";
            $payload .= "Host: " . $this->ApiUrl["host"] . "\r\n";
            $payload .= "User-Agent: YMLP_API\r\n";
            $payload .= "Content-type: application/x-www-form-urlencoded\r\n";
            $payload .= "Content-length: " . strlen($postdata) . "\r\n";
            $payload .= "Connection: close \r\n\r\n";
            $payload .= $postdata;

            ob_start();
            if ($this->Secure){
                $sock = fsockopen("ssl://".$this->ApiUrl["host"], 443, $errno, $errstr);
            } else {
                $sock = fsockopen($this->ApiUrl["host"], 80, $errno, $errstr);
            }

            if(!$sock) {
                $this->ErrorMessage = "ERROR $errno: $errstr";
                ob_end_clean();
                return false;
            }

            $response = "";
            fwrite($sock, $payload);
            while(!feof($sock)) {
                $response .= fread($sock,8192);
            }
            fclose($sock);
            ob_end_clean();

            list($throw, $response) = explode("\r\n\r\n", $response, 2);
        }

        if(ini_get("magic_quotes_runtime")) $response = stripslashes($response);

        if (strtoupper($params["output"]) == "PHP" ) {
            $serial = unserialize($response);
            if ($response && $serial === false) {
                $this->ErrorMessage = "Bad Response: " . $response;
                return false;
                }
            else {
                   $response = $serial;
                }
            }

    return $response;
    }

    function Ping() {
        return $this->doCall("Ping");
    }

    //------------------------------------------------------------
    // GROUPS [begin]
    //------------------------------------------------------------
    function GroupsGetList() {
        return $this->doCall("Groups.GetList");
    }

    function GroupsAdd($GroupName = '') {
        $params = array();
        $params["GroupName"] = $GroupName;
        return $this->doCall("Groups.Add", $params);
    }

    function GroupsDelete($GroupId = '') {
        $params = array();
        $params["GroupId"] = $GroupId;
        return $this->doCall("Groups.Delete", $params);
    }

    function GroupsUpdate($GroupId = '', $GroupName = '') {
        $params = array();
        $params["GroupId"] = $GroupId;
        $params["GroupName"] = $GroupName;
        return $this->doCall("Groups.Update", $params);
    }

    function GroupsEmpty($GroupId = '') {
        $params = array();
        $params["GroupId"] = $GroupId;
        return $this->doCall("Groups.Empty", $params);
    }
    //------------------------------------------------------------
    // GROUPS [end]
    //------------------------------------------------------------

    //------------------------------------------------------------
    // FIELDS [begin]
    //------------------------------------------------------------
    function FieldsGetList() {
        return $this->doCall("Fields.GetList");
    }

    function FieldsAdd($FieldName = '', $Alias = '', $DefaultValue = '', $CorrectUppercase = '') {
        $params = array();
        $params["FieldName"] = $FieldName;
        $params["Alias"] = $Alias;
        $params["DefaultValue"] = $DefaultValue;
        $params["CorrectUppercase"] = $CorrectUppercase;
        return $this->doCall("Fields.Add", $params);
    }

    function FieldsDelete($FieldId = '') {
        $params = array();
        $params["FieldId"] = $FieldId;
        return $this->doCall("Fields.Delete", $params);
    }

    function FieldsUpdate($FieldId = '', $FieldName = '', $Alias = '', $DefaultValue = '', $CorrectUppercase = '') {
        $params = array();
        $params["FieldId"] = $FieldId;
        $params["FieldName"] = $FieldName;
        $params["Alias"] = $Alias;
        $params["DefaultValue"] = $DefaultValue;
        $params["CorrectUppercase"] = $CorrectUppercase;
        return $this->doCall("Fields.Update", $params);
    }
    //------------------------------------------------------------
    // FIELDS [end]
    //------------------------------------------------------------

    //------------------------------------------------------------
    // CONTACTS [begin]
    //------------------------------------------------------------
    function ContactsAdd($Email = '', $OtherFields = '', $GroupID = '', $OverruleUnsubscribedBounced = '') {
        $params = array();
        $params["Email"] = $Email;
        if (!is_array($OtherFields)) $OtherFields=array();
        foreach ($OtherFields as $key=>$value) {
            $params[$key] = $value;
            }
        $params["GroupID"] = $GroupID;
        $params["OverruleUnsubscribedBounced"] = $OverruleUnsubscribedBounced;
        return $this->doCall("Contacts.Add", $params);
    }

    function ContactsUnsubscribe($Email = '') {
        $params = array();
        $params["Email"] = $Email;
        return $this->doCall("Contacts.Unsubscribe", $params);
    }

    function ContactsDelete($Email = '', $GroupID = '') {
        $params = array();
        $params["Email"] = $Email;
        $params["GroupID"] = $GroupID;
        return $this->doCall("Contacts.Delete", $params);
    }

    function ContactsGetContact($Email = '') {
        $params = array();
        $params["Email"] = $Email;
        return $this->doCall("Contacts.GetContact", $params);
    }

    function ContactsGetList($GroupID = '', $FieldID = '', $Page = '', $NumberPerPage = '', $StartDate = '', $StopDate = '') {
        $params = array();
        $params["GroupID"] = $GroupID;
        $params["FieldID"] = $FieldID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["StartDate"] = $StartDate;
        $params["StopDate"] = $StopDate;
        return $this->doCall("Contacts.GetList", $params);
    }

    function ContactsGetUnsubscribed($FieldID = '', $Page = '', $NumberPerPage = '', $StartDate = '', $StopDate = '') {
        $params = array();
        $params["FieldID"] = $FieldID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["StartDate"] = $StartDate;
        $params["StopDate"] = $StopDate;
        return $this->doCall("Contacts.GetUnsubscribed", $params);
    }

    function ContactsGetDeleted($FieldID = '', $Page = '', $NumberPerPage = '', $StartDate = '', $StopDate = '') {
        $params = array();
        $params["FieldID"] = $FieldID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["StartDate"] = $StartDate;
        $params["StopDate"] = $StopDate;
        return $this->doCall("Contacts.GetDeleted", $params);
    }

    function ContactsGetBounced($FieldID = '', $Page = '', $NumberPerPage = '', $StartDate = '', $StopDate = '') {
        $params = array();
        $params["FieldID"] = $FieldID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["StartDate"] = $StartDate;
        $params["StopDate"] = $StopDate;
        return $this->doCall("Contacts.GetBounced", $params);
    }
    //------------------------------------------------------------
    // CONTACTS [end]
    //------------------------------------------------------------

    //------------------------------------------------------------
    // FILTERS [begin]
    //------------------------------------------------------------
    function FiltersGetList() {
        return $this->doCall("Filters.GetList");
    }

    function FiltersAdd($FilterName = '', $Field = '', $Operand = '', $Value = '') {
        $params = array();
        $params["FilterName"] = $FilterName;
        $params["Field"] = $Field;
        $params["Operand"] = $Operand;
        $params["Value"] = $Value;
        return $this->doCall("Filters.Add", $params);
    }

    function FiltersDelete($FilterId = '') {
        $params = array();
        $params["FilterId"] = $FilterId;
        return $this->doCall("Filters.Delete", $params);
    }
    //------------------------------------------------------------
    // FILTERS [end]
    //------------------------------------------------------------

    //------------------------------------------------------------
    // ARCHIVE [begin]
    //------------------------------------------------------------
    function ArchiveGetList($Page = '', $NumberPerPage = '', $StartDate = '', $StopDate = '', $Sorting = '', $ShowTestMessages = '') {
        $params = array();
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["StartDate"] = $StartDate;
        $params["StopDate"] = $StopDate;
        $params["Sorting"] = $Sorting;
        $params["ShowTestMessages"] = $ShowTestMessages;
        return $this->doCall("Archive.GetList", $params);
    }

    function ArchiveGetSummary($NewsletterID = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        return $this->doCall("Archive.GetSummary", $params);
    }

    function ArchiveGetContent($NewsletterID = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        return $this->doCall("Archive.GetContent", $params);
    }

    function ArchiveGetRecipients($NewsletterID = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetRecipients", $params);
    }

    function ArchiveGetDelivered($NewsletterID = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetDelivered", $params);
    }

    function ArchiveGetBounces($NewsletterID = '', $ShowHardBounces = '', $ShowSoftBounces = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["ShowHardBounces"] = $ShowHardBounces;
        $params["ShowSoftBounces"] = $ShowSoftBounces;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetBounces", $params);
    }

    function ArchiveGetOpens($NewsletterID = '', $UniqueOpens = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["UniqueOpens"] = $UniqueOpens;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetOpens", $params);
    }

    function ArchiveGetUnopened($NewsletterID = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetUnopened", $params);
    }

    function ArchiveGetTrackedLinks($NewsletterID = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        return $this->doCall("Archive.GetTrackedLinks", $params);
    }

    function ArchiveGetClicks($NewsletterID = '', $LinkID = '', $UniqueClicks = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["LinkID"] = $LinkID;
        $params["UniqueClicks"] = $UniqueClicks;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetClicks", $params);
    }

    function ArchiveGetForwards($NewsletterID = '', $Page = '', $NumberPerPage = '', $Sorting = '') {
        $params = array();
        $params["NewsletterID"] = $NewsletterID;
        $params["Page"] = $Page;
        $params["NumberPerPage"] = $NumberPerPage;
        $params["Sorting"] = $Sorting;
        return $this->doCall("Archive.GetForwards", $params);
    }
    //------------------------------------------------------------
    // ARCHIVE [end]
    //------------------------------------------------------------

    //------------------------------------------------------------
    // NEWSLETTER [begin]
    //------------------------------------------------------------
    function NewsletterGetFroms() {
        return $this->doCall("Newsletter.GetFroms");
    }

    function NewsletterAddFrom($FromEmail = '', $FromName = '') {
        $params = array();
        $params["FromEmail"] = $FromEmail;
        $params["FromName"] = $FromName;
        return $this->doCall("Newsletter.AddFrom", $params);
    }

    function NewsletterDeleteFrom($FromID = '') {
        $params = array();
        $params["FromID"] = $FromID;
        return $this->doCall("Newsletter.DeleteFrom", $params);
    }

    function NewsletterSend($Subject = '', $HTML = '', $Text = '', $DeliveryTime = '',
                            $FromID = '', $TrackOpens = '', $TrackClicks = '', $TestMessage = '',
                            $GroupID = '', $FilterID = '', $CombineFilters = '') {
        $params = array();
        $params["Subject"] = $Subject;
        $params["HTML"] = $HTML;
        $params["Text"] = $Text;
        $params["DeliveryTime"] = $DeliveryTime;
        $params["FromID"] = $FromID;
        $params["TrackOpens"] = $TrackOpens;
        $params["TrackClicks"] = $TrackClicks;
        $params["TestMessage"] = $TestMessage;
        $params["GroupID"] = $GroupID;
        $params["FilterID"] = $FilterID;
        $params["CombineFilters"] = $CombineFilters;
        return $this->doCall("Newsletter.Send", $params);
    }
    //------------------------------------------------------------
    // NEWSLETTER [end]
    //------------------------------------------------------------

}
<?php
namespace app\extensions\action;

class InfoBip extends \lithium\action\Controller {
	/*************** http://www.infobip.com/messaging/connectivity/apis ************************/
	// InfoBip account credentials
	private $infobip_username = INFOBIP_USERNAME;
	private $infobip_password = INFOBIP_PASSWORD;

/**
* @var string InfoBip server URI
*
*/
	var $nx_uri = 'http://api.infobip.com/api/v3/sendsms/xml';

	function infoBipSMS ($infobip_username, $infobip_password) {
		$this->infobip_username = $infobip_username;
		$this->infobip_password = $infobip_password;
	}

	/**
	* @return: 
	Status Vale Description
	AUTH_FAILED - 1 Invalid username or password
	XML_ERROR - 2 Incorrect XML format
	NOT_ENOUGH_CREDITS - 3 Not enough credits in user account
	NO_RECIPIENTS - 4 No good recipients
	GENERAL_ERROR - 5 Error in processing your request
	SEND_OK > 0 Number of recipients to which message was sent
	*
	*/
	function send_sms_infobip( $recipient_no_with_plus, $sms_body, $msg_id=""){
		$sender_no_with_plus = INFOBIP_SID;
	if(substr($recipient_no_with_plus,0,1)!="+")
		$recipient_no_with_plus="+".$recipient_no_with_plus;
	$msg_id=($msg_id=="")?time():$msg_id;
	$postUrl  = "http://api.infobip.com/api/v3/sendsms/xml";

	//  XML-formatted data
	$infobip_username=$this->infobip_username;
	$infobip_password=$this->infobip_password;
	$no_of_sms_will_send=ceil(strlen($sms_body)/160);
	for($i=0;$i<$no_of_sms_will_send;$i++)
	{
	$sms_part=substr($sms_body,0,160);
	if(strlen($sms_body)>160)
				$sms_body=substr($sms_body,160);
		$xmlString = "
			<SMS> 
				<authentification> 
					<username>$infobip_username</username> 
					<password>$infobip_password</password> 
				</authentification> 
				<message> 
					<sender>$sender_no_with_plus</sender> 
					<text>$sms_part</text> 
					<flash></flash>
					<type></type> 
					<wapurl></wapurl> 
					<binary></binary> 
					<datacoding></datacoding> 
					<esmclass></esmclass> 
					<srcton></srcton> 
					<srcnpi></srcnpi> 
					<destton></destton> 
					<destnpi></destnpi> 
					<ValidityPeriod>23:59</ValidityPeriod> 
			</message> 
			<recipients> 
				<gsm messageId=\"$msg_id\">$recipient_no_with_plus</gsm> 
			</recipients> 
		</SMS> ";

		//echo $xmlString;
			
		//  previously formatted XML data becomes value of "XML" POST variable 
		$fields  = "XML=" . urlencode($xmlString); 
			
		//  in this example, POST request was made using PHP's CURL 
		$ch  = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $postUrl); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS,  $fields); 
			
		//  response of the POST request 
		$response  = curl_exec($ch);
		curl_close($ch); 
	}

	//  write out the response 
	
	return $response;

	}

}
?>
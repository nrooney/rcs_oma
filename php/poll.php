<?php
/*
 * RCS Web App Notifications PHP Proxy
 * author: Natasha Rooney
 * date: 25/10/12
 *
 * LOTS OF WORK NEEDED HERE - GET WORKS BUT NOT PUT OR POST
 */
 
$path = $_SERVER['DOCUMENT_ROOT'];
$config = $path . "/rcs/php/config.php";
include_once($config);
$rest = $path . "/rcs/php/rest.php";
include_once($rest);

if(isset($_POST['action']) && !empty($_POST['action'])) {
    
    $authuser = $_POST['authuser'];
    $authpw = $_POST['authpw'];
    $cometurl = $_POST['cometurl'];
 
    lasthope($_POST['username'], $authuser, $authpw, $cometurl);
}

/*
 * lasthope
 * http://api.oneapi-gw.gsma.com/notificationchannel/0.1/%2B15554000099/channels/44
 * http://api.oneapi-gw.gsma.com/cometoma/rcsbox-notifieroma/NotificationsCometHandler?username=%2B15554000098
 */
function lasthope($username, $authuser, $authpw, $cometurl){
	global $SERVER_URL, $APIVERSION;
	
	$request = new RestRequest($cometurl, 'POST', "", $authuser, $authpw);
	$request->execute();
	$requestAsArray = json_decode($request->getResponseBody(),true);
	
	// create object in php [messageID, text, sender, status, dateTime]
	$info = $request->getResponseInfo();	
	
	if($info['http_code'] == 200 && (array_key_exists('notificationList', $requestAsArray) || $requestAsArray == null)){		
		$requestAsArray = $requestAsArray['notificationList'];
		$returnArray = array();		
		
		foreach($requestAsArray as $key => $value){
			$onemessage = array("sessionID"=>"", "messageID"=>"", "text"=>"", "sender"=>"", "status"=>"", "dateTime"=>"");		
		
			if(array_key_exists('messageStatusNotification', $value)){
				$onemessage["messageID"] = $value['messageStatusNotification']['messageId'];
				$onemessage["status"] = $value['messageStatusNotification']['status'];
				$onemessage["sessionID"] = $value['messageStatusNotification']['link'][0]['href'];
				$onemessage["sessionID"] = explode("/", $onemessage["sessionID"])[8];
			}
			if(array_key_exists('messageNotification', $value)){
				$onemessage["messageID"] = $value['messageNotification']['messageId'];
				$onemessage["text"] = $value['messageNotification']['chatMessage']['text'];
				$onemessage["sender"] = $value['messageNotification']['senderAddress'];
				$onemessage["sender"] = str_replace("sip:+", "", $onemessage["sender"]);
				$onemessage["sender"] = str_replace("tel:+", "", $onemessage["sender"]);
				$onemessage["sender"] = str_replace("@rcstestconnect.net", "", $onemessage["sender"]);
				$onemessage["dateTime"] = $value['messageNotification']['dateTime'];
				$onemessage["sessionID"] = $value['messageNotification']['sessionId'];
			}
			if(array_key_exists('chatEventNotification', $value)){
				$onemessage["sessionID"] = $value['chatEventNotification']['sessionId'];
			}
			
			$returnArray[] = $onemessage;
		}
		$returnArray = json_encode($returnArray);
		echo $returnArray;
	}
	else{
		echo $info['http_code'];
	}
}


?>
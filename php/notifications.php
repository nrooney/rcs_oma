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
    
    $action = $_POST['action'];
    $authuser = $_POST['authuser'];
    $authpw = $_POST['authpw'];
    
    $channelID = null;
	if(isset($_POST['channelID'])){
		$channelID = $_POST['channelID'];
	}
    
    switch($action) {
        case 'createNotificationChan' : 
        	createNotificationChan($_POST['username'], $authuser, $authpw);
        	break;
        case 'deleteNotificationChan' : 
        	deleteNotificationChan($_POST['username'], $authuser, $authpw);
        	break;
        case 'getIndivNotificationChannel' : 
        	getIndivNotificationChannel($_POST['username'], $authuser, $authpw, $channelID);
        	break;
        case 'getNotificationChanList' : 
        	getNotificationChanList($_POST['username'], $authuser, $authpw);
        	break;
    }
}

/*
 * 4.6.1.Create a new notification channel
 * HTTP POST http://{serverRoot}/notificationchannel/{apiVersion}/{userId}/channels
 */
function createNotificationChan($username, $authuser, $authpw){ //NOT WORKING JUST YET!!!
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'notificationchannel/'.$APIVERSION.'/'.$usernameNoPlus.'/channels';
	//echo $url . '<br/>';
	//echo 'http://api.oneapi-gw.gsma.com/notificationchannel/0.1/%2B15554000002/channels<br/>';

	$action = '{"notificationChannel": {"channelData": {"maxNotifications": 100},"channelLifetime": 0,"channelType": "LongPolling"}}';
	
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();	
	//echo $info['http_code'];
	echo $request->getResponseBody();	
}

/*
 * 4.6.2.Delete a notification channel
 * HTTP DELETE http://{serverRoot}/notificationchannel/{apiVersion}/{userId}/channels/ {channelId}
 */
function deleteNotificationChan($username, $authuser, $authpw, $channelID){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'notificationchannel/'.$APIVERSION.'/'.$usernameNoPlus.'/channels/'.$channelID;
	echo $url;
	
	$data = ''; //NEED DATA!
		
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}


/*
 * 4.6.3.Retrieve an individual notification channel
 * HTTP GET http://{serverRoot}/notificationchannel/{apiVersion}/{userId}/channels/{channelId}
 */
function getIndivNotificationChannel($username, $authuser, $authpw, $channelID){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'notificationchannel/'.$APIVERSION.'/'.$usernameNoPlus.'/channels/'.$channelID;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}


/*
 * 4.6.4.Retrieve a list of notification channels
 * HTTP GET http://{serverRoot}/notificationchannel/{apiVersion}/{userId}/channels
 */
function getNotificationChanList($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'notificationchannel/'.$APIVERSION.'/'.$usernameNoPlus.'/channels';
	echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

?>
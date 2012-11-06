<?php
/*
 * RCS Web App Login PHP Proxy
 * author: Natasha Rooney
 * date: 25/10/12
 *
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
    
    $subID = null;
	if(isset($_POST['subID'])){
		$subID = $_POST['subID'];
	}
    
    switch($action) {
        case 'loginCreateSub' : 
        	loginCreateSub($_POST['username'], $authuser, $authpw);
        	break;
        case 'loginDeleteSub' : 
        	loginDeleteSub($_POST['username'], $authuser, $authpw);
        	break;
        case 'loginReadSub' : 
        	loginReadSub($_POST['username'], $authuser, $authpw, $subID);
        	break;
        case 'loginReadActiveSub' : 
        	loginReadActiveSub($_POST['username'], $authuser, $authpw);
        	break;
        case 'loginRegister' : 
        	loginRegister($_POST['username'], $authuser, $authpw);
        	break;
         case 'loginUnregister' : 
        	loginRegister($_POST['username'], $authuser, $authpw);
        	break;
    }
}

/*
 * 4.1.1.Create a new subscription to session notifications
 * HTTP POST http://{serverRoot}/register/{apiVersion}/{userId}/subscriptions
 */
function loginCreateSub($username, $authuser, $authpw){ 
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'register/'.$APIVERSION.'/'.$usernameNoPlus.'/subscriptions';
	echo $url;
	
	$action = '{"SessionSubscription": {"callbackReference": {"notifyURL": ""},"duration": 0}}';
	//$action = '';
	
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	echo $info['http_code'];
	echo $request->getResponseBody();	
}

/*
 *
 *
 */
function loginDeleteSub($username, $authuser, $authpw){ 
	//HTTP DELETE http://{serverRoot}/register/{apiVersion}/{userId}/subscriptions/{subscriptionId}
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'register/'.$APIVERSION.'/'.$usernameNoPlus.'/subscriptions/'.$subscriptionId;
	echo $url;
	
	$action = '{"chatNotificationSubscription":{ "callbackReference":{"notifyURL":"http://api.oneapi-gw.gsma.com/cometoma/rcsbox-notifieroma/NotificationsCometHandler?username=%2B15554000098","callbackData":"+15554000098"},"duration":900}}';
	
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	echo $info['http_code'];
	echo $request->getResponseBody();
}

/*
 * 4.1.3.Read an individual chat notification subscription
 * HTTP GET http://{serverRoot}/register/{apiVersion}/{userId}/subscriptions/{subscriptionId}
 */
function loginReadSub($username, $authuser, $authpw, $subID){ //NEW PARAMETER!
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'register/'.$APIVERSION.'/'.$usernameNoPlus.'/subscriptions/'.$subID;
	echo $url;
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}


/*
 * 4.1.4.Read the list of active chat notification subscriptions
 * HTTP GET http://{serverRoot}/register/{apiVersion}/{userId}/subscriptions
 */
function loginReadActiveSub($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'register/'.$APIVERSION.'/'.$usernameNoPlus.'/subscriptions';
	echo $url;
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();
}


/*
 * 4.1.5. Register
 * HTTP POST http://{serverRoot}/register/{apiVersion}/{userId}/sessions
 */
function loginRegister($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'register/'.$APIVERSION.'/'.$usernameNoPlus.'/sessions';
	
	$action = "";
	//echo $url . '<br/>';
	
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	//echo $info['http_code'];
	//echo $request->getResponseBody();	
	
	if($info['http_code'] == 204){
		echo "1";
	}
	else{
		echo "0";
	}
}

/*
 * 4.1.6. Unregister
 * HTTP DELETE http://{serverRoot}/register/{apiVersion}/{userId}/sessions
 */
function loginUnregister($username, $authuser, $authpw){
	//HTTP DELETE http://{serverRoot}/register/{apiVersion}/{userId}/sessions
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'register/'.$APIVERSION.'/'.$usernameNoPlus.'/sessions';
	
	$action = "";
	
	$request = new RestRequest($url, 'DELETE', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	
	if($info['http_code'] == 204){
		echo "1";
	}
	else{
		echo "0";
	}
}



?>

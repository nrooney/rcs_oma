
<?php
/*
 * RCS Web App Chat PHP Proxy
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
    
    $notifyURL = null;
	if(isset($_POST['notifyURL'])){  //do we need?!
		$notifyURL = $_POST['notifyURL'];
	}
    $contactID = null;
	if(isset($_POST['contactID'])){  //do we need?!
		$contactID = $_POST['contactID'];
	}
	$subID = null;
	if(isset($_POST['subID'])){  //do we need?!
		$subID = $_POST['subID'];
	}
	$sessionID = null;
	if(isset($_POST['sessionID'])){  //do we need?!
		$sessionID = $_POST['sessionID'];
	}
	$messageID = null;
	if(isset($_POST['messageID'])){  //do we need?!
		$messageID = $_POST['messageID'];
	}
	$participantID = null;
	if(isset($_POST['messageID'])){  //do we need?!
		$participantID = $_POST['messageID'];
	}
	$text = null;
	if(isset($_POST['text'])){  //do we need?!
		$text = $_POST['text'];
	}
    
    switch($action) {
        case 'createNewSub' : //1
        	createNewSub($_POST['username'], $authuser, $authpw, $notifyURL);
        	break;
        case 'deleteSub' : //2
        	deleteSub($_POST['username'], $authuser, $authpw, $subID);
        	break;
        case 'getIndivSubInfo' : //3
        	getIndivSubInfo($_POST['username'], $authuser, $authpw, $subID);
        	break;
        case 'getAllSubInfo' : //4
        	getAllSubInfo($_POST['username'], $authuser, $authpw);
        	break;
         case 'createChat' : //5
        	createChat($_POST['username'], $authuser, $authpw, $contactID);
        	break;
        case 'sendIM' : //6
        	sendIM($_POST['username'], $authuser, $authpw, $contactID, $sessionID, $text);
        	break;
        case 'closeChat' : //7
        	closeChat($_POST['username'], $authuser, $authpw, $contactID, $sessionID);
        	break;
        case 'sendComposing' : //8 
        	sendComposing($_POST['username'], $authuser, $authpw, $contactID, $sessionID);
        	break;
        case 'sendDisplay' : //9
        	sendDisplay($_POST['username'], $authuser, $authpw, $contactID, $sessionID, $messageID);
        	break;
        case 'acceptIMSessions' : //10 
        	acceptIMSessions($_POST['username'], $authuser, $authpw, $contactID, $sessionID);
        	break;
        case 'declineIMSession' : //11
        	declineIMSession($_POST['username'], $authuser, $authpw, $contactID, $sessionID);
        	break;
        case 'getChatSessionInfo' : //12
        	getChatSessionInfo($_POST['username'], $authuser, $authpw, $contactID, $sessionID);
        	break;
        case 'addContactsToGroup' : //13
        	addContactsToGroup($_POST['username'], $authuser, $authpw);
        	break;
        case 'createGroupChat' : //14
        	createGroupChat($_POST['username'], $authuser, $authpw, $sessionID);
        	break;
        case 'sendGroupChatMessage' : //15
        	sendGroupChatMessage($_POST['username'], $authuser, $authpw, $sessionID);
        	break;
        case 'exitGroupChat' : //16
        	exitGroupChat($_POST['username'], $authuser, $authpw, $sessionID, $participantID);
        	break;
        case 'acceptGroupChatInfo' : //17
        	acceptGroupChatInfo($_POST['username'], $authuser, $authpw, $sessionID, $participantID);
        	break;
         case 'declineGroupChat' : //18
        	declineGroupChat($_POST['username'], $authuser, $authpw, $sessionID, $participantID);
        	break;
        case 'getGroupSessionInfo' : //19
        	getGroupSessionInfo($_POST['username'], $authuser, $authpw, $sessionID);
        	break;
        case 'getGroupParticipants' : //20
        	getGroupParticipants($_POST['username'], $authuser, $authpw, $sessionID);
        	break;

    }
}

/*
 * 4.3.1.Create a new subscription to chat notifications
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/subscriptions
 */
function createNewSub($username, $authuser, $authpw, $notifyURL){ 
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$usernameNoPlus.'/subscriptions';
	
	$usernameNoPlus = substr($username, 1);
	
	$action = '{"chatNotificationSubscription":{ "callbackReference":{"notifyURL":"'.$notifyURL.'","callbackData":"'.$username.'"}}}';

	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	echo $info['http_code'];
	echo $request->getResponseBody();
}

/*
 * 4.3.2.Delete a subscription
 * HTTP DELETE http://{serverRoot}/chat/{apiVersion}/{userId}/subscriptions/{subscriptionId}
 */
function deleteSub($username, $authuser, $authpw, $subID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/subscriptions/'.$subID;
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.3.Retrieve information about individual subscription
 * HTTP GET http://{serverRoot}/chat/{apiVersion}/{userId}/subscriptions/{subscriptionId}
 */
function getIndivSubInfo($username, $authuser, $authpw, $subID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/subscriptions/'.$subID;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.4.Retrieve information about all active subscriptions
 * HTTP GET http://{serverRoot}/chat/{apiVersion}/{userId}/subscriptions
 */
function getAllSubInfo($username, $authuser, $authpw){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/subscriptions';

	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.5.Create chat
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}
 */
function createChat($username, $authuser, $authpw, $contactID){ 
	global $SERVER_URL, $APIVERSION;
	
	$usernameNoPlus = substr($username, 1);
	$contactIDNoPlus = substr($contactID, 1);
	
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/%2B'.$usernameNoPlus.'/oneToOne/tel%3A%2B'.$contactIDNoPlus;
	echo $url . "<br/>";
	echo "http://api.oneapi-gw.gsma.com/chat/0.1/%2B15554000001/oneToOne/tel%3A%2B15554000002<br/>";
	
	$action = '';
	
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();	
	echo $info['http_code'];
	echo $request->getResponseBody();
}

/*
 * 4.3.6.Send IM Message
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/ {sessionId}/messages
 */
function sendIM($username, $authuser, $authpw, $contactID, $sessionID, $text){ 
	global $SERVER_URL, $APIVERSION;
	
	if($sessionID == "" || $sessionID == null){
		$sessionID = 'adhoc';
	}
	
	$usernameNoPlus = '%2B' . substr($username, 1);
	$contactIDNoPlus = 'tel%3A%2B' . substr($contactID, 1);
	
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$usernameNoPlus.'/oneToOne/'.$contactIDNoPlus.'/'.$sessionID.'/messages'; 
	
	$action = '{"ChatMessage":{"text":"'.$text.'","reportRequest":"Displayed"}}';
	
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	$requestAsArray = json_decode($request->getResponseBody());
	
	$info = $request->getResponseInfo();	
	if($info['http_code'] == '201'){
		echo $info['http_code'] . ' ';
		$messageID = $requestAsArray->resourceReference->resourceURL; //HERE MAKING THIS WORK!
		$messageID = explode("/", $messageID);
		$messageID = $messageID[count($messageID)-2];
		echo $messageID;
	}
	else{
		echo $info['http_code'] . ' Error - ';
		//$requestAsArray = $requestAsArray->requestError->serviceException->variables;
		print_r($requestAsArray);
	}	
}

/*
 * 4.3.7.Close chat
 * HTTP DELETE http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/ {sessionId}
 */
function closeChat($username, $authuser, $authpw, $contactID, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/oneToOne/'.$contactID.'/'.$sessionID;
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.8.Send is composing
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/ {sessionId}/messages
 */
function sendComposing($username, $authuser, $authpw, $contactID, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/oneToOne/'.$contactID.'/'.$sessionID.'/messages';
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'POST', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.9.Send display
 * HTTP PUT http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/ {sessionId}/messages/{messageId}/status
 */
function sendDisplay($username, $authuser, $authpw, $contactID, $sessionID, $messageID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/oneToOne/'.$contactID.'/'.$sessionID.'/messages/'.$messageID.'/status';
	//um, no idea where we get the message id from yet
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'PUT', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.10. Accept IM session
 * HTTP PUT http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/ {sessionId}/status
 */
function acceptIMSessions($username, $authuser, $authpw, $contactID, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/oneToOne/'.$contactID.'/'.$sessionID.'/status';
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'PUT', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.11. Decline IM session
 * HTTP DELETE http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/ {sessionId}
 */
function declineIMSession($username, $authuser, $authpw, $contactID, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/oneToOne/'.$contactID.'/'.$sessionID;
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.12. Read 1-1 chat session information
 * HTTP GET http://{serverRoot}/chat/{apiVersion}/{userId}/oneToOne/{otherUserId}/{sessionId}
 */
function getChatSessionInfo($username, $authuser, $authpw, $contactID, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/oneToOne/'.$contactID.'/'.$sessionID;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.13. Create group chat
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/group
 */
function createGroupChat($username, $authuser, $authpw){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group';
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'POST', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.14. Add contacts to a group chat
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}/participants
 */
function addContactsToGroup($username, $authuser, $authpw, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID.'/participants';
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'POST', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.15. Send message to a group chat
 * HTTP POST http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}/messages
 */
function sendGroupChatMessage($username, $authuser, $authpw, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID.'/messages';
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'POST', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.16. Exit from a group chat
 * HTTP DELETE http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}/participants/ {participantId}
 */
function exitGroupChat($username, $authuser, $authpw, $sessionID, $participantID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID.'/participants/'.$participantID;
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.17. Accept group chat invitation
 * HTTP PUT http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}/participants/ {participantId}/status
 */
function acceptGroupChatInfo($username, $authuser, $authpw, $sessionID, $participantID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID.'/participants/'.$participantID.'/status';
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'PUT', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.18. Decline group chat invitation
 * HTTP DELETE http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}/participants/ {participantId}
 */
function declineGroupChat($username, $authuser, $authpw, $sessionID, $participantID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID.'/participants/'.$participantID;
	
	$data = ''; //NEED DATA!
	
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.19. Retrieve group chat session information
 * HTTP GET http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}
 */
function getGroupSessionInfo($username, $authuser, $authpw, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}

/*
 * 4.3.20. Read the list of group chat participants
 * HTTP GET http://{serverRoot}/chat/{apiVersion}/{userId}/group/{sessionId}/participants
 */
function getGroupParticipants($username, $authuser, $authpw, $sessionID){ 
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'chat/'.$APIVERSION.'/'.$username.'/group/'.$sessionID.'/participants';
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();

	echo $request->getResponseBody();	
}





?>
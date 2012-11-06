<?php
/*
 * RCS Web App Address Book PHP Proxy
 * author: Natasha Rooney
 * date: 25/10/12
 *     nm
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
    
    $contactID = null;
	if(isset($_POST['contactID'])){
		$contactID = $_POST['contactID'];
	}
	$subID = null;
	if(isset($_POST['subID'])){
		$subID = $_POST['subID'];
	}
	$attributeName = null;
	if(isset($_POST['attributeName'])){
		$subID = $_POST['attributeName'];
	}
	$contactName = null;
	if(isset($_POST['contactName'])){
		$contactName = $_POST['contactName'];
	}
    
    switch($action) {
        case 'postSubscription' : 
        	postSubscription($_POST['username'], $authuser, $authpw);
        	break;
        case 'updateSubscription' : 
        	updateSubscription($_POST['username'], $authuser, $authpw, $subID);
        	break;
        case 'deleteSub' : 
        	deleteSub($_POST['username'], $authuser, $authpw, $subID);
        	break;
        case 'getIndivSubInfo' : 
        	getIndivSubInfo($_POST['username'], $authuser, $authpw, $subID);
        	break;
        case 'getAllSubInfo' : 
        	getAllSubInfo($_POST['username'], $authuser, $authpw);
        	break;
        case 'getContacts' : 
        	getContacts($_POST['username'], $authuser, $authpw);
        	break;
        case 'getContact' : 
        	getContact($_POST['username'], $authuser, $authpw, $contactID);
        	break;
        case 'addEditContact' : 
        	addEditContact($_POST['username'], $authuser, $authpw, $contactName, $contactID);
        	break;
        case 'removeContact' : 
        	removeContact($_POST['username'], $authuser, $authpw, $contactID);
        	break;
        case 'getContactAttributes' : 
        	getContactAttributes($_POST['username'], $authuser, $authpw, $contactID);
        	break;
        case 'getContactAttribute' : 
        	getContactAttribute($_POST['username'], $authuser, $authpw, $attributeName);
        	break;
        case 'updateAttribute' : 
        	updateAttribute($_POST['username'], $authuser, $authpw, $contactName, $contactID);
        	break;
        case 'deleteAttribute' : 
        	deleteAttribute($_POST['username'], $authuser, $authpw, $attributeName);
        	break;
    }
}

/*
 * 4.2.1.Create a new subscription for address book changes
 * HTTP POST http://{serverRoot}/addressbook/{apiVersion}/{userId}/subscriptions/abChanges
 */
function postSubscription($username, $authuser, $authpw){ //NOT WORKING
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/subscriptions/abChanges';

	$action = array(
	            'contact' => array(
	            	"attributeList"=> array( 
	            		"attribute" => array(
							"name" => "display-name",
							"value" => "doyle3"
						)
					),
					"contactId" => "sip:doyle2@solaiemes.com"
	            )
	        );			
	$data = array('data' => urlencode(json_encode($action)));
		//echo $url;
	$request = new RestRequest($url, 'POST', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	
	if($info['http_code'] == '200'){
		echo '200';
	}
	else {
		echo $info['http_code'];
		echo $request->getResponseBody();
	}
}

/*
 * 4.2.2.Update/Extend the duration of the subscription
 * HTTP PUT http://{serverRoot}/addressbook/{apiVersion}/{userId}/subscriptions/abChanges/ {subscriptionId}
 */
function updateSubscription($username, $authuser, $authpw, $subID){ //NOT WORKING
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/subscriptions/abChanges/'.$subID;
	
	/*$action = '{"abChangesSubscription": {"callbackReference": {"callbackData": "addressbookSubscription1","notifyURL": "http://91.215.65.60/cometoma rcsbox-notifieroma/NotificationsCometHandler?username=YYYY"},"duration": 0}}';
	$data = array('data' => urlencode($action));*/

	$action = array(
	            'contact' => array(
	            	"attributeList"=> array( 
	            		"attribute" => array(
							"name" => "display-name",
							"value" => "doyle3"
						)
					),
					"contactId" => "sip:doyle2@solaiemes.com"
	            )
	        );			
	$data = array('data' => urlencode(json_encode($action)));
		//echo $url;
	$request = new RestRequest($url, 'PUT', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	
	if($info['http_code'] == '200'){
		echo '200';
	}
	else {
		echo $info['http_code'];
		echo $request->getResponseBody();
	}
}

/*
 * 4.2.3.Delete a subscription
 * HTTP DELETE http://{serverRoot}/addressbook/{apiVersion}/{userId}/subscriptions/ abChanges/{subscriptionId}
 */
function deleteSub($username, $authuser, $authpw, $subID){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/subscriptions/abChanges/'.$subID;
	//echo $url;
	
	$request = new RestRequest($url, 'DELETE', null, $authuser, $authpw); //MIGHT NEED DATA!
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.4.Retrieve information about individual subscription
 * HTTP GET http://{serverRoot}/addressbook/{apiVersion}/{userId}/subscriptions/abChanges/ {subscriptionId}
 */
function getIndivSubInfo($username, $authuser, $authpw, $subID){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/subscriptions/abChanges/'.$subID;
	//echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.5.Retrieve information about all active subscriptions
 * HTTP GET http://{serverRoot}/addressbook/{apiVersion}/{userId}/subscriptions/abChanges
 */
function getAllSubInfo($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/subscriptions/abChanges';
	//echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.6.Get contact list
 * HTTP GET http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts
 */
function getContacts($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$usernameNoPlus.'/contacts';
	//echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.7.Get contact
 * HTTP GET http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/{contactId}
 */
function getContact($username, $authuser, $authpw, $contactID){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$contactIDNoPlus = 'tel%3A%2B' . substr($contactID, 1);
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$usernameNoPlus.'/contacts/'.$contactIDNoPlus;
	//echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.8.Add / Edit contact
 * HTTP PUT http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/{contactId}
 */
function addEditContact($username, $authuser, $authpw, $contactName, $contactID){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$contactIDNoPlus = 'tel%3A%2B' . substr($contactID, 1);
	
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$usernameNoPlus.'/contacts/'.$contactIDNoPlus;
	//echo $url . '<br/>';
	//echo "http://api.oneapi-gw.gsma.com/addressbook/0.1/%2B15554000001/contacts/tel%3A%2B15554000002<br/>";
	
	//$action = '{"contact":{"contactId":"tel:'.$contactIDNoPlus.'"}}';
	$action = '{"contact": {"attributeList": {"attribute": [{"name": "display-name","value": "'.$contactName.'"}]},"contactId":"tel:'.$contactID.'"}}';
	//echo "ACTION: " . $action . '<br/>';
	
	$request = new RestRequest($url, 'PUT', $action, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	
	if($info['http_code'] == '200' || $info['http_code'] == '201'){
		echo 1;
	}
	else {
		echo 0;
		//echo $request->getResponseBody();
	}	
}

/*
 * 4.2.9.Remove contact
 * HTTP DELETE http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/{contactId}
 */
function removeContact($username, $authuser, $authpw, $contactID){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = str_replace('+', '%2B', $username);
	$contactIDNoPlus = str_replace('tel:+', 'tel%3A%2B', $contactID);
	//echo $contactID;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$usernameNoPlus.'/contacts/sip:+15554000098@rcstestconnect.net ';//.$contactIDNoPlus;
	//echo $url;
	
	$data = []; 
	
	$request = new RestRequest($url, 'DELETE', $data, $authuser, $authpw);
	$request->execute();
	
	$info = $request->getResponseInfo();
	
	if($info['http_code'] == '204'){
		echo 1;
	}
	else {
		echo 0;
	}	
}

/*
 * 4.2.10. Retrieve all attributes of a contact
 * HTTP GET http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/ {contactId}/attributes
 */
function getContactAttributes($username, $authuser, $authpw, $contactID){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/contacts/'.$contactID.'/attributes';
	//echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.11. Retrieve an attribute of a contact
 * HTTP GET http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/{contactId}/attributes/ [ResourceRelPath]
 */
function getContactAttribute($username, $authuser, $authpw, $contactID, $attributeName){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/contacts/'.$contactID.'/attributes/'.$attributeName;
		//echo $url;
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.12. Create/Update an attribute for a contact
 * HTTP PUT http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/ {contactId}/attributes/[ResourceRelPath]
 */
function updateAttribute($username, $authuser, $authpw, $attributeName, $contactID){
	global $SERVER_URL, $APIVERSION;
	$usernameNoPlus = '%2B' . substr($username, 1);
	$contactIDNoPlus = 'tel%3A%2B' . substr($contactID, 1);
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$usernameNoPlus.'/contacts/'.$contactIDNoPlus.'/attributes/display-name';
	echo  '<br/>' . $url . '<br/>';
	
	//$action = '{"contact": {"attributeList": {"attribute": [{"name": "display-name","value": "tel:'.$contactID.'"}]},"contactId":"tel:'.$contactID.'"}}';
	$action = '{"attribute": [{"name": "display-name","value": "tel:'.$contactID.'"}]}';
	
	$request = new RestRequest($url, 'PUT', $action, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/*
 * 4.2.13. Delete an attribute of a contact
 * HTTP DELETE http://{serverRoot}/addressbook/{apiVersion}/{userId}/contacts/ {contactId}/attributes/[ResourceRelPath]
 */
function deleteAttribute($username, $authuser, $authpw, $attributeName){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'addressbook/'.$APIVERSION.'/'.$username.'/contacts/'.$contactID.'/attributes/'.$attributeName;
	//echo $url;
	
	$request = new RestRequest($url, 'DELETE', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}


?>
<?php
/*
 * RCS Web App Capabilities PHP Proxy
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
    
    switch($action) {
        case 'getCapabilities' : 
        	getCapabilities($_POST['username'], $authuser, $authpw);
        	break;
        case 'setCapabilities' : 
        	setCapabilities($_POST['username'], $authuser, $authpw);
        	break;
    }
}

/*
 * 4.5.1.Retrieve own capabilities
 * HTTP GET http://{serverRoot}/capabilities/{apiVersion}/{userId}
 */
function getCapabilities($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'capabilities/'.$APIVERSION.'/'.$username;
	echo $url;
	
	$request = new RestRequest($url, 'GET', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}

/* 
 * 4.5.2.Set own capabilities
 * HTTP PUT http://{serverRoot}/capabilities/{apiVersion}/{userId}
 */
function setCapabilities($username, $authuser, $authpw){
	global $SERVER_URL, $APIVERSION;
	$url = $SERVER_URL.'capabilities/'.$APIVERSION.'/'.$username;

	$request = new RestRequest($url, 'PUT', null, $authuser, $authpw);
	$request->execute();
	
	echo $request->getResponseBody();
}


?>
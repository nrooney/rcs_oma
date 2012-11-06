<?php
/*
 * RCS Web App Weather PHP Proxy
 * author: Natasha Rooney
 * date: 25/10/12
 *
 * LOTS OF WORK NEEDED HERE - GET WORKS BUT NOT PUT OR POST
 */
 
$path = $_SERVER['DOCUMENT_ROOT'];
$rest = $path . "/rcs/php/rest.php";
include_once($rest);
$APIKEY = '1a4b57ad8c140130120411';

if(isset($_POST['action']) && !empty($_POST['action'])) {
    
    //$authuser = $_POST['authuser'];
    $location = $_POST['location'];
    getWeather($location);
}

/*
 * getWeather
 * http://free.worldweatheronline.com/feed/weather.ashx?q=London&format=json&num_of_days=2&key=1a4b57ad8c140130120411
 */
function getWeather($location){
	global $SERVER_URL, $APIKEY;
	
	$request = new RestRequest("http://free.worldweatheronline.com/feed/weather.ashx?q=".$location."&format=json&num_of_days=2&key=".$APIKEY, 'GET', "");
	//echo "http://free.worldweatheronline.com/feed/weather.ashx?q=".$location."&format=json&num_of_days=2&key=".$APIKEY;
	
	$request->execute();
	$info = $request->getResponseInfo();
	
	if($info['http_code'] == 200){
		echo $request->getResponseBody();
	}
	else{
		echo "Error - cannot gather weather info";
	}
}


?>
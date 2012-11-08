/*
 * RCS Demo App JavaScript File
 * Author: Natasha Rooney
 * Date: 30/10/12
 *
 * TODO:
 * - change to a real sessionID
 * - stop notifications on logout
 * - deleting contact bug
 */

var loginusername;
var loginpassword;
var loginurl = 'http://localhost:8888/rcs/php/login.php';
var addressbookurl = 'http://localhost:8888/rcs/php/addressbook.php';
var notificationurl = 'http://localhost:8888/rcs/php/notifications.php';
var chaturl = 'http://localhost:8888/rcs/php/chat.php';
var pollurl = 'http://localhost:8888/rcs/php/poll.php';
var weatherurl = 'http://localhost:8888/rcs/php/weather.php';
var sessionID = "";
var cometURL = "";
var currentContact = "";

var apiparameters = new Array();
apiparameters = [];


jQuery(document).ready(function(){

	/*
	 * LOGIN
	 */
	jQuery('#loginform #loginsubmit').live('click', function(event){
		event.preventDefault();
		
		loginusername = jQuery('#loginform #usernameinput').val(); //+15554000099
		loginpassword = jQuery('#loginform #passwordinput').val(); //06YG*h5{	
		login();
	});
	
	/*
	 * LOGOUT
	 */
	jQuery('#logoutform #logoutsubmit').live('click', function(event){
		event.preventDefault();
		logout();
	});
	
	 
	/*
	 * ADD NEW CONTACT
	 */
	//var chatcontact = "";
	jQuery('#newContactform #newcontactsubmit').live('click', function(event){
		event.preventDefault();
		newContactName = jQuery('#newContactform #newcontactnameinput').val(); //+15554000099
		newContactTel = jQuery('#newContactform #newcontacttelinput').val(); //06YG*h5{
		chatcontact = addNewContact(newContactName, newContactTel);	
	});	 
	 

	/*
	 * INSTIGATE CHAT
	 */
	var chatcontact = "";
	jQuery('div#acontact div.greenarrow').live('click', function(event){
		currentContact = jQuery(this).parent().children('.msisdn').attr('id');
		
		jQuery('.chatbox').hide();
		jQuery('.chatbox.'+currentContact).show();
		jQuery("#"+currentContact).parent().children('.contactname').css('font-weight','normal');
		jQuery("#"+currentContact).parent().children('.contactname').css('background','#d1dff1');
	});
	
	/*
	 * CHAT
	 */
	 jQuery('div#chatcontainer .chatbox .typemessagearea .sendmessagebutton').live('click', function(event){
	 	event.preventDefault();
	 	
		var chatmessage = jQuery('div#chatcontainer .chatbox.'+currentContact+' .typemessagearea .message').val();
		jQuery('div#chatcontainer .chatbox.'+currentContact+' .typemessagearea .message').val('');  
	 	
	 	var weatherregex = new RegExp('^(/weather)','im');
	 	var sendweatherregex = new RegExp('^(/weather -send)','im');
	 	
	 	if(sendweatherregex.test(chatmessage)){
	 		var weatherstate = weather(chatmessage, sendweatherregex, true);
	 		//now send me!
	 		chat(weatherstate);
	 	}
	 	else if(weatherregex.test(chatmessage)){
	 		var weatherstate = weather(chatmessage, weatherregex, false);
	 		jQuery('.chatbox.'+currentContact+' .displaymessagearea').append(weatherstate);
	 	}
	 	else {
		 	chat(chatmessage);
		}
	 });
	 
	 /*
	  * DELETE CONTACT
	  */
	 jQuery('.deletecontact').live('click',function(e){
	  	e.preventDefault();
	  	var contactmsisdn = jQuery(this).parent().parent().children('.msisdn').text();
	  	deleteContact(contactmsisdn);
	 });
	 
	 /*
	  * Error box
	  */
	 jQuery('#closeerrorbox').live('click', function(){
	  	jQuery('#errorbox').hide();
	 });
});


/*
 * WEATHER
 * Get weather using http://free.worldweatheronline.com APIs
 * tosend bool is set here as messages to send must be less then 140 chars, so weatherstate string is composed to be less then 140 chars
 */
function weather(chatmessage, regex, tosend){

	jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage weather"><div class="status">Sent</div><div class="message">me: ' + chatmessage + '</div></div>');
	
	chatmessage = chatmessage.replace(regex,"");
	chatmessage = chatmessage.trim();
	chatmessage = chatmessage.replace(" ","+");
	
	var weatherArray = [];
	var weatherstate = "";
	
	jQuery.ajax({ 
		url: weatherurl,
		data: {action: "weather", location : chatmessage},
		async: false,
		type: 'post',
		success: function(output) {
			weatherArray = jQuery.parseJSON(output);
			
			if(weatherArray.data.error){
				jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage weather"><div class="status">Error</div><div class="message">weather: There has been an error with your weather request, please try again later</div></div>');
			}
			else{
				var currentArray = weatherArray.data.current_condition[0];
				var todayArray = weatherArray.data.weather[0];
				var tomorrowArray = weatherArray.data.weather[1];
				//console.log(currentArray);
				//console.log(todayArray);
				//console.log(tomorrowArray);
				
				if(tosend){
					weatherstate = weatherArray.data.request[0].query+" | Now: "+currentArray.temp_C+"&#8451; "+currentArray.weatherDesc[0].value+ " | Tomorrow: "+tomorrowArray.tempMaxC+"&#8451; "+tomorrowArray.weatherDesc[0].value;
				}
				else{
					
					weatherstate = "<div class='weatherMessage'>"+weatherArray.data.request[0].type+": "+weatherArray.data.request[0].query+"<table id='weatherTable'><tr><td class='label'>Current</td><td class='label'>Today</td><td class='label'>Tomorrow</td></tr><tr><td id='currentTemp'>"+currentArray.temp_C+"&#8451;</td><td id='todayTempMax'>High: "+todayArray.tempMaxC+"&#8451;</td><td id='tomorrowTempMax'>High: "+tomorrowArray.tempMaxC+"&#8451;</td></tr><tr><td id='currentTemp'></td><td id='todayTempMin'>Low: "+todayArray.tempMinC+"&#8451;</td><td id='tomorrowTempMin'>Low: "+tomorrowArray.tempMinC+"&#8451;</td></tr><tr><td id='currentWeather'><img src='"+currentArray.weatherIconUrl[0].value+"'/></td><td id='todayWeather'><img src='"+todayArray.weatherIconUrl[0].value+"'/></td><td id='tomorrowWeather'><img src='"+tomorrowArray.weatherIconUrl[0].value+"'/></td></tr><tr><td id='currentRain'>"+currentArray.precipMM+"mm</td><td id='todayRain'>"+todayArray.precipMM+"mm</td><td id='tomorrowRain'>"+tomorrowArray.precipMM+"mm</td></tr></table></div>";
				}
			}	
		}
	});
	
	return weatherstate;
	
}

/*
 * CHAT
 * Send an IM taking the message as a perameter, must be less then 140 chars
 */
function chat(chatmessage){
	
	//4.3.6.Send IM Message  ***************************************************
	apiparameters['contactID'] = "+" + currentContact;
	apiparameters['sessionID'] = "adhoc"; // change this to $sessionID to manage the session yourself
	apiparameters['text'] = chatmessage;
	console.log(" session " + apiparameters['sessionID'] );

	var confirmation = query(chaturl, 'sendIM', loginusername, loginusername, loginpassword, apiparameters, false);
	
	apiparameters = [];
	
	try{
		var httpcode = confirmation.substr(1,3);
		var messageID = "";
		var time = "";
		var timeObj = new Date();
		var currentTime = timeObj.getHours() + ":" + ((timeObj.getMinutes() < 10) ? '0' + timeObj.getMinutes() : timeObj.getMinutes());
		//console.log('httpcode: ' + httpcode);
		
		if(httpcode == '201'){
			messageID = confirmation.substr(5);
			jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage" id="'+messageID+'"><div class="status">Sent</div><div class="time">'+ currentTime +'</div><div class="message">me: ' + chatmessage + '</div></div>');
		}
		else{
			errormessage = confirmation.substr(4);
			console.log('error: '+errormessage + ' | '+confirmation + '|');
			jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage" id="'+messageID+'"><div class="status">Error</div><div class="time">'+ currentTime +'</div><div class="error">me: ');// + errormessage + '</div></div>');
		}
	}
	catch(error){
  		jQuery('#errorbox').show();
  		jQuery('#errorbox #errors').text('Error initiating notifications channel.' + error.message);
  	}
}

/*
 * START NOTIFICATIONS POLLING
 * Start polling the comet url, every 3 second here. When a message is received, send message and meta to screen
 */
function beginNotifications(){
	
	cometArray = "";
	
	setInterval(function() {
		jQuery.ajax({ 
			url: pollurl,
			data: {action: 'lasthope', username: loginusername, authuser: loginusername, authpw: loginpassword, cometurl: cometURL},
			type: 'post',
			success: function(output) {
				console.log("Data type: " + typeof output);
				console.log('Output: ' + output);
				cometArray = jQuery.parseJSON(output);
			}
		});
		
		for(notification in cometArray){
			var messageTime = "";
			var messageID = "";
			var sender = "";
			var sessionID = "";
			var status = "";
			var text = "";
		
			if(cometArray[notification]['dateTime'] != ""){
				dateTime = cometArray[notification]['dateTime'];
				dateTime = new Date(dateTime);
				var messageTime = dateTime.getHours() + ":" + ((dateTime.getMinutes() < 10) ? '0' + dateTime.getMinutes() : dateTime.getMinutes());
			}
			if(cometArray[notification]['messageID'] != ""){
				messageID = cometArray[notification]['messageID'];
			}
			if(cometArray[notification]['sender'] != ""){
				sender = cometArray[notification]['sender'];
			}
			if(cometArray[notification]['sessionID'] != ""){
				sessionID = cometArray[notification]['sessionID'];
			}
			if(cometArray[notification]['sessionID'] != ""){
				status = cometArray[notification]['status'];
				
				if(status == "Delivered"){
					jQuery('div#' + messageID).children('.status').text("Delivered");
				}
			}
			if(cometArray[notification]['text'] != ""){
				text = cometArray[notification]['text'];
				var nameOfSender = jQuery('#'+sender).parent().children('.contactname').text(); 
				
				if (document.getElementById(messageID) == null){
					jQuery('.chatbox.'+sender+' div.displaymessagearea').append('<div class="amessage" id="'+messageID+'"><div class="status">Received</div><div class="time">'+ messageTime +'</div><div class="message">'+nameOfSender+': ' + text + '</div></div>');
					
					showNewMessageAlert(sender);
				}
				
					
			}
		}
	}, 5000);
}

/*
 * ALERT USER WHEN RECEIVED NEW MESSAGE FROM SENDER
 * Change background and font-weight to bold when user receives a message but is not currently chatting to the sender
 */
function showNewMessageAlert(sender){
	
	console.log("#"+sender+" - getting alert");

	if(jQuery('.chatbox.'+sender).css('display') == 'none'){
		jQuery("#"+sender).parent().children('.contactname').css('font-weight','bold');
		jQuery("#"+sender).parent().children('.contactname').css('background','#9abbe2');
	}
}

/*
 * LOGIN
 * login using the msisdn and password registers with the gateway
 */
function login(){
	// 4.1.5. Register *************************************************** 
	var loginresult = query(loginurl, 'loginRegister', loginusername, loginusername, loginpassword, apiparameters, false);
	
	if(loginresult == '1'){
		jQuery('section#login').addClass('small');
		jQuery('section#activeapp').css('opacity', '1'); 
		
		jQuery('#loginform').hide();
		jQuery('#logoutform').show();
		jQuery('p#logininfo').text('');
		jQuery('div#mynumber').text('my number: ' + loginusername);
		
		addContacts();
		instigateChat();
	}
	else{
		jQuery('p#logininfo').text('Error');
		jQuery('#errorbox').show();
	  	jQuery('#errorbox #errors').text('Error logging in, either offline or wrong user/password');
	}
}

/*
 * LOGOUT
 * logout and kill the session
 */
function logout(){

	// 4.1.6. Register *************************************************** 
	var logoutresult = query(loginurl, 'loginUnregister', loginusername, loginusername, loginpassword, apiparameters, false);
	
	if(logoutresult == '1'){
		jQuery('section#login').removeClass('small');
		jQuery('section#activeapp').css('opacity', '0.5'); //fix for other browsers
		
		jQuery('#logoutform').hide();
		jQuery('#loginform').show();
		jQuery('p#logininfo').text('');
		jQuery('div#contactlist').html('');
		jQuery('div#chatcontainer').text('');
	}
	else{
		jQuery('#errorbox').show();
	  	jQuery('#errorbox #errors').text('Error logging out');
	}
}

/*
 * ADD NEW CONTACT
 * add a new contact taking the name and tel from the form in the address book and adding new user to the addressbook
 */
function addNewContact(newContactName, newContactTel){

	//4.2.8.Add / Edit contact ***************************************************
	apiparameters['contactID'] = newContactTel;
	apiparameters['contactName'] = newContactName;
	var newcontact = query(addressbookurl, 'addEditContact',loginusername, loginusername, loginpassword, apiparameters, false);
	
	if(newcontact == 1){
		addContactElementAndChatBox(newContactName, newContactTel);
	}
	else{
		jQuery('#errorbox').show();
	  	jQuery('#errorbox #errors').text('Error adding, please try again');
	}
}

/*
 * DELETE CONTACT
 * delete the contact and remove chat box and addressbook entry
 */
function deleteContact(contactmsisdn){
	//4.2.9.Remove contact ***************************************************
  	apiparameters['contactID'] = contactmsisdn;
  	console.log('deleting: ' + contactmsisdn);
  	var contactnumberNoTel =   contactmsisdn.replace("tel:", "");
	contactnumberNoTel =  contactnumberNoTel.replace("+", "");
  	var confirmDelete = query(addressbookurl, 'removeContact',  loginusername, loginusername, loginpassword, apiparameters, false);
  	
  	try{
	  	apiparameters = [];
	  	console.log(confirmDelete);
	  	
	  	if(confirmDelete == '1'){
	  		jQuery('#'+contactmsisdn).parent().html('');
	  		jQuery('div.chatbox.'+contactnumberNoTel).parent().html('');
	  	}
	  	else{
	  		jQuery('#errorbox').show();
	  		jQuery('#errorbox #errors').text('Error deleting, please try again.');
	  	}
	}
	catch(error){
  		jQuery('#errorbox').show();
  		jQuery('#errorbox #errors').text('Error initiating notifications channel.' + error.message);
  	}
}


/*
 * INSTIGATE CHAT
 * get the comet url, sessionID, and begin polling!
 */
function instigateChat(){
	// 4.6.1.Create a new notification channel ***************************************************
	var notificationChannelData = query(notificationurl, 'createNotificationChan',loginusername, loginusername, loginpassword, apiparameters, false);
	
	try{
		notificationChannelData = jQuery.parseJSON(notificationChannelData);
		cometURL = notificationChannelData.notificationChannel.callbackURL;
		sessionID = notificationChannelData.notificationChannel.resourceURL;
		sessionID = sessionID.split('/');
		sessionID = sessionID[sessionID.length-1];
		//console.log(cometURL + " | " +sessionID);
		
		// 4.3.1.Create a new subscription to chat notifications ***************************************************
		apiparameters['notifyURL'] = cometURL;
		var chatNotificationData = query(chaturl, 'createNewSub', loginusername, loginusername, loginpassword, apiparameters, false);
		apiparameters = [];
		console.log(chatNotificationData);
		
		//start notifications!
		beginNotifications();
	}
  	catch(error){
  		jQuery('#errorbox').show();
  		jQuery('#errorbox #errors').text('Error initiating notifications channel.' + error.message);
  	}
}

/*
 * ADD CONTACTS LIST
 * grab contacts using APIs and pass to addContactElementAndChatBox to add to addressbook
 */
function addContacts(){

	// 4.2.6.Get contact list *************************************************** 
	var allContactsResult = query(addressbookurl, 'getContacts', loginusername, loginusername, loginpassword, apiparameters, false);	
	
	
	try{
		allContactsResult = jQuery.parseJSON(allContactsResult);
		var contactarray = allContactsResult.contactCollection.contact;
		
		for(contact in contactarray){
			var contactname = contactarray[contact].attributeList.attribute[0].value;
			var contactnumber = contactarray[contact].contactId;
			addContactElementAndChatBox(contactname, contactnumber);
		}	
	}
	catch(error){
  		jQuery('#errorbox').show();
  		jQuery('#errorbox #errors').text('Error initiating notifications channel.' + error.message);
  	}
		
	/*
	 * Show full contact info
	 */
	jQuery('div#acontact .contactname').live('click', function(){
		jQuery(this).parent().children('.msisdn').toggle();
	});

} 

/*
 * ADD CONTACT LIST ELEMENT
 * get contact data and place into addressbook
 */
function addContactElementAndChatBox(contactname, contactnumber){
	var contactnumberNoTel =  contactnumber.replace("tel:", "");
	contactnumberNoTel =  contactnumberNoTel.replace("+", "");
	
	//add to list   <a href='#' class='deletecontact'>delete</a> NEEDS TO GO IN THIS LIST SOMEWHERE!!!!
	var contact = 	"<div id='acontact'><div class='greenarrow'><img src='/rcs/images/greenarrow.png'/></div><div class='contactname'>"+contactname+"</div><div class='msisdn' id='"+contactnumberNoTel+"'>"+contactnumber+"</div></div>";	
	jQuery("div#contactlist").append(contact);
	
	//add chat box
	jQuery('.chatbox').hide();
	jQuery('div#chatcontainer').append('<div class="chatbox '+contactnumberNoTel+'"><h3>Chatting with '+contactname+'</h3><div class="displaymessagearea"></div><div class="typemessagearea"><form class="chatform"><textarea class="message"/><input type="submit" value="Send" class="sendmessagebutton"/></form></div></div>');
}



/*
 * QUERY
 * ajax query to php proxy files to grab API data
 */
function query(theurl, functioncall, mainusername, userforauth, pwforauth, apiparameters, asyncbool){
	//add params
	var dataforajax = {action: functioncall, username: mainusername, authuser: userforauth, authpw: pwforauth};	
	if(apiparameters != []){
		for(var perameter in apiparameters){	
			dataforajax[perameter] = apiparameters[perameter];
		}
	}
 	
	var queryresult = jQuery.ajax({ 
		url: theurl,
		data: dataforajax,
		type: 'post',
		async : asyncbool,
		success: function(output) {
			//console.log("Data type: " + typeof output);
			//console.log('Output: ' + output);
			queryoutput = output;
		}
	}).responseText;
	
	return queryresult;
}


/*
 * PARSE JSON
 * take a json formatted string and return an array
 */
function parseJSON(json){
	//console.log(json);
	var parsedjson = JSON.parse(json, function (key, value) {
		var type;
		if (value && typeof value === 'object') {
			type = value.type;
			if (typeof type === 'string' && typeof window[type] === 'function') {
				return new (window[type])(value);
			}
		}
		return value;
	});
	return parsedjson;
}

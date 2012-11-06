/*
 * - add errors to query calls
 * - change to a real sessionID
 * - deleting a contact keeps the div, get rid of this
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
		console.log('contact id: ' + currentContact);
		
		jQuery('.chatbox').hide();
		jQuery('.chatbox.'+currentContact).show();
	});
	
	/*
	 * CHAT
	 */
	 jQuery('div#chatcontainer .chatbox .typemessagearea .sendmessagebutton').live('click', function(event){
	 	event.preventDefault();
	 	
		var chatmessage = jQuery('div#chatcontainer .chatbox.'+currentContact+' .typemessagearea .message').val(); 
		console.log('get ready to send: ' + chatmessage + " | " + sessionID + " | " + currentContact + ' | ' + 'div#chatcontainer .chatbox.'+currentContact+' .typemessagearea .message');
	 	
	 	var regex = new RegExp('^(/weather)','im');
	 	var match = regex.test(chatmessage);
	 	
	 	if(match){
	 		console.log('match');
	 		weather(chatmessage, regex);
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
 * WEATHER - HERE!
 */
function weather(chatmessage, regex){

	jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage weather"><div class="status">Sent</div><div class="message">me: ' + chatmessage + '</div></div>');
	
	chatmessage = chatmessage.replace(regex,"");
	chatmessage = chatmessage.trim();
	console.log('weather' + chatmessage);
	
	var weatherArray = [];
	
	jQuery.ajax({ 
		url: weatherurl,
		data: {action: "weather", location : chatmessage},
		async: false,
		type: 'post',
		success: function(output) {
			weatherArray = jQuery.parseJSON(output);
			console.log(weatherArray.data);
			
			if(weatherArray.data.error){
				jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage weather"><div class="status">Error</div><div class="message">weather: There has been an error with your weather request, please try again later</div></div>');
			}
			else{
				var currentTemp = '';
				var todayTemp = '';
				var tomorrowTemp = '';
				var currentWeather = '';
				var todayWeather = '';
				var tomorrowWeather = '';
				var currentRain = '';
				var todayRain = '';
				var tomorrowRain = '';
				
				var currentArray = weatherArray.data.current_condition[0];
				var todayArray = weatherArray.data.weather[0];
				var tomorrowArray = weatherArray.data.weather[1];
				
				console.log(currentArray);
				console.log(todayArray);
				console.log(tomorrowArray);
				
				jQuery('.chatbox.'+currentContact+' .displaymessagearea').append("<div class='weatherMessage'>"+weatherArray.data.request[0].type+": "+weatherArray.data.request[0].query+"<table id='weatherTable'><tr><td class='label'>Current</td><td class='label'>Today</td><td class='label'>Tomorrow</td></tr><tr><td id='currentTemp'>"+currentArray.temp_C+"&#8451;</td><td id='todayTempMax'>High: "+todayArray.tempMaxC+"&#8451;</td><td id='tomorrowTempMax'>High: "+tomorrowArray.tempMaxC+"&#8451;</td></tr><tr><td id='currentTemp'></td><td id='todayTempMin'>Low: "+todayArray.tempMinC+"&#8451;</td><td id='tomorrowTempMin'>Low: "+tomorrowArray.tempMinC+"&#8451;</td></tr><tr><td id='currentWeather'><img src='"+currentArray.weatherIconUrl[0].value+"'/></td><td id='todayWeather'><img src='"+todayArray.weatherIconUrl[0].value+"'/></td><td id='tomorrowWeather'><img src='"+tomorrowArray.weatherIconUrl[0].value+"'/></td></tr><tr><td id='currentRain'>"+currentArray.precipMM+"mm</td><td id='todayRain'>"+todayArray.precipMM+"mm</td><td id='tomorrowRain'>"+tomorrowArray.precipMM+"mm</td></tr></table></div>");
			}
		}
	});
	
	
	
	}

/*
 * CHAT
 */
function chat(chatmessage){
	//4.3.6.Send IM Message  ***************************************************
	apiparameters['contactID'] = "+" + currentContact;
	apiparameters['sessionID'] = sessionID; //NEED TO GET THIS FROM createChat
	apiparameters['text'] = chatmessage;
	console.log('contact: ' + apiparameters['contactID'])
	var confirmation = query(chaturl, 'sendIM', loginusername, loginusername, loginpassword, apiparameters, false);
	apiparameters = [];
	
	var httpcode = confirmation.substr(1,3);
	var messageID = "";
	console.log('httpcode: ' + httpcode);
	
	if(httpcode == '201'){
		messageID = confirmation.substr(5);
		jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage" id="'+messageID+'"><div class="status">Sent</div><div class="message">me: ' + chatmessage + '</div></div>');
		console.log('message:' + messageID);
	}
	else{
		errormessage = confirmation.substr(4);
		console.log('error: '+errormessage + ' | '+confirmation + '|');
		jQuery('.chatbox.'+currentContact+' .displaymessagearea').append('<div class="amessage" id="'+messageID+'"><div class="status">Error</div><div class="error">me: ');// + errormessage + '</div></div>');
	}
}

/*
 * START NOTIFICATIONS POLLING
 */
function beginNotifications(){
	
	cometArray = "";
	
	setInterval(function() {
		jQuery.ajax({ 
			url: pollurl,
			data: {action: 'lasthope', username: loginusername, authuser: loginusername, authpw: loginpassword, cometurl: cometURL},
			type: 'post',
			success: function(output) {
				//console.log("Data type: " + typeof output);
				//console.log('Output: ' + output);
				cometArray = jQuery.parseJSON(output);
			}
		});
		
		console.log(cometArray);
		
		for(notification in cometArray){
			var dateTime = "";
			var messageID = "";
			var sender = "";
			var sessionID = "";
			var status = "";
			var text = "";
		
			if(cometArray[notification]['dateTime'] != ""){
				dateTime = cometArray[notification]['dateTime'];
			}
			if(cometArray[notification]['messageID'] != ""){
				messageID = cometArray[notification]['messageID'];
			}
			if(cometArray[notification]['sender'] != ""){
				sender = cometArray[notification]['sender'];
				console.log('sender: ' + sender);
			}
			if(cometArray[notification]['sessionID'] != ""){
				sessionID = cometArray[notification]['sessionID'];
			}
			if(cometArray[notification]['sessionID'] != ""){
				status = cometArray[notification]['status'];
				
				if(status == "Delivered"){
					//console.log('Delivered');
					jQuery('div#' + messageID).children('.status').text("Delivered");
				}
			}
			if(cometArray[notification]['text'] != ""){
				text = cometArray[notification]['text'];
				var nameOfSender = jQuery('#'+sender).parent().children('.contactname').text(); //BUG!!!!
				
				if (document.getElementById(messageID) == null){
					jQuery('.chatbox.'+sender+' div.displaymessagearea').append('<div class="amessage" id="'+messageID+'"><div class="status">Received</div><div class="message">'+nameOfSender+': ' + text + '</div></div>');
				}
				
					
			}
		}
	}, 5000);
}

/*
 * LOGIN
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
		jQuery('p#logininfo').text('Incorrect username or password');
	}
}

/*
 * LOGOUT
 */
function logout(){
	console.log('try');

	// 4.1.6. Register *************************************************** 
	var logoutresult = query(loginurl, 'loginUnregister', loginusername, loginusername, loginpassword, apiparameters, false);
	
	console.log('result' + logoutresult);
	
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
 */
function deleteContact(contactmsisdn){
	//4.2.9.Remove contact ***************************************************
  	apiparameters['contactID'] = contactmsisdn;
  	console.log('deleting: ' + contactmsisdn);
  	var contactnumberNoTel =   contactmsisdn.replace("tel:", "");
	contactnumberNoTel =  contactnumberNoTel.replace("+", "");
  	var confirmDelete = query(addressbookurl, 'removeContact',  loginusername, loginusername, loginpassword, apiparameters, false);
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


/*
 * INSTIGATE CHAT
 */
function instigateChat(){
	// 4.6.1.Create a new notification channel ***************************************************
	var notificationChannelData = query(notificationurl, 'createNotificationChan',loginusername, loginusername, loginpassword, apiparameters, false);
	notificationChannelData = jQuery.parseJSON(notificationChannelData);
	cometURL = notificationChannelData.notificationChannel.callbackURL;
	sessionID = notificationChannelData.notificationChannel.resourceURL;
	sessionID = sessionID.split('/');
	sessionID = sessionID[sessionID.length-1];
	console.log(cometURL + " | " +sessionID);
	
	// 4.3.1.Create a new subscription to chat notifications ***************************************************
	apiparameters['notifyURL'] = cometURL;
	var chatNotificationData = query(chaturl, 'createNewSub', loginusername, loginusername, loginpassword, apiparameters, false);
	apiparameters = [];
	console.log(chatNotificationData);
	
	//start notifications!
	beginNotifications();
}

/*
 * ADD CONTACTS LIST
 */
function addContacts(){

	// 4.2.6.Get contact list *************************************************** 
	var allContactsResult = query(addressbookurl, 'getContacts', loginusername, loginusername, loginpassword, apiparameters, false);	
	allContactsResult = jQuery.parseJSON(allContactsResult);
	var contactarray = allContactsResult.contactCollection.contact;
	//var noOfContacts = contactarray.length;
	//console.log('number of contacts' + noOfContacts);
	
	for(contact in contactarray){
		//console.log(contact);
		var contactname = contactarray[contact].attributeList.attribute[0].value;
		var contactnumber = contactarray[contact].contactId;
		addContactElementAndChatBox(contactname, contactnumber);
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
 */
function addContactElementAndChatBox(contactname, contactnumber){
	var contactnumberNoTel =  contactnumber.replace("tel:", "");
	contactnumberNoTel =  contactnumberNoTel.replace("+", "");
	console.log('contactnumberNoTel ' + contactnumberNoTel);
	
	//add to list   <a href='#' class='deletecontact'>delete</a> NEEDS TO GO IN THIS LIST SOMEWHERE!!!!
	var contact = 	"<div id='acontact'><div class='greenarrow'><img src='/rcs/images/greenarrow.png'/></div><div class='contactname'>"+contactname+"</div><div class='msisdn' id='"+contactnumberNoTel+"'>"+contactnumber+"</div></div>";	
	jQuery("div#contactlist").append(contact);
	
	//add chat box
	jQuery('.chatbox').hide();
	jQuery('div#chatcontainer').append('<div class="chatbox '+contactnumberNoTel+'"><h3>Chatting with '+contactname+'</h3><div class="displaymessagearea"></div><div class="typemessagearea"><form class="chatform"><textarea class="message"/><input type="button" value="Send" class="sendmessagebutton"/></form></div></div>');
}



/*
 * QUERY
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
	}).responseText;;
	
	return queryresult;
}


/*
 * PARSE JSON
 */
function parseJSON(json){
	console.log(json);
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

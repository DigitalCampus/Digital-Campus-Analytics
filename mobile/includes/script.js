
var API_URL = "/scorecard/api/";

function showStatistics(){
	if(!loggedIn()){
		showLogin();
		return;
	}
	$('#content').empty();
}

function showDeliveries(){
	if(!loggedIn()){
		showLogin();
		return;
	}
	$('#content').empty();
}

function showTasks(){
	if(!loggedIn()){
		showLogin();
		return;
	}
	$('#content').empty();
}

function showOverdue(){
	if(!loggedIn()){
		showLogin();
		return;
	}
	$('#content').empty();
}

function showLogin(){
	$('#content').empty();
	$('#content').append("<h1>Login</h1>");
	
	$('#content').append("<div class='formblock'>" +
		"<div class='formlabel'>Username:</div>" +
		"<div class='formfield'><input type='text' name='username' id='username'></input></div>" +
		"</div>");
	
	$('#content').append("<div class='formblock'>"+
		"<div class='formlabel'>Password</div>" +
		"<div class='formfield'><input type='password' name='password' id='password'></input></div>" +
		"</div>");
	
	$('#content').append("<div class='formblock'>" +
			"<div class='formfield'><input type='button' name='submit' value='Login' onclick='login()'></input></div>" +
			"</div>");
}

function loggedIn(){
	if(store.get('username') == null){
		return false;
	} else {
		return true;
	}
}

function login(){
	var username = $('#username').val();
	var password = $('#password').val();
	if(username == '' || password == ''){
		alert("Please enter your username and password");
		return;
	}
	
	$.ajax({
		   type:'POST',
		   url:API_URL,
		   headers:{},
		   dataType:'application/json',
		   data:{'method':'login','username':username,'password':password}, 
		   success:function(data){
			   //check for any error messages
			   var response = $.parseJSON(data);
			   if(response.error){
				   alert(response.error[0]);
				   return;
			   }
			   if(response.result){
				   // save username and password
			   }
		   }, 
		   error:function(data){
			   alert("No connection available. You need to be online to log in.");
		   }
		});
}

function logout(){
	store.clear();
	store.init();
	showLogin();
	showUsername();
}

function showUsername(){
	$('#logininfo').empty();
	if(store.get('username') != null){
		$('#logininfo').text(store.get('username') + " ");
		$('#logininfo').append("<a onclick='logout()'>Logout</a>");
	} else {
		$('#logininfo').text("Not logged in");
	}
}
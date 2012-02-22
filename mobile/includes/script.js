
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
	$('#content').append("<h2>Loading tasks...</h2>");
	
	displayTasks(store.get('tasks'));
}

function displayTasks(data){
	$('#content').empty();
	$('#content').append("<h2>Tasks Due</h2>");
	if(data == null || data.length == 0){
		$('#content').append("No current tasks");
		return;
	} 
	var curdate = "";
	for (var i=0; i<data.length; i++){
		// show data header
		if(data[i].datedue != curdate){
			$('#content').append("<div class='taskdate'>"+data[i].datedue+"</div>");
			curdate = data[i].datedue;
		}
		var task = $('<div>').addClass('task');
		task.append($('<div>').attr('name',data[i].protocol).addClass('taskleft').text(getString(data[i].protocol)));
		
		task.append("<div style='clear:both;'></div>");
		$('#content').append(task);
		
	}
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
		   dataType:'json',
		   data:{'method':'login','username':username,'password':password}, 
		   success:function(data){
			   //check for any error messages
			   if(data.error){
				   alert(data.error[0]);
				   return;
			   }
			   if(data.result){
				   // save username and password
				   store.set('username',$('#username').val());
				   store.set('password',$('#password').val());
				   showUsername();
				   showTasks();
				   dataUpdate();
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

function dataUpdate(){
	if(!loggedIn()){
		return;
	}
	
	// Get the tasks from remote server
	$.ajax({
		   type:'POST',
		   url:API_URL,
		   headers:{},
		   dataType:'json',
		   data:{'method':'gettasks','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   //check for any error messages
			   if(data && !data.error){
				   store.set('tasks',data);
			   }
		   }, 
		   error:function(data){
		   }
		});
}
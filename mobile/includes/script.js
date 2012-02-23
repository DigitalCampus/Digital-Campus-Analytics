
var API_URL = "/scorecard/api/";
var PAGE = "";
var DATA_CACHE_EXPIRY = 1; // no of hours before the data should be updated from server;
var LOGIN_EXPIRY = 14; // no days before the user needs to log in again

function showPage(page){
	if(!loggedIn()){
		showLogin();
		return;
	}
	dataUpdate();
	PAGE = page;
	$('#content').empty();
	if(page == 'tasks'){
		$('#content').append("<h2 name='lang' id='page_title_tasks'>"+getString('page_title_tasks')+"</h2>");
		$('#content').append("<h2 id='loading'>Loading...</h2>");
		displayTasks(store.get('tasks'));
	} else if(page == 'kpi'){
		$('#content').append("<h2 name='lang' id='page_title_kpis'>"+getString('page_title_kpis')+"</h2>");
		$('#content').append("<h2 id='loading'>Loading...</h2>");
	} else if(page == 'deliveries'){
		$('#content').append("<h2 name='lang' id='page_title_deliveries'>"+getString('page_title_deliveries')+"</h2>");
		$('#content').append("<h2 id='loading'>Loading...</h2>");
	} else if(page == 'overdue'){
		$('#content').append("<h2 name='lang' id='page_title_overdue'>"+getString('page_title_overdue')+"</h2>");
		$('#content').append("<h2 id='loading'>Loading...</h2>");
	}
}


function displayTasks(data){
	if(data == null || data.length == 0){
		return;
	} 
	$('#loading').remove();
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

function displayKPI(data){
	
}

function showLogin(){
	$('#content').empty();
	$('#content').append("<h1 name='lang' id='page_title_login'>"+getString('page_title_login')+"</h1>");
	
	$('#content').append("<div class='formblock'>" +
		"<div class='formlabel' name='lang' id='login_username'>"+getString('login_username')+"</div>" +
		"<div class='formfield'><input type='text' name='username' id='username'></input></div>" +
		"</div>");
	
	$('#content').append("<div class='formblock'>"+
		"<div class='formlabel'name='lang' id='login_password'>"+getString('login_password')+"</div>" +
		"<div class='formfield'><input type='password' name='password' id='password'></input></div>" +
		"</div>");
	
	$('#content').append("<div class='formblock'>" +
			"<div class='formfield'><input type='button' name='submit' value='Login' onclick='login()'></input></div>" +
			"</div>");
}

function loggedIn(){
	if(store.get('username') == null){
		return false;
	} 
	// check when last login made
	var now = new Date();
	var lastlogin = new Date(store.get('lastlogin'));
	
	if(lastlogin.addDays(LOGIN_EXPIRY) < now){
		logout();
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
				   store.set('lastlogin',Date());
				   showUsername();
				   showPage('tasks');
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
		$('#logininfo').append("<a onclick='logout()' name='lang' id='logout'>"+getString('logout')+"</a>");
	} 
}

function dataUpdate(){
	if(!loggedIn()){
		return;
	}
	// check when last update made, return if too early
	var now = new Date();
	var lastupdate = new Date(store.get('lastupdate'));
	if(lastupdate > now.addHours(-DATA_CACHE_EXPIRY)){
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
				   if(PAGE == 'tasks'){
					   displayTasks(store.get('tasks'));
				   }
				   store.set('lastupdate',Date());
				   setUpdated();
			   }
		   }, 
		   error:function(data){
			   if(PAGE == 'tasks'){
				   displayTasks(store.get('tasks'));
			   }
		   }
		});
}

function setUpdated(){
	$('#last_update').text(store.get('lastupdate'));
}

Date.prototype.addHours= function(h){
    this.setHours(this.getHours()+h);
    return this;
}

Date.prototype.addDays= function(d){
    this.setDate(this.getDate()+d);
    return this;
}
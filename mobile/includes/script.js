
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
	$('#grayout').show();
	if(page == 'tasks'){
		$('#content').append("<h2 name='lang' id='page_title_tasks'>"+getString('page_title_tasks')+"</h2>");
		displayTasks(store.get('tasks'));
	} else if(page == 'kpi'){
		$('#content').append("<h2 name='lang' id='page_title_kpis'>"+getString('page_title_kpis')+"</h2>");
		displayKPIs(store.get('kpis'));
	} else if(page == 'deliveries'){
		$('#content').append("<h2 name='lang' id='page_title_deliveries'>"+getString('page_title_deliveries')+"</h2>");
		displayDeliveries(store.get('deliveries'));
	} else if(page == 'overdue'){
		$('#content').append("<h2 name='lang' id='page_title_overdue'>"+getString('page_title_overdue')+"</h2>");
		displayOverdue(store.get('overdue'));
	}
}


function displayTasks(data){
	if(data == null || data.length == 0){
		return;
	} 
	var curdate = "";
	for (var i=0; i<data.length; i++){
		// show data header
		if(data[i].datedue != curdate){
			//convert to Ethio date
			var date = convertDate(data[i].datedue);
			$('#content').append("<div class='taskdate'>"+date['ethio']+" <small>("+date['greg'] +")</small>"+"</div>");
			curdate = data[i].datedue;
		}
		if(data[i].patientname){
			var pname = data[i].patientname;
		} else {
			var pname = "<span class='error'>Patient not registered</span>";
		}
		var task = $('<div>').addClass('task');
		task.append($('<div>').attr('name',data[i].protocol).addClass('taskleft').text(getString(data[i].protocol)));
		var patient = $('<div>').addClass('taskright').html(pname);
		patient.append($('<br>'));
		patient.append($('<small>').attr('name','healthpoint.id.'+data[i].patienthpcode).text(getString('healthpoint.id.'+data[i].patienthpcode)));
		patient.append($('<small>').text('/'+data[i].userid));
		task.append(patient);
		
		//add risk info 
		if(data[i].risk != 'none'){
			task.append("<div class='taskhighrisk'><img src='images/red-dot.png'/></div>");
		} else {
			task.append("<div class='taskhighrisk'></div>");
		}
		
		task.append("<div style='clear:both;'></div>");
		$('#content').append(task);
		
	}
	$('#grayout').hide();
}

function displayDeliveries(data){
	if(data == null || data.length == 0){
		return;
	} 
	var curdate = "";
	for (var i=0; i<data.length; i++){
		// show data header
		if(data[i].datedue != curdate){
			var date = convertDate(data[i].datedue);
			$('#content').append("<div class='taskdate'>"+date['ethio']+" <small>("+date['greg'] +")</small>"+"</div>");
			curdate = data[i].datedue;
		}
		if(data[i].patientname){
			var pname = data[i].patientname;
		} else {
			var pname = "<span class='error'>Patient not registered</span>";
		}
		var task = $('<div>').addClass('task');
		var patient = $('<div>').addClass('deltaskleft').html(pname);
		patient.append($('<br>'));
		patient.append($('<small>').attr('name','healthpoint.id.'+data[i].patienthpcode).text(getString('healthpoint.id.'+data[i].patienthpcode)));
		patient.append($('<small>').text('/'+data[i].userid));
		task.append(patient);
		var risk = $('<div>').addClass('deltaskright');
		var category = $('<span>').attr('name','risk.category.'+ data[i].risk.category).text(getString('risk.category.'+ data[i].risk.category));
		risk.append(category);
		var risks = $('<ul>');
		
		for(var j=0; j<data[i].risk.risks.length; j++){
			var r = $('<li>').attr('name','risk.factor.'+ data[i].risk.risks[j]).text(getString('risk.factor.'+ data[i].risk.risks[j]));
			risks.append(r);
		}
		
		risk.append(risks);
		task.append(risk);
		task.append("<div style='clear:both;'></div>");
		$('#content').append(task);
		
	}
	$('#grayout').hide();
}

function displayOverdue(data){
	if(data == null || data.length == 0){
		return;
	} 
	var curdate = "";
	for (var i=0; i<data.length; i++){
		// show data header
		if(data[i].datedue != curdate){
			//convert to Ethio date
			var date = convertDate(data[i].datedue);
			$('#content').append("<div class='taskdate'>"+date['ethio']+" <small>("+date['greg'] +")</small>"+"</div>");
			curdate = data[i].datedue;
		}
		if(data[i].patientname){
			var pname = data[i].patientname;
		} else {
			var pname = "<span class='error'>Patient not registered</span>";
		}
		var task = $('<div>').addClass('task');
		task.append($('<div>').attr('name',data[i].protocol).addClass('taskleft').text(getString(data[i].protocol)));
		var patient = $('<div>').addClass('taskright').html(pname);
		patient.append($('<br>'));
		patient.append($('<small>').attr('name','healthpoint.id.'+data[i].patienthpcode).text(getString('healthpoint.id.'+data[i].patienthpcode)));
		patient.append($('<small>').text('/'+data[i].userid));
		task.append(patient);
		
		//add risk info 
		if(data[i].risk != 'none'){
			task.append("<div class='taskhighrisk'><img src='images/red-dot.png'/></div>");
		} else {
			task.append("<div class='taskhighrisk'></div>");
		}
		
		task.append("<div style='clear:both;'></div>");
		$('#content').append(task);
		
	}
	$('#grayout').hide();
}

function displayKPIs(data){
	if(data == null || data.length == 0){
		return;
	} 
	
	if(data.districts && data.districts.length >0 && data.hps.length >0){
		var sel = $('<select>').attr('id','hpcodes');
		for(var d=0; d< data.districts.length; d++){
			var opt = $('<option>').attr('name','district.id.'+data.districts[d].did).attr('value',data.districts[d].did).text(getString('district.id.'+data.districts[d].did));
			sel.append(opt);
		}
		sel.append("<option value='' disabled='disabled'>---</option>");
		for(var d=0; d< data.hps.length; d++){
			var opt = $('<option>').attr('name','healthpoint.id.'+data.hps[d]).attr('value',data.hps[d]).text(getString('healthpoint.id.'+data.hps[d]));
			sel.append(opt);
		}
		$('#content').append(sel);
	}
	$('#content').append("<div class='kpiheader'>" +
							"<div class='kpiscore' name='lang' id='kpi.heading.thismonth'>"+getString('kpi.heading.thismonth')+"</div>" + 
							"<div class='kpichange' name='lang' id='kpi.heading.previousmonth'>"+getString('kpi.heading.previousmonth')+"</div>" +
							"<div class='kpitarget' name='lang' id='kpi.heading.target'>"+getString('kpi.heading.target')+"</div>" +
						"</div>");
	//show submitted
	
	
	//show anc1 submitted
	
	//show anc2 submitted
	
	$('#grayout').hide();
}

function showLogin(){
	$('#menu').hide();
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
	
	$('#grayout').show();
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
				   $('#menu').show();
				   showPage('kpi');
			   }
		   }, 
		   error:function(data){
			   $('#grayout').hide();
			   alert("No connection available. You need to be online to log in.");
		   }
		});
}

function logout(){
	var lo = confirm('Are you sure you want to log out?\n\nYou will need an active connection to log in again.');
	if(lo){
		store.clear();
		store.init();
		showLogin();
		showUsername();
	}
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
	
	// Get the deliveries from remote server
	$.ajax({
		   type:'POST',
		   url:API_URL,
		   headers:{},
		   dataType:'json',
		   data:{'method':'getdeliveries','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   //check for any error messages
			   if(data && !data.error){
				   store.set('deliveries',data);
				   if(PAGE == 'deliveries'){
					   displayDeliveries(store.get('deliveries'));
				   }
				   store.set('lastupdate',Date());
				   setUpdated();
			   }
		   }, 
		   error:function(data){
			   if(PAGE == 'deliveries'){
				   displayDeliveries(store.get('deliveries'));
			   }
		   }
		});
	
	// Get the overdue from remote server
	$.ajax({
		   type:'POST',
		   url:API_URL,
		   headers:{},
		   dataType:'json',
		   data:{'method':'getoverdue','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   //check for any error messages
			   if(data && !data.error){
				   store.set('overdue',data);
				   if(PAGE == 'overdue'){
					   displayOverdue(store.get('overdue'));
				   }
				   store.set('lastupdate',Date());
				   setUpdated();
			   }
		   }, 
		   error:function(data){
			   if(PAGE == 'overdue'){
				   displayOverdue(store.get('overdue'));
			   }
		   }
		});
	
	// Get KPIs from remote server
	$.ajax({
		   type:'POST',
		   url:API_URL,
		   headers:{},
		   dataType:'json',
		   data:{'method':'getkpis','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   //check for any error messages
			   if(data && !data.error){
				   store.set('kpis',data);
				   if(PAGE == 'kpi'){
					   displayKPIs(store.get('kpis'));
				   }
				   store.set('lastupdate',Date());
				   setUpdated();
			   }
		   }, 
		   error:function(data){
			   if(PAGE == 'kpi'){
				   displayKPIs(store.get('kpis'));
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
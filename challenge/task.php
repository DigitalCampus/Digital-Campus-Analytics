<?php 
include_once('../config.php');
writeToLog("info","pagehit","challenge-task");

//header('Cache-Control: no-cache, must-revalidate');
//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type:application/json;charset:utf-8');

$username = optional_param('username','',PARAM_TEXT);
$password = optional_param('password','',PARAM_TEXT);

$tasks = array();

if($username == ""){
	$error = new stdClass();
	$error->error = "You must supply a username";
	array_push($tasks, $error);
}

if($password == ""){
	$error = new stdClass();
	$error->error = "You must supply a password";
	array_push($tasks, $error);
	
}

if($username == "" || $password == ""){
	echo json_encode($tasks);
	die;
}

$today = new DateTime();

$yesterday = new DateTime();
$yesterday->sub(new DateInterval('P1D'));

$lastweek = new DateTime();
$lastweek->sub(new DateInterval('P7D'));

$tomorrow = new DateTime();
$tomorrow->add(new DateInterval('P1D'));

$nextweek = new DateTime();
$nextweek->add(new DateInterval('P7D'));
$tasks = array();

$task = new stdClass();
$task->duedate = $today->format('d-M-Y');
$task->type = "ANC Follow Up";
$task->patientname = "Sophie";
$task->patientid = "1000";
$task->location = "Negash Health Centre";
array_push($tasks,$task);

$task = new stdClass();
$task->duedate = $yesterday->format('d-M-Y');
$task->type = "ANC Follow Up";
$task->patientname = "Mihret";
$task->patientid = "1001";
$task->location = "Negash Health Centre";
array_push($tasks,$task);

$task = new stdClass();
$task->duedate = $lastweek->format('d-M-Y');
$task->type = "Delivery";
$task->patientname = "Suzie";
$task->patientid = "1002";
$task->location = "Negash Health Centre";
array_push($tasks,$task);

$task = new stdClass();
$task->duedate = $tomorrow->format('d-M-Y');
$task->type = "PNC follow up";
$task->patientname = "Suzie";
$task->patientid = "1002";
$task->location = "Negash Health Centre";
array_push($tasks,$task);

$task = new stdClass();
$task->duedate = $nextweek->format('d-M-Y');
$task->type = "Delivery";
$task->patientname = "Suzie";
$task->patientid = "1002";
$task->location = "Negash Health Centre";
array_push($tasks,$task);

echo json_encode($tasks);
?>
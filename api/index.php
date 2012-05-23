<?php 

include_once '../config.php';
header('Content-type: application/json; charset=UTF-8');

$username = optional_param('username',null,PARAM_TEXT);
$password = optional_param('password',null,PARAM_TEXT);
$method = optional_param('method',null,PARAM_TEXT);

$error = new stdClass();
// check login
if(!userLogin($username,$password)){
	$error->error = $MSG;
	echo json_encode($error);
	die;
}

if($method == null){
	$error->error = array("You must enter a method");
	echo json_encode($error);
	die;
}

if ($method == 'login'){
	$response = new stdClass();
	$response->result = true;
	$response->homehp = $USER->hpcode;
	echo json_encode($response);
} else if ($method == 'gettasks'){
	echo $USER->getProp('mobile.cache.tasks');
} else if ($method == 'getoverdue'){
	echo $USER->getProp('mobile.cache.overdue');
} else if ($method == 'getdeliveries'){
	echo $USER->getProp('mobile.cache.deliveries');
} else if ($method == 'getkpis'){
	echo $USER->getProp('mobile.cache.kpis');
} else {
	$error->error = array("Method not available");
	echo json_encode($error);
}

scriptFooter("info","api",$method.": ".$_SERVER["REQUEST_URI"]);


?>
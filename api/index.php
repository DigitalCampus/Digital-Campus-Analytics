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
	echo json_encode($response);
} else if ($method == 'gettasks'){
	$tasks = $API->getTasksDue(array('days'=>30));
	echo json_encode($tasks);
} else if ($method == 'getdeliveries'){
	$deliveries = $API->getDeliveriesDue(array('days'=>30));
	echo json_encode($deliveries);
} else {
	$error->error = array("Method not available");
	echo json_encode($error);
}

scriptFooter("info","api",$method.": ".$_SERVER["REQUEST_URI"]);

?>
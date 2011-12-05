<?php 
include_once "../config.php";
$PAGE="tasks";
include_once 'includes/header.php';

$opts = array("days"=>31);
$tasks = $API->getTasksDue($USER->userid,$opts);
//print_r($tasks);

foreach($tasks as $task){
	$d = strtotime($task->datedue);
	printf("<div class='task'>");
	printf("<div class='taskdate'>%s</div>",displayAsEthioDate($d));
	printf("<div class='taskprotocol'>%s</div>",$task->protocol);
	if($task->patientname == ""){
		$task->patientname = getString('warning.patientreg');
	}
	printf("<div class='taskpname'>%s</div>",$task->patientname);
	printf("<div class='taskpid'>%s/%s</div>",$task->patientlocation,$task->Q_USERID);
	printf("<div class='taskprisk'>%s</div>","high risk");
	printf("<div style='clear:both;'></div></div>");
}
include_once 'includes/footer.php';
?>
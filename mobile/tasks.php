<?php 
include_once "../config.php";
$PAGE="tasks";
include_once 'includes/header.php';

$opts = array("days"=>14);
$tasks = $API->getTasksDue($opts);
//print_r($tasks);

foreach($tasks as $task){
	$d = strtotime($task->datedue);
	printf("<div class='task'>");
	printf("<div class='taskdate'>%s (%s)</div>",displayAsEthioDate($d),date('d M Y',$d));
	printf("<div class='taskprotocol'>%s</div>",$task->protocol);
	if($task->patientname == ""){
		$task->patientname = getString('warning.patientreg');
	}
	printf("<div class='taskpname'>%s</div>",$task->patientname);
	printf("<div class='taskpid'>%s/%s</div>",$task->patientlocation,$task->Q_USERID);
	printf("<div class='taskprisk'>%s</div>","Risk not known");
	printf("<div style='clear:both;'></div></div>");
}
include_once 'includes/footer.php';
?>
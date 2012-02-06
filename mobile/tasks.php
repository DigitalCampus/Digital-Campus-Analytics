<?php 
include_once "../config.php";
$PAGE = "tasks";
include_once 'includes/header.php';

$opts = array("days"=>31,'hpcodes'=>$USER->hpcode);
$tasks = $API->getTasksDue($opts);
$ra = new RiskAssessment();

printf("<h2>%s</h2>",getstring('mobile.title.tasks'));

foreach($tasks as $task){
	$d = strtotime($task->datedue);
	printf("<div class='task'>");
	printf("<div class='taskdate'>%s (%s)</div>",displayAsEthioDate($d),date('d M Y',$d));
	printf("<div class='taskprotocol'>%s</div>",getstring($task->protocol));
	if($task->patientname == ""){
		$task->patientname = getString('warning.patient.notregistered');
	}
	printf("<div class='taskpname'>%s</div>",$task->patientname);
	printf("<div class='taskpid'>%s/%s</div>",$task->patientlocation,$task->userid);
	$risks = $ra->getRisks_Cache($task->hpcode, $task->userid);
	printf("<div class='taskprisk'>%s</div>",getstring("risk.".$risks->category));
	printf("<div class='taskprisk'><ul>");
	foreach ($risks->risks as $s){
		printf("<li>%s</li>",getstring("risk.factor.".$s));
	}
	printf("</ul></div>");
	printf("<div style='clear:both;'></div></div>");
}
include_once 'includes/footer.php';
?>
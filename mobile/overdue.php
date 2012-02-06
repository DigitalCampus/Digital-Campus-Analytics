<?php 
include_once "../config.php";
$PAGE = "overdue";
include_once 'includes/header.php';

$opts = array("days"=>31);
if($USER->getProp('permissions.role') == 'hew' || $USER->getProp('permissions.role') == 'midwife'){
	$opts['hpcodes'] = $USER->hpcode;
} else {
	$opts['hpcodes'] = $API->getUserHealthPointPermissions();
}

$tasks = $API->getOverdueTasks($opts);
$ra = new RiskAssessment();

printf("<h2>%s</h2>",getstring('mobile.title.overdue'));

foreach($tasks as $task){
	$d = strtotime($task->datedue);
	printf("<div class='task'>");
	printf("<div class='taskdate'>%s (%s)</div>",displayAsEthioDate($d),date('d M Y',$d));
	printf("<div class='taskprotocol'>%s</div>",getstring($task->protocol));
	if($task->patientname == ""){
		$task->patientname = sprintf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
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
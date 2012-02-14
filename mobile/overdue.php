<?php 
include_once "../config.php";
$PAGE = "overdue";
include_once 'includes/header.php';

$opts = array("days"=>$CONFIG->props['overdue.ignore']);
if($USER->getProp('permissions.role') == 'hew' || $USER->getProp('permissions.role') == 'midwife'){
	$opts['hpcodes'] = $USER->hpcode;
} else {
	$opts['hpcodes'] = $API->getUserHealthPointPermissions();
}

$tasks = $API->getOverdueTasks($opts);
$ra = new RiskAssessment();

printf("<h2>%s</h2>",getstring('mobile.title.overdue'));

$curdate = "";
foreach($tasks as $task){
	$d = strtotime($task->datedue);
	if($curdate != $d){
		printf("<div class='taskdate'>%s (%s)</div>",displayAsEthioDate($d),date('d M Y',$d));
	}
	$curdate = $d;
	
	printf("<div class='task'>");
	if($task->patientname == ""){
		$task->patientname = sprintf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
	}
	
	printf("<div class='taskleft'>%s</div>",getstring($task->protocol));
	printf("<div class='taskright'>%s<br/><small>%s</small></div>",$task->patientname,displayHealthPointName($task->patienthpcode,$task->userid));
	$risks = $ra->getRisks_Cache($task->patienthpcode, $task->userid);
	if($risks->category != 'none'){
		printf("<div class='taskhighrisk'><img src='images/red-dot.png'/></div>");
	} else {
		printf("<div class='taskhighrisk'></div>");
	}
	
	printf("<div style='clear:both;'></div>");
	printf("</div>"); // end task div
}
include_once 'includes/footer.php';
?>
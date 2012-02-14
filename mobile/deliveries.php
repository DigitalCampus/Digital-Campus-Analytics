<?php 
include_once "../config.php";
$PAGE = "deliveries";
include_once 'includes/header.php';

$opts = array("days"=>$CONFIG->props['mobile.deliveries.nodays']);
$deliveries = $API->getDeliveriesDue($opts);
$ra = new RiskAssessment();

printf("<h2>%s</h2>",getstring('mobile.title.deliveries'));

$curdate = "";
foreach($deliveries as $delivery){
	$d = strtotime($delivery->datedue);
	if($curdate != $d){
		printf("<div class='taskdate'>%s (%s)</div>",displayAsEthioDate($d),date('d M Y',$d));
	}
	$curdate = $d;
	
	printf("<div class='task'>");
	if($delivery->patientname == ""){
		$delivery->patientname = sprintf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
	}
	printf("<div class='deltaskleft'>%s<br/><small>%s</small></div>",$delivery->patientname,displayHealthPointName($delivery->patienthpcode,$delivery->userid));

	$risks = $ra->getRisks_Cache($delivery->patienthpcode, $delivery->userid);
	printf("<div class='deltaskright'>%s<ul>",getstring("risk.".$risks->category));
	foreach ($risks->risks as $s){
		printf("<li>%s</li>",getstring("risk.factor.".$s));
	}
	printf("</ul></div>");
	printf("<div style='clear:both;'></div>");
	printf("</div>"); // end task div
	
}
include_once 'includes/footer.php';
?>
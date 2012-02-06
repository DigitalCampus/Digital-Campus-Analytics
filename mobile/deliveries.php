<?php 
include_once "../config.php";
$PAGE = "deliveries";
include_once 'includes/header.php';

$opts = array("days"=>31);
$deliveries = $API->getDeliveriesDue($opts);
$ra = new RiskAssessment();

printf("<h2>%s</h2>",getstring('mobile.title.deliveries'));

foreach($deliveries as $delivery){
	$d = strtotime($delivery->datedue);
	printf("<div class='task'>");
	printf("<div class='taskdate'>%s (%s)</div>",displayAsEthioDate($d),date('d M Y',$d));
	//printf("<div class='taskprotocol'>%s</div>",getstring($delivery->protocol));
	if($delivery->patientname == ""){
		$delivery->patientname = sprintf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
	}
	printf("<div class='taskpname'>%s</div>",$delivery->patientname);
	printf("<div class='taskpid'>%s/%s</div>",$delivery->patientlocation,$delivery->userid);
	$risks = $ra->getRisks_Cache($delivery->hpcode, $delivery->userid);
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
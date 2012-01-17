<div id='riskfactor'>
<?php 
echo "<h4>".getstring('risk.title')."</h4>";
// display the risk factor analysis
$ra = new RiskAssessment();
$risk = $ra->getRisks_Cache($hpcode,$patientid);
echo getstring('risk.'.$risk->category);

for ($i=0; $i<count($risk->risks);$i++){
	if($i==0){
		echo ": ";
	}
	echo getstring('risk.factor.'.$risk->risks[$i]);
	if($i<count($risk->risks)-1){
		echo ", ";
	}
}



?>
</div>
<div id='riskfactor'>
<?php 
echo "<h4>".getstring('risk.title')."</h4>";
// display the risk factor analysis
switch ($patient->risk->category){
	case 'none':
		echo getstring('risk.none');
		break;
	case 'unavoidable':
		echo getstring('risk.unavoidable');
		break;
	case 'single':
		echo getstring('risk.single');
		break;
	case 'multiple':
		echo getstring('risk.multiple');
		break;
}
//print_r($patient->risk);

$temp = array();
foreach ($patient->risk->risks as $k=>$v){
	
	if($v){
		array_push($temp, getstring('risk.factor.'.$k));
	}
}
if(count($temp) >0){
	echo ": ";
	echo implode(', ',$temp);	
}


?>
</div>
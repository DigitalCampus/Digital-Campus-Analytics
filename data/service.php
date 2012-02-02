<?php 
// services available
require_once("../config.php");
checkLogin();
header("Content-type: text/xml; charset:UTF8");
$opts = array('days'=>31);
$submitted = $API->getProtocolsSubmitted_Cache($opts);
$anon = array();

foreach($submitted as $s){
	unset($s->patientname);
	unset($s->Q_USERID);
	unset($s->Q_GPSDATA_LAT);
	array_push($anon,$s);
}



/*for($c = 0; $c <$days+1; $c++){
	$tempc = date('d M Y',$date);
	$row = new stdClass();
	$row->date = $tempc;
	$x=0;
	foreach($locations as $l){
		$row->{"hp".$x}->name = $l->hpname;
		$row->{"hp".$x}->count = 0;
		$x++;
	}
	array_push($summary, $row);
	$date = $date + 86400;
}
foreach($submitted as $s){
	$d = date('d M Y',strtotime($s->datestamp));

	
	if(array_key_exists($d,$summary)){
		if(isset($summary[$d][$s->protocollocation])){
			$summary[$d][$s->protocollocation] += 1;
		} else {
			$summary[$d][$s->protocollocation] = 1;
		}
	} else {
		$summary[$d][$s->protocollocation] = 1;
	}
}*/

//print_r($summary);

echo XMLSerializer::generateValidXmlFromArray($anon,'data','protocol');
//header("Content-type: text/plain; charset:UTF8");
//echo json_encode($s);
?>
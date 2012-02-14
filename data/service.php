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


echo XMLSerializer::generateValidXmlFromArray($anon,'data','protocol');

?>
<?php 
include_once "../../config.php";
header("Content-type: text/html; charset:UTF8");
// get all the current patients with next appt dates 
// (from ANC First and ANC Follow up) due in the past
$sql = "SELECT 	hp.hpcode,
				Q_USERID as patientid
		FROM ".REGISTRATION." r
		INNER JOIN patientcurrent pc ON pc.hpcode = r.Q_HEALTHPOINTID AND pc.patid = r.Q_USERID
		INNER JOIN healthpoint hp ON hp.hpcode = r.Q_HEALTHPOINTID
		WHERE pc.pcurrent = 1";


$defaulters = array();


?>
loop through patients

	if ANC Follow up not completed within 1 week of the ANCFirst next appt date or follow up is missing
	EXCEPT if the next appt date is after the EDD 
			add to $defaulters list
	
	
	for each ANC follow up
		if the next follow up was not completed within 1 week of the next appt date or is missing
		EXCEPT if the next appt date is after the EDD
			add to $defaulters list




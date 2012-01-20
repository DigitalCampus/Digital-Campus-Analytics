<?php 

include_once('../config.php');
require_once "../includes/header.php";

// only allow access by admins
if($USER->getProp('permissions.admin') != "true"){
	writeToLog('warning','adminpage','accessdenied');
	echo getString ("warning.accessdenied");
	die;
}

// TODO - move this to be part of data checker page
$patients = $API->getCurrentPatients();

foreach($patients as $p){
	$age = array();
	
	// reg
	if(isset($p->Q_AGE)){
		$age[$p->Q_AGE] = PROTOCOL_REGISTRATION;
	}
	// first
	if(isset($p->ancfirst->Q_AGE)){
		$age[$p->ancfirst->Q_AGE] = PROTOCOL_ANCFIRST;
	}
	//follow
	foreach($p->ancfollow as $x){
		if(isset($x->Q_AGE)){
			$age[$x->Q_AGE] = PROTOCOL_ANCFOLLOW;
		}
	}
	//xfer
	foreach($p->anctransfer as $x){
		if(isset($x->Q_AGE)){
			$age[$x->Q_AGE] = PROTOCOL_ANCTRANSFER;
		}
	}
	//lab
	foreach($p->anclabtest as $x){
		if(isset($x->Q_AGE)){
			$age[$x->Q_AGE] = PROTOCOL_ANCLABTEST;
		}
	}
	//delivery
	if(isset($p->delivery->Q_AGE)){
		$age[$p->delivery->Q_AGE] = PROTOCOL_DELIVERY;
	}
	//pnc
	foreach($p->pnc as $x){
		if(isset($x->Q_AGE)){
			$age[$x->Q_AGE] = PROTOCOL_PNC;
		}
	}
	
	// now do year of birth
	$yob = array();
	// reg
	if(isset($p->Q_YEAROFBIRTH)){
		$yob[$p->Q_YEAROFBIRTH] = PROTOCOL_REGISTRATION;
	}
	// first
	if(isset($p->ancfirst->Q_YEAROFBIRTH)){
		$yob[$p->ancfirst->Q_YEAROFBIRTH] = PROTOCOL_ANCFIRST;
	}
	//follow
	foreach($p->ancfollow as $x){
		if(isset($x->Q_YEAROFBIRTH)){
			$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCFOLLOW;
		}
	}
	//xfer
	foreach($p->anctransfer as $x){
		if(isset($x->Q_YEAROFBIRTH)){
			$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCTRANSFER;
		}
	}
	//lab
	foreach($p->anclabtest as $x){
		if(isset($x->Q_YEAROFBIRTH)){
			$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCLABTEST;
		}
	}
	//delivery
	if(isset($p->delivery->Q_YEAROFBIRTH)){
		$yob[$p->delivery->Q_YEAROFBIRTH] = PROTOCOL_DELIVERY;
	}
	//pnc
	foreach($p->pnc as $x){
		if(isset($x->Q_YEAROFBIRTH)){
			$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_PNC;
		}
	}
	
	
	if(count($age)>1 || count($yob) >1 ){
		printf("<a href='http://odk.digital-campus.org/scorecard/patient.php?hpcode=%d&patientid=%d'>%s/%d</a>",$p->hpcode,$p->Q_USERID,$p->patientlocation,$p->Q_USERID);
		echo "<ul>";
		foreach($age as $k=>$v){
			printf("<li>Age on %s: %d</li>",getstring($v),$k);
		}
		echo "</ul>";
		echo "<ul>";
		foreach($yob as $k=>$v){
			printf("<li>Year of birth on %s: %d</li>",getstring($v),$k);
		}
		echo "</ul>";

	}
	
}


include_once "../includes/footer.php";
?>
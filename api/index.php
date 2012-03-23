<?php 

include_once '../config.php';
header('Content-type: application/json; charset=UTF-8');

$username = optional_param('username',null,PARAM_TEXT);
$password = optional_param('password',null,PARAM_TEXT);
$method = optional_param('method',null,PARAM_TEXT);

$error = new stdClass();
// check login
if(!userLogin($username,$password)){
	$error->error = $MSG;
	echo json_encode($error);
	die;
}

if($method == null){
	$error->error = array("You must enter a method");
	echo json_encode($error);
	die;
}

if ($method == 'login'){
	$response = new stdClass();
	$response->result = true;
	$response->homehp = $USER->hpcode;
	echo json_encode($response);
} else if ($method == 'gettasks'){
	$tasks = $API->getTasksDue(array('days'=>30));
	//add risk factors
	$ra = new RiskAssessment();
	foreach($tasks as $t){
		$risks = $ra->getRisks_Cache($t->patienthpcode, $t->userid);
		$t->risk = $risks->category;
	}
	echo json_encode($tasks);
} else if ($method == 'getoverdue'){
	$tasks = $API->getOverdueTasks(array('days'=>30));
	//add risk factors
	$ra = new RiskAssessment();
	foreach($tasks as $t){
		$risks = $ra->getRisks_Cache($t->patienthpcode, $t->userid);
		$t->risk = $risks->category;
	}
	echo json_encode($tasks);
} else if ($method == 'getdeliveries'){
	//add risk factors
	$ra = new RiskAssessment();
	$deliveries = $API->getDeliveriesDue(array('days'=>30));
	foreach($deliveries as $d){
		$risks = $ra->getRisks_Cache($d->patienthpcode, $d->userid);
		$d->risk = $risks;
	}
	echo json_encode($deliveries);
} else if ($method == 'getkpis'){
	$kpi = new stdClass();
	$ra = new RiskAssessment();
	
	$datetoday = new DateTime();
	
	$datemonthago = new DateTime();
	$datemonthago->sub(new DateInterval('P1M'));
	
	$date2monthago = new DateTime();
	$date2monthago->sub(new DateInterval('P2M'));
	
	if($USER->getProp('permissions.role') != 'hew' && $USER->getProp('permissions.role') != 'midwife'){
		$kpi->districts = $API->getDistricts();
		
		if(count($kpi->districts) > 1){
			$opts['hpcodes'] = $API->getUserHealthPointPermissions();
			$opts['limit'] = 0;
			$opts['startdate'] = $datemonthago->format('Y-m-d 00:00:00');
			$opts['enddate'] = $datetoday->format('Y-m-d 23:59:59');
			$temp = $API->getProtocolsSubmitted_Cache($opts);
			$temp->protocols = array();
			$kpi->submittedthismonth['all'] = $temp;
				
			$opts['startdate'] = $date2monthago->format('Y-m-d 00:00:00');
			$opts['enddate'] = $datemonthago->format('Y-m-d 23:59:59');
				
			$kpi->submittedprevmonth['all'] = $API->getProtocolsSubmitted_Cache($opts);
			
			$risks = $ra->getRiskStatistics($opts);
			// add in risks
			$riskcount = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>0);
			$riskpercent = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>100);
			
			foreach($risks as $k=>$v){
				$riskcount[$k] = $v;
				$riskcount['total'] += $v;
			}
			foreach($risks as $k=>$v){
				$riskpercent[$k] = round($riskcount[$k]*100/$riskcount['total']);
			}
			$kpi->riskcount['all'] = $riskcount;
			$kpi->riskpercent['all'] = $riskpercent;
			
		}
		
		// add the summaries for each district
		foreach($kpi->districts as $d){
			//echo $d->did;
			$hps4district = $API->getHealthPointsForDistict($d->did);
			$temp = array();
			foreach($hps4district as $h){
				array_push($temp,$h->hpcode);
			}
			$hps = implode(",",$temp);
			$opts['hpcodes'] = $hps;
			$opts['limit'] = 0;
			$opts['startdate'] = $datemonthago->format('Y-m-d 00:00:00');
			$opts['enddate'] = $datetoday->format('Y-m-d 23:59:59');
			$temp = $API->getProtocolsSubmitted_Cache($opts);
			
			$temp->protocols = array();
			$kpi->submittedthismonth[$d->did] = $temp;
			
			$opts['startdate'] = $date2monthago->format('Y-m-d 00:00:00');
			$opts['enddate'] = $datemonthago->format('Y-m-d 23:59:59');

			$kpi->submittedprevmonth[$d->did] = $API->getProtocolsSubmitted_Cache($opts);
			
			// add in risks
			$risks = $ra->getRiskStatistics($opts);
			$riskcount = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>0);
			$riskpercent = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>100);
				
			foreach($risks as $k=>$v){
				$riskcount[$k] = $v;
				$riskcount['total'] += $v;
			}
			foreach($risks as $k=>$v){
				$riskpercent[$k] = round($riskcount[$k]*100/$riskcount['total']);
			}
			$kpi->riskcount[$d->did] = $riskcount;
			$kpi->riskpercent[$d->did] = $riskpercent;
		}
	}
	$kpi->hps = $API->getUserHealthPointPermissions(false,true);
	
	foreach($kpi->hps as $hp){
		$opts['hpcodes'] = $hp;
		$opts['limit'] = 0;
		$opts['startdate'] = $datemonthago->format('Y-m-d 00:00:00');
		$opts['enddate'] = $datetoday->format('Y-m-d 23:59:59');
		$temp = $API->getProtocolsSubmitted_Cache($opts);
		$temp->protocols = array();
		$kpi->submittedthismonth[$hp] = $temp;
		
		$opts['startdate'] = $date2monthago->format('Y-m-d 00:00:00');
		$opts['enddate'] = $datemonthago->format('Y-m-d 23:59:59');
		$kpi->submittedprevmonth[$hp] = $API->getProtocolsSubmitted_Cache($opts);
		
		// add in risks
		$risks = $ra->getRiskStatistics($opts);
		$riskcount = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>0);
		$riskpercent = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>100);
		
		foreach($risks as $k=>$v){
			$riskcount[$k] = $v;
			$riskcount['total'] += $v;
		}
		foreach($risks as $k=>$v){
			$riskpercent[$k] = round($riskcount[$k]*100/$riskcount['total']);
		}
		$kpi->riskcount[$hp] = $riskcount;
		$kpi->riskpercent[$hp] = $riskpercent;
		
	}
	echo json_encode($kpi);
} else {

	$error->error = array("Method not available");
	echo json_encode($error);
}

scriptFooter("info","api",$method.": ".$_SERVER["REQUEST_URI"]);


?>
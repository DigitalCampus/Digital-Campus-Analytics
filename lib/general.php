<?php

function displayHealthPointSelectList($selected){
	global $API;
	$districts = $API->getDistricts();
	$cohort = $API->getCohortHealthPoints();
	
	$districtArray = array();
	
	foreach($districts as $d){
		//get the hps for this district
		$hps4district = $API->getHealthPointsForDistict($d->did);
		$temp = array();
		foreach($hps4district as $h){
			array_push($temp,$h->hpcode);
		}
		$hps = implode(",",$temp);
		$districtArray[$hps] = $d->dname;
	}
	
	
	if(count($districts) > 1){
		if($selected == 'overall'){
			printf("<option value='overall' selected='selected'>%s</option>",getstring('overall'));
		} else {
			printf("<option value='overall'>%s</option>",getstring('overall'));
		}
	
		printf("<option value='' disabled='disabled'>---</option>");
	}
	foreach($districtArray as $k=>$v){
		if(strcasecmp($selected,$k) == 0){
			printf("<option value='%s' selected='selected'>%s</option>", $k,getNameFromHPCodes($k,true));
		} else {
			printf("<option value='%s'>%s</option>", $k,getNameFromHPCodes($k,true));
		}
	}
	printf("<option value='' disabled='disabled'>---</option>");
	foreach($cohort as $chp){
		if(strcasecmp($selected,$chp->hpcode) == 0){
			printf("<option value='%s' selected='selected'>%s</option>", $chp->hpcode,displayHealthPointName($chp->hpcode));
		} else {
			printf("<option value='%s'>%s</option>", $chp->hpcode,displayHealthPointName($chp->hpcode));
		}
	}
}


function getNameFromHPCodes($hpcodes,$district = false){
	global $API;
	// set it to be overall - default
	$name ='Overall';
	$hps = $API->getHealthPoints(true);
	
	$hpcodesArray = explode(',',$hpcodes);
	if (count($hpcodesArray) == 1 && !$district){
		foreach($hps as $hp){
			if($hp->hpcode == $hpcodesArray[0]){
				$name = getString('healthpoint.id.'.$hp->hpcode);
			}
		}
	} else {
		$districts = $API->getDistricts();
		foreach($districts as $d){
			//get the hps for this district
			$hps4district = $API->getHealthPointsForDistict($d->did);
			$temp = array();
			foreach($hps4district as $h){
				array_push($temp,$h->hpcode);
			}
			$hps = implode(",",$temp);
			if($hps == $hpcodes){
				$name = getstring('district.id.'.$d->did);
			}
		}
	}
	return $name;
}


function lastOfMonth($d) {
	$date = strtotime($d->format('d-M-Y'));
	return date("d-M-Y", strtotime('-1 second',strtotime('+1 month',strtotime(date('m',$date).'/01/'.date('Y',$date)))));
}

class XMLSerializer {

	// functions adopted from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/

	public static function generateValidXmlFromObj(stdClass $obj, $node_block='nodes', $node_name='node') {
		$arr = get_object_vars($obj);
		return self::generateValidXmlFromArray($arr, $node_block, $node_name);
	}

	public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';

		$xml .= '<' . strtolower($node_block) . '>';
		$xml .= self::generateXmlFromArray($array, $node_name);
		$xml .= '</' . strtolower($node_block) . '>';

		return $xml;
	}

	private static function generateXmlFromArray($array, $node_name) {
		$xml = '';

		if (is_array($array) || is_object($array)) {
			foreach ($array as $key=>$value) {
				if (is_numeric($key)) {
					$key = $node_name;
				}

				$xml .= '<' . strtolower($key) . '>' . self::generateXmlFromArray($value, $node_name) . '</' . strtolower($key) . '>';
			}
		} else {
			$xml = htmlspecialchars($array, ENT_QUOTES);
		}

		return $xml;
	}

}


function scriptFooter($loglevel, $logtype, $logmsg){
	global $LOGGER,$API;
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $LOGGER->start), 4);
	writeToLog($loglevel, $logtype, $logmsg, $total_time, $LOGGER->mysql_queries_time, $LOGGER->mysql_queries_count);
	$API->cleanUpDB();
}



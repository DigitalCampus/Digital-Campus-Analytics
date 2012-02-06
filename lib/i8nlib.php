<?php
function getstring($name, $args=array()){
	global $CONFIG,$API,$USER;
	if (isset($_SESSION["session_lang"]) && $_SESSION["session_lang"] != ""){
		$sesslang = $_SESSION["session_lang"];
	} else {
		$sesslang = $CONFIG->props['default.lang'];
	}
	if(!isset($_SESSION["lang_strings"])){
		include_once $CONFIG->homePath.'lang/'.$sesslang.".php";
	 	$_SESSION["lang_strings"] =  $LANG;
	}
	
	$langstrs =  $_SESSION["lang_strings"];
	if (isset($langstrs[$name]) && trim($langstrs[$name]) != ""){
		return vsprintf($langstrs[$name], $args);
	}
	
	writeToLog('warning','lang',$name.' not found for '.$sesslang);
	return $name. " not found for ".$sesslang;

}

function setLang($lang, $redirect=false){
	global $USER,$API;
	$_SESSION["session_lang"] = $lang;
	unset($_SESSION["lang_strings"]);
	
	if($USER->userid != 0){
		$API->setUserProperty($USER->userid,'lang',$lang);
	}
	
	if($redirect){
		//redirect back to same page (to avoid the form resubmission popup)
		$url = "http" . ((!empty($_SERVER["HTTPS"])) ? "s" : "") . "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		writeToLog('info','langchange','changed to: '.$lang);
		header('Location: '.$url);  
	    die; 
	}
}

function ethioToGregorian($year, $month, $day){
	$ethio_joffset = 1723856;
	$jdn = ( $ethio_joffset + 365 )
	           + 365 * ( $year - 1 )
	           + floor( $year/ 4 )
	           + 30 * $month
	           + $day - 31
	           ;
	$t = strtotime(JDToGregorian($jdn));
	$date = array();
	$date['day'] = date('d',$t);
	$date['month'] = date('m',$t);
	$date['year'] = date('Y',$t);
	return $date;
}

function gregorianToEthio($year, $month, $day){
	$jdn = GregorianToJD($month,$day,$year);

	$ethio_joffset = 1723856;
	$r = ($jdn - $ethio_joffset) % 1461;

	$n = ( $r % 365 ) + 365 * floor( $r / 1460 ) ; 
	
	$date = array();
	
	$date['year'] = 4 * floor( ($jdn - $ethio_joffset)/ 1461 )
	            + floor($r / 365 )
	            - floor( $r / 1460 );
	$date['month'] = floor( $n / 30 ) + 1 ;
	$date['mtext'] = getstring('ethio.month.'.($date['month']-1));
	$date['day']   = ( $n % 30 ) + 1 ;
	
	return $date;
}

function displayAsEthioDate($datestamp){
	$day = date('d',$datestamp);
	$month = date('m', $datestamp);
	$year = date('Y',$datestamp);
	$dow = getstring('day.'.date('N',$datestamp));
	$date = gregorianToEthio($year,$month,$day);
	return sprintf('%s %d %s %d',$dow,$date['day'],$date['mtext'],$date['year']);
}


<?php

function writeToLog($loglevel,$logtype,$logmsg,$logpagephptime=0,$logpagemysqltime=0,$logpagequeries=0,$userid=0){
	global $USER,$API;
	if ( isset($_SERVER["REMOTE_ADDR"]) )    {
		$ip=$_SERVER["REMOTE_ADDR"];
	} else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {
		$ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
	} else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {
		$ip=$_SERVER["HTTP_CLIENT_IP"];
	} 
	
	$uagent = new uagent_info();
	if($USER){
		$uid = $USER->userid;
	} else {
		$uid = 0;
	}
	
	// add current page url (to help debug later)
	if($loglevel != 'info'){
		$logmsg = "[".$_SERVER['REQUEST_URI']."]: ".$logmsg;
	}
	$API->writeLog($loglevel,$uid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries,$uagent->useragent);
}

function _mysql_query($query,$db) {
    global $LOGGER;

    $start = microtime(true);
    $result = mysql_query($query,$db);
    $timetaken = microtime(true) - $start;
    // log any queries taking over 5 seconds
    if($timetaken >5){
    	writeToLog("warning","db",$query,0,$timetaken,1);
    }
    $LOGGER->mysql_queries_time += $timetaken;
    $LOGGER->mysql_queries_count++;

    return $result;
}

	
?>
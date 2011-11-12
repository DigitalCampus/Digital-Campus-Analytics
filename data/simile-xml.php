<?php 
require_once("../config.php");
checkLogin();
header("Content-type: text/xml; charset:UTF8");
$duein = optional_param("duein",7,PARAM_INT);
$tasks = $API->getAllTasks($duein);

echo "<?xml version=\"1.0\"?><data>";
foreach($tasks as $t){
	$time = strtotime($t->duedate);
	echo "<event start='".date('d M Y',$time)." UTC' title='".$t->patientname." (".$t->protocol.")'>";
	echo "Location: ".$t->location;
	echo "</event>";
	
}
echo "</data>";

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
writeToLog("info","pagehit",$_SERVER["REQUEST_URI"], $total_time, $CONFIG->mysql_queries_time, $CONFIG->mysql_queries_count);

$API->cleanUpDB();
?>
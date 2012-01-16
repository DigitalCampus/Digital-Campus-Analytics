<?php 
/*
 * cron for running various scheduled tasks
 */

// TODO - extend max exectuion time?

require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");

// check to see when cron was last run (and against min interval)
$lastrun = $API->getSystemProperty('cron.lastrun');
$minint = $API->getSystemProperty('cron.mininterval');
$now = time();

if($lastrun + ($minint*60) > $now){
	echo "exiting";
	die;
}

// update current patients
$API->updatePatients();

// clear up log table
$logdays = $API->getSystemProperty('log.archive.days');
if($logdays > 0){
	$sql = sprintf("DELETE FROM log WHERE logtime < DATE_ADD(NOW(), INTERVAL -%d DAY)",$logdays);
	$API->runSql($sql);
}
// update & cache patient risk factors

// update & cache task list

// update & cache KPI figures?


$API->setSystemProperty('cron.lastrun',$time);
echo "cron complete.";

// this must always be the last function to run in the page
scriptFooter('info','cron','cron complete');
?>
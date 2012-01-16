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
	//die;
}
// let cron run with admin permissions
$USER->props['permissions.admin'] = 'true';

// update current patients
$API->updatePatients();

// clear up log table
$logdays = $API->getSystemProperty('log.archive.days');
if($logdays > 0){
	$sql = sprintf("DELETE FROM log WHERE logtime < DATE_ADD(NOW(), INTERVAL -%d DAY)",$logdays);
	$API->runSql($sql);
}

// update & cache which HPs the patients have visited
// get all submitted protocols in last 2 days // TODO - should really be since cron last run or something
$submitted = $API->getProtocolsSubmitted(array('days'=>5,'limit'=>'all'));

foreach($submitted->protocols as $s){
	$API->cacheAddPatientHealthPointVisit($s->Q_USERID,$s->patienthpcode,$s->protocolhpcode,$s->datestamp,$s->protocol,$s->user_uri);
}

// update & cache patient risk factors

// update & cache task list

// update & cache KPI figures?


$API->setSystemProperty('cron.lastrun',$time);
echo "cron complete.";

// this must always be the last function to run in the page
scriptFooter('info','cron','cron complete');
?>
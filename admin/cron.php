<?php 
/*
 * cron for running various scheduled tasks
 */

// TODO - extend max exectuion time?

require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");

$days = optional_param('days',5,PARAM_INT);

// so can force cron to run even when inside min interval (only really useful for development)
$force = optional_param('force',false,PARAM_BOOL);

// check to see when cron was last run (and against min interval)
$lastrun = $API->getSystemProperty('cron.lastrun');
$minint = $API->getSystemProperty('cron.mininterval');
$now = time();

if(($lastrun + ($minint*60) > $now) && !$force){
	echo "exiting";
	die;
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
// get all submitted protocols in last 5 days // TODO - should really be since cron last run or something
$submitted = $API->getProtocolsSubmitted(array('days'=>$days,'limit'=>'all'));

foreach($submitted->protocols as $s){
	$API->cacheAddPatientHealthPointVisit($s->Q_USERID,$s->patienthpcode,$s->protocolhpcode,$s->datestamp,$s->protocol,$s->user_uri);
}

// update & cache task list
$API->cacheTasksDue($days);

// TODO remove any really old overdue tasks based on the ignore policy
$sql = sprintf("DELETE FROM cache_tasks 
				WHERE datedue < DATE_ADD(NOW(), INTERVAL -%d DAY)",$API->getSystemProperty('overdue.ignore'));
$API->runSql($sql);

// TODO update & cache patient risk factors



// TODO update & cache KPI figures?


$API->setSystemProperty('cron.lastrun',$time);
echo "cron complete.";

// this must always be the last function to run in the page
scriptFooter('info','cron','cron complete');
?>
<?php 
/*
 * cron for running various scheduled tasks
 */

set_time_limit(0);

require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");

$days = optional_param('days',2,PARAM_INT);

// so can force cron to run even when inside min interval (only really useful for development) & option to flush cache first
$force = optional_param('force',false,PARAM_BOOL);
$flush = optional_param('flush',false,PARAM_BOOL);

// check to see when cron was last run (and against min interval)
$lastrun = $CONFIG->props['cron.lastrun'];
$minint = $CONFIG->props['cron.mininterval'];
$now = time();

echo "Starting cron.....................................................\n";
flush_buffers();

if(($lastrun + ($minint*60) > $now) && !$force){
	echo "exiting";
	die;
}
echo date('r');

// run data migration script
/*$f = fopen('migrate.sql',"r");
$sqlFile = fread($f,filesize('migrate.sql'));
$sqlArray = explode(';',$sqlFile);
foreach ($sqlArray as $stmt) {
	$result = mysql_query($stmt);
	echo "ran sql\n";
}*/

$API->cron($flush, $days);

echo "cron complete.";
echo date('r');
// this must always be the last function to run in the page
scriptFooter('info','cron','cron complete');
?>
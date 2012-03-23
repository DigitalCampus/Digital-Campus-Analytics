<?php 
/*
 * cron for running various scheduled tasks
 */

set_time_limit(0);

require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");

$days = optional_param('days',5,PARAM_INT);

// so can force cron to run even when inside min interval (only really useful for development)
$force = optional_param('force',false,PARAM_BOOL);

// check to see when cron was last run (and against min interval)
$lastrun = $CONFIG->props['cron.lastrun'];
$minint = $CONFIG->props['cron.mininterval'];
$now = time();

if(($lastrun + ($minint*60) > $now) && !$force){
	echo "exiting";
	die;
}
// let cron run with admin permissions
$USER->props['permissions.admin'] = 'true';

// set username as 'demo' - this is so that it will generate records for all the 'for practice' records too.
$USER->username = 'demo';

$API->cron($days);

echo "cron complete.";

// this must always be the last function to run in the page
scriptFooter('info','cron','cron complete');
?>
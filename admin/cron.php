<?php 
/*
 * cron for running various scheduled tasks
 */

require_once "../config.php";

writeToLog('info','cron','Starting cron...');

// update current patients
$API->updatePatients();

// clear up log table


// update & cache patient risk factors

// update & cache task list

// update & cache KPI figures?



scriptFooter('info','cron','cron complete');
?>
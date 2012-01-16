<?php
require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");
// only allow access by admins
if($USER->getProp('permissions.admin') != "true"){
	writeToLog('warning','adminpage','accessdenied');
	echo getString ("warning.accessdenied");
	die;
}

$currentDBversion = $API->getSystemProperty('database.version');


if ($currentDBversion < 2){
	//run sql here
	$sql = "CREATE TABLE `cache_tasks` (
			  `taskid` bigint  NOT NULL AUTO_INCREMENT,
			  `hpcode` integer  NOT NULL,
			  `userid` int  NOT NULL,
			  `datedue` DATETIME  NOT NULL,
			  `protocol` varchar(255)  NOT NULL,
			  PRIMARY KEY (`taskid`)
			)
			ENGINE = InnoDB
			CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
			('overdue.ignore', '90', 'Number of days after which any incomplete overdue tasks should just be ignored. To prevent overdue task lisk becoming too long.')";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','2');
	echo "Upgraded to version 2\n";
}



echo "Upgrade complete";
?>
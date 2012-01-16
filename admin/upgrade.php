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
	// create table to cache task list
	$sql = "CREATE TABLE `cache_tasks` (
			  `taskid` bigint  NOT NULL AUTO_INCREMENT,
			  `hpcode` integer  NOT NULL,
			  `userid` integer  NOT NULL,
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


if ($currentDBversion < 3){
	// create table to cache which Healthpoints a patient has visited
	$sql = "CREATE TABLE `cache_visit` (
			  `pathpid` bigint  NOT NULL AUTO_INCREMENT,
			  `hpcode` integer  NOT NULL,
			  `userid` integer  NOT NULL,
			  `visithpcode` integer  NOT NULL,
			  `visitdate` DATETIME  NOT NULL,
			  `protocol` varchar(255)  NOT NULL,
			  PRIMARY KEY (`pathpid`)
			)
			ENGINE = InnoDB
			CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$API->runSql($sql);


	//now update the db version prop
	$API->setSystemProperty('database.version','3');
	echo "Upgraded to version 3\n";
}

if ($currentDBversion < 4){
	// add compound key
	$sql = "ALTER TABLE `cache_visit` ADD UNIQUE KEY (hpcode,userid,visithpcode,visitdate,protocol)";
	$API->runSql($sql);

	$sql = "ALTER TABLE `cache_visit` ADD COLUMN `user_uri` varchar(255)  NOT NULL AFTER `protocol`";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','4');
	echo "Upgraded to version 4\n";
}

/*
if ($currentDBversion < 5){
	
	//now update the db version prop
	$API->setSystemProperty('database.version','5');
	echo "Upgraded to version 5\n";
}*/

echo "Upgrade complete";
?>
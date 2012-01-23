<?php
require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");

include_once('install.php');

$currentDBversion = $CONFIG->props['database.version'];

$flushcache = false;

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
	$flushcache = true;
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
	$flushcache = true;
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
	$flushcache = true;
	echo "Upgraded to version 4\n";
}


if ($currentDBversion < 5){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
			('target.anc1', '60', 'Target percentage of ANC1 visits on time')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('target.anc2', '60', 'Target percentage of ANC2 visits on time')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('target.tt1', '60', 'Target percentage of TT1 injections on time')";
	$API->runSql($sql);
	
	// create table to cache patient risk factors
	$sql = "CREATE TABLE `cache_risk` (
				  `riskid` bigint  NOT NULL AUTO_INCREMENT,
				  `hpcode` integer  NOT NULL,
				  `userid` integer  NOT NULL,
				  `risk` varchar(255)  NOT NULL,
				  PRIMARY KEY (`riskid`)
				)
				ENGINE = InnoDB
				CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$API->runSql($sql);
	
	$sql = "CREATE TABLE `cache_risk_category` (
					  `riskcatid` bigint  NOT NULL AUTO_INCREMENT,
					  `hpcode` integer  NOT NULL,
					  `userid` integer  NOT NULL,
					  `riskcategory` varchar(255)  NOT NULL,
					  PRIMARY KEY (`riskcatid`)
					)
					ENGINE = InnoDB
					CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','5');
	$flushcache = true;
	echo "Upgraded to version 5\n";
}

if ($currentDBversion < 6){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
			('default.limit', '50', 'Default limit for number of records to return')";
	$API->runSql($sql);

	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc1.duebyend', '119', 'No days after LMP by ANC1 is due')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc2.duebystart', '168', 'No days after LMP by ANC2 due - start')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc2.duebyend', '203', 'No days after LMP by ANC2 due - end')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc3.duebystart', '210', 'No days after LMP by ANC3 due - start')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc3.duebyend', '231', 'No days after LMP by ANC3 due - end')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc4.duebystart', '238', 'No days after LMP by ANC4 due - start')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('anc4.duebyend', '259', 'No days after LMP by ANC4 due - end')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('tt1.validity', '42', 'No days TT1 only is valid for - before counting as defaulter')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('tt2.validity', '365', 'No days TT2 only is valid for - before counting as defaulter')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('default.lang', 'en', 'Default language')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('langs.available', '{'\en'\:'\English'\,'\tg'\:'\Tigrinyan'\},'\am'\:'\Amharic'\}', 'JSON encoded array of the lanaguages available')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('google.analytics', '', 'Google Analytics key')";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','6');
	echo "Upgraded to version 6\n";
}

echo "Upgrade complete\n";
if($flushcache){
	echo "Now running cron to update the cache tables... This may take some time!\n";
	$USER->props['permissions.admin'] = 'true';
	// regenerating cache for last 1 year - not ideal
	$API->cron(365);
	echo "cron complete.";
	scriptFooter('info','upgrade',sprintf('upgrade to version %d complete',$CONFIG->props['database.version']));
}

?>
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
				('langs.available', '{\"en\":\"English\",\"ti\":\"Tigrinyan\",\"am\":\"Amharic\"}', 'JSON encoded array of the lanaguages available')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('google.analytics', 'UA-3609005-8', 'Google Analytics key')";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','6');
	echo "Upgraded to version 6\n";
}

if ($currentDBversion < 7){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('pnc1.duebystart', '1', 'No days after delivery by PNC1 due - start')";
	$API->runSql($sql);

	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('pnc1.duebyend', '3', 'No days after delivery by PNC1 due - end')";
	$API->runSql($sql);

	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('pnc2.duebystart', '6', 'No days after delivery by PNC2 due - start')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('pnc2.duebyend', '7', 'No days after delivery by PNC2 due - end')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('pnc3.duebystart', '35', 'No days after delivery by PNC3 due - start')";
	$API->runSql($sql);
	
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('pnc3.duebyend', '42', 'No days after delivery by PNC3 due - end')";
	$API->runSql($sql);

	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('target.pnc1', '60', 'Target percentage of PNC1 visits on time')";
	$API->runSql($sql);
	//now update the db version prop
	$API->setSystemProperty('database.version','7');
	echo "Upgraded to version 7\n";
}

if ($currentDBversion < 8){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('target.protocols', '40', 'Target number of protocols which should be submitted per health point')";
	$API->runSql($sql);
	//now update the db version prop
	$API->setSystemProperty('database.version','8');
	echo "Upgraded to version 8\n";
}
	
if ($currentDBversion < 9){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('mobile.deliveries.nodays', '31', 'Show deliveries due in the next number of days')";
	$API->runSql($sql);
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
					('mobile.tasks.nodays', '31', 'Show tasks due in the next number of days')";
	$API->runSql($sql);
	//now update the db version prop
	$API->setSystemProperty('database.version','9');
	echo "Upgraded to version 9\n";
}

if ($currentDBversion < 10){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('target.anc1submitted', '6', 'Target number of ANC 1 visits submitted per health post per month')";
	$API->runSql($sql);
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
					('target.ancfollowsubmitted', '12', 'Target number of ANC Follow up visits submitted per health post per month')";
	$API->runSql($sql);
	//now update the db version prop
	$API->setSystemProperty('database.version','10');
	echo "Upgraded to version 10\n";
}

if ($currentDBversion < 11){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
				('target.deliverysubmitted', '6', 'Target number of delivery submitted per health post per month')";
	$API->runSql($sql);
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
					('target.pncsubmitted', '12', 'Target number of PNC visits submitted per health post per month')";
	$API->runSql($sql);
	//now update the db version prop
	$API->setSystemProperty('database.version','11');
	echo "Upgraded to version 11\n";
}

if ($currentDBversion < 12){
	// create table to cache patient risk factors
	$sql = "CREATE TABLE IF NOT EXISTS `cache_datacheck` (
				  `dcid` bigint(20) NOT NULL AUTO_INCREMENT,
				  `dcdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `patid` int(11) NOT NULL,
				  `hpcode` int(11) NOT NULL,
				  `protocol` varchar(50) DEFAULT NULL,
				  `dctype` varchar(20) NOT NULL,
				  `dcregby` varchar(50) DEFAULT NULL,
				  `dcdata` text,
				PRIMARY KEY (`dcid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','12');
	echo "Upgraded to version 12\n";
}

if ($currentDBversion < 13){
	$sql = "ALTER TABLE `cache_visit` MODIFY COLUMN `user_uri` VARCHAR(80)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
	$API->runSql($sql);
	
	$sql = "ALTER TABLE `user` MODIFY COLUMN `user_uri` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
	$API->runSql($sql); 
	
	//now update the db version prop
	$API->setSystemProperty('database.version','13');
	echo "Upgraded to version 13\n";
}

if ($currentDBversion < 14){
	$sql = "ALTER TABLE `healthpoint` ADD COLUMN `hpactive` boolean NOT NULL DEFAULT true AFTER `locationlng`;";
	$API->runSql($sql);

	$sql = "ALTER TABLE `user` ADD COLUMN `useractive` boolean  NOT NULL DEFAULT true AFTER `hpid`;";
	$API->runSql($sql);

	//now update the db version prop
	$API->setSystemProperty('database.version','14');
	echo "Upgraded to version 14\n";
}


if ($currentDBversion < 15){
	$sql = "INSERT INTO `properties` (`propname`, `propvalue`, `propinfo`) VALUES
					('target.ancsubmitted', '18', 'Target number of ANC visits submitted per health post per month')";
	$API->runSql($sql);
	
	//now update the db version prop
	$API->setSystemProperty('database.version','15');
	echo "Upgraded to version 15\n";
}

if ($currentDBversion < 16){
	$sql = "ALTER TABLE `healthpoint` ADD COLUMN `hptype` varchar(20)  NOT NULL AFTER `hpactive`;";
	$API->runSql($sql);

	//now update the db version prop
	$API->setSystemProperty('database.version','16');
	echo "Upgraded to version 16\n";
}

echo "Upgrade complete\n";
if($flushcache){
	echo "Now running cron to update the cache tables... This may take some time!\n";
	
	$API->cron(true);
	echo "cron complete.";
	scriptFooter('info','upgrade',sprintf('upgrade to version %d complete',$CONFIG->props['database.version']));
}




?>
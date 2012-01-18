<?php
require_once "../config.php";
header("Content-Type: text/plain; charset=UTF-8");

//find out if already installed...
$installed = $API->getSystemProperty('database.version');
if($installed == false){
	
	$sql = "CREATE TABLE IF NOT EXISTS `district` (
			  `did` bigint(20) NOT NULL AUTO_INCREMENT,
			  `dname` varchar(200) NOT NULL,
			  PRIMARY KEY (`did`)
			) ENGINE=InnoDB  
			CHARACTER SET utf8 
			COLLATE utf8_general_ci;";
	$API->runSql($sql);
	echo "'district' table created\n";
	
	
	$sql = "CREATE TABLE IF NOT EXISTS `healthpoint` (
			  `hpid` bigint(20) NOT NULL AUTO_INCREMENT,
			  `hpcode` int(11) NOT NULL,
			  `hpname` varchar(200) NOT NULL,
			  `did` bigint(20) NOT NULL,
			  `locationlat` float NOT NULL,
			  `locationlng` float NOT NULL,
			  PRIMARY KEY (`hpid`)
			) ENGINE=InnoDB  
			CHARACTER SET utf8 
			COLLATE utf8_general_ci;";
	$API->runSql($sql);
	echo "'healthpoint' table created\n";
	
	
	$sql = "CREATE TABLE IF NOT EXISTS `log` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `loglevel` varchar(20) NOT NULL,
			  `userid` bigint(20) NOT NULL,
			  `logtype` varchar(20) NOT NULL,
			  `logmsg` text NOT NULL,
			  `logip` varchar(20) NOT NULL,
			  `logpagephptime` float DEFAULT NULL,
			  `logpagequeries` int(11) DEFAULT NULL,
			  `logpagemysqltime` float DEFAULT NULL,
			  `logagent` text NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  
			CHARACTER SET utf8 
			COLLATE utf8_general_ci;";
	$API->runSql($sql);
	echo "'log' table created\n";
	
	$sql = "CREATE TABLE IF NOT EXISTS `patientcurrent` (
			  `pcid` bigint(20) NOT NULL AUTO_INCREMENT,
			  `pcupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `hpcode` varchar(255) NOT NULL,
			  `pcurrent` tinyint(1) NOT NULL DEFAULT '1',
			  `patid` int(9) NOT NULL,
			  PRIMARY KEY (`pcid`)
			) ENGINE=InnoDB  
			CHARACTER SET utf8 
			COLLATE utf8_general_ci;";
	$API->runSql($sql);
	echo "'patientcurrent' table created\n";
	
	$sql = "CREATE TABLE IF NOT EXISTS `user` (
			  `userid` bigint(20) NOT NULL AUTO_INCREMENT,
			  `username` varchar(50) NOT NULL,
			  `password` varchar(200) NOT NULL,
			  `email` varchar(200) DEFAULT NULL,
			  `firstname` varchar(100) NOT NULL,
			  `lastname` varchar(100) NOT NULL,
			  `defaultlang` varchar(2) NOT NULL,
			  `user_uri` varchar(255) NOT NULL,
			  `hpid` bigint(20) NOT NULL,
			  PRIMARY KEY (`userid`)
			) ENGINE=InnoDB
			CHARACTER SET utf8 
			COLLATE utf8_general_ci;";
	$API->runSql($sql);
	echo "'user' table created\n";
	
	$sql = "CREATE TABLE IF NOT EXISTS `userprops` (
			  `propid` bigint(20) NOT NULL AUTO_INCREMENT,
			  `userid` bigint(20) NOT NULL,
			  `propname` varchar(100) NOT NULL,
			  `propvalue` text NOT NULL,
			  PRIMARY KEY (`propid`)
			) ENGINE=InnoDB
			CHARACTER SET utf8 
			COLLATE utf8_general_ci;";
	$API->runSql($sql);
	echo "'userprops' table created\n";
	
	$sql = "INSERT INTO `district` (`did`, `dname`) VALUES
				(1, 'Hinatalo wajerat District'),
				(2, 'Kilteawelaelo District'),
				(3, 'Testing');";
	$API->runSql($sql);
	echo "'district' table populated\n";
	
	
	$sql = "INSERT INTO `healthpoint` (`hpid`, `hpcode`, `hpname`, `did`, `locationlat`, `locationlng`) VALUES
				(1, 1000, 'Mesanu - Kilteawelaelo health post', 2, 13.701, 39.6452),
				(2, 1001, 'Ayanlem health post', 2, 13.7542, 39.5513),
				(3, 1002, 'Gemad health post ', 2, 13.8767, 39.563),
				(4, 1003, 'Negash health post', 2, 13.876, 39.5984),
				(5, 1004, 'Tsadanaele health post', 2, 13.9101, 39.5814),
				(10, 1009, 'Frewoyni health post', 1, 0, 0),
				(9, 1008, 'Mesanu - Hintalowajerat health post', 1, 0, 0),
				(8, 1007, 'Hagerselam health post', 1, 0, 0),
				(7, 1006, 'Ara Alemsegeda health post', 1, 0, 0),
				(6, 1005, 'Maynebri health post', 1, 0, 0),
				(11, 50005, 'Negash health centre', 2, 0, 0),
				(12, 50004, 'Hiwane health centre', 1, 0, 0),
				(13, 50003, 'Agulae health centre ', 2, 0, 0),
				(14, 50002, 'Adigudom health centre', 1, 0, 0),
				(15, 50001, 'Abraha Atsbha health centre', 2, 0, 0),
				(16, 0, 'Digital Campus team', 3, 0, 0),
				(17, 9999, 'For practice', 3, 0, 0);";
	$API->runSql($sql);
	echo "'healthpoint' table populated\n";
	
	$sql = "INSERT INTO `user` (`userid`, `username`, `password`, `email`, `firstname`, `lastname`, `defaultlang`, `user_uri`, `hpid`) VALUES (1, 'admin', MD5('admin'), '', 'Admin', 'User', 'en', '', '16');";
	$API->runSql($sql);
	$sql = "INSERT INTO `userprops` (`propid`, `userid`, `propname`, `propvalue`) VALUES (NULL, '1', 'permissions.admin', 'true');";
	$API->runSql($sql);
	echo "admin user created\n";
	
	
	$sql = "CREATE TABLE IF NOT EXISTS `properties` (
			  `propid` bigint(20) NOT NULL AUTO_INCREMENT,
			  `propname` text COLLATE utf8_unicode_ci NOT NULL,
			  `propvalue` text COLLATE utf8_unicode_ci NOT NULL,
			  `propinfo` text COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`propid`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
	$API->runSql($sql);
	echo "'properties' table created\n";
	
	$sql = "INSERT INTO `properties` (`propid`, `propname`, `propvalue`, `propinfo`) VALUES
				(1, 'cron.lastrun', '0', 'Date/time cron was last run'),
				(2, 'log.archive.days', '365', 'Number of days to keep log entries for, anything older will be auto deleted. Use 0 to set as ''never delete'''),
				(3, 'cron.mininterval', '60', 'Minimum interval, in minutes, at which cron can run. To prevent running too often');
				(4, 'database.version', '1', 'Database version number. Used to detect database updates');";
	$API->runSql($sql);
	echo "default properties created\n";
}
?>
<?php 

/*
 * Admin Class
 */
class Admin {
	
	function lastLogin(){
		global $API;
		$sql = "SELECT u.userid, firstname, lastname, propvalue FROM user u
					INNER JOIN userprops up ON up.userid = u.userid
					WHERE propname='lastlogin'
					ORDER BY propvalue DESC";
		/*
		 * "select max(logtime) as propvalue, l.userid, firstname,lastname from log l
inner join user u on u.userid = l.userid
where logtype='login'
group by l.userid
order by max(logtime) desc"
		 */
		$stats = array();
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($stats,$row);
		}
		return $stats;
	}
	
	function neverLogin($nodays=31){
		global $API;
		$sql = "SELECT u.userid, firstname, lastname FROM user u
					WHERE u.userid NOT IN 
						(SELECT userid FROM userprops 
							WHERE propname='lastlogin' 
							AND CAST(propvalue AS DATETIME) > DATE_ADD(NOW(),INTERVAL -".$nodays." DAY))";
		$stats = array();
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($stats,$row);
		}
		return $stats;
	}
	
	function userHits($nodays=31){
		global $API;
		$sql = "SELECT COUNT(l.id) AS hits, u.userid, u.firstname, u.lastname FROM log l
					INNER JOIN user u ON u.userid = l.userid 
					WHERE l.logtime >= DATE_ADD(CURDATE(), INTERVAL -".$nodays." DAY)
					AND logtype = 'pagehit'
					GROUP BY u.userid, u.firstname, u.lastname
					ORDER BY hits DESC";
		$stats = array();
		$result = $API->runSql($sql);
		 
		while($row = mysql_fetch_object($result)){
			array_push($stats,$row);
		}
		return $stats;
	}
	
	function dailyHits($nodays=31){
		global $API;
		$sql = "SELECT COUNT(l.id) AS hits, DAY(logtime) AS logday, MONTH(logtime) as logmonth, YEAR(logtime)  as logyear FROM log l
					WHERE logtype = 'pagehit'
					AND l.logtime >= DATE_ADD(CURDATE(), INTERVAL -".$nodays." DAY)
					GROUP BY logday, logmonth, logyear
					ORDER BY logyear ASC, logmonth ASC, logday ASC";
		$stats = array();
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($stats,$row);
		}
		return $stats;
	}
	
	function popularPages($nodays=31,$limit=20){
		global $API;
		$sql = "SELECT COUNT(id) as hits, logmsg FROM log l
					WHERE logtype='pagehit'
					AND l.logtime >= DATE_ADD(CURDATE(), INTERVAL -".$nodays." DAY)
					GROUP BY logmsg
					ORDER BY hits DESC
					LIMIT 0,".$limit;
		$stats = array();
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($stats,$row);
		}
		return $stats;
	}
	
	function log($type,$nodays=31,$limit=50){
		global $API;
		$sql = "SELECT * FROM log
					WHERE logtime >= DATE_ADD(NOW(), INTERVAL -".$nodays." DAY)";
		if($type != 'all'){
			$sql .= " AND loglevel = '".$type."'";
		}
		$sql .=	"ORDER By logtime DESC LIMIT 0,".$limit;
		$stats = array();
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($stats,$row);
		}
		return $stats;
	}
}
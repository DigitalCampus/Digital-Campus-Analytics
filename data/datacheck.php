<?php 

/*
 * DataChecker Class
 */
class DataCheck {
	
	function summary() {
		$total = 0;
		$total += count($this->unregistered());
		$total += count($this->missingProtocols());
		$total += count($this->duplicates());
		if($total >0){
			return true;
		} else {
			return false;
		}
	}

	
	function unregistered(){
		global $API;
		$report = array();
		// unregistered from ancfirst
		$sql = "SELECT * FROM (";
		$sql .= "SELECT p.Q_HEALTHPOINTID,
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,  
							p.Q_USERID,
							'".PROTOCOL_ANCFIRST."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCFIRST." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null ";
	
		//unregistered from ancfollow
		$sql .= " UNION
					SELECT p.Q_HEALTHPOINTID,
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode, 
							php.hpname as patientlocation,
							hp.hpname as protocollocation, 
							p.Q_USERID,
							'".PROTOCOL_ANCFOLLOW."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCFOLLOW." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null";
	
		//unregistered from anclabtest
		$sql .= " UNION SELECT p.Q_HEALTHPOINTID,
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,   
							p.Q_USERID,
							'".PROTOCOL_ANCLABTEST."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCLABTEST." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null";
	
		// unregistered from anctransfer
		$sql .= " UNION
					SELECT p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation, 
							p.Q_USERID,
							'".PROTOCOL_ANCTRANSFER."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCTRANSFER." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null";
	
		// unregistered from delivery
		$sql .= " UNION
					SELECT p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
	 						hp.hpname as protocollocation, 
							p.Q_USERID,
							'".PROTOCOL_DELIVERY."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_DELIVERY." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null";
	
		// TODO add unregistered from PNC
		$sql .= ") a";
		$sql .= " WHERE (a.patienthpcode IN (".$API->getUserHealthPointPermissions().") " ;
		$sql .= " OR a.protocolhpcode IN (".$API->getUserHealthPointPermissions().")) " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}

		$result = $API->runSql($sql);
		
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
	
		return $report;
	}
	
	function duplicates(){
		global $API;
		$report = array();
		//duplicate Registration
		$sql = "SELECT 	i.Q_HEALTHPOINTID,
						php.hpname as patientlocation, 
						hp.hpname as protocollocation, 
						i.Q_USERID,
						'".PROTOCOL_REGISTRATION."' as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
						FROM ".TABLE_REGISTRATION." i
						INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
						INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
						INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		// add permissions
		$sql .= " WHERE i.Q_HEALTHPOINTID IN (".$API->getUserHealthPointPermissions().") " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND i.Q_HEALTHPOINTID NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY hp.hpname,
					i.Q_HEALTHPOINTID, 
					i.Q_USERID
				HAVING count(i._URI)>1";

		$result = $API->runSql($sql);
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		
		// duplicate ancfirst
		$sql = "SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_ANCFIRST."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCFIRST." i
					INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
					INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		$sql .= " WHERE (php.hpcode IN (".$API->getUserHealthPointPermissions().")" ;
		$sql .= " OR hp.hpcode IN (".$API->getUserHealthPointPermissions().")) " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND php.hpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY php.hpname,
						i.Q_HEALTHPOINTID, 
						i.Q_USERID
					HAVING count(i._URI)>1";
		$result = $API->runSql($sql);
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
	
		// duplicate follow up
		$sql = "SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_ANCFOLLOW."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							i.Q_FOLLOWUPNO
					FROM ".TABLE_ANCFOLLOW." i
					INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
					INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		$sql .= " WHERE  (php.hpcode IN (".$API->getUserHealthPointPermissions().")" ;
		$sql .= " OR hp.hpcode IN (".$API->getUserHealthPointPermissions().")) " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND php.hpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY php.hpname,
						i.Q_HEALTHPOINTID, 
						i.Q_USERID,
						i.Q_FOLLOWUPNO
					HAVING count(i._URI)>1";
		$result =$API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
	
	
		// TODO duplicate labtest
		// check in case they really do have 2?
		$sql = "SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_ANCLABTEST."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCLABTEST." i
					INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
					INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		$sql .= " WHERE  (php.hpcode IN (".$API->getUserHealthPointPermissions().")" ;
		$sql .= " OR hp.hpcode IN (".$API->getUserHealthPointPermissions().")) " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND php.hpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY php.hpname,
						i.Q_HEALTHPOINTID, 
						i.Q_USERID
					HAVING count(i._URI)>1";
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
	
		// TODO duplicate transfer
		// TODO add permissions
	
		// TODO duplicate delivery
		// TODO add permissions
	
		// TODO duplicate PNC
		// TODO add permissions
		return $report;
	}
	
	function missingProtocols(){
		global $API;
		$missing = array();
	
		// check anc first if follow up 1 exists
		$sql = "SELECT * FROM (";
		$sql .= "SELECT p.Q_USERID,
							p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
							'ANC Follow Up 1 submitted but no ANC First Visit' as reason
					FROM ".TABLE_ANCFOLLOW." p 
					LEFT OUTER JOIN ".TABLE_ANCFIRST." first ON p.Q_USERID = first.Q_USERID AND p.Q_HEALTHPOINTID = first.Q_HEALTHPOINTID 
					INNER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE p.Q_FOLLOWUPNO ='2'
					AND first.Q_USERID is null ";
	
	
		//check follow up 2 if follow up 1
		$sql .= " UNION
					SELECT p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
							'ANC Follow Up 3 submitted but no ANC Follow Up 2' as reason
					FROM ".TABLE_ANCFOLLOW." p 
					LEFT OUTER JOIN (SELECT * FROM ".TABLE_ANCFOLLOW." WHERE Q_FOLLOWUPNO='2') follow
						ON p.Q_USERID = follow.Q_USERID AND p.Q_HEALTHPOINTID = follow.Q_HEALTHPOINTID 
					INNER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE p.Q_FOLLOWUPNO ='3'
					AND follow.Q_USERID is null";
	
		//check follow up 3 if follow up 2
		$sql .= " UNION
					SELECT p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
							'ANC Follow Up 4 submitted but no ANC Follow Up 3' as reason
					FROM ".TABLE_ANCFOLLOW." p 
					LEFT OUTER JOIN (SELECT * FROM ".TABLE_ANCFOLLOW." WHERE Q_FOLLOWUPNO='2') follow
						ON p.Q_USERID = follow.Q_USERID AND p.Q_HEALTHPOINTID = follow.Q_HEALTHPOINTID 
					INNER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE p.Q_FOLLOWUPNO ='4'
					AND follow.Q_USERID is null";
		//labtest but no first visit
		$sql .= " UNION
					SELECT p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
							'ANC Lab Test submitted but no ANC First Visit' as reason
					FROM ".TABLE_ANCLABTEST." p 
					LEFT OUTER JOIN ".TABLE_ANCFIRST." first ON p.Q_USERID = first.Q_USERID AND p.Q_HEALTHPOINTID = first.Q_HEALTHPOINTID 
					INNER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE first.Q_USERID is null ";
	
		//TODO check anc when labour/delivery
	
		//TODO check labour/dlivery when pnc
	
		$sql .= ") a ";
		$sql .= " WHERE (a.patienthpcode IN (".$API->getUserHealthPointPermissions().")" ;
		$sql .= " OR a.protocolhpcode IN (".$API->getUserHealthPointPermissions().")) " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}
	
		$result = $API->runSql($sql);
	
		while($row = mysql_fetch_object($result)){
			array_push($missing,$row);
		}
		return $missing;
	}
}
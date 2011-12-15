<?php 

/*
 * DataChecker Class
 */
class DataCheck {
	
	function summary() {
		$total = 0;
		$total += count($this->duplicateReg());
		$total += count($this->unregistered());
		//$total += count($this->datacheckMissingProtocols());
		//$total += count($this->datacheckDuplicateProtocols());
		if($total >0){
			return true;
		} else {
			return false;
		}
	}
	
	function duplicateReg(){
		global $API;
		$report = array();
		//include_once 'datacheck/duplicatereg.php';
		$sql = "SELECT 	i.Q_HEALTHPOINTID as healthpointcode,
							hp.hpname as healthpointname, 
							i.Q_USERID as patientid
							FROM ".TABLE_REGISTRATION." i
							INNER JOIN healthpoint hp ON hp.hpcode = i.Q_HEALTHPOINTID";
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
		return $report;
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
}
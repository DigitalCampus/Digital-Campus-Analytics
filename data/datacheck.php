<?php 

/*
 * DataChecker Class
 */
class DataCheck {
	
	function summary() {
		$total = 0;
		$total += count($this->unregistered());
		$total += count($this->duplicates());
		if($total >0){
			return true;
		} else {
			return false;
		}
	}

	
	function unregistered($opts=array()){
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
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							p._CREATION_DATE as datestamp
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
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							p._CREATION_DATE as datestamp
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
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							p._CREATION_DATE as datestamp
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
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							p._CREATION_DATE as datestamp
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
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							p._CREATION_DATE as datestamp
					FROM ".TABLE_DELIVERY." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null";
	
		// unregistered from PNC
		$sql .= " UNION
					SELECT p.Q_HEALTHPOINTID, 
							php.hpcode as patienthpcode,
	 						hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
	 						hp.hpname as protocollocation, 
							p.Q_USERID,
							'".PROTOCOL_PNC."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							p._CREATION_DATE as datestamp
					FROM ".TABLE_PNC." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE r._URI is null";
		
		$sql .= ") a";
		$sql .= " WHERE (a.patienthpcode IN (".$API->getUserHealthPointPermissions().") " ;
		$sql .= " OR a.protocolhpcode IN (".$API->getUserHealthPointPermissions().")) " ;
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		if(array_key_exists('hpcodes',$opts)){
			$sql .= " AND  (a.patienthpcode IN ( ".$opts['hpcodes'].")";
			$sql .= " OR a.protocolhpcode IN (".$opts['hpcodes']."))";
		}
		$sql .= " ORDER BY submittedname ASC, patientlocation ASC, Q_USERID ASC"; 

		$result = $API->runSql($sql);
		if(!$result){
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		return $report;
	}
	
	function duplicates(){
		global $API;
		$report = array();
		
		$sql = "SELECT * FROM (";
		//duplicate Registration
		$sql .= "SELECT 	i.Q_HEALTHPOINTID,
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

		
		// duplicate ancfirst
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID,
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
	
		// duplicate follow up
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_ANCFOLLOW."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
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
						i.TODAY
					HAVING count(i._URI)>1";	
	
		// duplicate labtest
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID,
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
						i.Q_USERID,
						i.TODAY
					HAVING count(i._URI)>1";
		
	
		// duplicate transfer
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_ANCTRANSFER."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_ANCTRANSFER." i
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
						i.TODAY
					HAVING count(i._URI)>1";
	
		// duplicate delivery
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_DELIVERY."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_DELIVERY." i
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
	
		// duplicate PNC
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID,
							php.hpname as patientlocation, 
							hp.hpname as protocollocation, 
							i.Q_USERID ,
							'".PROTOCOL_PNC."' as protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname
					FROM ".TABLE_PNC." i
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
						i.TODAY
					HAVING count(i._URI)>1";
		
		$sql .= ") a ORDER BY submittedname ASC, patientlocation ASC, Q_USERID ASC"; 
		
		$result = $API->runSql($sql);
		if(!$result){
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		return $report;
	}
	
	function age(){
		global $API;
		$patients = $API->getCurrentPatients();
		
		foreach($patients as $p){
			$age = array();
		
			// reg
			if(isset($p->Q_AGE)){
				$age[$p->Q_AGE] = PROTOCOL_REGISTRATION;
			}
			// first
			if(isset($p->ancfirst->Q_AGE)){
				$age[$p->ancfirst->Q_AGE] = PROTOCOL_ANCFIRST;
			}
			//follow
			foreach($p->ancfollow as $x){
				if(isset($x->Q_AGE)){
					$age[$x->Q_AGE] = PROTOCOL_ANCFOLLOW;
				}
			}
			//xfer
			foreach($p->anctransfer as $x){
				if(isset($x->Q_AGE)){
					$age[$x->Q_AGE] = PROTOCOL_ANCTRANSFER;
				}
			}
			//lab
			foreach($p->anclabtest as $x){
				if(isset($x->Q_AGE)){
					$age[$x->Q_AGE] = PROTOCOL_ANCLABTEST;
				}
			}
			//delivery
			if(isset($p->delivery->Q_AGE)){
				$age[$p->delivery->Q_AGE] = PROTOCOL_DELIVERY;
			}
			//pnc
			foreach($p->pnc as $x){
				if(isset($x->Q_AGE)){
					$age[$x->Q_AGE] = PROTOCOL_PNC;
				}
			}
		
			// now do year of birth
			$yob = array();
			// reg
			if(isset($p->Q_YEAROFBIRTH)){
				$yob[$p->Q_YEAROFBIRTH] = PROTOCOL_REGISTRATION;
			}
			// first
			if(isset($p->ancfirst->Q_YEAROFBIRTH)){
				$yob[$p->ancfirst->Q_YEAROFBIRTH] = PROTOCOL_ANCFIRST;
			}
			//follow
			foreach($p->ancfollow as $x){
				if(isset($x->Q_YEAROFBIRTH)){
					$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCFOLLOW;
				}
			}
			//xfer
			foreach($p->anctransfer as $x){
				if(isset($x->Q_YEAROFBIRTH)){
					$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCTRANSFER;
				}
			}
			//lab
			foreach($p->anclabtest as $x){
				if(isset($x->Q_YEAROFBIRTH)){
					$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCLABTEST;
				}
			}
			//delivery
			if(isset($p->delivery->Q_YEAROFBIRTH)){
				$yob[$p->delivery->Q_YEAROFBIRTH] = PROTOCOL_DELIVERY;
			}
			//pnc
			foreach($p->pnc as $x){
				if(isset($x->Q_YEAROFBIRTH)){
					$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_PNC;
				}
			}
		
		
			if(count($age)>1 || count($yob) >1 ){
				printf("<a href='patient.php?hpcode=%d&patientid=%d'>%s/%d</a>",$p->hpcode,$p->Q_USERID,$p->patientlocation,$p->Q_USERID);
				echo "<ul>";
				foreach($age as $k=>$v){
					printf("<li>Age on %s: %d</li>",getstring($v),$k);
				}
				echo "</ul>";
				echo "<ul>";
				foreach($yob as $k=>$v){
					printf("<li>Year of birth on %s: %d</li>",getstring($v),$k);
				}
				echo "</ul>";
		
			}
		
		}
	}
}
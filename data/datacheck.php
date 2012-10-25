<?php 

/*
 * DataChecker Class
 */
class DataCheck {
	
	function summary() {
		global $API;
		$sql = "SELECT * FROM cache_datacheck";
		$result = $API->runSql($sql);
		$total = mysql_num_rows($result);
		
		if($total > 0){
			return true;
		} else {
			return false;
		}
	}

	
	function findUnregistered($opts=array()){
		global $API;
		$report = array();
		$sql = "SELECT * FROM (";
		
		//unregistered from ANC
		$sql .= " SELECT p.Q_HEALTHPOINTID as patienthpcode, 
							p.Q_USERID,
							'".PROTOCOL_ANC."' as protocol,
							p._CREATOR_URI_USER
					FROM ".TABLE_ANC." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					WHERE r._URI is null";
	
		//unregistered from anclabtest
		$sql .= " UNION SELECT p.Q_HEALTHPOINTID as patienthpcode,   
							p.Q_USERID,
							'".PROTOCOL_ANCLABTEST."' as protocol,
							p._CREATOR_URI_USER
					FROM ".TABLE_ANCLABTEST." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID)  
					WHERE r._URI is null";
	
		// unregistered from delivery
		$sql .= " UNION
					SELECT p.Q_HEALTHPOINTID as patienthpcode,
							p.Q_USERID,
							'".PROTOCOL_DELIVERY."' as protocol,
							p._CREATOR_URI_USER
					FROM ".TABLE_DELIVERY." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					WHERE r._URI is null";
	
		// unregistered from PNC
		$sql .= " UNION
					SELECT p.Q_HEALTHPOINTID as patienthpcode,
							p.Q_USERID,
							'".PROTOCOL_PNC."' as protocol,
							p._CREATOR_URI_USER
					FROM ".TABLE_PNC." p
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
					WHERE r._URI is null";
		
		$sql .= ") a";
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " WHERE a.patienthpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}

		$result = $API->runSql($sql);
		if(!$result){
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		return $report;
	}
	
	
	function getDataCheck($type){
		global $API;
		$report = array();
		$sql = sprintf("SELECT 
					cdc.hpcode as patienthpcode, 
					hp.hpcode as protocolhpcode, 
					cdc.patid,
					cdc.protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					dcdate
		 		FROM cache_datacheck cdc
				INNER JOIN healthpoint php ON php.hpcode = cdc.hpcode
				INNER JOIN user u ON cdc.dcregby = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE dctype='%s'
				ORDER BY dcdate ASC",$type);
		
		$result = $API->runSql($sql);
		if(!$result){
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		return $report;	
	}
	
	function findDuplicates(){
		global $API;
		$report = array();
		
		$sql = "SELECT * FROM (";
		//duplicate Registration
		$sql .= "SELECT i.Q_HEALTHPOINTID as patienthpcode, 
						i.Q_USERID,
						'".PROTOCOL_REGISTRATION."' as protocol,
						i._CREATOR_URI_USER
						FROM ".TABLE_REGISTRATION." i";
		$sql .= " GROUP BY 
					i.Q_HEALTHPOINTID, 
					i.Q_USERID
				HAVING count(i._URI)>1";
	
		// duplicate ANC
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID as patienthpcode,  
							i.Q_USERID ,
							'".PROTOCOL_ANC."' as protocol,
							i._CREATOR_URI_USER
					FROM ".TABLE_ANC." i
					GROUP BY 
						i.Q_HEALTHPOINTID, 
						i.Q_USERID,
						i.TODAY
					HAVING count(i._URI)>1";	
	
		// duplicate labtest
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID as patienthpcode, 
							i.Q_USERID ,
							'".PROTOCOL_ANCLABTEST."' as protocol,
							i._CREATOR_URI_USER
					FROM ".TABLE_ANCLABTEST." i
					GROUP BY 
						i.Q_HEALTHPOINTID, 
						i.Q_USERID,
						i.TODAY
					HAVING count(i._URI)>1";
		
	
		// duplicate delivery
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID as patienthpcode, 
							i.Q_USERID ,
							'".PROTOCOL_DELIVERY."' as protocol,
							i._CREATOR_URI_USER
					FROM ".TABLE_DELIVERY." i
					GROUP BY 
						i.Q_HEALTHPOINTID, 
						i.Q_USERID
					HAVING count(i._URI)>1";
	
		// duplicate PNC
		$sql .= " UNION
					SELECT 	i.Q_HEALTHPOINTID as patienthpcode, 
							i.Q_USERID ,
							'".PROTOCOL_PNC."' as protocol,
							i._CREATOR_URI_USER
					FROM ".TABLE_PNC." i
					GROUP BY i.Q_HEALTHPOINTID, 
						i.Q_USERID,
						i.TODAY
					HAVING count(i._URI)>1";
		
		$sql .= ") a ";
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " WHERE a.patienthpcode NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		
		$result = $API->runSql($sql);
		if(!$result){
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		return $report;
	}
	
	function findAgeYoB(){
		global $API;
		$patients = $API->getCurrentPatients();
		$report = array();
		$counter = 0;
		foreach($patients as $p){
			$age = array();
		
			// reg
			if(isset($p->Q_AGE)){
				$age[$p->Q_AGE] = PROTOCOL_REGISTRATION;
			}
			//anc
			if(isset($p->anc)){
				foreach($p->anc as $x){
					if(isset($x->Q_AGE)){
						$age[$x->Q_AGE] = PROTOCOL_ANC;
					}
				}
			}
			//lab
			if(isset($p->anclabtest)){
				foreach($p->anclabtest as $x){
					if(isset($x->Q_AGE)){
						$age[$x->Q_AGE] = PROTOCOL_ANCLABTEST;
					}
				}
			}
			//delivery
			if(isset($p->delivery->Q_AGE)){
				$age[$p->delivery->Q_AGE] = PROTOCOL_DELIVERY;
			}
			//pnc
			if(isset($p->pnc)){
				foreach($p->pnc as $x){
					if(isset($x->Q_AGE)){
						$age[$x->Q_AGE] = PROTOCOL_PNC;
					}
				}
			}
		
			// now do year of birth
			$yob = array();
			// reg
			if(isset($p->Q_YEAROFBIRTH)){
				$yob[$p->Q_YEAROFBIRTH] = PROTOCOL_REGISTRATION;
			}
			// ANC
			if(isset($p->anc)){
				foreach($p->anc as $x){
					if(isset($x->Q_YEAROFBIRTH)){
						$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANC;
					}
				}
			}
			//lab
			if(isset($p->anclabtest)){
				foreach($p->anclabtest as $x){
					if(isset($x->Q_YEAROFBIRTH)){
						$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_ANCLABTEST;
					}
				}
			}
			//delivery
			if(isset($p->delivery->Q_YEAROFBIRTH)){
				$yob[$p->delivery->Q_YEAROFBIRTH] = PROTOCOL_DELIVERY;
			}
			//pnc
			if(isset($p->pnc)){
				foreach($p->pnc as $x){
					if(isset($x->Q_YEAROFBIRTH)){
						$yob[$x->Q_YEAROFBIRTH] = PROTOCOL_PNC;
					}
				}
			}
		
		
			if(count($age)>1 || count($yob) >1 ){
				$report[$counter] = new stdClass;
				$report[$counter]->patid = $p->Q_USERID;
				$report[$counter]->hpcode = $p->patienthpcode;
				$counter++;
			}
		
		}
		
		return $report;
	}
	
	
	function updateCache(){
		global $API;
		
		// update cache unregistered
		$unreg = $this->findUnregistered();
		
		$sql = "SELECT * FROM cache_datacheck WHERE dctype='unreg'";
		$result = $API->runSql($sql);
		
		while($row = mysql_fetch_object($result)){
			$found = false;
			foreach($unreg as $ur){
				if($ur->Q_USERID == $row->patid
				&& $ur->patienthpcode == $row->hpcode
				&& $ur->protocol == $row->protocol){
					$found = true;
				}
			}
			// if not found then remove it
			if(!$found){
				$delsql = sprintf("DELETE FROM cache_datacheck WHERE
										dcid = %d",$row->dcid);
				$result = $API->runSql($delsql);
			}
		}
		
		// add in any new unregistered patients
		foreach ($unreg as $ur){
			$this->addUnregistered($ur);
		}
		
		// update cache duplicates
		$dups = $this->findDuplicates();

		// get current duplicates and check to see if they are still issues or not
		$sql = "SELECT * FROM cache_datacheck WHERE dctype='duplicate'";
		$result = $API->runSql($sql);
		
		while($row = mysql_fetch_object($result)){
			$found = false;
			foreach($dups as $dup){
				if($dup->Q_USERID == $row->patid
					&& $dup->patienthpcode == $row->hpcode
					&& $dup->protocol == $row->protocol){
					$found = true;
				}
			}
			// if not found then remove it
			if(!$found){
				$delsql = sprintf("DELETE FROM cache_datacheck WHERE
								dcid = %d",$row->dcid);
				$result = $API->runSql($delsql);
			}
		}
		
		foreach ($dups as $dup){
			$this->addDuplicate($dup);
		}
		
		
		// update cache age/yob
		$ageyobs = $this->findAgeYoB();
		
		// get current duplicates and check to see if they are still issues or not
		$sql = "SELECT * FROM cache_datacheck WHERE dctype='ageyob'";
		$res = $API->runSql($sql);
		
		while($row = mysql_fetch_object($res)){
			$found = false;
			foreach($ageyobs as $ay){
				if($ay->patid == $row->patid
				&& $ay->hpcode == $row->hpcode){
					$found = true;
				}
			}
			// if not found then remove it
			if(!$found){
				$delsql = sprintf("DELETE FROM cache_datacheck WHERE
										dcid = %d",$row->dcid);
				$result = $API->runSql($delsql);
			}
		}
		
		foreach ($ageyobs as $ay){
			$this->addAgeYoB($ay);
		}
		
		// TODO update cache name
		
		
	}
	
	private function addUnregistered($unreg){
		global $API;
		
		$selsql = sprintf("SELECT * FROM cache_datacheck
											WHERE
												patid = %d
											AND hpcode = %d
											AND protocol = '%s'
											AND dctype ='unreg'",
		$unreg->Q_USERID, $unreg->patienthpcode,$unreg->protocol);
		
		$result = $API->runSql($selsql);
		$total = mysql_num_rows($result);
		
		if($total == 0){
			$sql = sprintf("INSERT INTO cache_datacheck (dcdate, patid,hpcode,protocol,dctype,dcregby) 
									VALUES (now(),%d,%d,'%s','unreg','%s')",
									$unreg->Q_USERID,
									$unreg->patienthpcode,
									$unreg->protocol,
									$unreg->_CREATOR_URI_USER);
			$API->runSql($sql);
		}
		
	}
	
	private function addDuplicate($dup){
		global $API;
		$selsql = sprintf("SELECT * FROM cache_datacheck
									WHERE
										patid = %d
									AND hpcode = %d
									AND protocol = '%s'
									AND dctype ='duplicate'",
		$dup->Q_USERID, $dup->patienthpcode,$dup->protocol);
		
		$result = $API->runSql($selsql);
		$total = mysql_num_rows($result);
		
		if($total == 0){
			$sql = sprintf("INSERT INTO cache_datacheck (dcdate, patid,hpcode,protocol,dctype,dcregby)
										VALUES (now(),%d,%d,'%s','duplicate','%s')",
									$dup->Q_USERID,
									$dup->patienthpcode,
									$dup->protocol,
									$dup->_CREATOR_URI_USER);
			$API->runSql($sql);
		}
	}
	
	private function addAgeYoB($ay){
		global $API;
		$selsql = sprintf("SELECT * FROM cache_datacheck
										WHERE
											patid = %d
										AND hpcode = %d
										AND dctype ='ageyob'",
		$ay->patid, $ay->hpcode);
	
		$result = $API->runSql($selsql);
		$total = mysql_num_rows($result);
	
		if($total == 0){
			$sql = sprintf("INSERT INTO cache_datacheck (dcdate, patid,hpcode,protocol,dctype,dcregby)
											VALUES (now(),%d,%d,'','ageyob','')",
			$ay->patid,
			$ay->hpcode);
			$API->runSql($sql);
		}
	}
	
}
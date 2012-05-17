<?php 

/*
 * KPI Class
 */
class KPI {
	
	private function checkOpts($opts){
		if(array_key_exists('months',$opts)){
			$opts['months'] = max(0,$opts['months']);
		} else if(array_key_exists('startdate',$opts) && array_key_exists('enddate',$opts)) {
			$opts['startdate'] = $opts['startdate'];
			$opts['enddate'] = $opts['enddate'];
		} else {
			array_push($ERROR,"You must specify either months or start/end dates for this function");
			return false;
		}
		
		if(array_key_exists('hpcodes',$opts)){
			$opts['hps'] = $opts['hpcodes'];
		} else {
			$opts['hps'] = $API->getUserHealthPointPermissions(true);
		}
		return $opts;
	}
	
	private function convertPercent($summary){
		// change into a percentage rather than absolute values
		foreach($summary as $k=>$v){
			$total = $v->defaulters + $v->nondefaulters;
			if ($total > 0){
				$pc_default = round(($v->defaulters * 100)/$total);
				$pc_nondefault = round(($v->nondefaulters * 100)/$total);
				$summary[$k]->defaulters = $pc_default;
				$summary[$k]->nondefaulters = $pc_nondefault;
			}
		}
		return $summary;
	}
	
	function getANC1Defaulters($opts=array()){
		global $ERROR,$API,$CONFIG;
		
		$opts = $this->checkOpts($opts);
		if(!$opts){
			return;
		}
		
		// get all the submitted ANC1 protocols between the dates or months specified
		$sql = sprintf("SELECT 	p._URI,
							p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							p.Q_LMP, 
							p._CREATION_DATE as createdate, 
							DATE_ADD(p.Q_LMP, INTERVAL %d DAY) AS ANC1DUEBY ,
							hp.hpname as healthpoint
					FROM %s p 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid",$CONFIG->props['anc1.duebyend'],TABLE_ANC);
		if(array_key_exists('months',$opts)){
			$sql .= " WHERE p._CREATION_DATE > date_format(curdate() - interval ".$opts['months']." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$opts['startdate']);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$opts['enddate']);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p._CREATION_DATE ASC",$opts['hps']);
		
		// if createdate > ANC1DUEBY then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = $API->runSql($sql);
	
		$summary = array();
		// if months is set we need to divide up into months
		if(array_key_exists('months',$opts)){
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$opts['months'].'M'));
				
			for ($i=0; $i<$opts['months']+1 ;$i++){
				$summary[$date->format('M-Y')] = new stdClass;
				$summary[$date->format('M-Y')]->defaulters = 0;
				$summary[$date->format('M-Y')]->nondefaulters = 0;
				$summary[$date->format('M-Y')]->target = $CONFIG->props['target.anc1'];
				$date->add(new DateInterval('P1M'));
			}
				
			while($row = mysql_fetch_array($results)){
				$date = new DateTime($row['createdate']);
				$arrayIndex = $date->format('M-Y');
					
				if ($row['createdate'] > $row['ANC1DUEBY'] ){
					$summary[$arrayIndex]->defaulters++;
				} else {
					$summary[$arrayIndex]->nondefaulters++;
				}
			}
		} else {
			$summary[0] = new stdClass();
			$summary[0]->defaulters = 0;
			$summary[0]->nondefaulters = 0;
			$summary[0]->target = $CONFIG->props['target.anc1'];
			// otherwise we're only interested in the total over the dates given
			while($row = mysql_fetch_array($results)){
				if ($row['createdate'] > $row['ANC1DUEBY'] ){
					$summary[0]->defaulters++;
				} else {
					$summary[0]->nondefaulters++;
				}
			}
		}
	
		return $this->convertPercent($summary);
	}

	
	function getANC2Defaulters($opts=array()){
		global $API,$CONFIG;
		$opts = $this->checkOpts($opts);
		if(!$opts){
			return;
		}
	
		// all those who had an ANC follow up visit
		$sql = "SELECT 	p._URI,
							p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							p.Q_LMP, 
							p._CREATION_DATE as createdate,  
							DATE_ADD(p.Q_LMP, INTERVAL ".$CONFIG->props['anc2.duebystart']." DAY) AS ANC2_DUE_BY_START,
							DATE_ADD(p.Q_LMP, INTERVAL ".$CONFIG->props['anc2.duebyend']." DAY) AS ANC2_DUE_BY_END
					FROM ".TABLE_ANC." p";
		if(array_key_exists('months',$opts)){
			$sql .= " WHERE p._CREATION_DATE > date_format(curdate() - interval ".$opts['months']." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$opts['startdate']);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$opts['enddate']);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p._CREATION_DATE ASC",$opts['hps']);
	
		// if createdate not between $CONFIG->props['anc2.duebystart'] and $CONFIG->props['anc2.duebyend'] then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = $API->runSql($sql);
		$summary = array();
		// if months is set we need to divide up into months
		if(array_key_exists('months',$opts)){
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$opts['months'].'M'));
	
			for ($i=0; $i<$opts['months']+1 ;$i++){
				$summary[$date->format('M-Y')] = new stdClass;
				$summary[$date->format('M-Y')]->defaulters = 0;
				$summary[$date->format('M-Y')]->nondefaulters = 0;
				$summary[$date->format('M-Y')]->target = $CONFIG->props['target.anc2'];
				$date->add(new DateInterval('P1M'));
			}
	
			while($row = mysql_fetch_array($results)){
				$date = new DateTime($row['createdate']);
				$arrayIndex = $date->format('M-Y');
					
				if ($row['createdate'] > $row['ANC2_DUE_BY_START'] && $row['createdate'] < $row['ANC2_DUE_BY_END']){
					$summary[$arrayIndex]->nondefaulters++;
				} else {
					$summary[$arrayIndex]->defaulters++;
				}
			}
		} else {
			$summary[0] = new stdClass();
			$summary[0]->defaulters = 0;
			$summary[0]->nondefaulters = 0;
			$summary[0]->target = $CONFIG->props['target.anc2'];
			// otherwise we're only interested in the total over the dates given
			while($row = mysql_fetch_array($results)){
				if ($row['createdate'] > $row['ANC2_DUE_BY_START'] && $row['createdate'] < $row['ANC2_DUE_BY_END']){
					$summary[0]->nondefaulters++;
				} else {
					$summary[0]->defaulters++;
				}
			}
		}

		return $this->convertPercent($summary);
	}
	
	
	
	function getPNC1Defaulters($opts=array()){
		global $ERROR,$API,$CONFIG;
		
		$opts = $this->checkOpts($opts);
		if(!$opts){
			return;
		}
		
		/*
		 * Get the earliest PNC for each person who has had one entered
		 */
		$sql = sprintf("SELECT p._URI,
							p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							p.Q_DELIVERYDATE, 
							p._CREATION_DATE as createdate,
							DATE_ADD(p.Q_DELIVERYDATE, INTERVAL %d DAY) as startduedate,
							DATE_ADD(p.Q_DELIVERYDATE, INTERVAL %d DAY) as endduedate
						FROM %s p
						INNER JOIN (SELECT min(_CREATION_DATE) as createdate,Q_HEALTHPOINTID,Q_USERID FROM %s 
							GROUP BY Q_HEALTHPOINTID,Q_USERID) pnc1 
							ON pnc1.createdate = p._CREATION_DATE 
							AND pnc1.Q_USERID = p.Q_USERID 
							AND pnc1.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID",$CONFIG->props['pnc1.duebystart'],$CONFIG->props['pnc1.duebyend'],TABLE_PNC,TABLE_PNC);
		if(array_key_exists('months',$opts)){
			$sql .= " WHERE p._CREATION_DATE > date_format(curdate() - interval ".$opts['months']." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$opts['startdate']);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$opts['enddate']);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p._CREATION_DATE ASC",$opts['hps']);
		
		$results = $API->runSql($sql);
		$summary = array();
		if(array_key_exists('months',$opts)){
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$opts['months'].'M'));
		
			for ($i=0; $i<$opts['months']+1 ;$i++){
				$summary[$date->format('M-Y')] = new stdClass;
				$summary[$date->format('M-Y')]->defaulters = 0;
				$summary[$date->format('M-Y')]->nondefaulters = 0;
				$date->add(new DateInterval('P1M'));
			}
		
			while($row = mysql_fetch_array($results)){
				$date = new DateTime($row['createdate']);
				$arrayIndex = $date->format('M-Y');
					
				if ($row['createdate'] > $row['startduedate'] && $row['createdate'] < $row['endduedate']){
					$summary[$arrayIndex]->nondefaulters++;
				} else {
					$summary[$arrayIndex]->defaulters++;
				}
			}
		} else {
			$summary[0] = new stdClass();
			$summary[0]->defaulters = 0;
			$summary[0]->nondefaulters = 0;
			// otherwise we're only interested in the total over the dates given
			while($row = mysql_fetch_array($results)){
				if ($row['createdate'] > $row['startduedate'] && $row['createdate'] < $row['endduedate']){
					$summary[0]->nondefaulters++;
				} else {
					$summary[0]->defaulters++;
				}
			}
		}
		
		// TODO check against the deliveries - anyone who has had delivery but not a within the pnc1.startdatedue will be a defaulter
		
		return $this->convertPercent($summary);
	}
	
	function averageANCVisits($opts){
		global $API;
		if(!array_key_exists('hpcodes',$opts)){
			$opts['hpcodes'] = $API->getUserHealthPointPermissions(true);
		}
		
		$sql = "SELECT AVG(COALESCE(a.pcount,0)) as ancavg FROM patientcurrent pc 
				LEFT OUTER JOIN (SELECT count(*) as pcount, hpcode, userid 
						FROM cache_visit cv 
						WHERE
						(protocol = 'protocol.anc')
						GROUP BY hpcode, userid) a ON a.hpcode = pc.hpcode AND a.userid = pc.patid
				WHERE pc.pcurrent = false";
		$sql .= sprintf(" AND pc.hpcode IN (%s)",$opts['hpcodes']);
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND pc.hpcode NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$results = $API->runSql($sql);
		while($row = mysql_fetch_object($results)){
			return $row->ancavg; 
		}
		return 0;
	}
	
	function averagePNCVisits($opts){
		global $API;
		if(!array_key_exists('hpcodes',$opts)){
			$opts['hpcodes'] = $API->getUserHealthPointPermissions(true);
		}
	
		$sql = "SELECT AVG(COALESCE(a.pcount,0)) as pncavg FROM patientcurrent pc
					LEFT OUTER JOIN (SELECT count(*) as pcount, hpcode, userid 
							FROM cache_visit cv 
							WHERE
							(protocol = 'protocol.pnc')
							GROUP BY hpcode, userid) a ON a.hpcode = pc.hpcode AND a.userid = pc.patid
					WHERE pc.pcurrent = false";
		$sql .= sprintf(" AND pc.hpcode IN (%s)",$opts['hpcodes']);
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND pc.hpcode NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$results = $API->runSql($sql);
		while($row = mysql_fetch_object($results)){
			return $row->pncavg;
		}
		return 0;
	}
	
}
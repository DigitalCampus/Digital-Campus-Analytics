<?php 

/*
 * KPI Class
 */
class KPI {
	
	
	function getANC1Defaulters($opts=array()){
		global $ERROR,$API;
		
		if(array_key_exists('months',$opts)){
			$months = max(0,$opts['months']);
		} else if(array_key_exists('startdate',$opts) && array_key_exists('enddate',$opts)) {
			$startdate = $opts['startdate'];
			$enddate = $opts['enddate'];
		} else {
			array_push($ERROR,"You must specify either months or start/end dates for this function");
			return false;
		}
	
		if(array_key_exists('hps',$opts)){
			$hps = $opts['hps'];
		} else {
			$hps = $API->getUserHealthPointPermissions();
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
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid",ANC1_DUE_BY_END,TABLE_ANCFIRST);
		if(isset($months)){
			$sql .= " WHERE p._CREATION_DATE > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p._CREATION_DATE ASC",$hps);
	
		//echo "<br/>";
		//echo $sql;
		//echo "<br/>";
		// if createdate > ANC1DUEBY then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = $API->runSql($sql);
	
		$summary = array();
		// if months is set we need to divide up into months
		if(isset($months)){
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$months.'M'));
				
			for ($i=0; $i<$months+1 ;$i++){
				$summary[$date->format('M-Y')] = new stdClass;
				$summary[$date->format('M-Y')]->defaulters = 0;
				$summary[$date->format('M-Y')]->nondefaulters = 0;
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
			// otherwise we're only interested in the total over the dates given
			while($row = mysql_fetch_array($results)){
				if ($row['createdate'] > $row['ANC1DUEBY'] ){
					$summary[0]->defaulters++;
				} else {
					$summary[0]->nondefaulters++;
				}
			}
		}
	
		//echo "<pre>";
		//print_r($summary);
		//echo "</pre>";
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
	
	function getANC1DefaultersBestPerformer($opts=array()){
		global $API;
		if(array_key_exists('months',$opts)){
			$months = max(0,$opts['months']);
		} else {
			$months = 6;
		}
		if(array_key_exists('hps',$opts)){
			$hps = $opts['hps'];
		} else {
			$hps = $this->getUserHealthPointPermissions();
		}
	
		// get all the submitted ANC1 protocols from first day of the month 6 months ago
		$sql = "SELECT 	p._URI,
								p.Q_USERID, 
								p.Q_HEALTHPOINTID, 
								p.Q_LMP, 
								p._CREATION_DATE as createdate, 
								DATE_ADD(p.Q_LMP, INTERVAL ".ANC1_DUE_BY_END." DAY) AS ANC1DUEBY ,
								hp.hpname as healthpoint
						FROM ".TABLE_ANCFIRST." p 
						INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
						INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
						WHERE p._CREATION_DATE > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND p.Q_HEALTHPOINTID NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		$sql .= " AND p.Q_HEALTHPOINTID IN (".$hps.")
						ORDER BY p._CREATION_DATE ASC";
	
	
		// if createdate > ANC1DUEBY then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = $API->runSql($sql);
	
		$summary = array();
	
		while($row = mysql_fetch_array($results)){
			$arrayIndex = $row['Q_HEALTHPOINTID'];
			if(!array_key_exists($arrayIndex, $summary)){
				$summary[$arrayIndex] = new stdClass();
				$summary[$arrayIndex]->defaulters = 0;
				$summary[$arrayIndex]->nondefaulters = 0;
			}
			if ($row['createdate'] > $row['ANC1DUEBY'] ){
				$summary[$arrayIndex]->defaulters++;
			} else {
				$summary[$arrayIndex]->nondefaulters++;
			}
		}
		
		// change into a percentage rather than absolute values
		$besthp = 0;
		$previousbest = 0;
		foreach($summary as $k=>$v){
			$total = $v->defaulters + $v->nondefaulters;
			if ($total > 0){
				$pc_default = round(($v->defaulters * 100)/$total);
				$pc_nondefault = round(($v->nondefaulters * 100)/$total);
				$summary[$k]->defaulters = $pc_default;
				$summary[$k]->nondefaulters = $pc_nondefault;
				if($pc_nondefault>$previousbest){
					$previousbest = $pc_nondefault;
					$besthp = $k;
				}
			}
		}
		$opts['hps'] = $besthp;
	
		return $this->getANC1Defaulters($opts);
	}
	
	function getANC2Defaulters($opts=array()){
		global $API;
		if(array_key_exists('months',$opts)){
			$months = max(0,$opts['months']);
		} else if(array_key_exists('startdate',$opts) && array_key_exists('enddate',$opts)) {
			$startdate = $opts['startdate'];
			$enddate = $opts['enddate'];
		} else {
			array_push($ERROR,"You must specify either months or start/end dates for this function");
			return false;
		}
	
		if(array_key_exists('hps',$opts)){
			$hps = $opts['hps'];
		} else {
			$hps = $API->getUserHealthPointPermissions();
		}
	
		// all those who had an ANC follow up visit
		$sql = "SELECT 	p._URI,
							p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							p.Q_LMP, 
							p._CREATION_DATE as createdate,  
							DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_START." DAY) AS ANC2_DUE_BY_START,
							DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) AS ANC2_DUE_BY_END
					FROM ".TABLE_ANCFOLLOW." p";
		if(isset($months)){
			$sql .= " WHERE p._CREATION_DATE > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p._CREATION_DATE ASC",$hps);
	
		// if createdate not between ANC2_DUE_BY_START and ANC2_DUE_BY_END then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = $API->runSql($sql);
		$summary = array();
		// if months is set we need to divide up into months
		if(isset($months)){
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$months.'M'));
	
			for ($i=0; $i<$months+1 ;$i++){
				$summary[$date->format('M-Y')] = new stdClass;
				$summary[$date->format('M-Y')]->defaulters = 0;
				$summary[$date->format('M-Y')]->nondefaulters = 0;
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
			// otherwise we're only interested in the total over the dates given
			while($row = mysql_fetch_array($results)){
				if ($row['createdate'] > $row['ANC2_DUE_BY_START'] && $row['createdate'] < $row['ANC2_DUE_BY_END']){
					$summary[0]->nondefaulters++;
				} else {
					$summary[0]->defaulters++;
				}
			}
		}
		// all those who had an ANC1 but not a second visit and didn't have termination protocol entered before ANC was due
		$sql = "SELECT 	p._URI,
							p.Q_USERID, 
							p.Q_HEALTHPOINTID, 
							p.Q_LMP, 
							DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) AS ANC2_DUE_BY_END 
					FROM ".TABLE_ANCFIRST." p
					LEFT OUTER JOIN ".TABLE_ANCFOLLOW." f ON f.Q_USERID = p.Q_USERID AND f.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID
					WHERE f.Q_USERID IS NULL";
		if(isset($months)){
			$sql .= " AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')
							AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) < curdate()";
		} else {
			$sql .= sprintf(" AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) > '%s'",$startdate);
			$sql .= sprintf(" AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) <= '%s'",$enddate);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= " AND p.Q_HEALTHPOINTID NOT IN (".$API->getIgnoredHealthPoints().")";
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s)",$hps);
		$sql .= " ORDER BY DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) ASC";
		// TODO add constraint about terminations
	
		// all those returned by above query are defaulters - as have now Follow up
		$results = $API->runSql($sql);
		if(isset($months)){
			while($row = mysql_fetch_array($results)){
				$date = new DateTime($row['ANC2_DUE_BY_END']);
				$arrayIndex = $date->format('M-Y');
				$summary[$arrayIndex]->defaulters++;
			}
		} else {
				
		}
	
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
	
	
	function getTT1Defaulters($opts=array()){
		global $ERROR,$API;
		
		if(array_key_exists('months',$opts)){
			$months = max(0,$opts['months']);
		} else if(array_key_exists('startdate',$opts) && array_key_exists('enddate',$opts)) {
			$startdate = $opts['startdate'];
			$enddate = $opts['enddate'];
		} else {
			array_push($ERROR,"You must specify either months or start/end dates for this function");
			return false;
		}
		
		if(array_key_exists('hps',$opts)){
			$hps = $opts['hps'];
		} else {
			$hps = $API->getUserHealthPointPermissions();
		}
		
		$sql = "SELECT 	p._URI,
						p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						p._CREATION_DATE as createdate,
						p.Q_TETANUS
				FROM ".TABLE_ANCFIRST." p ";
		if(isset($months)){
			$sql .= " WHERE p._CREATION_DATE > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		if($API->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$API->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p._CREATION_DATE ASC",$hps);
		
		//echo $sql;
		$results = $API->runSql($sql);
		$summary = array();
		
		if(isset($months)){
			$date = new DateTime();
			$date->sub(new DateInterval('P'.$months.'M'));
			
			for ($i=0; $i<7 ;$i++){
				$summary[$date->format('M-Y')] = new stdClass;
				$summary[$date->format('M-Y')]->defaulters = 0;
				$summary[$date->format('M-Y')]->nondefaulters = 0;
				$date->add(new DateInterval('P1M'));
			}
			
			while($row = mysql_fetch_array($results)){
				$date = new DateTime($row['createdate']);
				$arrayIndex = $date->format('M-Y');
			
				if ($row['Q_TETANUS'] == 'none' ){
					$summary[$arrayIndex]->defaulters++;
				} else {
					$summary[$arrayIndex]->nondefaulters++;
				}
			}
		} else {
			$summary[0] = new stdClass();
			$summary[0]->defaulters = 0;
			$summary[0]->nondefaulters = 0;
			// otherwise we're only interested in the total over the dates given
			while($row = mysql_fetch_array($results)){
				if ($row['Q_TETANUS'] == 'none' ){
					$summary[0]->defaulters++;
				} else {
					$summary[0]->nondefaulters++;
				}
			}
		}
		
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
}
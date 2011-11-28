<?php

/*
 * Comparison functions
 */
function cmpPatients($a,$b){
	if ($a == $b) {
        return 0;
    }
    return ($a->risks['count'] < $b->risks['count']) ? 1 : -1;
}

/*
 * API Class
 */
class API {
	
	private $DB = false;
	   
	/*
	 * Constructor
	 */
	function api(){
	    global $CONFIG;
	    if($this->DB){
	        return $this->DB;
	    }
	    $this->DB = mysql_connect( $CONFIG->dbhost, $CONFIG->dbuser, $CONFIG->dbpass) or die('Could not connect to server.' );
	    mysql_select_db($CONFIG->dbname, $this->DB) or die('Could not select database.');
	    mysql_set_charset('utf8',$this->DB); 
	    return $this->DB;
	}
	    
	function runSql($sql){
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	    return $result;
	}
	
	function cleanUpDB(){
	    if( $this->DB != false ){
	        mysql_close($this->DB);
	    }
	    $this->DB = false;
	} 
	
	function getUser($user){
		$sql = "SELECT * FROM user WHERE username ='".$user->username."' LIMIT 0,1";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_array($result)){
	  		$user->userid = $row['userid'];
			$user->username = $row['username'];
			$user->firstname = $row['firstname'];
			$user->lastname =  $row['lastname'];
		}
		return $user;
	} 
	
	function getUsers(){
		$sql = "SELECT * FROM user u
				INNER JOIN healthpoint hp ON hp.hpid = u.hpid
				ORDER BY u.firstname";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$users = array();
		while($row = mysql_fetch_object($result)){
			array_push($users,$row);
		}
		return $users;
	}
	
	function getUserProperties(&$user){
		$sql = "SELECT * FROM userprops WHERE userid=".$user->userid;
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  		$user->props[$row['propname']] = $row['propvalue'];
		}
	} 
	
	function setUserProperty($userid,$name,$value){
		// first check to see if it exists already
		$sql = sprintf("SELECT * FROM userprops WHERE userid= %d AND propname='%s'",$userid,$name);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  		$updateSql = sprintf("UPDATE userprops SET propvalue='%s' WHERE userid= %d AND propname='%s'",$value,$userid,$name);
	  		$result = _mysql_query($updateSql,$this->DB);
	  		if (!$result){
	  			writeToLog('error','database',$sql);
	  		}
	  		return;
		}
		
		$insertSql = sprintf("INSERT INTO userprops (propvalue, userid,propname) VALUES ('%s',%d,'%s')",$value,$userid,$name);
	  	$result = _mysql_query($insertSql,$this->DB);
	  	if (!$result){
	  		writeToLog('error','database',$sql);
	  	}
	}
	
	function userValidatePassword($username,$password){
		global $USER;
		$sql = sprintf("SELECT userid FROM user WHERE username='%s' AND password=md5('%s')",$username,$password);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return false;
	    }
	  	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  		return true;
		}
		return false;
	}
	
	function userChangePassword($newpass){
		global $USER;
		$sql = sprintf("UPDATE user SET password = md5('%s') WHERE userid=%d",$newpass,$USER->userid);
		$result = _mysql_query($sql,$this->DB);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 
	 */
	function writeLog($loglevel,$userid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries){
		$sql = sprintf("INSERT INTO log (loglevel,userid,logtype,logmsg,logip,logpagephptime,logpagemysqltime,logpagequeries) VALUES ('%s',%d,'%s','%s','%s',%f,%f,%d)", $loglevel,$userid,$logtype,mysql_real_escape_string($logmsg),$ip,$logpagephptime,$logpagemysqltime,$logpagequeries);
		_mysql_query($sql,$this->DB);
	}
	
	// return list of Health posts
	function getHealthPoints(){
		$sql = "SELECT * FROM healthpoint ORDER BY hpname ASC;";
		$healthposts = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_object($result)){
		   	$healthposts[$row->hpcode] = $row;
		}
	    return $healthposts;
	}
	
	
	function updatePatients(){
		//add any new patients to the patientcurrent table
		$sql = "INSERT INTO patientcurrent (hpcode,patid) 
				SELECT DISTINCT i.Q_HEALTHPOINTID, i.Q_USERID FROM ".REGISTRATION." i
				LEFT OUTER JOIN patientcurrent pc ON i.Q_HEALTHPOINTID = pc.hpcode AND i.Q_USERID = pc.patid
				WHERE pc.pcid is NULL";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	    
		//archive old patients
		// TODO update for real PNC protocol
		/*$sql = "UPDATE patientcurrent pc, 
					(SELECT hpcode, patid FROM pnc
						WHERE datestamp <= DATE_ADD(NOW(), INTERVAL -70 DAY)) pnc1
				SET pc.pcurrent = 0
				WHERE pc.hpcode = pnc1.hpcode
				AND pc.patid = pnc1.patid
				AND pc.pcurrent = 1";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }*/
	}
	
	function getCurrentPatients(){
		$sql = "SELECT 	hp.hpcode,
						Q_USERID as patientid
				FROM ".REGISTRATION." r
				INNER JOIN patientcurrent pc ON pc.hpcode = r.Q_HEALTHPOINTID AND pc.patid = r.Q_USERID
				INNER JOIN healthpoint hp ON hp.hpcode = r.Q_HEALTHPOINTID
				WHERE pc.pcurrent = 1";	
		$patients = array();	
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($pat = mysql_fetch_object($result)){
	  		$patient = $this->getPatient(array('hpcode'=>$pat->hpcode,'patid'=>$pat->patientid));
	  		array_push($patients,$patient);
	  	}
	  	usort($patients,"cmpPatients");
	  	return $patients;
	}
	
	
	function getPatient($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						Q_AGE,
						Q_CONSENT,
						Q_DAYOFBIRTH,
						Q_EDUCATION,
						Q_GPSDATA_ACC,
						Q_GPSDATA_ALT,
						Q_GPSDATA_LAT,
						Q_GPSDATA_LNG,
						Q_HEALTHPOINTID,
						Q_HOMEFUELSOURCE,
						Q_HOMESANITATION,
						Q_HOMEWATERSOURCE,
						Q_HOUSEELECTRICITY,
						Q_HOUSEROOF,
						Q_HOUSEWALL,
						Q_IDCARD,
						Q_LOCATION,
						Q_MARITALSTATUS,
						Q_MOBILENUMBER,
						Q_MOBILEPHONE,
						Q_MONTHOFBIRTH,
						Q_OCCUPATION,
						Q_SEX,
						Q_USERFATHERSNAME,
						Q_USERGRANDFATHERSNAME,
						Q_USERID,
						Q_USERNAME,
						Q_YEAROFBIRTH,
						_URI,
						TODAY AS CREATEDON
				FROM ".REGISTRATION." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	$pat = mysql_fetch_object($result);
	  	if($pat == null){
	  		$pat->regcomplete = false;
	  	} else {
	  		$pat->regcomplete = true;
	  		$pat->Q_HOMEAPPLIANCES = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".REG_HOMEAPPLIANCES." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = _mysql_query($appsql,$this->DB);
	  		if (!$appresult){
	  			writeToLog('error','database',$appsql);
	  			return;
	  		}
	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_HOMEAPPLIANCES,$app->VALUE);
	  		}
	  	}
  		
		$pat->ancfirst = $this->getPatientANCFirst($opts);
		$pat->ancfollow = $this->getPatientANCFollow($opts);
		 
		$pat->anctransfer = $this->getPatientANCTransfer($opts);
		$pat->anclabtest= $this->getPatientANCLabTest($opts);
		
		$pat->risk = $this->riskAssessment($pat);
		return $pat;		
	}
	
	function riskAssessment($p){
		$risk = new stdClass;
		$risk->risks = array();
		$risk->count = 0;
		/*
		 * Age
		 */ 
		$risk->risks['Q_AGE_UNDER18'] = false;
		// from Registration 
		if(isset($p->Q_AGE) && $p->Q_AGE < 18 ){
			$risk->risks['Q_AGE_UNDER18'] = true;
		} 
		// from ANC first
		if(isset($p->ancfirst->Q_AGE) && $p->ancfirst->Q_AGE < 18){
			$risk->risks['Q_AGE_UNDER18'] = true;
		}
		//from ANC Follow
		foreach($p->ancfollow as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE < 18){
				$risk->risks['Q_AGE_UNDER18'] = true;
			}
		}
		//from ANC Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE < 18){
				$risk->risks['Q_AGE_UNDER18'] = true;
			}
		}
		
		$risk->risks['Q_AGE_OVER34'] = false;
		// from Registration
		if(isset($p->Q_AGE) && $p->Q_AGE > 34){
			$risk->risks['Q_AGE_OVER34'] = true;
		}
		// from ANC first
		if(isset($p->ancfirst->Q_AGE) && $p->ancfirst->Q_AGE > 34){
			$risk->risks['Q_AGE_OVER34'] = true;
		}
		//from ANC Follow
		foreach($p->ancfollow as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE > 34){
				$risk->risks['Q_AGE_OVER34'] = true;
			}
		}
		//from ANC Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE > 34){
				$risk->risks['Q_AGE_OVER34'] = true;
			}
		}
		if($risk->risks['Q_AGE_OVER34'] == true || $risk->risks['Q_AGE_UNDER18'] == true){
			$risk->count++;
		}
		
		/*
		 * Birth Interval
		 */
		$risk->risks['Q_BIRTHINTERVAL'] = false;
		// from ANC First
		if(isset($p->ancfirst->Q_BIRTHINTERVAL) && ($p->ancfirst->Q_BIRTHINTERVAL == 'within1' || $p->ancfirst->Q_BIRTHINTERVAL == 'within2')){
			$risk->risks['Q_BIRTHINTERVAL'] = true;
		}
		// from Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_BIRTHINTERVAL) && ($x->Q_BIRTHINTERVAL == 'within1' || $x->Q_BIRTHINTERVAL == 'within2')){
				$risk->risks['Q_BIRTHINTERVAL'] = true;
			}
		}
		if($risk->risks['Q_BIRTHINTERVAL'] == true){
			$risk->count++;
		}
		
		/*
		 * Birth order/gravida
		 */
		$risk->risks['Q_GRAVIDA'] = false;
		// from ANC First
		if(isset($p->ancfirst->Q_GRAVIDA) && ($p->ancfirst->Q_GRAVIDA > 6)){
			$risk->risks['Q_GRAVIDA'] = true;
		}
		// from Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_GRAVIDA) && ($x->Q_GRAVIDA > 6)){
				$risk->risks['Q_GRAVIDA'] = true;
			}
		}
		if($risk->risks['Q_GRAVIDA'] == true){
			$risk->count++;
		}
		
		/*
		 * eclampsia, bleeding, fatigue
		 */
		$otherrisks = array ('Q_PREECLAMPSIA', 'Q_BLEEDING', 'Q_FATIGUE');
		foreach ($otherrisks AS $r){
			$risk->risks[$r] = false;
			// from ANC First
			if(isset($p->ancfirst->{$r}) && ($p->ancfirst->{$r} == 'yes')){
				$risk->risks[$r] = true;
			}
			// from Transfer
			foreach($p->anctransfer as $x){
				if(isset($x->{$r}) && ($x->{$r} == 'yes')){
					$risk->risks[$r] = true;
				}
			}
			if($risk->risks[$r] == true){
				$risk->count++;
			}
		}
		
		//abdominal pain
		

		
		// work out the risk category
		$risk->category = 'none';
		
		// unavoidable risk
		//if no other risks and first order birth
		if($risk->count == 0){
			// from ANC First
			if(isset($p->ancfirst->Q_GRAVIDA) && ($p->ancfirst->Q_GRAVIDA == 1)){
				$risk->category = 'unavoidable';
			}
			// from Transfer
			foreach($p->anctransfer as $x){
				if(isset($x->Q_GRAVIDA) && ($x->Q_GRAVIDA == 1)){
					$risk->category = 'unavoidable';
				}
			}	
		}
		
		// single
		if ($risk->count == 1){
			$risk->category = 'single';
		}
		// multiple
		if ($risk->count > 1){
			$risk->category = 'multiple';
		}
		
		return $risk;
	}
	
	function getPatientANCFirst($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						_URI,
						_CREATOR_URI_USER,
						Q_ABDOMINALPAIN,
						Q_ABORTION,
						Q_AGE,
						Q_APPOINTMENTDATE,
						Q_BABYWEIGHT,
						Q_BEDNETS,
						Q_BIRTHINTERVAL,
						Q_BLEEDING,
						Q_BLEEDINGPREVPREG,
						Q_CARDIACPULSE,
						Q_CONSENT,
						Q_CSECTION,
						Q_DELIVERYPLACE,
						Q_DELIVERYPLAN,
						Q_DIABETES,
						Q_DIASTOLICBP,
						Q_DRUGS,
						Q_DRUGSDESCRIPTION,
						Q_ECONOMICS,
						Q_EDD,
						Q_EDEMA,
						Q_FAMILYPLAN,
						Q_FATIGUE,
						Q_FETALHEARTRATE24W,
						Q_FETALHEARTRATEAUDIBLE,
						Q_FEVER,
						Q_FISTULA,
						Q_FOLICACID,
						Q_FUNDALHEIGHT,
						Q_GESTATIONALAGE,
						Q_GPSDATA_ACC,
						Q_GPSDATA_ALT,
						Q_GPSDATA_LAT,
						Q_GPSDATA_LNG,
						Q_GRAVIDA,
						Q_HEADACHE,
						Q_HEALTHPOINTID,
						Q_HEIGHT,
						Q_HIV,
						Q_HIVTREATMENT,
						Q_HYPERTENSION,
						Q_IDCARD,
						Q_INFANTDEATH,
						Q_IRONGIVEN,
						Q_IRONTABLETS,
						Q_LIVEBIRTHS,
						Q_LIVINGCHILDREN,
						Q_LMP,
						Q_LOCATION,
						Q_MALARIA,
						Q_MALPOSITION,
						Q_MEBENDAZOL,
						Q_NEWBORNDEATH,
						Q_OTHERHEALTHISSUES,
						Q_OTHERHEALTHPROBLEMS,
						Q_OTHERPREVPREG,
						Q_PALLORANEMIA,
						Q_PARITY,
						Q_PREECLAMPSIA,
						Q_PREPOSTTERM,
						Q_PRESENTATION,
						Q_PROLONGEDLABOR,
						Q_SOCIALSUPPORT,
						Q_STILLBIRTHS,
						Q_SYSTOLICBP,
						Q_TETANUS,
						Q_TRANSPORTATION,
						Q_TT1,
						Q_TT2,
						Q_TUBERCULOSIS,
						Q_TWIN,
						Q_USERID,
						Q_VACUUMDELIVERY,
						Q_WEIGHT,
						Q_YEAROFBIRTH,
						Q_YOUNGESTCHILD,
						TODAY AS CREATEDON
				FROM ".ANCFIRST." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($pat = mysql_fetch_object($result)){
	  		$pat->Q_FPMETHOD = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".ANCFIRST_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = _mysql_query($appsql,$this->DB);
		  	if (!$appresult){
		    	writeToLog('error','database',$appsql);
		    	return;
		    }
	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_FPMETHOD,$app->VALUE);
	  		}
	  		
	  		$pat->Q_WHOATTENDED = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".ANCFIRST_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = _mysql_query($appsql,$this->DB);
		  	if (!$appresult){
		    	writeToLog('error','database',$appsql);
		    	return;
		    }
	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_WHOATTENDED,$app->VALUE);
	  		}
	  		
	  		return $pat;
	  	}
		
	}
	
	function getPatientANCFollow($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						_URI,
						Q_ABDOMINALPAIN,
						Q_AGE,
						Q_APPOINTMENTDATE,
						Q_BEDNETS,
						Q_BLEEDING,
						Q_CARDIACPULSE,
						Q_CONSENT,
						Q_DELIVERYPLAN,
						Q_DIABETES,
						Q_DIASTOLICBP,
						Q_DRUGS,
						Q_DRUGSDESCRIPTION,
						Q_ECONOMICS,
						Q_EDD,
						Q_EDEMA,
						Q_FATIGUE,
						Q_FETALHEARTRATE24W,
						Q_FETALHEARTRATEAUDIBLE,
						Q_FEVER,
						Q_FOLICACID,
						Q_FOLLOWUPNO,
						Q_FUNDALHEIGHT,
						Q_GESTATIONALAGE,
						Q_GPSDATA_ACC,
						Q_GPSDATA_ALT,
						Q_GPSDATA_LAT,
						Q_GPSDATA_LNG,
						Q_HEADACHE,
						Q_HEALTHPOINTID,
						Q_HEIGHT,
						Q_HIV,
						Q_HIVTREATMENT,
						Q_HYPERTENSION,
						Q_IDCARD,
						Q_IODIZEDSALTS,
						Q_IRONGIVEN,
						Q_IRONTABLETS,
						Q_LMP,
						Q_LOCATION,
						Q_MALARIA,
						Q_MEBENDAZOL,
						Q_OTHERHEALTHISSUES,
						Q_OTHERHEALTHPROBLEMS,
						Q_PALLORANEMIA,
						Q_PRESENTATION,
						Q_SOCIALSUPPORT,
						Q_SYSTOLICBP,
						Q_TETANUS,
						Q_TRANSPORTATION,
						Q_TT1,
						Q_TT2,
						Q_TUBERCULOSIS,
						Q_USERID,
						Q_WEIGHT,
						Q_YEAROFBIRTH,
						TODAY AS CREATEDON
				FROM ".ANCFOLLOW." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'
				ORDER BY TODAY ASC";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	    $protocols = array();
	  	while($pat = mysql_fetch_object($result)){
	  		$protocols[$pat->Q_FOLLOWUPNO-1] = $pat;
	  	}
		
	  	return $protocols;
	}
	
	function getPatientANCTransfer($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						_URI,
						Q_ABORTION,
						Q_AGE,
						Q_BABYWEIGHT,
						Q_BIRTHINTERVAL,
						Q_BLEEDINGPREVPREG,
						Q_CONSENT,
						Q_CSECTION,
						Q_DELIVERYPLACE,
						Q_FAMILYPLAN,
						Q_FISTULA,
						Q_GPSDATA_ACC,
						Q_GPSDATA_ALT,
						Q_GPSDATA_LAT,
						Q_GPSDATA_LNG,
						Q_GRAVIDA,
						Q_HEALTHPOINTID,
						Q_IDCARD,
						Q_INFANTDEATH,
						Q_LIVEBIRTHS,
						Q_LIVINGCHILDREN,
						Q_LOCATION,
						Q_MALPOSITION,
						Q_NEWBORNDEATH,
						Q_PARITY,
						Q_PREECLAMPSIA,
						Q_PREPOSTTERM,
						Q_PROLONGEDLABOR,
						Q_STILLBIRTHS,
						Q_TWIN,
						Q_USERID,
						Q_VACUUMDELIVERY,
						Q_YEAROFBIRTH,
						Q_YOUNGESTCHILD,
						TODAY AS CREATEDON
				FROM ".ANCTRANSFER." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'
				ORDER BY TODAY ASC";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	    $protocols = array();
	    $count=0;
	  	while($pat = mysql_fetch_object($result)){
	  		
	  		$pat->Q_FPMETHOD = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".ANCTRANSFER_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = _mysql_query($appsql,$this->DB);
		  	if (!$appresult){
		    	writeToLog('error','database',$appsql);
		    	return;
		    }
	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_FPMETHOD,$app->VALUE);
	  		}
	  		
	  		$pat->Q_WHOATTENDED = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".ANCTRANSFER_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = _mysql_query($appsql,$this->DB);
		  	if (!$appresult){
		    	writeToLog('error','database',$appsql);
		    	return;
		    }
	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_WHOATTENDED,$app->VALUE);
	  		}
	  		$protocols[$count] = $pat;
	  		$count++;
	  	}
		
	  	return $protocols;
	}
	
	function getPatientANCLabTest($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						_URI,
						Q_AGE,
						Q_BLOODFILM,
						Q_BLOODGROUP,
						Q_DATEOFTEST,
						Q_HEALTHCENTER,
						Q_HEALTHPOINTID,
						Q_HEMATOCRITLEVEL,
						Q_HEMOGLOBINLEVEL,
						Q_PREGNANCYTEST,
						Q_RHFACTOR,
						Q_STOOLEXAMINATION,
						Q_SYPHILIS,
						Q_URINEANALYSIS,
						Q_URINEGLUCOSE,
						Q_URINEPROTEIN,
						Q_USERID,
						Q_YEAROFBIRTH,
						TODAY AS CREATEDON
				FROM ".ANCLABTEST." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'
				ORDER BY TODAY ASC";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	    $protocols = array();
	    $count=0;
	  	while($pat = mysql_fetch_object($result)){
	  		$protocols[$count] = $pat;
	  		/*
	  		 * TODO add fpmethod and whoattended
	  		 */
	  		$count++;
	  	}
		
	  	return $protocols;
	}
	
	function getPatientDelivery($opts=array()){
		$sql = "
		
					Q_ADVICEDANGERSIGNS,
					Q_ADVICEFEEDING,
					Q_AGE,
					Q_ANEMIA,
					Q_APPOINTMENTDATE,
					Q_ARVMOM,
					Q_BREASTFEEDING,
					Q_CARDIACPULSE,
					Q_CONDITION,
					Q_CONSENT,
					Q_CSECTION,
					Q_DELIVERYDATE,
					Q_DELIVERYOUTCOME,
					Q_DELIVERYSITE,
					Q_DELIVERYTIME,
					Q_DIASTOLICBP,
					Q_ECLAMPSIA,
					Q_EPISIOTOMY,
					Q_GENITALIAEXTERNAL,
					Q_GESTATIONALAGE,
					Q_GPSDATA_ACC,
					Q_GPSDATA_ALT,
					Q_GPSDATA_LAT,
					Q_GPSDATA_LNG,
					Q_HEALTHPOINTID,
					Q_IDCARD,
					Q_IRONSUPPL,
					Q_LABORONSETTIME,
					Q_LOCATION,
					Q_MATERNALDEATH,
					Q_MECONIUM,
					Q_MISOPROSTOL,
					Q_MISOPROSTOLTABLETS,
					Q_MISOPROSTOLTIMING,
					Q_OXYTOCIN,
					Q_PLACENTA,
					Q_PPH,
					Q_PRESENTATION,
					Q_PROM,
					Q_REFERRALREASON,
					Q_SYSTOLICBP,
					Q_TEMPERATURE,
					Q_USERID,
					Q_VACUUMFORCEPS,
					Q_VAGINALDELIVERY,
					Q_VITASUPPL,
					Q_YEAROFBIRTH,
		";
	}
	
	function getPatientDeliveryBaby(){
		$sql = "
						Q_APGAR1MIN,
						Q_APGAR5MIN,
						Q_ARVNEWBORNHIV,
						Q_BABYBREATHING,
						Q_BABYMOMBOND,
						Q_BCGIMMUNO,
						Q_LIVEBIRTH,
						Q_NEWBORNHEAD,
						Q_NEWBORNHIV,
						Q_NEWBORNRESUSCITATION,
						Q_NEWBORNSEX,
						Q_NEWBORNWEIGHT,
						Q_OTHERCOMMENTS,
						Q_POLIO0IMMUNO,
						Q_TTCEYEOINTMENT,
						Q_VITAMINK,
			
		";
		
	}
	function getProtocolsSubmitted($opts=array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
		if(array_key_exists('limit',$opts)){
			$limit = max(0,$opts['limit']);
		} else {
			$limit = DEFAULT_LIMIT;
		}
		if(array_key_exists('start',$opts)){
			$start = max($opts['start'],0);
		} else {
			$start = DEFAULT_START;
		}
		
		$sql = "SELECT * FROM (";
		// registration
		$sql .= "SELECT 
					p._CREATION_DATE as datestamp,
					p.Q_USERID,
					CONCAT(p.Q_USERNAME,' ',p.Q_USERFATHERSNAME,' ',p.Q_USERGRANDFATHERSNAME) as patientname,
					p.Q_HEALTHPOINTID,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".getString('protocol.registration')."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					p._CREATOR_URI_USER,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".REGISTRATION." p 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		
		// anc first
		$sql .= " UNION
				SELECT 
					p._CREATION_DATE as datestamp,
					p.Q_USERID,
					CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
					p.Q_HEALTHPOINTID,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".getString('protocol.ancfirst')."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					p._CREATOR_URI_USER,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".ANCFIRST." p 
				LEFT OUTER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		// follow ups
		$sql .= " UNION
				SELECT 
					p._CREATION_DATE as datestamp,
					p.Q_USERID,
					CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
					p.Q_HEALTHPOINTID,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					CONCAT('".getString('protocol.ancfollow')."',' ',p.Q_FOLLOWUPNO) as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					p._CREATOR_URI_USER,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".ANCFOLLOW." p 
				LEFT OUTER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		
		// lab test
		$sql .= " UNION
				SELECT 
					p._CREATION_DATE as datestamp,
					p.Q_USERID,
					CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
					p.Q_HEALTHPOINTID,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".getString('protocol.anclabtest')."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					p._CREATOR_URI_USER,
					'' AS Q_GPSDATA_LAT,
					'' AS Q_GPSDATA_LNG,
					'' AS Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".ANCLABTEST." p 
				LEFT OUTER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		
		// transfer
		$sql .= " UNION
				SELECT
					p._CREATION_DATE as datestamp,
					p.Q_USERID,
					CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
					p.Q_HEALTHPOINTID,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".getString('protocol.anctransfer')."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					p._CREATOR_URI_USER,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".ANCTRANSFER." p 
				LEFT OUTER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		
		/*delivery
		$sql .= " UNION
					SELECT
						p._CREATION_DATE as datestamp,
						p.Q_USERID,
						CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
						p.Q_HEALTHPOINTID,
						php.hpname as patientlocation,
						hp.hpname as protocollocation,
						'".getString('protocol.delivery')."' as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						p._CREATOR_URI_USER,
						p.Q_GPSDATA_LAT,
						p.Q_GPSDATA_LNG,
						p.Q_LOCATION,
						hp.locationlat,
						hp.locationlng
					FROM ".DELIVERY." p 
					LEFT OUTER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		*/
		$sql .= ") a ORDER BY datestamp DESC";
		//query to get the total no of records
		$countsql = "SELECT COUNT(*) AS norecords FROM (".$sql.") a;";
		
		$countres = _mysql_query($countsql,$this->DB);
		if (!$countres){
			writeToLog('error','database',$sql);
			return;
		}
		
		$submitted = new stdClass();
		
		$submitted->count = 0;
		while($row = mysql_fetch_object($countres)){
			$submitted->count = $row->norecords;
		}

		$submitted->start = max(min($submitted->count-1,$start),0);
		$submitted->limit = $limit;
		$start = $submitted->start;
		
		//add a limit if necessary
		if($limit != 'all'){
			$sql .= " LIMIT ".$start.",".$limit;
		}
		
		$submitted->protocols = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_object($result)){
		   	array_push($submitted->protocols,$row);
		}
	    return $submitted; 
	}
	
	function getANC1Defaulters($opts=array()){
		if(array_key_exists('months',$opts)){
			$months = max(0,$opts['months']);
		} else {
			$months = 6;
		}
		if(array_key_exists('viewby',$opts)){
			$viewby = $opts['viewby'];
		} else {
			$viewby = 'months';
		}
		
		// get all the submitted ANC1 protocols from first day of the month 6 months ago
		$sql = "SELECT 	p._URI,
						p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						p.Q_LMP, 
						p.TODAY as createdate, 
						DATE_ADD(p.Q_LMP, INTERVAL ".ANC1_DUE_BY_END." DAY) AS ANC1DUEBY ,
						hp.hpname as healthpoint
				FROM ".ANCFIRST." p 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				WHERE p.TODAY > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')
				AND p.Q_HEALTHPOINTID != '9999'
				ORDER BY p.TODAY ASC";

		// if createdate > ANC1DUEBY then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = _mysql_query($sql,$this->DB);
		$summary = array();
		
		if($viewby == 'months'){
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
		} else if($viewby == 'healthpoints'){
			$hps = $this->getHealthPoints();
			
			foreach ($hps as $hp){
				$summary[$hp->hpname] = new stdClass;
				$summary[$hp->hpname]->defaulters = 0;
				$summary[$hp->hpname]->nondefaulters = 0;
			}
			while($row = mysql_fetch_array($results)){
				$arrayIndex = $row['healthpoint'];
					
				if ($row['createdate'] > $row['ANC1DUEBY'] ){
					$summary[$arrayIndex]->defaulters++;
				} else {
					$summary[$arrayIndex]->nondefaulters++;
				}
			}
		}
		
		// change into a percentage rather than absolute values
		foreach($summary as $k=>$v){
			$total = $v->defaulters + $v->nondefaulters;
			if ($total > 0){
				$pc_default = ($v->defaulters * 100)/$total;
				$pc_nondefault = ($v->nondefaulters * 100)/$total;
				$summary[$k]->defaulters = $pc_default;
				$summary[$k]->nondefaulters = $pc_nondefault;
			}
		}
		return $summary;
	}
	
	function datacheckSummary(){
		$total = 0;
		$total += count($this->datacheckDuplicateReg());
		$total += count($this->datacheckUnregistered());
		$total += count($this->datacheckMissingProtocols());
		$total += count($this->datacheckDuplicateProtocols());
		if($total >0){
			return true;
		} else {
			return false;
		}
	}
	
	function datacheckDuplicateReg(){
		$sql = "SELECT 	i.Q_HEALTHPOINTID as healthpointcode, 
				hp.hpname as healthpointname, 
				i.Q_USERID as patientid
				FROM ".REGISTRATION." i
				INNER JOIN healthpoint hp ON hp.hpcode = i.Q_HEALTHPOINTID
				GROUP BY hp.hpname, 
					i.Q_HEALTHPOINTID, 
					i.Q_USERID
				HAVING count(i._URI)>1";
		$report = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_object($result)){
		   	array_push($report,$row);
		}
	    return $report; 
	}
	
	function datacheckUnregistered(){
		$report = array();
		// unregistered from ancfirst
		$sql = "SELECT p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation,  
						p.Q_USERID,
						'".getstring('protocol.ancfirst')."' as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
				FROM ".ANCFIRST." p
				LEFT OUTER JOIN ".REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE r._URI is null ";
		
		//unregistered from ancfollow
		$sql .= " UNION 
				SELECT p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation, 
						p.Q_USERID,
						CONCAT('".getString('protocol.ancfollow')."',' ',p.Q_FOLLOWUPNO) as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
				FROM ".ANCFOLLOW." p
				LEFT OUTER JOIN ".REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE r._URI is null";
		
		//unregistered from anclabtest
		$sql .= " UNION SELECT p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation,   
						p.Q_USERID,
						'".getstring('protocol.anclabtest')."'as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
				FROM ".ANCLABTEST." p
				LEFT OUTER JOIN ".REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE r._URI is null";
		
		// unregistered from anctransfer
		$sql .= " UNION
				SELECT p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation, 
						p.Q_USERID,
						'".getstring('protocol.anctransfer')."'as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
				FROM ".ANCTRANSFER." p
				LEFT OUTER JOIN ".REGISTRATION." r ON (p.Q_HEALTHPOINTID = r.Q_HEALTHPOINTID AND p.Q_USERID = r.Q_USERID) 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE r._URI is null";

		//TODO unregistered from delivery
		
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_object($result)){
		   	array_push($report,$row);
		}
		
		return $report;
	}
	
	function datacheckDuplicateProtocols(){
		$report = array();
		// duplicate ancfirst
		$sql = "SELECT 	i.Q_HEALTHPOINTID,
						php.hpname as patientlocation, 
						hp.hpname as protocollocation, 
						i.Q_USERID ,
						'".getstring('protocol.ancfirst')."'as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
				FROM ".ANCFIRST." i
				INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
				INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				GROUP BY php.hpname, 
					i.Q_HEALTHPOINTID, 
					i.Q_USERID
				HAVING count(i._URI)>1";
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}

		//TODO duplicate follow up 
		$sql = "SELECT 	i.Q_HEALTHPOINTID,
						php.hpname as patientlocation, 
						hp.hpname as protocollocation, 
						i.Q_USERID ,
						'".getstring('protocol.ancfollow')."'as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						i.Q_FOLLOWUPNO
				FROM ".ANCFOLLOW." i
				INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
				INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				GROUP BY php.hpname, 
					i.Q_HEALTHPOINTID, 
					i.Q_USERID,
					i.Q_FOLLOWUPNO
				HAVING count(i._URI)>1";
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($row = mysql_fetch_object($result)){
			array_push($report,$row);
		}
		
		
		//TODO duplicate labtest?
		
		//TODO duplicate transfer?
		
		return $report;
	}
	
	function datacheckMissingProtocols(){
		$missing = array();
		
		// check anc first if follow up 1 exists
		$sql = "SELECT p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
						'ANC Follow Up 1 submitted but no ANC First Visit' as reason
				FROM ".ANCFOLLOW." p 
				LEFT OUTER JOIN ".ANCFIRST." first ON p.Q_USERID = first.Q_USERID AND p.Q_HEALTHPOINTID = first.Q_HEALTHPOINTID 
				INNER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p.Q_FOLLOWUPNO ='2'
				AND first.Q_USERID is null ";
		
		//check follow up 2 if follow up 1
		$sql .= " UNION
				SELECT p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
						'ANC Follow Up 3 submitted but no ANC Follow Up 2' as reason
				FROM ".ANCFOLLOW." p 
				LEFT OUTER JOIN (SELECT * FROM ".ANCFOLLOW." WHERE Q_FOLLOWUPNO='2') follow
					ON p.Q_USERID = follow.Q_USERID AND p.Q_HEALTHPOINTID = follow.Q_HEALTHPOINTID 
				INNER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p.Q_FOLLOWUPNO ='3'
				AND follow.Q_USERID is null";

		
		//check follow up 3 if follow up 2
		$sql	.= " UNION
				SELECT p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
						'ANC Follow Up 4 submitted but no ANC Follow Up 3' as reason
				FROM ".ANCFOLLOW." p 
				LEFT OUTER JOIN (SELECT * FROM ".ANCFOLLOW." WHERE Q_FOLLOWUPNO='2') follow
					ON p.Q_USERID = follow.Q_USERID AND p.Q_HEALTHPOINTID = follow.Q_HEALTHPOINTID 
				INNER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p.Q_FOLLOWUPNO ='4'
				AND follow.Q_USERID is null";
		
		//labtest but no first visit
		$sql .= " UNION
				SELECT p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						php.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
						'ANC Lab Test submitted but no ANC First Visit' as reason
				FROM ".ANCLABTEST." p 
				LEFT OUTER JOIN ".ANCFIRST." first ON p.Q_USERID = first.Q_USERID AND p.Q_HEALTHPOINTID = first.Q_HEALTHPOINTID 
				INNER JOIN ".REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE first.Q_USERID is null ";
		
		//TODO check anc when labour/delivery
		
		//TODO check labour/dlivery when pnc
		
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_object($result)){
		   	array_push($missing,$row);
		}
		return $missing;
	}
	
/*
 * *********************************************************************************************
 */	
	function adminLastLogin(){
		$sql = "SELECT u.userid, firstname, lastname, propvalue FROM user u
				INNER JOIN userprops up ON up.userid = u.userid
				WHERE propname='lastlogin'
				ORDER BY propvalue";
		$stats = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_object($result)){
		   	array_push($stats,$row);
		}
	    return $stats; 
	}
	
	function adminNeverLogin($nodays=31){
		$sql = "SELECT u.userid, firstname, lastname FROM user u
				WHERE u.userid NOT IN 
					(SELECT userid FROM userprops 
						WHERE propname='lastlogin' 
						AND CAST(propvalue AS DATETIME) > DATE_ADD(NOW(),INTERVAL -".$nodays." DAY))";
		$stats = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_object($result)){
		   	array_push($stats,$row);
		}
	    return $stats; 
	}
	
	function adminUserHits($nodays=31){
		$sql = "SELECT COUNT(l.id) AS hits, u.userid, u.firstname, u.lastname FROM log l
				INNER JOIN user u ON u.userid = l.userid 
				WHERE l.logtime >= DATE_ADD(CURDATE(), INTERVAL -".$nodays." DAY)
				AND logtype = 'pagehit'
				GROUP BY u.userid, u.firstname, u.lastname
				ORDER BY hits DESC";
		$stats = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_object($result)){
		   	array_push($stats,$row);
		}
	    return $stats; 
	}
	
	function adminDailyHits($nodays=31){
		$sql = "SELECT COUNT(l.id) AS hits, DAY(logtime) AS logday, MONTH(logtime) as logmonth, YEAR(logtime)  as logyear FROM log l
				WHERE logtype = 'pagehit'
				AND l.logtime >= DATE_ADD(CURDATE(), INTERVAL -".$nodays." DAY)
				GROUP BY logday, logmonth, logyear
				ORDER BY logyear ASC, logmonth ASC, logday ASC";
		$stats = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_object($result)){
		   	array_push($stats,$row);
		}
	    return $stats;
	}
	
	function adminPopularPages($nodays=31,$limit=20){
		$sql = "SELECT COUNT(id) as hits, logmsg FROM log l
				WHERE logtype='pagehit'
				AND l.logtime >= DATE_ADD(CURDATE(), INTERVAL -".$nodays." DAY)
				GROUP BY logmsg
				ORDER BY hits DESC
				LIMIT 0,".$limit;
		$stats = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_object($result)){
		   	array_push($stats,$row);
		}
	    return $stats;
	}
	
	function adminLog($type,$nodays=31,$limit=50){
		$sql = "SELECT * FROM log 
				WHERE logtime >= DATE_ADD(NOW(), INTERVAL -".$nodays." DAY)";
		if($type != 'all'){
			$sql .= " AND loglevel = '".$type."'";
		}
		$sql .=	"ORDER By logtime DESC LIMIT 0,".$limit;
		$stats = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
		while($row = mysql_fetch_object($result)){
		   	array_push($stats,$row);
		}
	    return $stats;
	}

}

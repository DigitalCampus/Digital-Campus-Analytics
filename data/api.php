<?php
include_once($CONFIG->homePath.'data/riskassess.php');
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
		$sql = sprintf("SELECT u.*, hp.hpcode 
						FROM user u
						INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
						WHERE username ='%s' LIMIT 0,1",$user->username);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($o = mysql_fetch_object($result)){
	  		$user->userid = $o->userid;
			$user->username = $o->username;
			$user->firstname = $o->firstname;
			$user->lastname =  $o->lastname;
			$user->hpid=  $o->hpid;
			$user->hpcode=  $o->hpcode;
			$user->user_uri =  $o->user_uri;
		}
		return $user;
	} 
	
	function getUserByID($userid){
		$sql = sprintf("SELECT * 
							FROM user u
						LEFT OUTER JOIN healthpoint hp ON u.hpid = hp.hpid
						LEFT OUTER JOIN district d ON hp.did = d.did
						WHERE userid = %d
						AND hp.hpcode IN (%s) 
						LIMIT 0,1",$userid, $this->getUserHealthPointPermissions());
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($o = mysql_fetch_object($result)){
			$user = new User();
			$user->userid = $o->userid;
			$user->username = $o->username;
			$user->firstname = $o->firstname;
			$user->lastname =  $o->lastname;
			$user->hpid=  $o->hpid;
			$user->user_uri =  $o->user_uri;
			return $user;
		}
		return null;
	}
	
	function getUsers($getall = false){
		if($getall){
			$sql = "SELECT 
						u.userid,
						u.firstname,
						u.username,
						u.lastname,
						u.user_uri,
						hp.hpname,
						hp.hpcode,
						d.dname
			 		FROM user u
					LEFT OUTER JOIN healthpoint hp ON u.hpid = hp.hpid
					LEFT OUTER JOIN district d ON hp.did = d.did
					ORDER BY u.firstname";
		} else {
			$sql = sprintf("SELECT 
						u.userid,
						u.firstname,
						u.username,
						u.lastname,
						u.user_uri,
						hp.hpname,
						hp.hpcode,
						d.dname
			 		FROM user u
					LEFT OUTER JOIN healthpoint hp ON u.hpid = hp.hpid
					LEFT OUTER JOIN district d ON hp.did = d.did
					WHERE hp.hpcode IN (%s)
					ORDER BY u.firstname",$this->getUserHealthPointPermissions());
		}
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
	  		writeToLog('error','database',$insertSql);
	  	}
	}
	
	function updateUser($userid,$firstname,$lastname,$user_uri,$hpid){
		$sql = sprintf("UPDATE user SET firstname='%s', lastname='%s', user_uri='%s', hpid = %d WHERE userid = %d",$firstname,$lastname,$user_uri,$hpid,$userid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		return true;
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
	
	function userChangePassword($userid, $newpass){
		$sql = sprintf("UPDATE user SET password = md5('%s') WHERE userid=%d",$newpass,$userid);
		$result = _mysql_query($sql,$this->DB);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	
	// returns comma separated list of the hpcodes the current user is allowed to view
	function getUserHealthPointPermissions(){
		global $USER;
		
		if($USER->getProp('permissions.admin') == 'true'){
			// admin user can view everything
			$sql = "SELECT hpcode FROM healthpoint";
		} else if($USER->getProp('permissions.viewall') == 'true'){
			// "permissions.all" can view all districts & healthpoints, but aren't admin users (can't view logs, edit users etc)
			$sql = "SELECT hpcode FROM healthpoint";
		} else if($USER->getProp('permissions.districts') != null) {
			// "permissions.districts" can view all the districts listed
			$sql = sprintf("SELECT hpcode FROM healthpoint WHERE did IN (%s)",$USER->getProp('permissions.districts'));
		} else if($USER->getProp('permissions.healthpoints')!= null) {
			// "permissions.healthpoints" can view all the healthpoints listed
			$sql = sprintf("SELECT hpcode FROM healthpoint WHERE hpid IN (%s)",$USER->getProp('permissions.healthpoints'));
		} else {
			//otherwise can only see the date from the hpid in their user table record (hpid) 
			$sql = sprintf("SELECT hpcode FROM healthpoint WHERE hpid = %d",$USER->hpid);
		}
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$temp = array();
		while($o = mysql_fetch_object($result)){
			array_push($temp,$o->hpcode);
		}
		$hpcodes = implode(",",$temp);
		
		// if hpcodes is empty, set it to -1 (this prevents errors where querying "IN ()", so instead it's "IN( -1)"
		if($hpcodes == ""){
			$hpcodes = "-1";
		}
		return $hpcodes;
	}
	
	
	// get the list of ignored health points
	function getIgnoredHealthPoints(){
		return IGNORE_HEALTHPOINTS;
	}
	
	
	/*
	 * 
	 */
	function writeLog($loglevel,$userid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries){
		$sql = sprintf("INSERT INTO log (loglevel,userid,logtype,logmsg,logip,logpagephptime,logpagemysqltime,logpagequeries) VALUES ('%s',%d,'%s','%s','%s',%f,%f,%d)", $loglevel,$userid,$logtype,mysql_real_escape_string($logmsg),$ip,$logpagephptime,$logpagemysqltime,$logpagequeries);
		_mysql_query($sql,$this->DB);
	}
	
	// return list of Health posts
	function getHealthPoints($getall = false){
		if($getall){
			$sql = "SELECT * FROM healthpoint ORDER BY hpname ASC;";
		} else {
			$sql = sprintf("SELECT * FROM healthpoint WHERE hpcode IN (%s) ORDER BY hpname ASC;",$this->getUserHealthPointPermissions());
		}
		$healthposts = array();
	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($row = mysql_fetch_object($result)){
		   	$healthposts[$row->hpid] = $row;
		}
	    return $healthposts;
	}
	
	
	function getCohortHealthPoints(){
		$sql = sprintf("SELECT * FROM healthpoint hp
						INNER JOIN district d ON d.did = hp.did
						WHERE d.did IN (SELECT did 
										FROM healthpoint 
										WHERE hpcode IN (%s))
						ORDER BY hp.hpname ASC",
						$this->getUserHealthPointPermissions());
		
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
	
	// get list of the distrcits for the current user
	function getDistricts(){
		$sql = sprintf("SELECT DISTINCT d.* FROM district d
						INNER JOIN healthpoint hp ON hp.did = d.did
						WHERE hp.hpcode IN (%s) ORDER BY d.dname ASC", $this->getUserHealthPointPermissions());
		$districts = array();
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($o = mysql_fetch_object($result)){
			array_push($districts,$o);
		}
		return $districts;
	}
	
	function getHealthPointsForDistict($did){
		$sql = sprintf("SELECT * FROM healthpoint hp 
						WHERE hp.did=%d ORDER BY hp.hpname ASC", $did);
		$healthposts = array();
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($o = mysql_fetch_object($result)){
			array_push($healthposts,$o);
		}
		return $healthposts;
	}
	
	function updatePatients(){
		//add any new patients to the patientcurrent table
		$sql = "INSERT INTO patientcurrent (hpcode,patid) 
				SELECT DISTINCT i.Q_HEALTHPOINTID, i.Q_USERID FROM ".TABLE_REGISTRATION." i
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
				FROM ".TABLE_REGISTRATION." r
				INNER JOIN patientcurrent pc ON pc.hpcode = r.Q_HEALTHPOINTID AND pc.patid = r.Q_USERID
				INNER JOIN healthpoint hp ON hp.hpcode = r.Q_HEALTHPOINTID
				WHERE pc.pcurrent = 1";	
		// TODO add permissions
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
				FROM ".TABLE_REGISTRATION." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;

	    $result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	$pat = mysql_fetch_object($result);
	  	if($pat == null){
	  		$pat = new stdClass();
	  		$pat->regcomplete = false;
	  	} else {
	  		$pat->regcomplete = true;
	  		$pat->Q_HOMEAPPLIANCES = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_REG_HOMEAPPLIANCES." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = _mysql_query($appsql,$this->DB);
	  		if (!$appresult){
	  			writeToLog('error','database',$appsql);
	  			return;
	  		}
	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_HOMEAPPLIANCES,$app->VALUE);
	  		}
	  	}
  		
	  	// add protocol details
		$pat->ancfirst = $this->getPatientANCFirst($opts);
		$pat->ancfollow = $this->getPatientANCFollow($opts); 
		$pat->anctransfer = $this->getPatientANCTransfer($opts);
		$pat->anclabtest= $this->getPatientANCLabTest($opts);
		$pat->delivery = $this->getPatientDelivery($opts);
		
		// TODO add PNC
		
		// risk assessment
		$ra = new RiskAssessment();
		$pat->risk = $ra->getRisks($pat);
		return $pat;		
	}
	
	
	
	private function getPatientANCFirst($opts=array()){
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
				FROM ".TABLE_ANCFIRST." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR hp.hpcode IN (".$this->getUserHealthPointPermissions()."))" ;
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	  	while($pat = mysql_fetch_object($result)){
	  		$pat->Q_FPMETHOD = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCFIRST_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
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
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCFIRST_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
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
	
	private function getPatientANCFollow($opts=array()){
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
				FROM ".TABLE_ANCFOLLOW." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' AND r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return;
	    }
	    $protocols = array();
	    $count=0;
	  	while($pat = mysql_fetch_object($result)){
	  		$protocols[$count] = $pat;
	  		$count++;
	  	}
		
	  	return $protocols;
	}
	
	private function getPatientANCTransfer($opts=array()){
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
				FROM ".TABLE_ANCTRANSFER." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
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
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCTRANSFER_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
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
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCTRANSFER_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
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
	
	private function getPatientANCLabTest($opts=array()){
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
				FROM ".TABLE_ANCLABTEST." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
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
	
	private function getPatientDelivery($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						_URI,
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
						TODAY AS CREATEDON
				FROM ".TABLE_DELIVERY." p
				INNER JOIN healthpoint pathp ON pathp.hpcode = p.Q_HEALTHPOINTID
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($o = mysql_fetch_object($result)){
			// add birth attendants
			$o->Q_BIRTHATTENDANT = array();
			// get the Home applicances source
			$appsql = "SELECT VALUE FROM ".TABLE_DELIVERY_ATTENDED ." WHERE _PARENT_AURI = '".$o->_URI."'";
			$appresult = _mysql_query($appsql,$this->DB);
			if (!$appresult){
				writeToLog('error','database',$appsql);
				return;
			}
			while($app = mysql_fetch_object($appresult)){
				array_push($o->Q_BIRTHATTENDANT,$app->VALUE);
			}
			// TODO add babies
			$o->Q_BABY = $this->getPatientDeliveryBaby($o->_URI);
			return $o;
		}
	}
	
	private function getPatientDeliveryBaby($uri){
		$sql = sprintf("SELECT
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
						Q_VITAMINK
				FROM %s
				WHERE _PARENT_AURI = '%s'",TABLE_DELIVERY_BABY,$uri);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$babies = array();
		while($o = mysql_fetch_object($result)){
			array_push($babies, $o);
		}
		return $babies;
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
					php.hpcode as patienthpcode,
					hp.hpcode as protocolhpcode,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".PROTOCOL_REGISTRATION."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					u.userid,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".TABLE_REGISTRATION." p 
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
					php.hpcode as patienthpcode,
					hp.hpcode as protocolhpcode,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".PROTOCOL_ANCFIRST."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					u.userid,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".TABLE_ANCFIRST." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
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
					php.hpcode as patienthpcode,
					hp.hpcode as protocolhpcode,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".PROTOCOL_ANCFOLLOW."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					u.userid,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".TABLE_ANCFOLLOW." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
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
					php.hpcode as patienthpcode,
					hp.hpcode as protocolhpcode,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".PROTOCOL_ANCLABTEST."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					u.userid,
					'' AS Q_GPSDATA_LAT,
					'' AS Q_GPSDATA_LNG,
					'' AS Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".TABLE_ANCLABTEST." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
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
					php.hpcode as patienthpcode,
					hp.hpcode as protocolhpcode,
					php.hpname as patientlocation,
					hp.hpname as protocollocation,
					'".PROTOCOL_ANCTRANSFER."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					u.userid,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng
				FROM ".TABLE_ANCTRANSFER." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
				WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";
		
		//delivery
		$sql .= " UNION
					SELECT
						p._CREATION_DATE as datestamp,
						p.Q_USERID,
						CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
						p.Q_HEALTHPOINTID,
						php.hpcode as patienthpcode,
						hp.hpcode as protocolhpcode,
						php.hpname as patientlocation,
						hp.hpname as protocollocation,
						'".PROTOCOL_DELIVERY."' as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						u.userid,
						p.Q_GPSDATA_LAT,
						p.Q_GPSDATA_LNG,
						p.Q_LOCATION,
						hp.locationlat,
						hp.locationlng
					FROM ".TABLE_DELIVERY." p 
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID
					WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -".$days." DAY)";

		$sql .= ") a ";
		$sql .= "WHERE (a.patienthpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR a.protocolhpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= "ORDER BY datestamp DESC";
		
		
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
	
	function getTasksDue($userid, $opts=array()){
		// TODO check task list
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
		
		$sql = "SELECT * FROM (";
		$sql .= "SELECT A.Q_APPOINTMENTDATE as datedue,
						u.userid,
						A.Q_USERID,
						CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
						A.Q_HEALTHPOINTID,
						php.hpname as patientlocation,
						'".getstring(PROTOCOL_ANCFOLLOW)."' AS protocol
				FROM ".TABLE_ANCFIRST." A
				LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON A.Q_USERID = R.Q_USERID AND A.Q_HEALTHPOINTID = R.Q_HEALTHPOINTID
				INNER JOIN healthpoint php ON php.hpcode = A.Q_HEALTHPOINTID
				INNER JOIN user u ON u.user_uri = A._CREATOR_URI_USER
				WHERE A.Q_APPOINTMENTDATE > now()
				AND A.Q_APPOINTMENTDATE < DATE_ADD(now(), INTERVAL +".$days." DAY)
				AND u.userid =".$userid;
		
		$sql .= " UNION
				SELECT 	A.Q_APPOINTMENTDATE as datedue,
						u.userid,
						A.Q_USERID,
						CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
						A.Q_HEALTHPOINTID,
						php.hpname as patientlocation,
						'".getstring(PROTOCOL_ANCFOLLOW)."' AS protocol
				FROM ".TABLE_ANCFOLLOW." A
				LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON A.Q_USERID = R.Q_USERID AND A.Q_HEALTHPOINTID = R.Q_HEALTHPOINTID
				INNER JOIN healthpoint php ON php.hpcode = A.Q_HEALTHPOINTID
				INNER JOIN user u ON u.user_uri = A._CREATOR_URI_USER
				WHERE A.Q_APPOINTMENTDATE > now()
				AND A.Q_APPOINTMENTDATE < DATE_ADD(now(), INTERVAL +".$days." DAY)
				AND u.userid =".$userid;
		// TODO add delivery
		
		// TODO add PNC
		$sql .= ") C  ORDER BY datedue";
		// TODO add permissions??
		
		$tasks = array();
		$result = $this->runSql($sql);
		while($o = mysql_fetch_object($result)){
			array_push($tasks, $o);
		}
		return $tasks;
	}
	
	
	function getANC1Defaulters($opts=array()){
		global $ERROR;
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
			$hps = $this->getUserHealthPointPermissions();
		}
		
		// get all the submitted ANC1 protocols from first day of the month 6 months ago
		$sql = sprintf("SELECT 	p._URI,
						p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						p.Q_LMP, 
						p.TODAY as createdate, 
						DATE_ADD(p.Q_LMP, INTERVAL %s DAY) AS ANC1DUEBY ,
						hp.hpname as healthpoint
				FROM %s p 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid",ANC1_DUE_BY_END,TABLE_ANCFIRST);
		if(isset($months)){
			$sql .= " WHERE p.TODAY > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		} else {
			$sql .= sprintf(" WHERE p.TODAY > '%s'",$startdate);
			$sql .= sprintf(" AND  p.TODAY <= '%s'",$enddate);
		}
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND p.Q_HEALTHPOINTID NOT IN (%s)",$this->getIgnoredHealthPoints());
		}
		$sql .= sprintf(" AND p.Q_HEALTHPOINTID IN (%s) ORDER BY p.TODAY ASC",$hps);

		// if createdate > ANC1DUEBY then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = _mysql_query($sql,$this->DB);
		if (!$results){
			writeToLog('error','database',$sql);
			return;
		}
		
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
		
		// if more than one HP then divide to get the average
		$hpcount = count(explode(",",$hps));
		
		// change into a percentage rather than absolute values
		foreach($summary as $k=>$v){
			$total = $v->defaulters + $v->nondefaulters;
			if ($total > 0){
				$pc_default = round(($v->defaulters * 100)/$total/$hpcount);
				$pc_nondefault = round(($v->nondefaulters * 100)/$total/$hpcount);
				$summary[$k]->defaulters = $pc_default;
				$summary[$k]->nondefaulters = $pc_nondefault;
			}
		}
		return $summary;
	}
	
	function getANC1DefaultersBestPerformer($opts=array()){
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
							p.TODAY as createdate, 
							DATE_ADD(p.Q_LMP, INTERVAL ".ANC1_DUE_BY_END." DAY) AS ANC1DUEBY ,
							hp.hpname as healthpoint
					FROM ".TABLE_ANCFIRST." p 
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					WHERE p.TODAY > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND p.Q_HEALTHPOINTID NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " AND p.Q_HEALTHPOINTID IN (".$hps.")
					ORDER BY p.TODAY ASC";
	
	
		// if createdate > ANC1DUEBY then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = _mysql_query($sql,$this->DB);
		if (!$results){
			writeToLog('error','database',$sql);
			return;
		}
	
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
		
		// set up summary/results array/objects
		$date = new DateTime();
		$date->sub(new DateInterval('P'.$months.'M'));
		$summary = array();
		for ($i=0; $i<7 ;$i++){
			$summary[$date->format('M-Y')] = new stdClass;
			$summary[$date->format('M-Y')]->defaulters = 0;
			$summary[$date->format('M-Y')]->nondefaulters = 0;
			$date->add(new DateInterval('P1M'));
		}
		
		// all those who had an ANC follow up visit
		$sql = "SELECT 	p._URI,
						p.Q_USERID, 
						p.Q_HEALTHPOINTID, 
						p.Q_LMP, 
						p.TODAY as createdate,  
						DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_START." DAY) AS ANC2_DUE_BY_START,
						DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) AS ANC2_DUE_BY_END
				FROM ".TABLE_ANCFOLLOW." p
				WHERE p.TODAY > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')";
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND p.Q_HEALTHPOINTID NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " ORDER BY p.TODAY ASC";
		
		// TODO add permissions
		
		// if createdate not between ANC2_DUE_BY_START and ANC2_DUE_BY_END then defaulter, group by month/year of createdate
		// otherwise non defaulter
		$results = $this->runSql($sql);
		while($row = mysql_fetch_array($results)){
			$date = new DateTime($row['createdate']);
			$arrayIndex = $date->format('M-Y');
		
			if ($row['createdate'] > $row['ANC2_DUE_BY_START'] && $row['createdate'] < $row['ANC2_DUE_BY_END']){
				$summary[$arrayIndex]->nondefaulters++;
			} else {
				$summary[$arrayIndex]->defaulters++;
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
				WHERE f.Q_USERID IS NULL
				AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) > date_format(curdate() - interval ".$months." month,'%Y-%m-01 00:00:00')
				AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) < curdate()";
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND p.Q_HEALTHPOINTID NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " ORDER BY DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) ASC";
		// TODO add constraint about terminations
		// TODO add permissions
		// all those returned by above query are defaulters - as have now Follow up
		$results = $this->runSql($sql);
		while($row = mysql_fetch_array($results)){
			$date = new DateTime($row['ANC2_DUE_BY_END']);
			$arrayIndex = $date->format('M-Y');
			$summary[$arrayIndex]->defaulters++;
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
		$report = array();
		//include_once 'datacheck/duplicatereg.php';
		$sql = "SELECT 	i.Q_HEALTHPOINTID as healthpointcode,
						hp.hpname as healthpointname, 
						i.Q_USERID as patientid
						FROM ".TABLE_REGISTRATION." i
						INNER JOIN healthpoint hp ON hp.hpcode = i.Q_HEALTHPOINTID";
		// add permissions
		$sql .= " WHERE i.Q_HEALTHPOINTID IN (".$this->getUserHealthPointPermissions().") " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND i.Q_HEALTHPOINTID NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY hp.hpname,
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
		return $report;
	}
	
	function datacheckUnregistered(){
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
		$sql .= " WHERE (a.patienthpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR a.protocolhpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		
		
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
	    	writeToLog('error','database',$sql);
	    	return false;
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
						'".PROTOCOL_ANCFIRST."' as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname
				FROM ".TABLE_ANCFIRST." i
				INNER JOIN healthpoint php ON php.hpcode = i.Q_HEALTHPOINTID
				INNER JOIN user u ON i._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		$sql .= " WHERE (php.hpcode IN (".$this->getUserHealthPointPermissions().")" ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND php.hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY php.hpname, 
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
		$sql .= " WHERE  (php.hpcode IN (".$this->getUserHealthPointPermissions().")" ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND php.hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY php.hpname, 
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
		$sql .= " WHERE  (php.hpcode IN (".$this->getUserHealthPointPermissions().")" ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND php.hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		$sql .= " GROUP BY php.hpname, 
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
		
		// TODO duplicate transfer
		// TODO add permissions
		
		// TODO duplicate delivery
		// TODO add permissions
		
		// TODO duplicate PNC
		// TODO add permissions
		return $report;
	}
	
	function datacheckMissingProtocols(){
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
		// TODO add permissions
		
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
		// TODO add permissions
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
		$sql .= " WHERE (a.patienthpcode IN (".$this->getUserHealthPointPermissions().")" ;
		$sql .= " OR a.protocolhpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}

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

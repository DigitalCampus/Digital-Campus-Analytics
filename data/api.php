<?php
include_once($CONFIG->homePath.'data/riskassess.php');
include_once($CONFIG->homePath.'data/datacheck.php');
include_once($CONFIG->homePath.'data/admin.php');
include_once($CONFIG->homePath.'data/kpi.php');

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
	    	return false;
	    }
	    return $result;
	}
	
	function cleanUpDB(){
	    if( $this->DB != false ){
	        mysql_close($this->DB);
	    }
	    $this->DB = false;
	} 
	
	function cron($days){
		
		$this->updatePatients();
		
		// clear up log table
		$logdays = $this->getSystemProperty('log.archive.days');
		if($logdays > 0){
			$sql = sprintf("DELETE FROM log WHERE logtime < DATE_ADD(NOW(), INTERVAL -%d DAY)",$logdays);
			$this->runSql($sql);
		}
		
		// update & cache which HPs the patients have visited
		// get all submitted protocols in last $days // TODO - should really be since cron last run or something
		$submitted = $this->getProtocolsSubmitted(array('days'=>$days,'limit'=>'all'));
		
		foreach($submitted->protocols as $s){
			$this->cacheAddPatientHealthPointVisit($s->Q_USERID,$s->patienthpcode,$s->protocolhpcode,$s->datestamp,$s->protocol,$s->user_uri);
		}
		
		// update & cache task list
		$this->cacheTasksDue($days);
		
		// remove any really old overdue tasks based on the ignore policy
		$sql = sprintf("DELETE FROM cache_tasks
						WHERE datedue < DATE_ADD(NOW(), INTERVAL -%d DAY)",$this->getSystemProperty('overdue.ignore'));
		$this->runSql($sql);
		
		// update & cache patient risk factors
		$this->cacheRisks($days);
		
		// TODO update & cache KPI figures?
		
		$this->setSystemProperty('cron.lastrun',time());
	}
	
	
	function getUser($user){
		$sql = sprintf("SELECT u.*, hp.hpcode 
						FROM user u
						INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
						WHERE username ='%s' LIMIT 0,1",$user->username);
		$result = $this->runSql($sql);
		if(!$result){
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
		$result = $this->runSql($sql);
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
		$result = $this->runSql($sql);
		$users = array();
		while($row = mysql_fetch_object($result)){
			array_push($users,$row);
		}
		return $users;
	}
	
	function getSystemProperty($propname){
		$sql = sprintf("SELECT * FROM properties WHERE propname='%s'",$propname);
		$result = $this->runSql($sql);
		if(!$result){
			return $result;
		}
		while($o = mysql_fetch_object($result)){
			return $o->propvalue;
		}
		return null;
	}
	
	function setSystemProperty($propname,$propvalue){
		// first check to see if it exists already
		$sql = sprintf("SELECT * FROM properties WHERE propname='%s'",$propname);
		$result = $this->runSql($sql);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$updateSql = sprintf("UPDATE properties SET propvalue='%s' WHERE propname='%s'",$propvalue,$propname);
			$result = $this->runSql($updateSql);
			return;
		}
	
		$insertSql = sprintf("INSERT INTO properties (propvalue, propname) VALUES ('%s','%s')",$propvalue,$propname);
		$result = $this->runSql($insertSql);
	}
	
	
	function getUserProperties($userid){
		$sql = "SELECT * FROM userprops WHERE userid=".$userid;
		$result = $this->runSql($sql);
		if(!$result){
			return;
		}
		$props = array();
	  	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  		$props[$row['propname']] = $row['propvalue'];
		}
		return $props;
	} 
	
	function setUserProperty($userid,$name,$value){
		// first check to see if it exists already
		$sql = sprintf("SELECT * FROM userprops WHERE userid= %d AND propname='%s'",$userid,$name);
		$result = $this->runSql($sql);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  		$updateSql = sprintf("UPDATE userprops SET propvalue='%s' WHERE userid= %d AND propname='%s'",$value,$userid,$name);
	  		$result = $this->runSql($updateSql);
	  		return;
		}
		
		$insertSql = sprintf("INSERT INTO userprops (propvalue, userid,propname) VALUES ('%s',%d,'%s')",$value,$userid,$name);
	  	$result = $this->runSql($insertSql);
	}
	
	function updateUser($userid,$firstname,$lastname,$user_uri,$hpid){
		$sql = sprintf("UPDATE user SET firstname='%s', lastname='%s', user_uri='%s', hpid = %d WHERE userid = %d",$firstname,$lastname,$user_uri,$hpid,$userid);
		$result = $this->runSql($sql);
		return true;
	}
	
	function userValidatePassword($username,$password){
		global $USER;
		$sql = sprintf("SELECT userid FROM user WHERE username='%s' AND password=md5('%s')",$username,$password);
		$result = $this->runSql($sql);
		if(!$result){
			return false;
		}
	  	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  		return true;
		}
		return false;
	}
	
	function userChangePassword($userid, $newpass){
		$sql = sprintf("UPDATE user SET password = md5('%s') WHERE userid=%d",$newpass,$userid);
		$result = $this->runSql($sql);
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
		
		$result = $this->runSql($sql);
		if(!$result){
			return "-1";
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
	function writeLog($loglevel,$userid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries,$logagent){
		$sql = sprintf("INSERT INTO log (loglevel,userid,logtype,logmsg,logip,logpagephptime,logpagemysqltime,logpagequeries,logagent) 
						VALUES ('%s',%d,'%s','%s','%s',%f,%f,%d,'%s')", 
						$loglevel,$userid,$logtype,mysql_real_escape_string($logmsg),$ip,$logpagephptime,$logpagemysqltime,$logpagequeries,$logagent);
		// just run sql directly (without using $this->runSql) so doesn't try to log an error writing to the log!
		_mysql_query($sql,$this->DB);
	}
	
	// return list of Health posts
	function getHealthPoints($getall = false){
		if($getall){
			$sql = "SELECT * FROM healthpoint ORDER BY hpname ASC;";
		} else  {
			$sql = sprintf("SELECT * FROM healthpoint WHERE hpcode IN (%s) ORDER BY hpname ASC;",$this->getUserHealthPointPermissions());
		} 
		$healthposts = array();
	    $result = $this->runSql($sql);
	  	while($row = mysql_fetch_object($result)){
		   	$healthposts[$row->hpid] = $row;
		}
	    return $healthposts;
	}
	
	
	function getCohortHealthPoints($aslist = false){
		$sql = sprintf("SELECT * FROM healthpoint hp
						INNER JOIN district d ON d.did = hp.did
						WHERE d.did IN (SELECT did 
										FROM healthpoint 
										WHERE hpcode IN (%s))
						ORDER BY hp.hpname ASC",
						$this->getUserHealthPointPermissions());
		
		$healthposts = array();
		$result = $this->runSql($sql);
		while($row = mysql_fetch_object($result)){
			$healthposts[$row->hpcode] = $row;
		}
		if($aslist){
			$temp = array();
			foreach ($healthposts as $k=>$v){
				array_push($temp,$k);
			}
			return implode(',',$temp);
		}
		return $healthposts;
	}
	
	// get list of the distrcits for the current user
	function getDistricts(){
		$sql = sprintf("SELECT DISTINCT d.* FROM district d
						INNER JOIN healthpoint hp ON hp.did = d.did
						WHERE hp.hpcode IN (%s) ORDER BY d.dname ASC", $this->getUserHealthPointPermissions());
		$districts = array();
		$result = $this->runSql($sql);

		while($o = mysql_fetch_object($result)){
			array_push($districts,$o);
		}
		return $districts;
	}
	
	function getHealthPointsForDistict($did){
		$sql = sprintf("SELECT * FROM healthpoint hp 
						WHERE hp.did=%d ORDER BY hp.hpname ASC", $did);
		$healthposts = array();
		$result = $this->runSql($sql);

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
		$result = $this->runSql($sql);
	    
		//archive old patients
		// TODO update for real PNC protocol
		
		// TODO update for termination protocol
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
	
	/*
	 * returns an array of current patients for given user
	 */
	function getCurrentPatients($opts=array()){
		
		if(array_key_exists('hps',$opts)){
			$hps = $opts['hps'];
		} else {
			$hps = $API->getUserHealthPointPermissions();
		}
		
		$sql = sprintf("SELECT 	r.Q_HEALTHPOINTID,
						r.Q_USERID,
						pc.pcurrent
				FROM %s r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN patientcurrent pc ON pc.hpcode = r.Q_HEALTHPOINTID AND pc.patid = r.Q_USERID
				WHERE r.Q_HEALTHPOINTID IN (%s) 
				AND r.Q_HEALTHPOINTID NOT IN (%s)
				AND pc.pcurrent = 1",TABLE_REGISTRATION,$hps,$this->getIgnoredHealthPoints()) ;
		
		$result = $this->runSql($sql);
		$patients = array();
		while($o = mysql_fetch_object($result)){
			$opts = array('hpcode'=>$o->Q_HEALTHPOINTID,'patid'=>$o->Q_USERID);
			$p = $this->getPatient($opts);
			array_push($patients,$p);
		}
		
		return $patients;
	}
	
	function getPatient($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						hp.hpcode as protocolhpcode,
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
						_CREATION_DATE AS CREATEDON
				FROM ".TABLE_REGISTRATION." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;

	    $result = $this->runSql($sql);
	    
	  	$pat = mysql_fetch_object($result);
	  	if($pat == null){
	  		$pat = new stdClass();
	  		$pat->regcomplete = false;
	  	} else {
	  		$pat->regcomplete = true;
	  		$pat->Q_HOMEAPPLIANCES = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_REG_HOMEAPPLIANCES." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = $this->runSql($appsql); 

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
		$pat->pnc = $this->getPatientPNC($opts);
		
		// if patient record has no protocols then assume invalid patientid/hpcode 
		if($pat->regcomplete == false
			&& $pat->ancfirst == null
			&& count($pat->ancfollow) == 0
			&& count($pat->anctransfer) == 0
			&& count($pat->anclabtest) == 0
			&& $pat->delivery == null
			&& count($pat->pnc) == 0){
			echo "here";
			return false;
		} else {
			return $pat;
		}		
	}
	
	
	
	private function getPatientANCFirst($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						hp.hpcode as protocolhpcode,
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
						_CREATION_DATE AS CREATEDON
				FROM ".TABLE_ANCFIRST." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR hp.hpcode IN (".$this->getUserHealthPointPermissions()."))" ;
		
		$result = $this->runSql($sql);

	  	while($pat = mysql_fetch_object($result)){
	  		$pat->Q_FPMETHOD = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCFIRST_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = $this->runSql($appsql);

	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_FPMETHOD,$app->VALUE);
	  		}
	  		
	  		$pat->Q_WHOATTENDED = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCFIRST_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = $this->runSql($appsql);

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
						hp.hpcode as protocolhpcode,
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
						_CREATION_DATE AS CREATEDON
				FROM ".TABLE_ANCFOLLOW." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' AND r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
		$result = $this->runSql($sql);
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
						hp.hpcode as protocolhpcode,
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
						_CREATION_DATE AS CREATEDON
				FROM ".TABLE_ANCTRANSFER." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
		$result = $this->runSql($sql);

	    $protocols = array();
	    $count=0;
	  	while($pat = mysql_fetch_object($result)){
	  		
	  		$pat->Q_FPMETHOD = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCTRANSFER_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = $this->runSql($appsql); 

	  		while($app = mysql_fetch_object($appresult)){
	  			array_push($pat->Q_FPMETHOD,$app->VALUE);
	  		}
	  		
	  		$pat->Q_WHOATTENDED = array();
	  		// get the Home applicances source
	  		$appsql = "SELECT VALUE FROM ".TABLE_ANCTRANSFER_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
	  		$appresult = $this->runSql($appsql); 
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
						hp.hpcode as protocolhpcode,
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
						_CREATION_DATE AS CREATEDON
				FROM ".TABLE_ANCLABTEST." r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
		$result = $this->runSql($sql);

	    $protocols = array();
	    $count=0;
	  	while($pat = mysql_fetch_object($result)){
	  		$protocols[$count] = $pat;
	  		$count++;
	  	}
		
	  	return $protocols;
	}
	
	private function getPatientDelivery($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
						pathp.hpname as patientlocation,
						hp.hpname as protocollocation,
						hp.hpcode as protocolhpcode,
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
						_CREATION_DATE AS CREATEDON
				FROM ".TABLE_DELIVERY." p
				INNER JOIN healthpoint pathp ON pathp.hpcode = p.Q_HEALTHPOINTID
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE pathp.hpcode = '".$opts['hpcode']."' and p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		$sql .= " ORDER BY TODAY ASC";
		
		$result = $this->runSql($sql);

		while($o = mysql_fetch_object($result)){
			// add birth attendants
			$o->Q_BIRTHATTENDANT = array();
			// get the Home applicances source
			$appsql = "SELECT VALUE FROM ".TABLE_DELIVERY_ATTENDED ." WHERE _PARENT_AURI = '".$o->_URI."'";
			$appresult = $this->runSql($appsql);

			while($app = mysql_fetch_object($appresult)){
				array_push($o->Q_BIRTHATTENDANT,$app->VALUE);
			}
			// add babies
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
		$result = $this->runSql($sql);

		$babies = array();
		while($o = mysql_fetch_object($result)){
			array_push($babies, $o);
		}
		return $babies;
	}
	
	private function getPatientPNC($opts=array()){
		$sql = "SELECT 	pathp.hpcode,
							pathp.hpname as patientlocation,
							hp.hpname as protocollocation,
							hp.hpcode as protocolhpcode,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							_URI,
							_CREATOR_URI_USER,
							Q_AGE,
							Q_APPOINTMENTDATE,
							Q_CARDIACPULSE,
							Q_COMMENTSMOTHER,
							Q_CONSENT,
							Q_DELIVERYDATE,
							Q_DELIVERYMODE,
							Q_DELIVERYNOBABIES,
							Q_DELIVERYSITE,
							Q_DIASTOLICBP,
							Q_DRUGS,
							Q_DRUGSDESCRIPTION,
							Q_FPACCEPTED,
							Q_FPCOUNSELED,
							Q_GENITALIAEXTERNA,
							Q_GPSDATA_ACC,
							Q_GPSDATA_ALT,
							Q_GPSDATA_LAT,
							Q_GPSDATA_LNG,
							Q_HEALTHPOINTID,
							Q_HIV,
							Q_HIVTREATMENT,
							Q_IDCARD,
							Q_IRONSUPPL,
							Q_LEAKINGURINE,
							Q_LOCATION,
							Q_LOCHIAAMOUNT,
							Q_LOCHIACOLOUR,
							Q_LOCHIAODOUR,
							Q_MATERNALDEATH,
							Q_MOTHERCONDITION,
							Q_PALLORANEMIA,
							Q_PNCVISITNO,
							Q_SYSTOLICBP,
							Q_TEMPERATURE,
							Q_TETANUS,
							Q_TT1,
							Q_TT2,
							Q_USERID,
							Q_VITASUPPL,
							Q_YEAROFBIRTH,
							_CREATION_DATE AS CREATEDON
					FROM ".TABLE_PNC." r
					INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
					INNER JOIN user u ON r._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid
					WHERE pathp.hpcode = '".$opts['hpcode']."' and r.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (pathp.hpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR hp.hpcode IN (".$this->getUserHealthPointPermissions()."))" ;
	
		$result = $this->runSql($sql);
	
		$protocols = array();
		$count=0;
		while($pat = mysql_fetch_object($result)){
			
			$pat->Q_BABIES = $this->getPatientPNCBaby($pat->_URI);
			
			$pat->Q_BIRTHATTENDANT = array();
			// get the Home applicances source
			$appsql = "SELECT VALUE FROM ".TABLE_PNC_ATTENDED." WHERE _PARENT_AURI = '".$pat->_URI."'";
			$appresult = $this->runSql($appsql);
	
			while($app = mysql_fetch_object($appresult)){
				array_push($pat->Q_BIRTHATTENDANT,$app->VALUE);
			}
		  
			$pat->Q_COMPLICATIONS = array();
			// get the Home applicances source
			$appsql = "SELECT VALUE FROM ".TABLE_PNC_COMPLICATIONS ." WHERE _PARENT_AURI = '".$pat->_URI."'";
			$appresult = $this->runSql($appsql);
	
			while($app = mysql_fetch_object($appresult)){
				array_push($pat->Q_COMPLICATIONS,$app->VALUE);
			}
			$protocols[$count] = $pat;
	  		$count++;
		}
		return $protocols;
	}
	
	
	private function getPatientPNCBaby($uri){
		$sql = sprintf("SELECT
							Q_ARV_HIV_BABY,
							Q_BABYBREASTFEED,
							Q_BABYBREATHING,
							Q_BABYCONDITION,
							Q_BABYMUMBOND,
							Q_COMMENTSBABY,
							Q_CORDCONDITION,
							Q_DEATHCOMMENTS,
							Q_DEATHDATE,
							Q_HIV_BABY,
							Q_IMMUNO_BCG,
							Q_IMMUNO_BCG_LASTDATE,
							Q_IMMUNO_IPTI,
							Q_IMMUNO_IPTI_LASTDATE,
							Q_IMMUNO_OPV,
							Q_IMMUNO_OPV_LASTDATE,
							Q_IMMUNO_PENTA,
							Q_IMMUNO_PENTA_LASTDATE,
							Q_NEWBORNHEADCIRCUM,
							Q_NEWBORNHEIGHT,
							Q_NEWBORNWEIGHT
					FROM %s
					WHERE _PARENT_AURI = '%s'",TABLE_PNC_BABY,$uri);
		$result = $this->runSql($sql);
	
		$babies = array();
		while($o = mysql_fetch_object($result)){
			array_push($babies, $o);
		}
		return $babies;
	}
	
	function getProtocolsSubmitted($opts=array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else if(array_key_exists('startdate',$opts) && array_key_exists('enddate',$opts)) {
			$startdate = $opts['startdate'];
			$enddate = $opts['enddate'];
		} else {
			array_push($ERROR,"You must specify either no days or start/end dates for this function");
			return false; 
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
					hp.locationlng,
					u.user_uri 
				FROM ".TABLE_REGISTRATION." p 
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
	
		
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
					hp.locationlng,
					u.user_uri 
				FROM ".TABLE_ANCFIRST." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
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
					hp.locationlng,
					u.user_uri 
				FROM ".TABLE_ANCFOLLOW." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		
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
					hp.locationlng,
					u.user_uri 
				FROM ".TABLE_ANCLABTEST." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
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
					hp.locationlng,
					u.user_uri 
				FROM ".TABLE_ANCTRANSFER." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
				INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		
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
						hp.locationlng,
						u.user_uri 
					FROM ".TABLE_DELIVERY." p 
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}

		// PNC
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
						'".PROTOCOL_PNC."' as protocol,
						CONCAT(u.firstname,' ',u.lastname) as submittedname,
						u.userid,
						p.Q_GPSDATA_LAT,
						p.Q_GPSDATA_LNG,
						p.Q_LOCATION,
						hp.locationlat,
						hp.locationlng,
						u.user_uri 
					FROM ".TABLE_PNC." p 
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = p.Q_HEALTHPOINTID";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		
		
		$sql .= ") a ";
		$sql .= "WHERE (a.patienthpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR a.protocolhpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		if(array_key_exists('hpcode',$opts)){
			$sql .= " AND  (a.patienthpcode = ".$opts['hpcode'];
			$sql .= " OR a.protocolhpcode = ".$opts['hpcode'].")";
		}
		$sql .= "ORDER BY datestamp DESC";
		
		
		//query to get the total no of records
		$countsql = "SELECT COUNT(*) AS norecords FROM (".$sql.") a;";
		
		$countres = $this->runSql($countsql);
		
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
	    $result = $this->runSql($sql);

	  	while($row = mysql_fetch_object($result)){
		   	array_push($submitted->protocols,$row);
		}
	    return $submitted; 
	}
	
	function getProtocolsSubmitted_Cache($opts=array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else if(array_key_exists('startdate',$opts) && array_key_exists('enddate',$opts)) {
			$startdate = $opts['startdate'];
			$enddate = $opts['enddate'];
		} else {
			array_push($ERROR,"You must specify either no days or start/end dates for this function");
			return false;
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
		$sql .= "	SELECT 	cv.visitdate as datestamp,
							cv.userid AS Q_USERID,
							CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
							php.hpcode as patienthpcode,
							hp.hpcode as protocolhpcode,
							php.hpname as patientlocation,
							hp.hpname as protocollocation,
							cv.protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							cv.user_uri 
					FROM cache_visit cv 
					LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = cv.userid AND r.Q_HEALTHPOINTID = cv.hpcode)
					INNER JOIN user u ON cv.user_uri = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid 
					INNER JOIN healthpoint php ON php.hpcode = cv.hpcode";
		if(isset($days)){
			$sql .= sprintf(" WHERE cv.visitdate >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE cv.visitdate > '%s'",$startdate);
			$sql .= sprintf(" AND cv.visitdate <= '%s'",$enddate);
		}
		
		
		$sql .= ") a ";
		$sql .= "WHERE (a.patienthpcode IN (".$this->getUserHealthPointPermissions().") " ;
		$sql .= "OR a.protocolhpcode IN (".$this->getUserHealthPointPermissions().")) " ;
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND a.patienthpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		if(array_key_exists('hpcode',$opts)){
			$sql .= " AND  (a.patienthpcode = ".$opts['hpcode'];
			$sql .= " OR a.protocolhpcode = ".$opts['hpcode'].")";
		}
		$sql .= "ORDER BY datestamp DESC";
		
		//query to get the total no of records
		$countsql = "SELECT COUNT(*) AS norecords FROM (".$sql.") a;";
		
		$countres = $this->runSql($countsql);
		
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
		$result = $this->runSql($sql);
		
		while($row = mysql_fetch_object($result)){
			array_push($submitted->protocols,$row);
		}
		return $submitted;
		
	}
	
	function cacheEmptyPatientHealthPointVisit($userid, $hpcode){
		$sql = sprintf("DELETE FROM cache_visit WHERE userid= %d AND hpcode=%d",$userid,$hpcode);
		$this->runSql($sql);
	}
	
	function cacheAddPatientHealthPointVisit($userid, $hpcode,$visithpcode,$visitdate,$protocol,$user_uri){
		$sql = sprintf("SELECT * FROM cache_visit 
						WHERE
							hpcode = %d
						AND userid = %d
						AND visithpcode = %d
						AND visitdate = '%s'
						AND protocol = '%s'
						AND user_uri = '%s'",$hpcode,$userid,$visithpcode,$visitdate,$protocol,$user_uri);
		$result = $this->runSql($sql);
		
		if(mysql_num_rows($result) == 0){
			$sql = sprintf("INSERT INTO cache_visit (hpcode,userid,visithpcode,visitdate,protocol,user_uri)
							VALUES (%d,%d,%d,'%s','%s','%s')",$hpcode,$userid,$visithpcode,$visitdate,$protocol,$user_uri);
			$this->runSql($sql);
		}
	}
	
	function cacheTasksDue($days){
		// get all the patients submitted recently (according to past no $days)
		$submitted = $this->getProtocolsSubmitted(array('days'=>$days,'limit'=>'all'));
		$toupdate = array();
		foreach($submitted->protocols as $s){
			$toupdate[$s->Q_HEALTHPOINTID."/".$s->Q_USERID] = true;
		}
		
		foreach($toupdate as $k=>$v){
			$temp = explode('/',$k);
			$hpcode = $temp[0];
			$userid = $temp[1];
			$patient = $this->getPatient(array('hpcode'=>$hpcode,'patid'=>$userid));
			// empty the cache for this patient 
			$this->cacheDeleteTasks($userid,$hpcode);
			
			$edd = "";
			
			// based on ANC First, are they due for ANC follow up 
			if(isset($patient->ancfirst) && count($patient->ancfollow)==0 && !isset($patient->delivery) && count($patient->pnc)==0){
				if($patient->ancfirst->Q_APPOINTMENTDATE != ""){
					$this->cacheAddTask($userid, $hpcode, $patient->ancfirst->Q_APPOINTMENTDATE,PROTOCOL_ANCFOLLOW);
				}
				$edd = $patient->ancfirst->Q_EDD;
			}
			
			// based on ANC Follow up are they due for another 
			if(count($patient->ancfollow)>0 && !isset($patient->delivery) && count($patient->pnc)==0){
				//get the most recent ANC follow
				if($patient->ancfollow[count($patient->ancfollow)-1]->Q_APPOINTMENTDATE != ""){
					$this->cacheAddTask($userid, $hpcode, $patient->ancfollow[count($patient->ancfollow)-1]->Q_APPOINTMENTDATE,PROTOCOL_ANCFOLLOW);
				}
				$edd = $patient->ancfollow[count($patient->ancfollow)-1]->Q_EDD;
			}
			
			// get their most recent EDD to enter delivery date 
			if(!isset($patient->delivery) && count($patient->pnc)==0 && $edd != ''){
				$this->cacheAddTask($userid, $hpcode, $edd, PROTOCOL_DELIVERY);
			}
			
			// based on when due for first PNC
			if($edd != "" && !isset($patient->delivery) && count($patient->pnc)==0){
				$date = new DateTime($edd);
				$date->add(new DateInterval('P1D'));
				$this->cacheAddTask($userid, $hpcode, $date->format('Y-m-d'), PROTOCOL_PNC);
			}
			
			// if had delivery protocol but no PNC
			if(isset($patient->delivery) && count($patient->pnc)==0){
				$date = new DateTime($patient->delivery->Q_DELIVERYDATE);
				$date->add(new DateInterval('P1D'));
				$this->cacheAddTask($userid, $hpcode, $date->format('Y-m-d'), PROTOCOL_PNC);
			}
			// are they due for next PNC
			if (count($patient->pnc)>0){
				if($patient->pnc[count($patient->pnc)-1]->Q_APPOINTMENTDATE != ""){
					$this->cacheAddTask($userid, $hpcode, $patient->pnc[count($patient->pnc)-1]->Q_APPOINTMENTDATE,PROTOCOL_PNC);
				}
			}
		}
	}
	
	function cacheDeleteTasks($userid, $hpcode){
		$sql = sprintf("DELETE FROM cache_tasks WHERE userid=%d AND hpcode=%d",$userid,$hpcode);
		$this->runSql($sql);
	}
	
	function cacheAddTask($userid, $hpcode,$datedue,$protocol){
		$sql = sprintf("INSERT INTO cache_tasks (hpcode,userid,datedue,protocol)
							VALUES (%d,%d,'%s','%s')", $hpcode, $userid ,$datedue, $protocol);
		$this->runSql($sql);
	}
	
	
	function cacheRisks($days){
		// get all the patients submitted recently (according to past no $days)
		$submitted = $this->getProtocolsSubmitted(array('days'=>$days,'limit'=>'all'));
		$toupdate = array();
		foreach($submitted->protocols as $s){
			$toupdate[$s->Q_HEALTHPOINTID."/".$s->Q_USERID] = true;
		}
	
		foreach($toupdate as $k=>$v){
			$temp = explode('/',$k);
			$hpcode = $temp[0];
			$userid = $temp[1];
			//delete existing risks & risk category info
			$this->cacheDeleteRisks($userid, $hpcode);
			$this->cacheDeleteRiskCategory($userid, $hpcode);
			
			$patient = $this->getPatient(array('hpcode'=>$hpcode,'patid'=>$userid));
			
			$ra = new RiskAssessment();
			$risks = $ra->getRisks($patient);
			
			// cache the risk factors
			foreach($risks->risks as $k=>$v){
				if($v){
					$this->cacheAddRisk($userid, $hpcode,$k);
				}
			}
			
			// cache the category
			$this->cacheAddRiskCategory($userid, $hpcode, $risks->category);
		}
	}
	
	
	function cacheDeleteRisks($userid, $hpcode){
		$sql = sprintf("DELETE FROM cache_risk WHERE userid=%d AND hpcode=%d",$userid,$hpcode);
		$this->runSql($sql);
	}
	
	function cacheAddRisk($userid, $hpcode,$risk){
		$sql = sprintf("INSERT INTO cache_risk (hpcode,userid,risk)
								VALUES (%d,%d,'%s')", $hpcode, $userid ,$risk);
		$this->runSql($sql);
	}
	
	function cacheDeleteRiskCategory($userid, $hpcode){
		$sql = sprintf("DELETE FROM cache_risk_category WHERE userid=%d AND hpcode=%d",$userid,$hpcode);
		$this->runSql($sql);
	}
	
	function cacheAddRiskCategory($userid, $hpcode,$riskcat){
		$sql = sprintf("INSERT INTO cache_risk_category (hpcode,userid,riskcategory)
									VALUES (%d,%d,'%s')", $hpcode, $userid ,$riskcat);
		$this->runSql($sql);
	}
	
	function getTasksDue($opts=array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
		
		$sql = "SELECT  
						ct.datedue,
						ct.userid,
						CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
						ct.hpcode,
						php.hpname as patientlocation,
						ct.protocol
					FROM cache_tasks ct 
				INNER JOIN (SELECT DISTINCT hpcode, userid FROM cache_visit 
					WHERE (hpcode IN (".$this->getUserHealthPointPermissions().") 
					OR visithpcode IN (".$this->getUserHealthPointPermissions().") )";
		if(array_key_exists('hpcode',$opts)){
			$sql .= " AND  (hpcode = ".$opts['hpcode'];
			$sql .= " OR visithpcode = ".$opts['hpcode'].")";
		}
		$sql .= ") cv ON cv.userid = ct.userid AND cv.hpcode = ct.hpcode" ;
		$sql .= " LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON ct.userid = R.Q_USERID AND ct.hpcode = R.Q_HEALTHPOINTID";
		$sql .= " INNER JOIN healthpoint php ON php.hpcode = ct.hpcode";
		$sql .= sprintf(" WHERE ct.datedue > NOW()
					AND ct.datedue < DATE_ADD(now(), INTERVAL +%d DAY)",$days);
		$sql .= " ORDER BY datedue ASC";
	
			
		$tasks = array();
		$result = $this->runSql($sql);
		while($o = mysql_fetch_object($result)){
			array_push($tasks, $o);
		}
		return $tasks;
	}
	
	function getOverdueTasks($opts=array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
		
		$sql = "SELECT
						ct.datedue,
						ct.userid,
						CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
						ct.hpcode,
						php.hpname as patientlocation,
						ct.protocol
					FROM cache_tasks ct 
				INNER JOIN (SELECT DISTINCT hpcode, userid FROM cache_visit 
					WHERE (hpcode IN (".$this->getUserHealthPointPermissions().") 
					OR visithpcode IN (".$this->getUserHealthPointPermissions().") )";
		if(array_key_exists('hpcode',$opts)){
			$sql .= " AND  (hpcode = ".$opts['hpcode'];
			$sql .= " OR visithpcode = ".$opts['hpcode'].")";
		}
		$sql .= ") cv ON cv.userid = ct.userid AND cv.hpcode = ct.hpcode" ;
		$sql .= " LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON ct.userid = R.Q_USERID AND ct.hpcode = R.Q_HEALTHPOINTID";
		$sql .= " INNER JOIN healthpoint php ON php.hpcode = ct.hpcode";
		$sql .= sprintf(" WHERE ct.datedue < now()");
		$sql .= " ORDER BY datedue ASC";
		
		$tasks = array();
		$result = $this->runSql($sql);
		while($o = mysql_fetch_object($result)){
			array_push($tasks, $o);
		}
		return $tasks;
	}

	function getDeliveriesDue($opts=array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
	
		$sql = "SELECT
					ct.datedue,
					ct.userid,
					CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
					ct.hpcode,
					php.hpname as patientlocation,
					ct.protocol
				FROM cache_tasks ct 
					INNER JOIN (SELECT DISTINCT hpcode, userid FROM cache_visit 
						WHERE (hpcode IN (".$this->getUserHealthPointPermissions().") 
						OR visithpcode IN (".$this->getUserHealthPointPermissions().") )";
		if(array_key_exists('hpcode',$opts)){
			$sql .= " AND  (hpcode = ".$opts['hpcode'];
			$sql .= " OR visithpcode = ".$opts['hpcode'].")";
		}
		$sql .= ") cv ON cv.userid = ct.userid AND cv.hpcode = ct.hpcode" ;
		$sql .= " LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON ct.userid = R.Q_USERID AND ct.hpcode = R.Q_HEALTHPOINTID";
		$sql .= " INNER JOIN healthpoint php ON php.hpcode = ct.hpcode";
		$sql .= sprintf(" WHERE ct.datedue > NOW()
						AND ct.datedue < DATE_ADD(now(), INTERVAL +%d DAY)",$days);
		$sql .= sprintf(" AND protocol='%s'",PROTOCOL_DELIVERY);
		$sql .= " ORDER BY datedue ASC";
		
		$tasks = array();
		$result = $this->runSql($sql);
		while($o = mysql_fetch_object($result)){
			array_push($tasks, $o);
		}
		return $tasks;
	}
	function getANC1Defaulters($opts=array()){
		$kpi = new KPI();
		return $kpi->getANC1Defaulters($opts);
	}
	
	function getANC1DefaultersBestPerformer($opts=array()){
		$kpi = new KPI();
		return $kpi->getANC1DefaultersBestPerformer($opts);
	}
	
	function getANC2Defaulters($opts=array()){
		$kpi = new KPI();
		return $kpi->getANC2Defaulters($opts);
	}
	
	function getTT1Defaulters($opts=array()){
		$kpi = new KPI();
		return $kpi->getTT1Defaulters($opts);
	}

}

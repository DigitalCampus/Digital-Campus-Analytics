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
	
	function cron($flush, $days = 2){
		global $CONFIG,$LOGGER,$USER;
		
		$USER->props['permissions.admin'] = 'true';
		$USER->username = 'cron';
		
		if($flush){
			echo "Flushing Cache tables\n";
			$tables = array('cache_datacheck','cache_risk','cache_risk_category', 'cache_tasks' ,'cache_visit', 'patientcurrent' );
			
			foreach($tables as $t){
				$sql = sprintf("TRUNCATE `%s` ; ",$t);
				$this->runSql($sql);
			}
			//set days to be from begining of project (1st Nov 2011)
			$start = new DateTime('1 Nov 2011');
			$today = new DateTime();
			$days = $today->diff($start)->days;
		}
		
		
		echo "Updating patients\n";
		$this->updatePatients();
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		// clear up log table
		$logdays = $CONFIG->props['log.archive.days'];
		if($logdays > 0){
			$sql = sprintf("DELETE FROM log WHERE logtime < DATE_ADD(NOW(), INTERVAL -%d DAY)",$logdays);
			$this->runSql($sql);
			echo "Archived log\n";
		}
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		// update & cache which HPs the patients have visited
		// get all submitted protocols in last $days
		$submitted = $this->getProtocolsSubmitted(array('days'=>$days,'limit'=>'all'));
		
		foreach($submitted->protocols as $s){
			$this->cacheAddPatientHealthPointVisit($s->Q_USERID,$s->patienthpcode,$s->protocolhpcode,$s->datestamp,$s->protocol,$s->user_uri);
		}
		
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		// update & cache task list
		$this->cacheTasksDue($days);
		echo "cached tasks due\n";
		
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		// remove any really old overdue tasks based on the ignore policy
		$sql = sprintf("DELETE FROM cache_tasks
						WHERE datedue < DATE_ADD(NOW(), INTERVAL -%d DAY)",$CONFIG->props['overdue.ignore']);
		$this->runSql($sql);
		echo "removed old overdue tasks\n";
		
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		// update & cache patient risk factors
		$this->cacheRisks($days);
		echo "cached risks\n";
		
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		// run the data checks and update accordingly
		$dc = new DataCheck();
		$dc->updateCache();
		echo "cached data checks\n";
		
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
		if($dc->summary()){
			$this->setSystemProperty('datacheck.errors','true');
			echo "data errors = true\n";
		} else {
			$this->setSystemProperty('datacheck.errors','false');
			echo "data errors = false\n";
		}
		
		echo "Queries so far: ".$LOGGER->mysql_queries_count."\n";
		flush_buffers();
		
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
						d.dname,
						d.did
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
						d.dname,
						d.did
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
	
	function getSystemProperties(){
		$sql = "SELECT * FROM properties";
		$result = $this->runSql($sql);
		$props = array();
		while($o = mysql_fetch_object($result)){
			$props[$o->propname] = $o->propvalue;
		}
		return $props;
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
		$sql = sprintf("SELECT userid FROM user WHERE username='%s' AND password=md5('%s') and useractive=true",$username,$password);
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
	function getUserHealthPointPermissions($returnsql=false, $returnarray= false){
		global $USER;
		
		if($USER->getProp('permissions.admin') == 'true'){
			// admin user can view everything
			$sql = "SELECT hpcode FROM healthpoint WHERE hpactive = true";
		} else if($USER->getProp('permissions.viewall') == 'true'){
			// "permissions.all" can view all districts & healthpoints, but aren't admin users (can't view logs, edit users etc)
			$sql = "SELECT hpcode FROM healthpoint WHERE hpactive = true";
		} else if($USER->getProp('permissions.districts') != null) {
			// "permissions.districts" can view all the districts listed
			$sql = sprintf("SELECT hpcode FROM healthpoint WHERE did IN (%s) AND hpactive = true",$USER->getProp('permissions.districts'));
		} else if($USER->getProp('permissions.healthpoints')!= null) {
			// "permissions.healthpoints" can view all the healthpoints listed
			$sql = sprintf("SELECT hpcode FROM healthpoint WHERE hpid IN (%s) AND hpactive = true",$USER->getProp('permissions.healthpoints'));
		} else {
			//otherwise can only see the date from the hpid in their user table record (hpid) 
			$sql = sprintf("SELECT hpcode FROM healthpoint WHERE hpid = %d AND hpactive = true",$USER->hpid);
		}
		$sql .= " ORDER BY hpname ASC";
		
		if($returnsql){
			return $sql;
		}
		
		$result = $this->runSql($sql);
		if(!$result){
			return "-1";
		}
		$temp = array();
		while($o = mysql_fetch_object($result)){
			array_push($temp,$o->hpcode);
		}
		if($returnarray){
			return $temp;
		}
		$hpcodes = implode(",",$temp);
		
		// if hpcodes is empty, set it to -1 (this prevents errors where querying "IN ()", so instead it's "IN( -1)"
		if($hpcodes == ""){
			$hpcodes = "-1";
		}
		return $hpcodes;
	}
	
	function isDemoUser(){
		global $USER;
		if ($USER->username == 'demo'){
			return true;
		} else {
			return false;
		}
	}
	
	// get the list of ignored health points
	function getIgnoredHealthPoints($cron = false){
		if($this->isDemoUser() && $cron == false){
			return "";
		} else {
			return IGNORE_HEALTHPOINTS;
		}
	}
	
	
	/*
	 * 
	 */
	function writeLog($loglevel,$userid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries,$logagent){
		$sql = sprintf("INSERT INTO log (loglevel,userid,logtype,logmsg,logip,logpagephptime,logpagemysqltime,logpagequeries,logagent) 
						VALUES ('%s',%d,'%s','%s','%s',%f,%f,%d,'%s')", 
						$loglevel,$userid,$logtype,mysql_real_escape_string($logmsg),$ip,$logpagephptime,$logpagemysqltime,$logpagequeries,$logagent);
		// just run sql directly (without using $this->runSql) so doesn't try to log an error writing to the log!
		mysql_query($sql,$this->DB);
	}
	
	// return list of Health posts
	function getHealthPoints($getall = false){
		if($getall){
			$sql = "SELECT * FROM healthpoint WHERE hpactive = true ORDER BY hpname ASC;";
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
	
	
	function getCohortHealthPoints($aslist = false,$hpcodes = false){
		if(!$hpcodes){
			$sql = sprintf("SELECT * FROM healthpoint hp
							INNER JOIN district d ON d.did = hp.did
							WHERE d.did IN (SELECT did 
											FROM healthpoint 
											WHERE hpcode IN (%s))
							AND hpactive = true
							ORDER BY hp.hpname ASC",
							$this->getUserHealthPointPermissions());
		} else {
			$sql = sprintf("SELECT * FROM healthpoint hp
										INNER JOIN district d ON d.did = hp.did
										WHERE d.did IN (SELECT did 
														FROM healthpoint 
														WHERE hpcode IN (%s) AND hpcode IN (%s))
										AND hpactive = true
										ORDER BY hp.hpname ASC",
			$this->getUserHealthPointPermissions(),$hpcodes);
		}
		
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
						WHERE hp.did=%d 
						AND hpactive = true
						ORDER BY hp.hpname ASC", $did);
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
		if($this->getIgnoredHealthPoints(true) != ""){
			$sql .= sprintf(" AND i.Q_HEALTHPOINTID NOT IN (%s)",$this->getIgnoredHealthPoints(true));
		}
		$result = $this->runSql($sql);
		
		// archive any patients who either:
		// - are past 1 month of delivery 
		// - 2 month past the EDD (if no delivery)
		// - have had termination protocol entered
		$pats = $this->getCurrentPatients(); 
		$today = new DateTime();
		
		foreach($pats as $pat){
			$archive = false;
			// past 1 month of delivery
			if(isset($pat->delivery)){
				$deldate = new DateTime($pat->delivery->CREATEDON);
				$deldate->add(new DateInterval('P1M'));
				if($today > $deldate){
					$archive = true;
				}
			} else {
				// 2 month past the EDD (no delivery)
				unset($edd);
				if(count($pat->anc) > 0){
					$edd = new DateTime($pat->anc[(count($pat->anc)-1)]->Q_EDD);
				}
				if(isset($edd)){
					$edd->add(new DateInterval('P2M'));
					if($today > $edd){
						$archive = true;
					}
				} 
			}
			
			if(isset($pat->termination)){
				$archive = true;
			}
			
			if($archive){
				$sql = sprintf("UPDATE patientcurrent SET pcurrent = false where hpcode=%d AND patid=%d",$pat->Q_HEALTHPOINTID,$pat->Q_USERID);
				$this->runSql($sql);
			}
		}
		
		
	}
	
	/*
	 * returns an array of current patients for given user
	 */
	function getCurrentPatients($opts=array()){
		
		if(array_key_exists('hps',$opts)){
			$hps = $opts['hps'];
		} else {
			$hps = $this->getUserHealthPointPermissions();
		}
		
		$sql = sprintf("SELECT 	r.Q_HEALTHPOINTID,
						r.Q_USERID,
						pc.pcurrent
				FROM %s r
				INNER JOIN healthpoint pathp ON pathp.hpcode = r.Q_HEALTHPOINTID
				INNER JOIN patientcurrent pc ON pc.hpcode = r.Q_HEALTHPOINTID AND pc.patid = r.Q_USERID
				WHERE r.Q_HEALTHPOINTID IN (%s) 
				AND pc.pcurrent = 1",TABLE_REGISTRATION,$hps) ;
		
		if($this->getIgnoredHealthPoints(true) != ""){
			$sql .= sprintf(" AND r.Q_HEALTHPOINTID NOT IN (%s)",$this->getIgnoredHealthPoints(true));
		}
		
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
		$sql = "SELECT 	p.Q_HEALTHPOINTID as patienthpcode,
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
						_CREATION_DATE AS CREATEDON,
						TODAY
				FROM ".TABLE_REGISTRATION." p
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE p.Q_HEALTHPOINTID = '".$opts['hpcode']."' and p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (p.Q_HEALTHPOINTID IN (".$this->getUserHealthPointPermissions().") " ;
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
		$pat->anc = $this->getPatientANC($opts);
		$pat->anclabtest= $this->getPatientANCLabTest($opts);
		$pat->delivery = $this->getPatientDelivery($opts);
		$pat->pnc = $this->getPatientPNC($opts);
		
		// if patient record has no protocols then assume invalid patientid/hpcode 
		if($pat->regcomplete == false
			&& count($pat->anc) == 0
			&& count($pat->anclabtest) == 0
			&& $pat->delivery == null
			&& count($pat->pnc) == 0){
			return false;
		} else {
			return $pat;
		}		
	}
	
	
	private function getPatientANC($opts=array()){
		$sql = "SELECT p.Q_HEALTHPOINTID as patienthpcode,
								hp.hpcode as protocolhpcode,
								CONCAT(u.firstname,' ',u.lastname) as submittedname,
								_URI,
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
								Q_FIRSTVISIT,
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
								Q_USERNAME,
								Q_VACUUMDELIVERY,
								Q_WEIGHT,
								Q_YEAROFBIRTH,
								Q_YOUNGESTCHILD,
								_CREATION_DATE AS CREATEDON,
								TODAY
						FROM ".TABLE_ANC." p
						INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
						INNER JOIN healthpoint hp ON u.hpid = hp.hpid
						WHERE p.Q_HEALTHPOINTID = '".$opts['hpcode']."' AND p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= sprintf(" AND (p.Q_HEALTHPOINTID IN (%s) ",$this->getUserHealthPointPermissions(true));
		$sql .= sprintf(" OR hp.hpcode IN (%s)) ",$this->getUserHealthPointPermissions(true));
		$sql .= " ORDER BY TODAY ASC";
		
		$result = $this->runSql($sql);
		$protocols = array();
		$count=0;
		while($pat = mysql_fetch_object($result)){
			
			$pat->Q_FPMETHOD = array();
			// get the Home applicances source
			$appsql = "SELECT VALUE FROM ".TABLE_ANC_FPMETHOD." WHERE _PARENT_AURI = '".$pat->_URI."'";
			$appresult = $this->runSql($appsql);
			
			while($app = mysql_fetch_object($appresult)){
				array_push($pat->Q_FPMETHOD,$app->VALUE);
			}
			
			$pat->Q_WHOATTENDED = array();
			// get the Home applicances source
			$appsql = "SELECT VALUE FROM ".TABLE_ANC_ATTENDED ." WHERE _PARENT_AURI = '".$pat->_URI."'";
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
		$sql = "SELECT 	p.Q_HEALTHPOINTID as patienthpcode,
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
						_CREATION_DATE AS CREATEDON,
						TODAY
				FROM ".TABLE_ANCLABTEST." p
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE p.Q_HEALTHPOINTID = '".$opts['hpcode']."' and p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (p.Q_HEALTHPOINTID IN (".$this->getUserHealthPointPermissions().") " ;
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
		$sql = "SELECT 	p.Q_HEALTHPOINTID as patienthpcode,
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
						_CREATION_DATE AS CREATEDON,
						TODAY
				FROM ".TABLE_DELIVERY." p
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid
				WHERE p.Q_HEALTHPOINTID = '".$opts['hpcode']."' and p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (p.Q_HEALTHPOINTID IN (".$this->getUserHealthPointPermissions().") " ;
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
			$o->Q_BABIES = $this->getPatientDeliveryBaby($o->_URI);
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
		$sql = "SELECT 	p.Q_HEALTHPOINTID as patienthpcode,
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
							_CREATION_DATE AS CREATEDON,
							TODAY
					FROM ".TABLE_PNC." p
					INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid
					WHERE p.Q_HEALTHPOINTID = '".$opts['hpcode']."' and p.Q_USERID='".$opts['patid']."'";
		// add permissions
		$sql .= " AND (p.Q_HEALTHPOINTID IN (".$this->getUserHealthPointPermissions().") " ;
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
					p.Q_HEALTHPOINTID as patienthpcode,
					hp.hpcode as protocolhpcode,
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
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}

		// anc
		$sql .= " UNION
				SELECT 
					p._CREATION_DATE as datestamp,
					p.Q_USERID,
					CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
					p.Q_HEALTHPOINTID as patienthpcode,
					hp.hpcode as protocolhpcode,
					'".PROTOCOL_ANC."' as protocol,
					CONCAT(u.firstname,' ',u.lastname) as submittedname,
					u.userid,
					p.Q_GPSDATA_LAT,
					p.Q_GPSDATA_LNG,
					p.Q_LOCATION,
					hp.locationlat,
					hp.locationlng,
					u.user_uri 
				FROM ".TABLE_ANC." p 
				LEFT OUTER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = p.Q_USERID AND r.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID)
				INNER JOIN user u ON p._CREATOR_URI_USER = u.user_uri 
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
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
					p.Q_HEALTHPOINTID as patienthpcode,
					hp.hpcode as protocolhpcode,
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
				INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
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
						p.Q_HEALTHPOINTID as patienthpcode,
						hp.hpcode as protocolhpcode,
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
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
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
						p.Q_HEALTHPOINTID as patienthpcode,
						hp.hpcode as protocolhpcode,
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
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		if(isset($days)){
			$sql .= sprintf(" WHERE p._CREATION_DATE >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" WHERE p._CREATION_DATE > '%s'",$startdate);
			$sql .= sprintf(" AND  p._CREATION_DATE <= '%s'",$enddate);
		}
		
		
		$sql .= ") a ";
		$sql .= sprintf("WHERE (a.patienthpcode IN (%s) ", $this->getUserHealthPointPermissions(true));
		$sql .= sprintf("OR a.protocolhpcode IN (%s)) ",$this->getUserHealthPointPermissions());
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= sprintf(" AND a.patienthpcode NOT IN (%s)",$this->getIgnoredHealthPoints());
		}
		if(array_key_exists('hpcode',$opts)){
			$sql .= " AND  (a.patienthpcode = ".$opts['hpcode'];
			$sql .= " OR a.protocolhpcode = ".$opts['hpcode'].")";
		}
		$sql .= " ORDER BY datestamp DESC";
		
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
		global $CONFIG;
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
			if(is_numeric($opts['limit'])){
				$limit = max(0,$opts['limit']);
			} else {
				$limit = $opts['limit'];
			}
		} else {
			$limit = DEFAULT_LIMIT;
		}
		if(array_key_exists('start',$opts)){
			$start = max($opts['start'],0);
		} else {
			$start = DEFAULT_START;
		}
	
		$sql = "SELECT cv.visitdate as datestamp,
							cv.userid AS Q_USERID,
							CONCAT(r.Q_USERNAME,' ',r.Q_USERFATHERSNAME,' ',r.Q_USERGRANDFATHERSNAME) as patientname,
							cv.hpcode as patienthpcode,
							hp.hpcode as protocolhpcode,
							cv.protocol,
							CONCAT(u.firstname,' ',u.lastname) as submittedname,
							cv.user_uri 
					FROM cache_visit cv 
					INNER JOIN ".TABLE_REGISTRATION." r ON (r.Q_USERID = cv.userid AND r.Q_HEALTHPOINTID = cv.hpcode)
					INNER JOIN user u ON cv.user_uri = u.user_uri 
					INNER JOIN healthpoint hp ON u.hpid = hp.hpid";
		$sql .= " WHERE 1 = 1";
		if($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND cv.hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		if(isset($days)){
			$sql .= sprintf(" AND cv.visitdate >= DATE_ADD(NOW(), INTERVAL -%d DAY)",$days);
		} else {
			$sql .= sprintf(" AND cv.visitdate > '%s'",$startdate);
			$sql .= sprintf(" AND cv.visitdate <= '%s'",$enddate);
		}
		
		$sql .= " AND (cv.hpcode IN (".$this->getUserHealthPointPermissions(true).") " ;
		$sql .= " OR hp.hpcode IN (".$this->getUserHealthPointPermissions(true).")) " ;
		
		if(array_key_exists('hpcodes',$opts)){
			$sql .= " AND cv.hpcode IN (".$opts['hpcodes'].")";
		}
		$sql .= "ORDER BY cv.visitdate DESC";
		
		//query to get the total no of records
		$countsql = "SELECT COUNT(*) AS norecords, protocol FROM (".$sql.") a group by protocol;";
		
		$countres = $this->runSql($countsql);
		
		$submitted = new stdClass();
		
		$submitted->count['protocol.total'] = 0;
		$submitted->count[PROTOCOL_ANC] = 0;
		$submitted->count[PROTOCOL_DELIVERY] = 0;
		$submitted->count[PROTOCOL_PNC] = 0;
		while($row = mysql_fetch_object($countres)){
			$submitted->count[$row->protocol] = $row->norecords;
			$submitted->count['protocol.total'] += $row->norecords;
		}
		
		//add in the targets
		if(array_key_exists('nohps',$opts)){
			$submitted->target['protocol.total'] = $CONFIG->props['target.protocols']*$opts['nohps'];
			$submitted->target[PROTOCOL_ANC] = $CONFIG->props['target.ancsubmitted']*$opts['nohps'];
			$submitted->target[PROTOCOL_DELIVERY] = $CONFIG->props['target.deliverysubmitted']*$opts['nohps'];
			$submitted->target[PROTOCOL_PNC] = $CONFIG->props['target.pncsubmitted']*$opts['nohps'];
		}
		
		$submitted->start = max(min($submitted->count['protocol.total']-1,$start),0);
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
		// delete tasks cache for those who are no longer current patients
		$sql = "SELECT taskid FROM cache_tasks ct
				LEFT OUTER JOIN patientcurrent pc ON pc.hpcode = ct.hpcode AND pc.patid = ct.userid
				WHERE pc.pcurrent = false";
		$result = $this->runSql($sql);
		while($o = mysql_fetch_object($result)){
			$delsql = sprintf("DELETE FROM cache_tasks WHERE taskid=%d",$o->taskid);
			$this->runSql($delsql);
		}
		
		// get all the patients submitted recently (according to past no $days)
		$submitted = $this->getProtocolsSubmitted(array('days'=>$days,'limit'=>'all'));
		$toupdate = array();
		foreach($submitted->protocols as $s){
			$toupdate[$s->patienthpcode."/".$s->Q_USERID] = true;
		}
		
		foreach($toupdate as $k=>$v){
			$temp = explode('/',$k);
			$hpcode = $temp[0];
			$userid = $temp[1];
			$patient = $this->getPatient(array('hpcode'=>$hpcode,'patid'=>$userid));
			// empty the cache for this patient 
			$this->cacheDeleteTasks($userid,$hpcode);

			$edd = "";
			
			// based on ANC Follow up are they due for another 
			if(count($patient->anc)>0 && !isset($patient->delivery) && count($patient->pnc)==0){
				//get the most recent ANC follow
				if($patient->anc[count($patient->anc)-1]->Q_APPOINTMENTDATE != ""){
					$this->cacheAddTask($userid, $hpcode, $patient->anc[count($patient->anc)-1]->Q_APPOINTMENTDATE,PROTOCOL_ANC);
				}
				$edd = $patient->anc[count($patient->anc)-1]->Q_EDD;
			}
			
			// get their most recent EDD to enter delivery date 
			if(!isset($patient->delivery) && count($patient->pnc)==0 && $edd != ''){
				$this->cacheAddTask($userid, $hpcode, $edd, PROTOCOL_DELIVERY);
				
				// if they are due for Lab test
				if(!isset($patient->labtest) && count($patient->anc)>0){
					$date = new DateTime($patient->anc[count($patient->anc)-1]->CREATEDON);
					$date->add(new DateInterval('P4M'));
				
					//if this date is greater than the delivery, make it the day before delivery
					if($date > $edd){
						$date = new DateTime($edd);
					}
					$this->cacheAddTask($userid, $hpcode, $date->format('Y-m-d'), PROTOCOL_ANCLABTEST);
				}
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
			$toupdate[$s->patienthpcode."/".$s->Q_USERID] = true;
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
						ct.hpcode as patienthpcode,
						ct.protocol
					FROM cache_tasks ct 
				INNER JOIN (SELECT DISTINCT hpcode, userid FROM cache_visit 
					WHERE (hpcode IN (".$this->getUserHealthPointPermissions(true).") 
					OR visithpcode IN (".$this->getUserHealthPointPermissions(true).") )";
		if ($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND  hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		if(array_key_exists('hpcodes',$opts)){
			$sql .= " AND  (hpcode IN (".$opts['hpcodes'].")";
			$sql .= " OR visithpcode IN (".$opts['hpcodes']."))";
		}
		$sql .= ") cv ON cv.userid = ct.userid AND cv.hpcode = ct.hpcode" ;
		$sql .= " LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON ct.userid = R.Q_USERID AND ct.hpcode = R.Q_HEALTHPOINTID";
		$sql .= sprintf(" WHERE ct.datedue >= DATE_FORMAT(NOW(),'%%Y-%%m-%%d 00:00:00')
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
						ct.hpcode as patienthpcode,
						ct.protocol
					FROM cache_tasks ct 
				INNER JOIN (SELECT DISTINCT hpcode, userid FROM cache_visit 
					WHERE (hpcode IN (".$this->getUserHealthPointPermissions(true).") 
					OR visithpcode IN (".$this->getUserHealthPointPermissions(true).") )";
		if ($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND  hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		if(array_key_exists('hpcodes',$opts)){
			$sql .= " AND  (hpcode IN (".$opts['hpcodes'].")";
			$sql .= " OR visithpcode IN ( ".$opts['hpcodes']."))";
		}
		$sql .= ") cv ON cv.userid = ct.userid AND cv.hpcode = ct.hpcode" ;
		$sql .= " LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON ct.userid = R.Q_USERID AND ct.hpcode = R.Q_HEALTHPOINTID";
		$sql .= sprintf(" WHERE ct.datedue < DATE_FORMAT(NOW(),'%%Y-%%m-%%d 00:00:00')
							AND ct.datedue > DATE_ADD(now(), INTERVAL -%d DAY)",$days);
		$sql .= " ORDER BY datedue DESC";
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
					ct.hpcode as patienthpcode,
					ct.protocol
				FROM cache_tasks ct 
					INNER JOIN (SELECT DISTINCT hpcode, userid FROM cache_visit 
						WHERE (hpcode IN (".$this->getUserHealthPointPermissions().") 
						OR visithpcode IN (".$this->getUserHealthPointPermissions().") )";
		if ($this->getIgnoredHealthPoints() != ""){
			$sql .= " AND  hpcode NOT IN (".$this->getIgnoredHealthPoints().")";
		}
		if(array_key_exists('hpcodes',$opts)){
			$sql .= " AND  (hpcode IN (".$opts['hpcodes'].")";
			$sql .= " OR visithpcode IN (".$opts['hpcodes']."))";
		}
		$sql .= ") cv ON cv.userid = ct.userid AND cv.hpcode = ct.hpcode" ;
		$sql .= " LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON ct.userid = R.Q_USERID AND ct.hpcode = R.Q_HEALTHPOINTID";
		$sql .= sprintf(" WHERE ct.datedue >= DATE_FORMAT(NOW(),'%%Y-%%m-%%d 00:00:00')
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
	
	function getPNC1Defaulters($opts=array()){
		$kpi = new KPI();
		return $kpi->getPNC1Defaulters($opts);
	}

}

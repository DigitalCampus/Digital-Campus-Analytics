<?php


function userLogin($username,$password){
	global $USER,$MSG,$API;
    clearSession();
    
    if($password == ""){
    	array_push($MSG,getstring('warning.login.nopassword'));
        return false;
    }   
    
    $USER = new User($username);
    $USER->setUsername($username);
    if ($USER instanceof User)  {
            $passwordCheck = $USER->validPassword($password);
            if($passwordCheck){
                createSession($USER);
                setLang($USER->getProp('lang'));
                $API->setUserProperty($USER->userid,'lastlogin',date('Y-m-d H:i:s'));
                writeToLog('info','login','user logged in: '.$username);
                return true;
            } else {
            	array_push($MSG,getstring('warning.login.invalid'));
            	writeToLog('info','loginfailure','username: '.$username);
                return false;   
            }       
    } else {
        return false;   
    }   
}   


/**
 * Start a session
 *
 * @return string | false
 */ 
function startSession($time = 99999999, $ses = 'OpenQuiz') {
    ini_set('session.cache_expire', $time);
    session_set_cookie_params($time);
    session_name($ses);
    session_start();
    
    // Reset the expiration time upon page load
    if (isset($_COOKIE[$ses])){
    	setcookie($ses, $_COOKIE[$ses], time() + $time, "/");
    }
}
/**
 * Clear all session variables
 * 
 */ 
 function clearSession() {
    $_SESSION["session_username"] = "";  
    setcookie("user","",time()-3600, "/");                                  
 } 
 
 /**
  * Create the user session details.
  */
 function createSession($user) {
    $_SESSION["session_username"] = $user->getUsername();
    setcookie("user",$user->getUsername(),time()+99999999,"/");                   
 }

/**
 * Check that the session is active and valid for the user passed.
 */
function validateSession($username) {
	try {
		//if (startSession()) {
			if ($_SESSION["session_username"] == $username) {
				return "OK";
			} else {
				return "Session Invalid";
		    }
		//} else {
		//	return error_get_last();
		//}
	} catch(Exception $e) {
		return "Session Invalid";
	}		
}
 
 
 
/**
 * Checks if current user is logged in
 * if not, they get redirected to homepage 
 * 
 */
function checkLogin($prefix=""){
    global $USER,$CONFIG;
    $url = "http" . ((!empty($_SERVER["HTTPS"])) ? "s" : "") . "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    if(!isset($USER->username)){
        header('Location: '.$CONFIG->homeAddress.$prefix.'login.php?ref='.urlencode($url));  
        die; 
    }
}



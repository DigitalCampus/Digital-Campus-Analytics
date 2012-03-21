<?php
include_once "config.php";
$PAGE="index";
$HEADER = "<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
$HEADER .= '<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>';
//$BODY_ATT = 'onunload="GUnload()" onload="initialize()"';

include_once "includes/header.php";
$days = optional_param("days",31,PARAM_INT);

//get user profile and display dashboard accordingly
$profile = 'default';
if(isset($USER->props['profile'])){
	$profile = $USER->props['profile'];
	if(!file_exists($CONFIG->homePath."profile/".$profile.".php")){
		$profile = "default";
		$API->setUserProperty($USER->userid, 'profile', $profile);
	}
} else {
	$API->setUserProperty($USER->userid, 'profile', $profile);
}
include_once 'profile/'.$profile.".php";

include_once "includes/footer.php";
?>
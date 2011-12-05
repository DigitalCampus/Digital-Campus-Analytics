<?php
require_once "../config.php";
require_once "../includes/header.php";

// only allow access by admins
if($USER->getProp('isadmin') != "true"){
	writeToLog('warning','adminpage','accessdenied');
	echo getString ("warning.accessdenied");
	die;
}
include_once('../includes/menu-admin.php');
?>





<?php 
include_once "../includes/footer.php";
?>
<?php
require_once "config.php";
$PAGE = "datacheck";
require_once "includes/header.php";

include_once('includes/datacheck/duplicate-reg.php');
include_once('includes/datacheck/unregistered.php');
include_once('includes/datacheck/missing-protocols.php');
include_once('includes/datacheck/duplicate-protocols.php');


include_once "includes/footer.php";

?>
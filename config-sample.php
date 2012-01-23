<?php

unset($CONFIG);
$CONFIG = new stdClass;

// include trailing slashes
$CONFIG->homeAddress = "http://localhost/scorecard/";
$CONFIG->homePath = "/home/alex/data/websites/scorecard/";

// this must be a writable directory
$CONFIG->imagecache = $CONFIG->homePath."images/cache/";

$CONFIG->dbtype = "mysql";
$CONFIG->dbhost = "localhost";
$CONFIG->dbname = "****";
$CONFIG->dbuser = "****";
$CONFIG->dbpass = "****"; 

include_once("setup.php");


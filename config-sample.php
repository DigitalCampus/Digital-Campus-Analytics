<?php

unset($CONFIG);
$CONFIG = new stdClass;

// include trailing slashes
$CONFIG->homeAddress = "http://localhost/scorecard/";
$CONFIG->homePath = "/home/alex/data/websites/scorecard/";

// this must be a writable directory
$CONFIG->imagecache = $CONFIG->homePath."images/cache/";

$CONFIG->langs = array("en"=>"English", "am"=>"Amharic", "ti"=>"Tigrinya");
$CONFIG->defaultlang = "en";

$CONFIG->dbtype = "mysql";
$CONFIG->dbhost = "localhost";
$CONFIG->dbname = "****";
$CONFIG->dbuser = "****";
$CONFIG->dbpass = "****"; 

$CONFIG->googleanalytics = "*******";

include_once("setup.php");


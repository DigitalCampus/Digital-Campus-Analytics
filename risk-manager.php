<?php
include_once "config.php";
echo "page under development";
die;
$PAGE = "riskman";
$HEADER ="<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
include_once "includes/header.php";
$view = optional_param('view','list',PARAM_TEXT);

printf("<h2>%s</h2>", getString("riskmanager.title"));
printf("<p>%s", getString("riskmanager.view.viewas"));
if ($view == "list"){
	printf("<span class='selected'>%s</span>",getString('riskmanager.view.detail'));
} else {
	printf("<a href='?view=list'>%s</a>",getString('riskmanager.view.detail'));
}
printf(" | ");
if ($view == "piereason"){
	printf("<span class='selected'>%s</span>",getString('riskmanager.view.reason'));
} else {
	printf("<a href='?view=piereason'>%s</a>",getString('riskmanager.view.reason'));
}
printf(" | ");
if ($view == "pielocation"){
	printf("<span class='selected'>%s</span>",getString('riskmanager.view.location'));
} else {
	printf("<a href='?view=pielocation'>%s</a>",getString('riskmanager.view.location'));
}
printf(" | ");
if ($view == "bypatient"){
	printf("<span class='selected'>%s</span>",getString('riskmanager.view.bypatient'));
} else {
	printf("<a href='?view=bypatient'>%s</a>",getString('riskmanager.view.bypatient'));
}
printf("</p><br/>");

$atRisk = $API->getCurrentPatients();

if ($view == "list"){
	include_once('includes/riskman/list.php');
} else if ($view == "piereason"){
	$options = Array('height'=>500,'width'=>800,'class'=>'graph');
	include_once('includes/riskman/reasondist.php');
} else if ($view == "pielocation"){
	$options = Array('height'=>500,'width'=>800,'class'=>'graph');
	include_once('includes/riskman/location.php');
} else if ($view == "bypatient"){
	$options = Array('height'=>500,'width'=>800,'class'=>'graph');
	include_once('includes/riskman/bypatient.php');
} else {
	echo getString("warning.viewnotfound");
}
include_once "includes/footer.php";
?>
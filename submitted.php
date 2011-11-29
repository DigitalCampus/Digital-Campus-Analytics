<?php
include_once "config.php";
$PAGE = "submitted";
$HEADER = "<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
$HEADER .= '<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>';
include_once "includes/header.php";
 
$days = optional_param("days",7,PARAM_INT);
$view = optional_param('view','list',PARAM_TEXT);
$start = optional_param('start',0,PARAM_INT);
$limit = optional_param('limit',50,PARAM_INT);

printf("<h2>%s</h2>", getString("submitted.title"));

// display options for selecting timespan
printf("<p>%s", getString("submitted.timespan"));
$dayopts = Array(7,14,31);
for($i=0; $i<count($dayopts);$i++){
	if($days == $dayopts[$i]){
		printf("<span class='selected'>%s</span>",getstring("submitted.nodays",array($dayopts[$i])));
	} else {
		printf("<a href='?days=%d&view=%s'>%s</a>",$dayopts[$i],$view,getstring("submitted.nodays",array($dayopts[$i])));
	}
	if($i != count($dayopts)-1){
		printf(" | ");
	}
}
printf("</p>");

//display view options
printf("<p>%s", getString("submitted.view.viewas"));
if ($view == "list"){
	printf("<span class='selected'>%s</span>",getString('submitted.view.detail'));
} else {
	printf("<a href='?view=list&days=%d'>%s</a>",$days,getString('submitted.view.detail'));
}
printf(" | ");
if ($view == "byprotocol"){
	printf("<span class='selected'>%s</span>",getString('submitted.view.byprotocol'));
} else {
	printf("<a href='?view=byprotocol&days=%d'>%s</a>",$days,getString('submitted.view.byprotocol'));
}
printf(" | ");
if ($view == "bylocation"){
	printf("<span class='selected'>%s</span>",getString('submitted.view.location'));
} else {
	printf("<a href='?view=bylocation&days=%d'>%s</a>",$days,getString('submitted.view.location'));
}
printf("</p><br/>");

$options = Array('height'=>500,'width'=>800,'class'=>'graph');
if ($view == "list"){
	include_once("includes/submitted/list.php");
} else if ($view == "byprotocol"){
	include_once("includes/submitted/byprotocol.php");
} else if ($view == "bylocation"){
	include_once("includes/submitted/bylocation.php");
} else {
	echo getString("warning.viewnotfound");
} 
include_once "includes/footer.php";
?>
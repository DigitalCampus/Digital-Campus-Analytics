<?php
include_once "config.php";
$PAGE = "taskman";
$BODY_ATT = 'onload="onLoad();" onresize="onResize();"';
$HEADER = "<script>
	Timeline_ajax_url='lib/timeline_2.3.0/timeline_ajax/simile-ajax-api.js';
	Timeline_urlPrefix='lib/timeline_2.3.0/timeline_js/';       
	Timeline_parameters='bundle=true';
</script>
<script src='lib/timeline_2.3.0/timeline_js/timeline-api.js' type='text/javascript'></script>";

echo "page under development";
die;

include_once "includes/header.php";
$duein = optional_param("duein",7,PARAM_INT);
$view = optional_param('view','list',PARAM_TEXT);

printf("<h2>%s</h2>", getString("taskmanager.title"));

// display options for selecting timespan
printf("<p>%s", getString("taskmanager.timespan"));
$duespans = Array(7,14,31);
for($i=0; $i<count($duespans);$i++){
	if($duein == $duespans[$i]){
		printf("<span class='selected'>%d days</span>",$duespans[$i]);
	} else {
		printf("<a href='?duein=%d&view=%s'>%d days</a>",$duespans[$i],$view,$duespans[$i]);
	}
	if($i != count($duespans)-1){
		printf(" | ");
	}
}
printf("</p>");

//display view options
printf("<p>%s", getString("taskmanager.view.viewas"));
if ($view == "list"){
	printf("<span class='selected'>%s</span>",getString('taskmanager.view.list'));
} else {
	printf("<a href='?view=list&duein=%d'>%s</a>",$duein,getString('taskmanager.view.list'));
}
printf(" | ");
if ($view == "timeline"){
	printf("<span class='selected'>%s</span>",getString('taskmanager.view.timeline'));
} else {
	printf("<a href='?view=timeline&duein=%d'>%s</a>",$duein,getString('taskmanager.view.timeline'));
}
printf(" | ");
if ($view == "healthpost"){
	printf("<span class='selected'>%s</span>",getString('taskmanager.view.healthpost'));
} else {
	printf("<a href='?view=healthpost&duein=%d'>%s</a>",$duein,getString('taskmanager.view.healthpost'));
}
printf("</p><br/>");

$tasks = $API->getAllTasks($duein);

if ($view == "list"){
	include_once('includes/taskman/list.php');
} else if ($view == "timeline"){
	include_once('includes/taskman/timeline.php');
} else if ($view == "healthpost"){
	include_once('includes/taskman/healthpost.php');
} else {
	echo getString("warning.viewnotfound");
}
include_once "includes/footer.php";
?>

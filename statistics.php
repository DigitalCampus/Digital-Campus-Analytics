<?php
include_once "config.php";
$PAGE="stats";
include_once "includes/header.php";
$stats = array ('atrisk' =>'At Risk');

$stat = optional_param('stat','atrisk',PARAM_TEXT);


//select KPI....
$counter = 1;
foreach ($stats as $k=>$v){
	if ($k == $stat){
		printf("<span class='selected'>%s</span>",$v);
	} else {
		printf("<a href='?kpi=%s'>%s</a>",$k,$v);
	}
	if($counter < count($stats)){
		echo " | ";
	}
	$counter++;
}

if ($stat == "atrisk"){
	$viewopts = array('height'=>400,'width'=>500,'class'=>'graph','comparison'=>true);
	
	include_once "includes/statistics/atrisk.php";
}


include_once "includes/footer.php";
?>
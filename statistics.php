<?php
include_once "config.php";
$PAGE="stats";
include_once "includes/header.php";
$stats = array ('atrisk' =>'At Risk',
				'anc1defaulters'=>"ANC 1 Non Defaulters",
				'anc2defaulters'=>"ANC 2 Non Defaulters",
				'pnc1defaulters'=>"PNC 1 Non Defaulters");

$stat = optional_param('stat','atrisk',PARAM_TEXT);


//select KPI....
$counter = 1;
foreach ($stats as $k=>$v){
	if ($k == $stat){
		printf("<span class='selected'>%s</span>",$v);
	} else {
		printf("<a href='?stat=%s'>%s</a>",$k,$v);
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
if ($stat == "anc1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/statistics/anc1defaulters.php";
}
if ($stat == "anc2defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/statistics/anc2defaulters.php";
}
if ($stat == "pnc1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/statistics/pnc1defaulters.php";
}


include_once "includes/footer.php";
?>
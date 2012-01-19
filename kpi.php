<?php
include_once "config.php";
$PAGE="kpi";
include_once "includes/header.php";
$kpis = array ('submitted' =>'Submitted', 'anc1defaulters'=>"ANC 1 Non Defaulters",'anc2defaulters'=>"ANC 2 Non Defaulters");
// ,'tt1defaulters'=>"TT 1 Non Defaulters"
$kpi = optional_param('kpi','submitted',PARAM_TEXT);


//select KPI....
$counter = 1;
foreach ($kpis as $k=>$v){
	if ($k == $kpi){
		printf("<span class='selected'>%s</span>",$v);
	} else {
		printf("<a href='?kpi=%s'>%s</a>",$k,$v);
	}
	if($counter < count($kpis)){
		echo " | ";
	}
	$counter++;
}

if ($kpi == "submitted"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/submitted.php";
}
if ($kpi == "anc1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/anc1defaulters.php";	
}
if ($kpi == "anc2defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/anc2defaulters.php";
}
if ($kpi == "tt1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/tt1defaulters.php";
}
if ($kpi == "tt2defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/tt2defaulters.php";
}

include_once "includes/footer.php";
?>
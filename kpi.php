<?php
include_once "config.php";
$PAGE="kpi";
include_once "includes/header.php";
$kpis = array ('anc1defaulters'=>"ANC 1 Non Defaulters",'anc2defaulters'=>"ANC 2 Defaulters",'tt1defaulters'=>"TT 1 Defaulters",'tt2defaulters'=>"TT 2 Defaulters");

$kpi = optional_param('kpi','anc1defaulters',PARAM_TEXT);


//select KPI....
foreach ($kpis as $k=>$v){
	if ($k == $kpi){
		printf("<span class='selected'>%s</span>",$v);
	} else {
		printf("<a href='?kpi=%s'>%s</a>",$k,$v);
	}
	echo " | ";
}

if ($kpi == "anc1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/anc1defaulters.php";	
}
include_once "includes/footer.php";
?>
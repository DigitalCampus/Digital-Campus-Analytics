<?php
include_once "config.php";
$PAGE="kpi";
include_once "includes/header.php";
$kpis = array ('anc1defaulters'=>"ANC 1 Defaulters",'anc2defaulters'=>"ANC 2 Defaulters",'tt1defaulters'=>"TT 1 Defaulters",'tt2defaulters'=>"TT 2 Defaulters");
$views = array ('months','healthpoints');

$kpi = optional_param('kpi','anc1defaulters',PARAM_TEXT);
$view = optional_param("view","months",PARAM_TEXT);

$healthpoints = $API->getHealthPoints();

//select KPI....
foreach ($kpis as $k=>$v){
	if ($k == $kpi){
		printf("<span class='selected'>%s</span>",$v);
	} else {
		printf("<a href='?kpi=%s&view=%s'>%s</a>",$k,$view,$v);
	}
	echo " | ";
}
// select view....
foreach ($views as $v){
	if ($v == $view){
		printf("<span class='selected'>%s</span>",$v);
	} else {
		printf("<a href='?kpi=%s&view=%s'>%s</a>",$kpi,$v,$v);
	}
	echo " | ";
}

if ($kpi == "anc1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph');
	$opts = array('months'=>6);
	include_once "includes/kpi/anc1defaulters.php";	
}
include_once "includes/footer.php";
?>
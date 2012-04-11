<?php
include_once "config.php";
$PAGE="submitted";
include_once "includes/header.php";
				
$kpi = optional_param('kpi','submitted',PARAM_TEXT);

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
if ($kpi == "pnc1defaulters"){
	$viewopts = array('height'=>500,'width'=>800,'class'=>'graph','comparison'=>true);
	$opts = array('months'=>6);
	include_once "includes/kpi/pnc1defaulters.php";
}

include_once "includes/footer.php";
?>
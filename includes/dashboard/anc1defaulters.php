<div id="anc1defaulters" class="summary">
<h2><?php echo getstring('dashboard.anc1defaulters.title');?></h2>
<?php 
	$viewopts = array('height'=>300,'width'=>450,'class'=>'graph','comparison'=>false);
	$opts = array('months'=>6, 'viewby'=>'months');
	include_once "includes/kpi/anc1defaulters.php";
?>

</div>
<div id="assessmentsummary" class="summary">
<h2><?php echo getString("assessment.chart.dashboard.title")?></h2>
<?php 
	$options = Array('height'=>300,'width'=>450,'class'=>'graph');
	include('includes/assessment/total.php');
?>
</div>
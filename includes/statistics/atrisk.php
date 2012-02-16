<?php 

$hpcodes = optional_param("hpcodes",$USER->hpcode,PARAM_TEXT);
if($hpcodes == 'all'){
	$hpcodes = $API->getUserHealthPointPermissions();
}

$opts = array();
$opts['hpcodes'] = $hpcodes;
$currenthpname = getNameFromHPCodes($hpcodes);

$ra = new RiskAssessment();
$risks = $ra->getRiskStatistics($opts);


$summary = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0);
// loop through and update the counters for each patient:
foreach($risks as $k=>$v){
	$summary[$k] = $v;
}

?>

<div class="comparison">
<form action="" name="compareHealthPoint" method="get">
	<p>Show:
	<select name="hpcodes">
		<?php 
			displayHealthPointSelectList($opts['hpcodes']);
		?>
	</select>
	<input type="hidden" name="stat" value="atrisk">
	<input type="submit" name="submit" value="go"/></p>
</form>
</div>

<?php
$total = 0; 
foreach($summary as $k=>$v){
	$total += $v;
}

if ($total == 0){
	echo getstring("warning.norecords");
} else {
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {
		packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Risk Category');
			data.addColumn('number', 'Number');
			<?php 
				foreach($summary as $k=>$v){
					printf("data.addRow(['%s',%d]);",getString("risk.".$k),$v);
				}
			?>		
	
			var options = {
				width: <?php echo $viewopts['width']; ?>, 
				height: <?php echo $viewopts['height']; ?>,
				title: '<?php echo $currenthpname; ?>',
				chartArea:{left:50,top:20,width:"90%",height:"75%"},
			};
	
			var chart = new google.visualization.PieChart(document.getElementById('atrisk_piechart'));
			chart.draw(data, options);
		}
</script>
<div id="atrisk_piechart" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>

	


<?php 
} 
?>
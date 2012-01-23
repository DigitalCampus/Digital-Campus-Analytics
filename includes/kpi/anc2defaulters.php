<?php 

/*
 * ANC2 defaulters
 */

$submit = optional_param("submit","",PARAM_TEXT);
$hpcodes = optional_param("hpcodes",$USER->hpcode,PARAM_TEXT);
$hpcomparecodes = optional_param("hpcomparecodes",$API->getCohortHealthPoints(true),PARAM_TEXT);

$currentopts = $opts;
if($hpcodes == 'overall'){
	$hpcodes = $API->getUserHealthPointPermissions();
}
$currentopts['hpcodes'] = $hpcodes;
$currentHPname = getNameFromHPCodes($hpcodes);

$compareopts = $opts;
if($hpcomparecodes == 'overall'){
	$hpcomparecodes = $API->getUserHealthPointPermissions();
}
$compareopts['hpcodes'] = $hpcomparecodes;
$compareHPname =  getNameFromHPCodes($hpcomparecodes);

$summary = $API->getANC2Defaulters($currentopts);
$compare = $API->getANC2Defaulters($compareopts);

?>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'MonthYear');
        data.addColumn('number', '<?php echo $currentHPname; ?>');
        data.addColumn('number', '<?php echo $compareHPname; ?>');
        data.addColumn('number', 'Target');
        data.addRows(<?php echo count($summary); ?>);
		<?php 
			$counter = 0;
			foreach($summary as $k=>$v){
				printf("data.setValue(%d, 0, '%s');\n", $counter,$k );
				printf("data.setValue(%d, 1, %d);\n", $counter, $v->nondefaulters );
				printf("data.setValue(%d, 2, %d);\n", $counter, $compare[$k]->nondefaulters);
				printf("data.setValue(%d, 3, %d);\n", $counter, $CONFIG->props['target.anc2']);
				$counter++;
			}
		?>

        var chart = new google.visualization.AreaChart(document.getElementById('chart_anc2defaulters'));
        chart.draw(data, {width: <?php echo $viewopts['width']; ?>, 
							height:<?php echo $viewopts['height']; ?>,
                          	hAxis: {title: 'Month-Year'}, 
                          	vAxis: {title: 'Percentage', maxValue: 100, minValue: 0},
                          	chartArea:{left:50,top:20,width:"90%",height:"75%"},
                          	colors:['#FACC2E','#A4A4A4','#04B431','#5882FA'],
                          	series:[{lineWidth:3, areaOpacity:0},{lineWidth:3, areaOpacity:0},{areaOpacity:0.1,pointSize:0},{areaOpacity:0}],
                          	pointSize:5,
                          	legend:{position:'in'}
                          });
      }
    </script>
    

<div class="comparison">
<form action="kpi.php?kpi=anc2defaulters" name="compareHealthPoint" method="get">
	<p>Compare:
	<select name="hpcodes">
		<?php 
			displayHealthPointSelectList($currentopts['hpcodes']);
		?>
	</select>
	with:
	<select name="hpcomparecodes">
		<?php 			
			displayHealthPointSelectList($compareopts['hpcodes']);
		?>
	</select>
	<input type="hidden" name="kpi" value="anc2defaulters"/>
	<input type="submit" name="submit" value="compare"/></p>
</form>
</div>


<h2>ANC2 Non-defaulters</h2>
<div id="chart_anc2defaulters" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
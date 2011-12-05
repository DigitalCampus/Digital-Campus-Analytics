<div id="anc2defaulters" class="summary">
<h2><?php echo getstring('dashboard.anc2defaulters.title');?></h2>
<?php 

/*
 * ANC2 defaulters
 */
$summary = $API->getANC2Defaulters($opts);

?>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'MonthYear');
        data.addColumn('number', 'Non Defaulters');
        data.addColumn('number', 'Defaulters');
        data.addRows(<?php echo count($summary); ?>);
		<?php 
			$counter = 0;
			foreach($summary as $k=>$v){
				printf("data.setValue(%d, 0, '%s');\n", $counter,$k );
				printf("data.setValue(%d, 1, %d);\n", $counter, $v->nondefaulters );
				printf("data.setValue(%d, 2, %d);\n", $counter, $v->defaulters);
				$counter++;
			}
		?>

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_anc2defaulters'));
        chart.draw(data, {width: 450, 
            				height: 300,
                          	hAxis: {title: 'Month-Year'}, 
                          	vAxis: {title: 'Percentage', maxValue: 100, minValue: 0},
                          	chartArea:{left:50,top:5,width:"60%",height:"75%"}
                          });
      }
    </script>
	<div id="chart_anc2defaulters" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
	
</div>

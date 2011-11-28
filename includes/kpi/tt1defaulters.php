<div id="tt1defaulters" class="summary">
<h2><?php echo getstring('dashboard.tt1defaulters.title');?></h2>
<?php 
/*
* TT1 defaulters
* 1) A woman with no TT history, and did not receive TT in her first ANC, mark as defaulter for TT1
*/

// set up summary/results array/objects
$date = new DateTime();
$date->sub(new DateInterval('P6M'));
$summary = array();
for ($i=0; $i<7 ;$i++){
	$summary[$date->format('M-Y')] = new stdClass;
	$summary[$date->format('M-Y')]->defaulters = 0;
	$summary[$date->format('M-Y')]->nondefaulters = 0;
	$date->add(new DateInterval('P1M'));
}

// get all the submitted ANC1 protocols from first day of the month 6 months ago
$sql = "SELECT 	p._URI,
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.TODAY as createdate,
				p.Q_TETANUS
		FROM ".TABLE_ANCFIRST." p 
		WHERE p.TODAY > date_format(curdate() - interval 6 month,'%Y-%m-01 00:00:00')
		AND p.Q_HEALTHPOINTID != '9999'
		ORDER BY p.TODAY ASC";

$results = $API->runSql($sql);
while($row = mysql_fetch_array($results)){
	$date = new DateTime($row['createdate']);
	$arrayIndex = $date->format('M-Y');

	if ($row['Q_TETANUS'] == 'none' ){
		$summary[$arrayIndex]->defaulters++;
	} else {
		$summary[$arrayIndex]->nondefaulters++;
	}
}

// change into a percentage rather than absolute values
foreach($summary as $k=>$v){
	$total = $v->defaulters + $v->nondefaulters;
	if ($total > 0){
		$pc_default = ($v->defaulters * 100)/$total;
		$pc_nondefault = ($v->nondefaulters * 100)/$total;
		$summary[$k]->defaulters = $pc_default;
		$summary[$k]->nondefaulters = $pc_nondefault;
	}
}
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
				printf("data.setValue(%d, 1, %d);\n", $counter,$v->nondefaulters );
				printf("data.setValue(%d, 2, %d);\n", $counter, $v->defaulters);
				$counter++;
			}
		?>

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_tt1defaulters'));
        chart.draw(data, {width: 450, 
            				height: 300,
                          	hAxis: {title: 'Month-Year'}, 
                          	vAxis: {title: 'Percentage', maxValue: 100, minValue: 0},
                          	chartArea:{left:50,top:5,width:"60%",height:"75%"}
                          });
      }
    </script>
	<div id="chart_tt1defaulters" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
	
</div>
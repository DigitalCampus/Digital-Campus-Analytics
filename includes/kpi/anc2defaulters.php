<div id="anc1defaulters" class="summary">
<h2><?php echo getstring('dashboard.anc2defaulters.title');?></h2>
<?php 

/*
 * ANC2 defaulters
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

// all those who had an ANC follow up visit
$sql = "SELECT 	p._URI,
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.Q_LMP, 
				p.TODAY as createdate,  
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_START." DAY) AS ANC2_DUE_BY_START,
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) AS ANC2_DUE_BY_END
		FROM ".ANCFOLLOW." p
		WHERE p.TODAY > date_format(curdate() - interval 6 month,'%Y-%m-01 00:00:00')
		AND p.Q_HEALTHPOINTID != '9999'
		ORDER BY p.TODAY ASC";

// if createdate not between ANC2_DUE_BY_START and ANC2_DUE_BY_END then defaulter, group by month/year of createdate
// otherwise non defaulter
$results = $API->runSql($sql);
while($row = mysql_fetch_array($results)){
	$date = new DateTime($row['createdate']);
	$arrayIndex = $date->format('M-Y');

	if ($row['createdate'] > $row['ANC2_DUE_BY_START'] && $row['createdate'] < $row['ANC2_DUE_BY_END']){
		$summary[$arrayIndex]->nondefaulters++;
	} else {
		$summary[$arrayIndex]->defaulters++;
	}
}

// all those who had an ANC1 but not a second visit and didn't have termination protocol entered before ANC was due
$sql = "SELECT 	p._URI,
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.Q_LMP, 
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) AS ANC2_DUE_BY_END 
		FROM ".ANCFIRST." p
		LEFT OUTER JOIN ".ANCFOLLOW." f ON f.Q_USERID = p.Q_USERID AND f.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID
		WHERE f.Q_USERID IS NULL
		AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) > date_format(curdate() - interval 6 month,'%Y-%m-01 00:00:00')
		AND DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) < curdate()
		AND p.Q_HEALTHPOINTID != '9999'
		ORDER BY DATE_ADD(p.Q_LMP, INTERVAL ".ANC2_DUE_BY_END." DAY) ASC";
// @TODO add constraint about terminations

// all those returned by above query are defaulters - as have now Follow up
$results = $API->runSql($sql);
while($row = mysql_fetch_array($results)){
	$date = new DateTime($row['ANC2_DUE_BY_END']);
	$arrayIndex = $date->format('M-Y');
	$summary[$arrayIndex]->defaulters++;
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

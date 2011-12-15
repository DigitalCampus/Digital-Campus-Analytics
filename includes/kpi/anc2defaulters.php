
<?php 

/*
 * ANC2 defaulters
 */

$submit = optional_param("submit","",PARAM_TEXT);

$userhealthpoints = $API->getHealthPoints();
$currentHPname = "";
$compareHPname = "";
$cohorthealthpoints = $API->getCohortHealthPoints();
$districts = $API->getDistricts();


$cohorthps = array();
foreach ($cohorthealthpoints as $k=>$v){
	array_push($cohorthps, $k);
}
$cohorthps = implode(',',$cohorthps);

$hpcode = optional_param("hpcode",$USER->hpcode,PARAM_TEXT);
$hpcomparecode = optional_param("hpcomparecode","average",PARAM_TEXT);

$AverageArray = array();
$ComparisonHPArray = array();
foreach($districts as $d){
	//get the hps for this district
	$hps4district = $API->getHealthPointsForDistict($d->did);
	$temp = array();
	foreach($hps4district as $h){
		array_push($temp,$h->hpcode);
	}
	$hps = implode(",",$temp);
	$AverageArray[$hps] = $d->dname;
	if($hpcode == $hps){
		$currentHPname = $d->dname;
	}
	if($hpcomparecode == $hps){
		$compareHPname = $d->dname;
	}
}
foreach($cohorthealthpoints as $hp){
	$ComparisonHPArray[$hp->hpcode] = $hp->hpname;
	if($hpcode == $hp->hpcode){
		$currentHPname = $hp->hpname;
	}
	if($hpcomparecode == $hp->hpcode){
		$compareHPname = $hp->hpname;
	}
}


$currentopts = $opts;
if($hpcode === 'average'){
	$currentopts['hps'] = $cohorthps;
	$currentHPname = "Average";
} else {
	$currentopts['hps'] = $hpcode;
}

$compareopts = $opts;
if($hpcomparecode == 'average'){
	$compareopts['hps'] = $cohorthps;
	$compareHPname = "Average";
} else {
	$compareopts['hps'] = $hpcomparecode;
}

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
				printf("data.setValue(%d, 3, %d);\n", $counter, 60);
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
	<select name="hpcode">
		<?php 
			outputSelectList($districts,$AverageArray,$ComparisonHPArray,$currentopts['hps']);
		?>
	</select>
	with:
	<select name="hpcomparecode">
		<?php 			
			outputSelectList($districts,$AverageArray,$ComparisonHPArray,$compareopts['hps']);
		?>
	</select>
	<input type="hidden" name="kpi" value="anc2defaulters"/>
	<input type="submit" name="submit" value="compare"/></p>
</form>
</div>


<h2>ANC2 Non-defaulters</h2>
<div id="chart_anc2defaulters" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
<?php 

function outputSelectList($districts,$AverageArray,$ComparisonHPArray,$selected){
	if(count($districts) > 1){
		if($selected == 'average'){
			printf("<option value='average' selected='selected'>Average for all</option>");
		} else {
			printf("<option value='average'>Average for all</option>");
		}
		
		printf("<option value='' disabled='disabled'>---</option>");
	}
	foreach($AverageArray as $k=>$v){
		if(strcasecmp($selected,$k) == 0){
			printf("<option value='%s' selected='selected'>Average for %s</option>", $k,$v);
		} else {
			printf("<option value='%s'>Average for %s</option>", $k,$v);
		}
	}
	printf("<option value='' disabled='disabled'>---</option>");
	foreach($ComparisonHPArray as $k=>$v){
		if(strcasecmp($selected,$k) == 0){
			printf("<option value='%s' selected='selected'>%s</option>", $k,$v);
		} else {
			printf("<option value='%s'>%s</option>", $k,$v);
		}
	}
}
?>
	
	

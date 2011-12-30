<?php 

/*
 * ANC1 defaulters
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
$hpcomparecode = optional_param("hpcomparecode","overall",PARAM_TEXT);

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
if($hpcode === 'overall'){
	$currentopts['hps'] = $cohorthps;
	$currentHPname = "Overall";
} else {
	$currentopts['hps'] = $hpcode;
}

$compareopts = $opts;
if($hpcomparecode == 'overall'){
	$compareopts['hps'] = $cohorthps;
	$compareHPname = "Overall";
} else {
	$compareopts['hps'] = $hpcomparecode;
}

$summary = $API->getANC1Defaulters($currentopts);
$compare = $API->getANC1Defaulters($compareopts);

$bestopts = $opts;
$bestopts['hps'] = $cohorthps;
$best = $API->getANC1DefaultersBestPerformer($bestopts);

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
        data.addColumn('number', 'Best');
        
        data.addRows(<?php echo count($summary); ?>);
		<?php 
			$counter = 0;
			foreach($summary as $k=>$v){
				printf("data.setValue(%d, 0, '%s');\n", $counter,$k );
				printf("data.setValue(%d, 1, %d);\n", $counter, $v->nondefaulters );
				printf("data.setValue(%d, 2, %d);\n", $counter, $compare[$k]->nondefaulters);
				printf("data.setValue(%d, 3, %d);\n", $counter, 60);
				printf("data.setValue(%d, 4, %d);\n", $counter, $best[$k]->nondefaulters);
				
				$counter++;
			}
		?>

        var chart = new google.visualization.AreaChart(document.getElementById('chart_anc1defaulters'));
        chart.draw(data, {width: <?php echo $viewopts['width']; ?>, 
            				height:<?php echo $viewopts['height']; ?>,
                          	hAxis: {title: 'Month-Year'}, 
                          	vAxis: {title: 'Percentage', maxValue: 100, minValue: 0},
                          	chartArea:{left:50,top:20,width:"90%",height:"75%"},
                          	legend:{position:'in'},
                          	colors:['#FACC2E','#A4A4A4','#04B431','#5882FA'],
                          	pointSize:5,
                          	series:[{lineWidth:3, areaOpacity:0},{lineWidth:3, areaOpacity:0},{areaOpacity:0.1,pointSize:0},{areaOpacity:0}]
                          });
      }
    </script>


<?php if ($viewopts['comparison'] == true){?>
<div class="comparison">
<form action="" name="compareHealthPoint" method="get">
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
	<input type="hidden" name="kpi" value="anc1defaulters">
	<input type="submit" name="submit" value="compare"/></p>
</form>
</div>
<?php } ?>

<h2>ANC1 Non-defaulters</h2>
<div id="chart_anc1defaulters" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
<?php 

function outputSelectList($districts,$AverageArray,$ComparisonHPArray,$selected){
	if(count($districts) > 1){
		if($selected == 'average'){
			printf("<option value='overall' selected='selected'>Overall</option>");
		} else {
			printf("<option value='overall'>Overall</option>");
		}
		
		printf("<option value='' disabled='disabled'>---</option>");
	}
	foreach($AverageArray as $k=>$v){
		if(strcasecmp($selected,$k) == 0){
			printf("<option value='%s' selected='selected'>%s</option>", $k,$v);
		} else {
			printf("<option value='%s'>%s</option>", $k,$v);
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



<?php 

/*
 * ANC1 defaulters
 */

$submit = optional_param("submit","",PARAM_TEXT);
// if form has been submitted
if ($submit != ""){
	echo $submit;
} else {
// else figure out the default (current users HP against their cohort average)

}



$cohorthps = $API->getCohortHealthPoints();
$temp = array();
foreach ($cohorthps as $k=>$v){
	array_push($temp, $k);
	
}
$temphps = implode(',',$temp);
$hpcode = optional_param("hpcode","",PARAM_TEXT);
$hpcomparecode = optional_param("hpcomparecode",$temphps,PARAM_TEXT);
echo "hpcode:".$hpcode;
$currentHPname = "";
$compareHPname = "";
$hps = $API->getHealthPoints(true);
foreach ($hps as $k=>$v){
	echo $k.":<br/>";
	print_r($v);
	echo "<br/>";
	if($k == $hpcode){
		$currentHPname = $v->hpname;
		echo "setting currentHPname:".$v->hpname;
	}
	if($k == $hpcomparecode){
		$compareHPname = $v->hpname;
		echo "setting compareHPname:".$v->hpname;
	}
}

$hps = $API->getHealthPoints();
$summary = $API->getANC1Defaulters($opts);
if($hpcomparecode == 'average'){
	$opts['hps'] = $temphps;
	$compareHPname = "Average";
} else {
	$opts['hps'] = $hpcomparecode;
}
$cohortavg = $API->getANC1Defaulters($opts);

?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'MonthYear');
        data.addColumn('number', '<?php echo $currentHPname; ?>');
        data.addColumn('number', 'Target');
        data.addColumn('number', '<?php echo $compareHPname; ?>');
        data.addRows(<?php echo count($summary); ?>);
		<?php 
			$counter = 0;
			foreach($summary as $k=>$v){
				printf("data.setValue(%d, 0, '%s');\n", $counter,$k );
				printf("data.setValue(%d, 1, %d);\n", $counter,$v->nondefaulters );
				printf("data.setValue(%d, 2, %d);\n", $counter, 60);
				printf("data.setValue(%d, 3, %d);\n", $counter, $cohortavg[$k]->nondefaulters);
				$counter++;
			}
		?>

        var chart = new google.visualization.LineChart(document.getElementById('chart_anc1defaulters'));
        chart.draw(data, {width: <?php echo $viewopts['width']; ?>, 
            				height:<?php echo $viewopts['height']; ?>,
                          	hAxis: {title: 'Month-Year'}, 
                          	vAxis: {title: 'Percentage', maxValue: 100, minValue: 0},
                          	chartArea:{left:50,top:20,width:"90%",height:"75%"},
                          	legend:{position:'in'},
                          	colors:['blue','green','grey'],
                          	pointSize:5
                          });
      }
    </script>
<div id="chart_anc1defaulters" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>


<div class="comparison">
<h3>Now displaying</h3>

<h3>Comparison</h3>


<!--  p>Compare districts (if user permissions allow more than 1 district)</p-->

<p>Compare Health posts
<form action="" name="compareHealthPoint" method="post">
	<p>Compare:</p>
	<select name="hpcode">
		<?php 
			foreach($hps as $hp){
				printf("<option value='%s'>%s</option>",$hp->hpcode,$hp->hpname);
			}
		?>
	</select>
	<p>With:</p>
	<select name="hpcomparecode">
		<option value="average">Average for District</option>
		<?php 
			foreach($cohorthps as $hp){
				printf("<option value='%s'>%s</option>",$hp->hpcode,$hp->hpname);
			}
		?>
	</select>
	<p><input type="submit" name="submit" value="compare"/></p>
</form>
</p>
</div>





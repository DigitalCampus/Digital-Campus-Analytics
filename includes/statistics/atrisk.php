<?php 

$userhealthpoints = $API->getHealthPoints();
$currentHPname = "";
$cohorthealthpoints = $API->getCohortHealthPoints();
$districts = $API->getDistricts();
$opts = array();
$cohorthps = array();
foreach ($cohorthealthpoints as $k=>$v){
	array_push($cohorthps, $k);
}
$cohorthps = implode(',',$cohorthps);

$hpcode = optional_param("hpcode",$USER->hpcode,PARAM_TEXT);

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
}
foreach($cohorthealthpoints as $hp){
	$ComparisonHPArray[$hp->hpcode] = $hp->hpname;
	if($hpcode == $hp->hpcode){
		$currentHPname = $hp->hpname;
	}
}


$currentopts = $opts;
if($hpcode == 'overall'){
	$currentopts['hps'] = $cohorthps;
	$currentHPname = "Overall";
} else {
	$currentopts['hps'] = $hpcode;
}

$patients = $API->getCurrentPatients($currentopts);

$risks = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0);
// loop through and update the counters for each patient:
foreach($patients as $p){
	$risks[$p->risk->category]++;
}

?>

<div class="comparison">
<form action="" name="compareHealthPoint" method="get">
	<p>Show:
	<select name="hpcode">
		<?php 
			outputSelectList($districts,$AverageArray,$ComparisonHPArray,$currentopts['hps']);
		?>
	</select>
	<input type="hidden" name="stat" value="atrisk">
	<input type="submit" name="submit" value="go"/></p>
</form>
</div>

<?php
$total = 0; 
foreach($risks as $k=>$v){
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
				foreach($risks as $k=>$v){
					printf("data.addRow(['%s',%d]);",getString("risk.".$k),$v);
				}
			?>		
	
			var options = {
				width: <?php echo $viewopts['width']; ?>, 
				height: <?php echo $viewopts['height']; ?>,
				title: '<?php echo $currentHPname; ?>',
				chartArea:{left:50,top:20,width:"90%",height:"75%"},
			};
	
			var chart = new google.visualization.PieChart(document.getElementById('atrisk_piechart'));
			chart.draw(data, options);
		}
</script>
<div id="atrisk_piechart" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>

	


<?php 
} 
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
<?php 
$cohort = $API->getCohortHealthPoints(false,$report->hpcodes);

$summary = array();
$i = 0;
foreach($cohort as $c){
	$summary[$i] = new stdClass();
	$summary[$i]->hpcode = $c->hpcode;
	$opts=array('startdate'=>$report->start,'enddate'=>$report->end,'hpcodes'=>$c->hpcode,'limit'=>'0');
	$nondefaulters = $API->getANC1Defaulters($opts);
	$summary[$i]->count = $nondefaulters[0]->nondefaulters;
	//print_r($submitted);
	$i++;
}
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Health Point');
        data.addColumn('number', 'Percent non-defaulters');
        data.addColumn('number', 'Target');
		<?php 
			printf("data.addRow(['%s',%d,%d]);",'',0,$CONFIG->props['target.anc1']);
			foreach($summary as $s){
				printf("data.addRow(['%s',%d,%d]);",displayHealthPointName($s->hpcode),$s->count,$CONFIG->props['target.anc1']);	
			}
			printf("data.addRow(['%s',%d,%d]);",'',0,$CONFIG->props['target.anc1']);
		?>
		
        var options = {
          	width: 700, 
          	height: 350,
          	chartArea:{left:200,top:10,width:"60%",height:"80%"},
        	legend:{position:'none'},
        	series: [{type: "bars"},{type: "area",areaOpacity:0.1,color:'#04B431'}],
        	hAxis: {title: 'Percentage', maxValue: 100, minValue: 0}
        };

        var chart = new google.visualization.BarChart(document.getElementById('anc1-bar-div'));
        chart.draw(data, options);
      }
    </script>
<div id="anc1-bar-div"></div>
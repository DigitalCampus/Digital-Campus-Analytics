<?php 
$cohort = $API->getCohortHealthPoints(false,$report->hpcodes);

$summary = array();
$i = 0;
foreach($cohort as $c){
	$summary[$i] = new stdClass();
	$summary[$i]->hpcode = $c->hpcode;
	$opts=array('startdate'=>$report->start,'enddate'=>$report->end,'hpcodes'=>$c->hpcode,'limit'=>'0');
	$submitted = $API->getProtocolsSubmitted_Cache($opts);
	$summary[$i]->count = $submitted->count;
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
        data.addColumn('number', 'Number submitted');
		<?php 
			foreach($summary as $s){
				printf("data.addRow(['%s',%d]);",displayHealthPointName($s->hpcode),$s->count);	
			}
		?>
		
        var options = {
          	width: 700, 
          	height: 350,
          	chartArea:{left:200,top:10,width:"60%",height:"80%"},
        	legend:{position:'none'},
        	hAxis: {title: 'Number submitted', minValue: 0}
        };

        var chart = new google.visualization.BarChart(document.getElementById('submitted-bar-div'));
        chart.draw(data, options);
      }
    </script>
<div id="submitted-bar-div"></div>
    
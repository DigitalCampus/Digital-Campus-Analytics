<?php 
$cohort = $API->getCohortHealthPoints(false,$report->hpcodes);

$summary = array();
$i = 0;
foreach($cohort as $c){
	$summary[$i] = new stdClass();
	$summary[$i]->hpname = $c->hpname;
	$summary[$i]->hpcode = $c->hpcode;
	$opts=array('startdate'=>'2012-01-01 00:00:00','enddate'=>'2012-01-31 23:59:59','hpcodes'=>$c->hpcode,'limit'=>'0');
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
				printf("data.addRow(['%s',%d]);",$s->hpname,$s->count);	
			}
		?>
		
        var options = {
          	width: 600, 
          	height: 400,
          	title: 'Protocols Submitted',
          	chartArea:{left:150,top:50,width:"60%",height:"70%"},
        	legend:{position:'none'}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
<div id="chart_div"></div>
    
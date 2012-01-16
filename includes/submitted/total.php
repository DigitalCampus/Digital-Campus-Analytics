<?php 
$opts = array('days'=>$days,'limit'=>'all');
$submitted = $API->getProtocolsSubmitted_Cache($opts);

printf("<h3>%s</h3>",getstring('submitted.total.count',array(count($submitted->protocols),$days)));


$summary = array();
foreach($submitted->protocols as $s){
	$d = date('d M Y',strtotime($s->datestamp));
	if(array_key_exists($d,$summary)){
		$summary[$d] += 1;
	} else {
		$summary[$d] = 1;
	}	
}	
?>

<script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      function drawChart() {
          
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Total');
		<?php 
        
        echo "data.addRows(".($days+1).");\n";
       	$date = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $date = $date - ($days*86400);

		for($c = 0; $c <$days+1; $c++){
        	$tempc =  date('d M Y',$date);
			$total = 0;
			if(isset($summary[$tempc])){
	        	$total = $summary[$tempc];
			}
			printf("data.setValue(%d,%d,'%s');\n",$c,0,$tempc);
			printf("data.setValue(%d,%d,%d);\n", $c, 1, $total);
			$date = $date + 86400;
        }
        
        ?>

        var chart = new google.visualization.AreaChart(document.getElementById('submitted_location_chart_div'));
        chart.draw(data, {	width: <?php echo $options['width'] ?>, 
                			height: <?php echo $options['height'] ?>,
                			hAxis: {title: 'Date'},
                			vAxis: {title: 'Number submitted'},
                			legend: 'none',
                			chartArea:{left:50,top:20,width:"90%",height:"75%"},
                			pointSize:3,
                			series:[{areaOpacity:0.2}]
							});
      }
    </script>

	<div id="submitted_location_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
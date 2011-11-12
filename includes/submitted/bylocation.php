<?php 
$opts = array('days'=>$days,'limit'=>'all');
$submitted = $API->getProtocolsSubmitted($opts);

printf("<h3>%s</h3>",getstring('submitted.total.count',array(count($submitted->protocols),$days)));


$locations = $API->getHealthPoints();
$summary = array();
foreach($submitted->protocols as $s){
	$d = date('d M Y',strtotime($s->datestamp));

	if(array_key_exists($d,$summary)){
		if(isset($summary[$d][$s->protocollocation])){
			$summary[$d][$s->protocollocation] += 1;
		} else {
			$summary[$d][$s->protocollocation] = 1;
		}
	} else {
		$summary[$d][$s->protocollocation] = 1;
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
        foreach($locations as $l){
        	echo "data.addColumn('number', '".$l->hpname."');";
        }
        
        echo "data.addRows(".($days+1).");";
       	$date = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $date = $date - ($days*86400);

		for($c = 0; $c <$days+1; $c++){
        	$tempc =  date('d M Y',$date);
			$total = 0;
			if(isset($summary[$tempc])){
	        	foreach($locations as $l){
	        		if(isset($summary[$tempc][$l->hpname])){
	        			$total = $total + $summary[$tempc][$l->hpname];
	        		}
	        	}
			}

			printf("data.setValue(%d,%d,%d);", $c, 1, $total);
			
        	if(isset($summary[$tempc])){
        		printf("data.setValue(%d,%d,'%s');",$c,0,$tempc);
	        	$loccount = 2;
	        	foreach($locations as $l){
	        		if(isset($summary[$tempc][$l->hpname])){
	        			printf("data.setValue(%d,%d,%d);", $c, $loccount, $summary[$tempc][$l->hpname]);
	        		} else {
	        			printf("data.setValue(%d,%d,%d);", $c, $loccount, 0);
	        		}
	        		$loccount++;
	        	}
        	} else {
        		echo "data.setValue(".$c.",0,'".$tempc."');";
	        	$loccount = 2;
	        	foreach($locations as $l){
	        		printf("data.setValue(%d,%d,%d);", $c, $loccount, 0);
	        		$loccount++;
	        	}		
        	}
        	
        	
        	$date = $date + 86400;
        }
        
        ?>

        var chart = new google.visualization.LineChart(document.getElementById('submitted_location_chart_div'));
        chart.draw(data, {width: <?php echo $options['width'] ?>, height: <?php echo $options['height'] ?>, title: '<?php echo getString("submitted.chart.bylocation.title")?>'});
      }
    </script>

	<div id="submitted_location_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
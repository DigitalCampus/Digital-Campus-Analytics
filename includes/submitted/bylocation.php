<?php 
$opts = array('days'=>$days,'limit'=>'all');
$submitted = $API->getProtocolsSubmitted($opts);

printf("<h3>%s</h3>",getstring('submitted.total.count',array(count($submitted->protocols),$days)));

$healthpoints = $API->getHealthPoints();

$locations = array();

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
	
	if(!in_array($s->protocollocation,$locations)){
		array_push($locations, $s->protocollocation);
	}
}	

foreach($healthpoints as $hp){
	if(!in_array($hp->hpname,$locations)){
		array_push($locations, $hp->hpname);
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
        	echo "data.addColumn('number', '".$l."');";
        }
        
        echo "data.addRows(".($days+1).");";
       	$date = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $date = $date - ($days*86400);

		for($c = 0; $c <$days+1; $c++){
        	$tempc =  date('d M Y',$date);
			$total = 0;
			if(isset($summary[$tempc])){
	        	foreach($locations as $k=>$v){
	        		if(isset($summary[$tempc][$v])){
	        			$total = $total + $summary[$tempc][$v];
	        		}
	        	}
			}

			printf("data.setValue(%d,%d,%d);", $c, 1, $total);
			
        	if(isset($summary[$tempc])){
        		printf("data.setValue(%d,%d,'%s');",$c,0,$tempc);
	        	$loccount = 2;
	        	foreach($locations as $k=>$v){
	        		if(isset($summary[$tempc][$v])){
	        			printf("data.setValue(%d,%d,%d);", $c, $loccount, $summary[$tempc][$v]);
	        		} else {
	        			printf("data.setValue(%d,%d,%d);", $c, $loccount, 0);
	        		}
	        		$loccount++;
	        	}
        	} else {
        		echo "data.setValue(".$c.",0,'".$tempc."');";
	        	$loccount = 2;
	        	foreach($locations as $k=>$v){
	        		printf("data.setValue(%d,%d,%d);", $c, $loccount, 0);
	        		$loccount++;
	        	}		
        	}
        	
        	
        	$date = $date + 86400;
        }
        
        ?>

        var chart = new google.visualization.LineChart(document.getElementById('submitted_location_chart_div'));
        chart.draw(data, {	width: <?php echo $options['width'] ?>, 
                			height: <?php echo $options['height'] ?>,
                			hAxis: {title: 'Date'},
                			vAxis: {title: 'Number submitted'},
                			chartArea:{left:50,top:20,width:"55%",height:"75%"}
							});
      }
    </script>

	<div id="submitted_location_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
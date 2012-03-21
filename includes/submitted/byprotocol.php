<?php 
$protocols = array();
$summary = array();
$opts = array('days'=>$days,'limit'=>'all');
$submitted = $API->getProtocolsSubmitted_Cache($opts);

printf("<h3>%s</h3>",getstring('submitted.total.count',array($submitted->count['total'],$days)));

foreach($submitted->protocols as $s){
	$d = date('d M Y',strtotime($s->datestamp));
	$p = getstring($s->protocol);
	if(array_key_exists($d,$summary)){
		if(isset($summary[$d][$p])){
			$summary[$d][$p] += 1;
		} else {
			$summary[$d][$p] = 1;
		}
	} else {
		$summary[$d][$p] = 1;
	}
	
	if (!array_key_exists($p,$protocols)){
		$protocols[$p] = 1;
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
        	foreach($protocols as $k => $v){
        		echo "data.addColumn('number','".$k."');";
        	}
        ?>
       
        
        <?php 
        echo "data.addRows(".($days+1).");";
        $date = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $date = $date - ($days*86400);

		for($c = 0; $c <$days+1; $c++){
        	$tempc =  date('d M Y',$date);
        	
			$total = 0;
			if(isset($summary[$tempc])){
	        	foreach($protocols as $k => $v){
	        		if(isset($summary[$tempc][$k])){
	        			$total = $total + $summary[$tempc][$k];
	        		}
	        	}
			}
        	printf("data.setValue(%d,%d,%d);\n", $c, 1, $total);
        	
        	if(isset($summary[$tempc])){
        		printf("data.setValue(%d,%d,'%s');",$c,0,$tempc);
	        	$pcount = 2;
	        	foreach($protocols as $k => $v){
	        		if(isset($summary[$tempc][$k])){
	        			printf("data.setValue(%d,%d,%d);", $c, $pcount, $summary[$tempc][$k]);
	        		} else {
	        			printf("data.setValue(%d,%d,%d);", $c, $pcount, 0);
	        		}
	        		$pcount++;
	        	}
        	} else {
        		echo "data.setValue(".$c.",0,'".$tempc."');\n";
	        	$pcount = 2;
	        	foreach($protocols as $p){
	        		printf("data.setValue(%d,%d,%d);\n", $c, $pcount, 0);
	        		$pcount++;
	        	}		
        	}
        	$date = $date + 86400;
		}
    
        ?>

        var chart = new google.visualization.LineChart(document.getElementById('submitted_chart_div'));

        chart.draw(data, {	width: <?php echo $options['width'] ?>, 
    			height: <?php echo $options['height'] ?>,
    			hAxis: {title: 'Date'},
    			vAxis: {title: 'Number submitted'},
    			chartArea:{left:50,top:20,width:"75%",height:"75%"},
              	legend:{position:'right'},
    			pointSize:5
				});
		
        /*chart.draw(data, {width: 800, height: 300, title: '<?php echo getString("submitted.chart.bytype.title")?>'});*/
      }
    </script>

	<div id="submitted_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
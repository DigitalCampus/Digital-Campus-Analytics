<?php 
	$tasks = $API->getAllTasks($duein);
	$healthposts = $API->getHealthPosts();
	
	$summary = Array();
	foreach($healthposts as $hp){
		$summary[$hp->hpcode] = Array();
	}
	$protocols = Array();
	foreach($tasks as $task){
		if(array_key_exists($task->protocol,$summary[$task->hpcode])){
			$summary[$task->hpcode][$task->protocol]++;
		} else {
			$summary[$task->hpcode][$task->protocol] = 1;
		}
		if(!in_array($task->protocol,$protocols)){
			array_push($protocols,$task->protocol);
		}
	}
?>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Health Post');
		<?php 
			foreach($protocols as $p){
				echo "data.addColumn('number', '".$p."');";
			}
		?>
		data.addColumn('number','Total');
        data.addRows(<?php echo count($summary);?>);
		<?php 
			$sc = 0;
			foreach ($summary as $k=>$v){
				echo "data.setValue(".$sc.",0,'".$healthposts[$k]->hpname."');";
				$pc = 1;
				$total = 0;
				foreach ($protocols as $p){
					if(array_key_exists($p,$v)){
						$total = $total + $v[$p];
						echo "data.setValue(".$sc.",". $pc.",".$v[$p].");";
					} else {
						echo "data.setValue(".$sc.",". $pc.",0);";
					}
					$pc++;
				} 	
				echo "data.setValue(".$sc.",". $pc.",".$total.");";
				$sc++;
			}
		?>
        
        var chart = new google.visualization.ColumnChart(document.getElementById('social_div'));
        chart.draw(data, {width: 800, height: 300, title: 'Protocols due in next <?php echo $duein; ?> days'});
      }
    </script>
    
    <div id="social_div"><?php echo getstring('warning.graph.unavailable');?></div>
<?php 
	$summary = array();
	foreach($atRisk as $ar){
		foreach($ar->riskfactors as $reason){
			if(array_key_exists($reason,$summary)){
				$summary[$reason] += 1;
			} else {
				$summary[$reason] = 1;
			}
		}
	}	
?>

<script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      
      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Reason');
      data.addColumn('number', 'percent');
      data.addRows([
      <?php        
      	$count = 0;
      	$len = count($summary);        
		foreach($summary as $key => $val){
			$count++;
			echo "['".$key."' , ".$val."]";
			if($count != $len){
				echo ",";
			}
		}
		?>
      ]);

      // Set chart options
      var options = {'title':'Risk factor distribution',
                     'width':<?php echo $options['width'] ?>,
                     'height':<?php echo $options['height'] ?>};
      /*var options = {'title':'Risk factor distribution',
              'width':800,
              'height':500};*/

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    </script>
	<div id="chart_div" class="<?php echo $options['class'] ?>"><?php echo getstring('warning.graph.unavailable');?></div>
	
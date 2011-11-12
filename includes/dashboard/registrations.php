<?php 
	$submitted_days = optional_param("submitted_days",7,PARAM_INT);
	$reg = $API->getPatientRegistrations($submitted_days);
?>
<h2>Patient Registrations in last:
	<?php 
		if ($submitted_days == 7){
			echo "<span style='color:green'>7 days</span>";
		} else {
			echo "<a href='?submitted_days=7'>7 days</a>";
		}
	?>
	|
	<?php 
		if ($submitted_days == 14){
			echo "<span style='color:green'>14 days</span>";
		} else {
			echo "<a href='?submitted_days=14'>14 days</a>";
		}
	?>
	|
	<?php 
		if ($submitted_days == 31){
			echo "<span style='color:green'>31 days</span>";
		} else {
			echo "<a href='?submitted_days=31'>31 days</a>";
		}
	?>
</h2>
<h3>Total: <?php echo count($reg);?></h3>
<h3>Submitted by date:</h3>
<?php 
if(count($reg>0)){
	$summary = array();
	foreach($reg as $s){
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
            echo "data.addRows(".($submitted_days+1).");";
            $date = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $date = $date - ($submitted_days*86400);
            
            //$date->sub(new DateInterval('P'.$submitted_days.'D'));

        for($c = 0; $c <$submitted_days+1; $c++){
             $tempc =  date('d M Y',$date);
             if(isset($summary[$tempc])){
	             printf("data.setValue(%d,%d,'%s');",$c,0,$tempc);
	             printf("data.setValue(%d,%d,%d);",$c,1,$summary[$tempc]);
             } else {
	             printf("data.setValue(%d,%d,'%s');",$c,0,$tempc);
	             printf("data.setValue(%d,%d,%d);",$c,1,0);	
             }
             $date = $date + 86400;
        }
            
        ?>

        var chart = new google.visualization.LineChart(document.getElementById('patientreg_chart_div'));
        chart.draw(data, {width: 500, height: 300, title: 'Patient Registrations'});
      }
    </script>

<div id="patientreg_chart_div" class="summarygraph"><?php echo getstring('warning.graph.unavailable');?></div>
<?php 
}
?>
<h2>Age Distribution</h2>
<?php 

$sql = sprintf("SELECT Q_AGE, COUNT(*) AS countage FROM %s
		WHERE Q_HEALTHPOINTID != 9999
		GROUP BY Q_AGE",TABLE_REGISTRATION);

$age = $API->runSql($sql);

?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      function drawChart() {
    	  var data = google.visualization.arrayToDataTable([
	    	  ['Age', 'Count'],
	    	  <?php 
	    	  	while($o = mysql_fetch_object($age)){
	    	  		printf("[%d,%d],",$o->Q_AGE,$o->countage);
	    	  	}
	    	  ?>
    	  	]);

    	  var options = {	width: 500, 
      			height: 400,
    			hAxis: {title: 'Age'},
    			vAxis: {title: 'Count'},
    			legend: 'none',
    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
    			pointSize:3,
    			series:[{areaOpacity:0.2}]
				}

    	  var chart = new google.visualization.LineChart(document.getElementById('age_dist_chart_div'));
    	  chart.draw(data, options);

    	  google.visualization.events.addListener(chart, 'select', function() {
              console.log(chart.getSelection());
          });
	  
    	                                          		
      }
    </script>

	<div id="age_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
	
	
	

<h2>Mothers Weight Distribution</h2>
<?php 

$sql = sprintf("SELECT Q_WEIGHT, COUNT(*) as countweight FROM %s
		WHERE Q_HEALTHPOINTID != 9999
		AND Q_WEIGHT IS NOT NULL
		GROUP BY Q_WEIGHT",TABLE_ANC);

$weight = $API->runSql($sql);

?>
<script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      function drawChart() {
    	  var data = google.visualization.arrayToDataTable([
	    	  ['Weight', 'Count'],
	    	  <?php 
	    	  	while($o = mysql_fetch_object($weight)){
	    	  		printf("[%d,%d],",$o->Q_WEIGHT,$o->countweight);
	    	  	}
	    	  ?>
    	  	]);

    	  var options = {	width: 500, 
      			height: 400,
    			hAxis: {title: 'Weight'},
    			vAxis: {title: 'Count'},
    			legend: 'none',
    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
    			pointSize:3,
    			series:[{areaOpacity:0.2}]
				}

    	  var chart = new google.visualization.LineChart(document.getElementById('weight_dist_chart_div'));
    	  chart.draw(data, options);
    	                                          		
      }
    </script>

	<div id="weight_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>

	
<h2>Fundal Height/Gestional Age Scatter Chart </h2>
<?php 

$sql = sprintf("SELECT Q_FUNDALHEIGHT,Q_GESTATIONALAGE,Q_USERID, Q_HEALTHPOINTID FROM %s
		WHERE Q_HEALTHPOINTID != 9999
		ORDER BY Q_GESTATIONALAGE ASC",TABLE_ANC);

$fh = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_GESTATIONALAGE', 'Q_FUNDALHEIGHT'],
					<?php
					while($o = mysql_fetch_object($fh)){
					printf("[%d,%d],",$o->Q_GESTATIONALAGE,$o->Q_FUNDALHEIGHT);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_GESTATIONALAGE'},
	    			vAxis: {title: 'Q_FUNDALHEIGHT'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.ScatterChart(document.getElementById('fundgest_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="fundgest_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>

<h2>Diastolic BP Chart </h2>
<?php 

$sql = sprintf("SELECT Q_DIASTOLICBP,COUNT(*) as countbp FROM %s
				WHERE Q_HEALTHPOINTID != 9999
				AND Q_DIASTOLICBP != 0
				AND Q_DIASTOLICBP IS NOT NULL
				GROUP BY Q_DIASTOLICBP
				ORDER BY Q_DIASTOLICBP ASC",TABLE_ANC);

$dbp = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_DIASTOLIC', 'Count'],
					<?php
					while($o = mysql_fetch_object($dbp)){
					printf("[%d,%d],",$o->Q_DIASTOLICBP,$o->countbp);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_DIASTOLIC'},
	    			vAxis: {title: 'Count'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.LineChart(document.getElementById('dbp_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="dbp_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
	
<h2>Systolic BP Chart </h2>
<?php 

$sql = sprintf("SELECT Q_SYSTOLICBP,COUNT(*) as countbp FROM %s
				WHERE Q_HEALTHPOINTID != 9999
				AND Q_SYSTOLICBP != 0
				AND Q_SYSTOLICBP IS NOT NULL
				GROUP BY Q_SYSTOLICBP
				ORDER BY Q_SYSTOLICBP ASC",TABLE_ANC);

$dbp = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_SYSTOLIC', 'Count'],
					<?php
					while($o = mysql_fetch_object($dbp)){
					printf("[%d,%d],",$o->Q_SYSTOLICBP,$o->countbp);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_SYSTOLICBP'},
	    			vAxis: {title: 'Count'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.LineChart(document.getElementById('sbp_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="sbp_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
		

<h2>Cardiac Pulse Chart </h2>
<?php 

$sql = sprintf("SELECT Q_CARDIACPULSE,COUNT(*) as countcp FROM %s
				WHERE Q_HEALTHPOINTID != 9999
				GROUP BY Q_CARDIACPULSE
				ORDER BY Q_CARDIACPULSE ASC",TABLE_ANC);

$cp = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_CARDIACPULSE', 'Count'],
					<?php
					while($o = mysql_fetch_object($cp)){
					printf("[%d,%d],",$o->Q_CARDIACPULSE,$o->countcp);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_CARDIACPULSE'},
	    			vAxis: {title: 'Count'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.LineChart(document.getElementById('cp_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="cp_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
		
		
<h2>Newborn Weight</h2>
<?php 

$sql = sprintf("SELECT Q_NEWBORNWEIGHT,COUNT(*) as countw FROM %s dd
				INNER JOIN %s d ON d._uri = dd._parent_auri
				WHERE d.Q_HEALTHPOINTID != 9999
				GROUP BY Q_NEWBORNWEIGHT
				ORDER BY Q_NEWBORNWEIGHT ASC",TABLE_DELIVERY_BABY, TABLE_DELIVERY);

$w = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_NEWBORNWEIGHT', 'Count'],
					<?php
					while($o = mysql_fetch_object($w)){
					printf("[%d,%d],",$o->Q_NEWBORNWEIGHT,$o->countw);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_NEWBORNWEIGHT'},
	    			vAxis: {title: 'Count'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.LineChart(document.getElementById('nbw_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="nbw_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>

<h2>APGAR 1 MIN</h2>
<?php 

$sql = sprintf("SELECT Q_APGAR1MIN_RESULT,COUNT(*) as countap FROM %s dd
				INNER JOIN %s d ON d._uri = dd._parent_auri
				WHERE d.Q_HEALTHPOINTID != 9999
				AND Q_APGAR1MIN_RESULT IS NOT NULL
				GROUP BY Q_APGAR1MIN_RESULT
				ORDER BY Q_APGAR1MIN_RESULT ASC",TABLE_DELIVERY_BABY, TABLE_DELIVERY);

$ap1 = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_APGAR1MIN_RESULT', 'Count'],
					<?php
					while($o = mysql_fetch_object($ap1)){
					printf("[%d,%d],",$o->Q_APGAR1MIN_RESULT,$o->countap);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_APGAR1MIN_RESULT'},
	    			vAxis: {title: 'Count'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.ColumnChart(document.getElementById('ap1_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="ap1_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>

<h2>APGAR 5 MIN</h2>
<?php 

$sql = sprintf("SELECT Q_APGAR5MIN_RESULT,COUNT(*) as countap FROM %s dd
				INNER JOIN %s d ON d._uri = dd._parent_auri
				WHERE d.Q_HEALTHPOINTID != 9999
				AND Q_APGAR5MIN_RESULT IS NOT NULL
				GROUP BY Q_APGAR5MIN_RESULT
				ORDER BY Q_APGAR5MIN_RESULT ASC",TABLE_DELIVERY_BABY, TABLE_DELIVERY);

$ap5 = $API->runSql($sql);
?>
	<script type="text/javascript">
	
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {
		'packages':['corechart']});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
					['Q_APGAR5MIN_RESULT', 'Count'],
					<?php
					while($o = mysql_fetch_object($ap5)){
					printf("[%d,%d],",$o->Q_APGAR5MIN_RESULT,$o->countap);
				}
			?>
	    	  	]);
	
	    	  var options = {	width: 500, 
	      			height: 400,
	    			hAxis: {title: 'Q_APGAR5MIN_RESULT'},
	    			vAxis: {title: 'Count'},
	    			legend: 'none',
	    			chartArea:{left:50,top:20,width:"90%",height:"75%"},
	    			pointSize:3,
	    			series:[{areaOpacity:0.2}]
					}
	
	    	  var chart = new google.visualization.ColumnChart(document.getElementById('ap5_dist_chart_div'));
	    	  chart.draw(data, options);
	    	                                          		
	      }
	    </script>
	
		<div id="ap5_dist_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
<?php
include_once "../config.php";
header("Content-type: text/html; charset:UTF8");


// check for missing follow up visit
$sql = "SELECT 	A._URI, 
				A.Q_USERID, 
				A.Q_HEALTHPOINTID, 
				DATE_ADD(A.Q_APPOINTMENTDATE, INTERVAL +7 DAY) AS DueDate,
				hp.hpname,
				B._CREATION_DATE 
		FROM ".ANCFIRST." A 
		LEFT OUTER JOIN (SELECT _URI, 
								Q_USERID, 
								Q_HEALTHPOINTID,
								_CREATION_DATE 
						FROM ".ANCFOLLOW." WHERE Q_FOLLOWUPNO = 2) 
						B ON A.Q_USERID = B.Q_USERID AND A.Q_HEALTHPOINTID = B.Q_HEALTHPOINTID
		INNER JOIN healthpoint hp ON hp.hpcode = A.Q_HEALTHPOINTID
		WHERE Q_APPOINTMENTDATE < DATE_ADD(Now(), INTERVAL -7 DAY)
		AND Q_APPOINTMENTDATE < Q_EDD
		ORDER BY A.Q_APPOINTMENTDATE ASC";

$result = $API->runSql($sql);
$summary = array();
while($row = mysql_fetch_object($result)){
	array_push($summary,$row);
}



// check for follow up 3 after follow up 2
for ($i=2; $i<6; $i++){
	$sql = "SELECT 	A._URI, 
					A.Q_USERID, 
					A.Q_HEALTHPOINTID, 
					DATE_ADD(A.Q_APPOINTMENTDATE, INTERVAL +7 DAY) AS DueDate, 
					hp.hpname,
					B._CREATION_DATE 
			FROM ".ANCFOLLOW." A 
			LEFT OUTER JOIN (SELECT _URI, 
									Q_USERID, 
									Q_HEALTHPOINTID,
									_CREATION_DATE 
							FROM ".ANCFOLLOW." WHERE Q_FOLLOWUPNO = ".($i+1).") 
							B ON A.Q_USERID = B.Q_USERID AND A.Q_HEALTHPOINTID = B.Q_HEALTHPOINTID
			INNER JOIN healthpoint hp ON hp.hpcode = A.Q_HEALTHPOINTID
			WHERE Q_APPOINTMENTDATE < DATE_ADD(Now(), INTERVAL -7 DAY)
			AND Q_FOLLOWUPNO = ".$i."
			ORDER BY A.Q_APPOINTMENTDATE ASC";
	
	$result = $API->runSql($sql);
	while($row = mysql_fetch_object($result)){
		array_push($summary,$row);
	}
}

$monthsummary = array();
foreach($summary as $s){
	
	$duedate = strtotime($s->DueDate);
	$key = date('m-Y',$duedate);
	if(!isset($monthsummary[$key])){
		$monthsummary[$key] = new stdClass;
		$monthsummary[$key]->met = 0;
		$monthsummary[$key]->unmet = 0;
	}
	if($s->_CREATION_DATE == ""){
		$monthsummary[$key]->unmet++;
	} else {
		$createdate = strtotime($s->_CREATION_DATE);
		if($duedate<$createdate){
			$monthsummary[$key]->unmet++;
		} else {
			$monthsummary[$key]->met++;
		}
	}
}
foreach($monthsummary as $k=>$v){
	$monthsummary[$k]->percent = 100*$v->met/($v->met + $v->unmet);
}

?>
<html>
<head>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {
	packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Month/Year');
		data.addColumn('number', 'Percent on target');
		data.addRows(<?php echo count($monthsummary);?>);

		<?php 
			$c = 0;
			foreach($monthsummary as $k=>$v){
				printf("data.setValue(%d,%d,'%s');",$c,0,$k);
				printf("data.setValue(%d,%d,%d);",$c,1,$v->percent);
				$c++;
			}
		?>

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 400, height: 240, title: 'Percentage of met appointments',
                          hAxis: {title: 'Month/Year'},vAxis:{maxValue:100,minValue:0}
                         });
      }
    </script>
  </head>

  <body>
    <div id="chart_div"></div>
  </body>
</html>
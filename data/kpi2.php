<?php
include_once "../config.php";
header("Content-type: text/html; charset:UTF8");

$sql = "SELECT 	A._URI, 
				A.Q_USERID, 
				A.Q_HEALTHPOINTID, 
				Year(A.Q_EDD) AS EDDYear,
				Month(A.Q_EDD) AS EDDMonth, 
				hpname, 
				B._URI AS LabTestID
		FROM ANCFIRST_VISITV4_CORE A 
		LEFT OUTER JOIN ANCLAB_TESTV2_CORE B ON A.Q_USERID = B.Q_USERID AND A.Q_HEALTHPOINTID = B.Q_HEALTHPOINTID
		INNER JOIN healthpoint hp ON hp.hpcode = A.Q_HEALTHPOINTID
		WHERE A.Q_EDD < Now()
		ORDER BY Q_EDD ASC";

$result = $API->runSql($sql);

$monthsummary = array();
while($row = mysql_fetch_array($result)){
	//$key = $row['EDDMonth']."-".$row['EDDYear'];
	$key = $row['EDDYear'];
	if (!isset($monthsummary[$key])){
		$monthsummary[$key] = new stdClass();
		$monthsummary[$key]->done = 0;
		$monthsummary[$key]->notdone = 0;
	}
	
	if($row['LabTestID'] == ""){
		$monthsummary[$key]->notdone++;
	} else {
		$monthsummary[$key]->done++;
	}
}

/*foreach($monthsummary as $k=>$v){
	$monthsummary[$k]->percent = 100*$v->done/($v->done + $v->notdone);
}*/

print_r($monthsummary);

?>

<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(2);
        data.setValue(0, 0, 'Test complete');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Eat');
        data.setValue(1, 1, 2);

        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 450, height: 300, title: 'My Daily Activities'});
      }
    </script>
  </head>

  <body>
    <div id="chart_div"></div>
  </body>
</html>
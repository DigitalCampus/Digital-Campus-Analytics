<?php
include_once "../config.php";
header("Content-type: text/html; charset:UTF8");
$sql = "SELECT 	A.Q_USERID, 
				A.Q_HEALTHPOINTID,
				hp.hpname, 
				MONTH(Q_EDD) AS EDDMonth, 
				YEAR(Q_EDD) AS EDDYear, 
				COUNT(B._URI) AS NoFollowUps 
		FROM ".ANCFIRST." A
		LEFT OUTER JOIN ".ANCFOLLOW." B ON A.Q_USERID = B.Q_USERID AND A.Q_HEALTHPOINTID = B.Q_HEALTHPOINTID
		INNER JOIN healthpoint hp ON hp.hpcode = A.Q_HEALTHPOINTID
		WHERE Q_EDD <= NOW()
		GROUP BY A.Q_USERID, A.Q_HEALTHPOINTID, MONTH(Q_EDD), YEAR(Q_EDD)
		ORDER BY EDDYear, EDDMonth ";

$result = $API->runSql($sql);

$monthsummary = array();
while($row = mysql_fetch_array($result)){
	$key = $row['EDDMonth']."-".$row['EDDYear'];
	if (!isset($monthsummary[$key])){
		$monthsummary[$key] = new stdClass();
		$monthsummary[$key]->ontargetcount = 0;
		$monthsummary[$key]->offtargetcount = 0;
	}
	if($row['NoFollowUps'] == 0){
		$monthsummary[$key]->offtargetcount++;
	} else {
		$monthsummary[$key]->ontargetcount++;
	}
}
foreach($monthsummary as $k=>$v){
	$monthsummary[$k]->percent = 100*$v->ontargetcount/($v->ontargetcount + $v->offtargetcount);
}
?>

<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
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
        chart.draw(data, {width: 400, height: 240, title: 'Percentage of women receiving 2 or more visits',
                          hAxis: {title: 'Month/Year'},vAxis:{maxValue:100,minValue:0}
                         });
      }
    </script>
  </head>

  <body>
    <div id="chart_div"></div>
  </body>
</html>
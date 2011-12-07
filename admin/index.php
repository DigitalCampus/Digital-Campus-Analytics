<?php
require_once "../config.php";
$HEADER = "<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
$HEADER .= '<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>';
require_once "../includes/header.php";

// only allow access by admins
if($USER->getProp('permissions.admin') != "true"){
	writeToLog('warning','adminpage','accessdenied');
	echo getString ("warning.accessdenied");
	die;
}

$nodays = optional_param("nodays",31,PARAM_INT);
$limit = optional_param("limit",20,PARAM_INT);
$lastlogin = $API->adminLastLogin();
$neverlogin = $API->adminNeverLogin($nodays);
$userhits = $API->adminUserHits($nodays);
$dailyhits = $API->adminDailyHits($nodays);
$popular = $API->adminPopularPages($nodays,$limit);

include_once('../includes/menu-admin.php');
?>

<h2>Last Login</h2>
<table class="admin">
		<tr>
			<th>Name</th>
			<th>Last Login</th>
		</tr>
		<?php 
			foreach ($lastlogin as $ll){
				echo "<tr class='n'>";
				echo "<td nowrap>".$ll->firstname." ".$ll->lastname."</td>";
				echo "<td nowrap>".$ll->propvalue."</td>";
				echo "</tr>";
			}
		?>
</table>


<h2>Not logged in for last <?php echo $nodays;?> days</h2>
<table class="admin">
		<tr>
			<th>Name</th>
		</tr>
		<?php 		
			foreach ($neverlogin as $ll){
				echo "<tr class='n'>";
				echo "<td nowrap>".$ll->firstname." ".$ll->lastname."</td>";
				echo "</tr>";
			}
		?>
</table>
 
<h2>User hits for last <?php echo $nodays;?> days</h2>
<table class="admin">
		<tr>
			<th>Name</th>
			<th>No hits</th>
		</tr>
		<?php 	
			foreach ($userhits as $uh){
				echo "<tr class='n'>";
				echo "<td nowrap>".$uh->firstname." ".$uh->lastname."</td>";
				echo "<td nowrap>".$uh->hits."</td>";
				echo "</tr>";
			}
				
		?>
</table>

<h2>Daily hits for last <?php echo $nodays;?> days</h2>
 
<script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
      function drawChart() {
          
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number','Total Hits');
        <?php 
        echo "data.addRows(".($nodays+1).");";
        $d = 0;
        $date = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $date = $date - ($nodays*86400);
        for($c = 0; $c <$nodays+1; $c++){
        	//$tempc =  $date->format('d-n-Y');
        	$tempc =  date('j-n-Y',$date);
        	$tempd = $dailyhits[$d]->logday."-".$dailyhits[$d]->logmonth."-".$dailyhits[$d]->logyear;
        	if($tempc == $tempd){
        		echo "data.setValue(".$c.",0,'".$tempc."');";
        		echo "data.setValue(".$c.",1,".$dailyhits[$d]->hits.");";
        		$d++;
        	} else {
        		echo "data.setValue(".$c.",0,'".$tempc."');";
        		echo "data.setValue(".$c.",1,0);";	 		
        	}
        	$date = $date + 86400;
        }
        ?>

        var chart = new google.visualization.LineChart(document.getElementById('dailyhits_chart_div'));
        chart.draw(data, {width: 800, height: 240, title: 'Daily Hits'});
      }
    </script>

	<div id="dailyhits_chart_div" class="graph"><?php echo getstring('warning.graph.unavailable');?></div>
 

<h2><?php echo $limit; ?> most popular pages in last <?php echo $nodays; ?> days</h2>
<table class="admin">
		<tr>
			<th>Page</th>
			<th>Hits</th>
		</tr>
		<?php 
			foreach ($popular as $p){
				echo "<tr class='n'>";
				echo "<td>".$p->logmsg."</td>";
				echo "<td nowrap>".$p->hits."</td>";
				echo "</tr>";
			}
		?>
</table>


<?php 

include_once "../includes/footer.php";
?>
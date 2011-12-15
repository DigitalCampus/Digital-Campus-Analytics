<h2><?php echo getString("datacheck.missing.title")?></h2>
<p><?php echo getString("datacheck.missing.info")?></p>
<?php 
$missing = $dc->missingProtocols();
if(count($missing)>0){
?>

	<table class="admin">
		<tr>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Reason</th>
		</tr>
		<?php 	
			foreach ($missing as $m){
				echo "<tr class='n'>";
				echo "<td nowrap>".$m->submittedname." at ".$m->protocollocation."</td>";
				echo "<td nowrap>".$m->patientlocation."/".$m->Q_USERID."</td>";
				echo "<td nowrap>".$m->reason."</td>";
				echo "</tr>";
			}
				
		?>
	</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.missing.none")."</p>";
	}
?>


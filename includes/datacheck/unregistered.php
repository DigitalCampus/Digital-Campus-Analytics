<h2><?php echo getString("datacheck.unregistered.title")?></h2>
<p><?php echo getString("datacheck.unregistered.info")?></p>
<?php 

	$unregistered = $API->datacheckUnregistered();
	if(count($unregistered)>0){
?>
	
<table class="admin">
		<tr>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($unregistered as $d){
				echo "<tr class='n'>";
				echo "<td nowrap>".$d->submittedname." at ".$d->protocollocation."</td>";
				echo "<td nowrap>".$d->patientlocation."/".$d->Q_USERID."</td>";
				echo "<td nowrap>".getstring($d->protocol)."</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.unregistered.none")."</p>";
	}
?>
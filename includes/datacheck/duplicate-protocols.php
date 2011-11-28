<h2><?php echo getString("datacheck.duplicate.protocol.title")?></h2>
<p><?php echo getString("datacheck.duplicate.protocol.info")?></p>
<?php 

	$dup = $API->datacheckDuplicateProtocols();
	if(count($dup)>0){
?>
	
<table class="admin">
		<tr>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($dup as $d){
				echo "<tr class='n'>";
				echo "<td nowrap>".$d->submittedname." at ".$d->protocollocation."</td>";
				echo "<td nowrap>".$d->patientlocation."/".$d->Q_USERID."</td>";
				echo "<td nowrap>".getstring($d->protocol);
				if(isset($d->Q_FOLLOWUPNO)){
					echo " - ".$d->Q_FOLLOWUPNO;
				}
				echo "</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.duplicate.protocol.none")."</p>";
	}
?>
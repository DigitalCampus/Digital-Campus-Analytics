<h2><?php echo getString("datacheck.duplicate.reg.title")?></h2>
<p><?php echo getString("datacheck.duplicate.reg.info")?></p>
<?php 
	$duplicateReg = $API->datacheckDuplicateReg();
	if(count($duplicateReg)>0){
?>
	
	<table class="admin">
			<tr>
				<th>Health Post</th>
				<th>Patient ID</th>
			</tr>
			<?php 	
				foreach ($duplicateReg as $d){
					echo "<tr class='n'>";
					echo "<td nowrap>".$d->healthpointname."</td>";
					echo "<td nowrap>".$d->patientid."</td>";
					echo "</tr>";
				}
					
			?>
	</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.duplicate.reg.none")."</p>";
	}
?>
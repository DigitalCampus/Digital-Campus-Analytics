<?php 
$opts=array('hpcodes'=>$report->hpcodes);
$dc = new DataCheck();
$unregistered = $dc->unregistered($opts);

if(count($unregistered)>0){
?>
	
<table class="taskman">
		<tr>
			<th><?php echo getString("submitted.th.date")?></th>
			<th><?php echo getString("submitted.th.by")?></th>
			<th><?php echo getString("submitted.th.patientid")?></th>
			<th><?php echo getString("submitted.th.protocol")?></th>
		</tr>
		<?php 	
			foreach ($unregistered as $o){
				$d= strtotime($o->datestamp);
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
							$CONFIG->homeAddress,
							$o->patienthpcode,
							$o->Q_USERID,
							$o->protocol
							);
				printf("<td nowrap>%s<br/>%s</td>",displayAsEthioDate($d), date('D d M Y',$d));
				printf("<td nowrap>%s (%s)</td>",$o->submittedname,displayHealthPointName($o->protocolhpcode));
				echo "<td nowrap>".displayHealthPointName($o->patienthpcode,$o->Q_USERID)."</td>";
				echo "<td nowrap>".getstring($o->protocol)."</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.unregistered.none")."</p>";
	}
?>
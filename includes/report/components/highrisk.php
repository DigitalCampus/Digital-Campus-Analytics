<?php 
$opts=array('hpcodes'=>$report->hpcodes);
$ra = new RiskAssessment();
$highrisk = $ra->getHighRiskPatients($opts);

if(count($highrisk)>0){
?>
	
<table class="taskman">
		<tr>
			<th><?php echo getString("report.highrisk.th.patientid")?></th>
			<th><?php echo getString("report.highrisk.th.patientname")?></th>
			<th><?php echo getString("report.highrisk.th.risks")?></th>
		</tr>
				<?php 	
			foreach ($highrisk as $hr){
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
							$CONFIG->homeAddress,
							$hr->hpcode,
							$hr->userid,
							PROTOCOL_REGISTRATION
							);
				echo "<td nowrap>".displayHealthPointName($hr->patienthpcode,$hr->userid)."</td>";
				echo "<td nowrap>";
				if(trim($hr->patientname) == ""){
					printf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
				} else {
					echo $hr->patientname;
				}
				echo "</td>";
				echo "<td nowrap><ul>";
				$risks = $ra->getRisks_Cache($hr->hpcode, $hr->userid);
				foreach ($risks->risks as $s){
					printf("<li>%s</li>",getstring("risk.factor.".$s));	
				}
				echo "</ul></td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("report.highrisk.none")."</p>";
	}
?>


<table class="taskman">
	<tr>
		<th><?php echo getString("riskmanager.th.riskweight")?></th>
		<th><?php echo getString("riskmanager.th.patient")?></th>
		<th><?php echo getString("riskmanager.th.location")?>/<?php echo getString("riskmanager.th.patientid")?></th>
		<th><?php echo getString("riskmanager.th.reason")?></th>
		<th><?php echo getString("riskmanager.th.options")?></th>
	</tr>
	<?php 
		foreach ($atRisk as $ar){
			$class="n";
			echo "<tr class='".$class."'>";
			echo "<td nowrap style='text-align:center'>";
			//displayUserRiskFactor($ar->riskfactorweight);
			echo "</td>";
			echo "<td nowrap>".$ar->Q_USERNAME." ".$ar->Q_USERFATHERSNAME." ".$ar->Q_USERGRANDFATHERSNAME."</td>";
			echo "<td nowrap>".$ar->patientlocation."/".$ar->Q_USERID."</td>";
			
			echo "<td>";
			//foreach($ar->riskfactors as $risk){
			//	echo $risk."<br/>";
			//}
			echo "</td>";
			printf("<td><a href='patient.php?hpcode=%s&patientid=%s'>%s</a></td>",$ar->hpcode,$ar->Q_USERID,getString('riskmanager.view.info'));
			echo "</tr>";
		}
			
	?>
</table>
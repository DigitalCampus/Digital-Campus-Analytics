<?php 
	$tasks = $API->getAllTasks($duein);
?>

<table class="taskman">
	<tr>
		<th><?php echo getString("apptmanager.th.duedate")?></th>
		<th><?php echo getString("apptmanager.th.protocol")?></th>
		<th><?php echo getString("apptmanager.th.patient")?></th>
		<th><?php echo getString("apptmanager.th.riskfactor")?></th>
		<th><?php echo getString("apptmanager.th.location")?></th>
	</tr>

	<?php 
		$today = strtotime(date('Y-m-d'));
		foreach ($tasks as $task){
			$task->duedate = strtotime($task->duedate);
			if ($task->duedate < $today){
				$class="overdue";
			} else if ($task->duedate == $today){
				$class="duetoday";
			} else {
				$class="n";
			}
			
			echo "<tr class='".$class."'>";
			echo "<td nowrap>".date('D d M Y',$task->duedate)." (";
			echo displayAsEthioDate($task->duedate);
			echo ")</td>";
			echo "<td>".$task->protocol."</td>";
			echo "<td>".$task->patientname."</td>";
			$pat = $API->getPatients(array('hpcode'=>$task->hpcode,'patid'=>$task->patid));
			echo "<td>";
			displayUserRiskFactor($pat[0]->riskfactorweight);
			echo "</td>";
			echo "<td>".$task->location."</td>";
			echo "</tr>";
		}
	?>
</table>
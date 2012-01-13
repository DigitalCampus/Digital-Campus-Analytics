<?php 

$opts = array("days"=>$days);
$tasks = $API->getTasksDue($opts);

?>
<table class="taskman">
	<tr>
		<th><?php echo getString("tasks.th.date")?></th>
		<th><?php echo getString("tasks.th.patientid")?></th>
		<th><?php echo getString("tasks.th.patient")?></th>
		<th><?php echo getString("tasks.th.protocol")?></th>
	</tr>
	<?php 

		/*foreach($tasks as $task){
			$d = strtotime($task->datedue);
			echo "<tr class='n'>";
			echo "<td nowrap>".displayAsEthioDate($d)."</td>";
			echo "<td nowrap>".$task->patientlocation."/".$task->Q_USERID."</td>";
			echo "<td nowrap>";
			if(trim($task->patientname) == ""){
				printf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
			} else {
				echo $task->patientname;
			}
			echo "</td>";
			echo "<td nowrap>".$task->protocol."</td>";
			echo "</tr>";
		}*/
			
	?>
</table>




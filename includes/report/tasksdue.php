<?php 

$opts = array("days"=>$days,'hpcode'=>$currentHPcode);
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

		foreach($tasks as $t){
			$d = strtotime($t->datedue);
			echo "<tr class='n'>";
			echo "<td nowrap>".displayAsEthioDate($d)."<br/>". date('D d M Y',$d)."</td>";
			echo "<td nowrap>".$t->patientlocation."/".$t->userid."</td>";
			echo "<td nowrap>";
			if(trim($t->patientname) == ""){
				printf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
			} else {
				echo $t->patientname;
			}
			echo "</td>";
			echo "<td nowrap>".getstring($t->protocol)."</td>";
			echo "</tr>";
		}
			
	?>
</table>




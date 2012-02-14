<?php 

$opts = array("days"=>$days,'hpcodes'=>$report->hpcodes);
$tasks = $API->getDeliveriesDue($opts);
$ra = new RiskAssessment();
if(count($tasks)>0){
?>
<table class="taskman">
	<tr>
		<th><?php echo getString("tasks.th.date")?></th>
		<th><?php echo getString("tasks.th.patientid")?></th>
		<th><?php echo getString("tasks.th.patient")?></th>
		<th><?php echo getString("report.highrisk.th.risks")?></th>
	</tr>
	<?php 

		foreach($tasks as $t){
			$d = strtotime($t->datedue);
			echo "<tr class='n'>";
			echo "<td nowrap>".displayAsEthioDate($d)."<br/>". date('D d M Y',$d)."</td>";
			echo "<td nowrap>".displayHealthPointName($t->patienthpcode,$t->userid)."</td>";
			echo "<td nowrap>";
			if(trim($t->patientname) == ""){
				printf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
			} else {
				echo $t->patientname;
			}
			echo "</td>";
			echo "<td nowrap>";
			$risks = $ra->getRisks_Cache($t->patienthpcode, $t->userid);
			echo getstring("risk.".$risks->category);
			echo "<ul>";
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
	echo getstring('report.deliveriesdue.none',$days);
}
?>




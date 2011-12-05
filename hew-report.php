<?php
include_once "config.php";
$PAGE = "hew";

include_once "includes/header.php";

$days = optional_param("days",31,PARAM_INT);
$userid = optional_param("userid","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);

// TODO check permissions, so can't alter the url to get record of someone else
$users = $API->getUsers();

printf("<h2 class='printhide'>%s</h2>", getString("hewmanager.title"));
$currentHEW = "";
?>
<form action="" method="get" class="printhide">
	<?php echo getString("hewmanager.form.hew");?>
	<select name="userid">
		<?php 
			foreach($users as $u){
				if ($userid == $u->userid){
					$currentHEW = $u->firstname." ".$u->lastname;
					printf("<option value='%d' selected='selected'>%s (%s)</option>",$u->userid, $currentHEW, $u->hpname);
				} else {
					printf("<option value='%d'>%s (%s)</option>",$u->userid, $u->firstname." ".$u->lastname, $u->hpname);
				}
			}
		?>
	</select>
	<input type="submit" name="submit" value="<?php echo getString("hewmanager.form.searchbtn");?>"></input>
</form>

<?php 
if ($userid == ""){
	include_once "includes/footer.php";
	die;
}
printf("<h2>%s</h2>", getString("hewmanager.submitted",array($days,$currentHEW)));

$opts=array('days'=>$days);
$submitted = $API->getProtocolsSubmitted($opts);
?>
<table class="taskman">
<tr>
		<th><?php echo getString("submitted.th.date")?></th>
		<th><?php echo getString("submitted.th.patientid")?></th>
		<th><?php echo getString("submitted.th.patient")?></th>
		<th><?php echo getString("submitted.th.protocol")?></th>
	</tr>
	<?php 

		foreach ($submitted->protocols as $s){
			if ($s->userid== $userid){
				$d= strtotime($s->datestamp);
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
							$CONFIG->homeAddress,
							$s->Q_HEALTHPOINTID,
							$s->Q_USERID,
							preg_replace('([0-9])','',str_replace(' ','',strtolower($s->protocol)))
							);
				echo "<td nowrap>".displayAsEthioDate($d)."</td>";
				echo "<td nowrap>".$s->patientlocation."/".$s->Q_USERID."</td>";
				echo "<td nowrap>";
				if(trim($s->patientname) == ""){
					echo getString('warning.patientreg');
				} else {
					echo $s->patientname;
				}
				echo "</td>";
				echo "<td nowrap>".getstring($s->protocol)."</td>";
				echo "</tr>";
			}
		}
			
	?>
</table>

<?php 

$opts = array("days"=>$days);
$tasks = $API->getTasksDue($userid,$opts);

printf("<h2>%s</h2>", getString("hewmanager.tasks",array($days)));
?>
<table class="taskman">
	<tr>
		<th><?php echo getString("tasks.th.date")?></th>
		<th><?php echo getString("tasks.th.patientid")?></th>
		<th><?php echo getString("tasks.th.patient")?></th>
		<th><?php echo getString("tasks.th.protocol")?></th>
	</tr>
	<?php 

		foreach($tasks as $task){
			$d = strtotime($task->datedue);
			echo "<tr class='n'>";
			echo "<td nowrap>".displayAsEthioDate($d)."</td>";
			echo "<td nowrap>".$task->patientlocation."/".$task->Q_USERID."</td>";
			echo "<td nowrap>";
			if(trim($task->patientname) == ""){
				echo getString('warning.patientreg');
			} else {
				echo $task->patientname;
			}
			echo "</td>";
			echo "<td nowrap>".$task->protocol."</td>";
			echo "</tr>";
		}
			
	?>
</table>

<?php 
include_once "includes/footer.php";
?>
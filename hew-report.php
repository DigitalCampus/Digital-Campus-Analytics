<?php
include_once "config.php";
$PAGE = "hew";

include_once "includes/header.php";

$days = optional_param("days",31,PARAM_INT);
$user = optional_param("user","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);

// TODO check permissions, so can;t alter the url to get record of someone else
// TODO switch to use 
$users = $API->getUsers();

printf("<h2 class='printhide'>%s</h2>", getString("hewmanager.title"));
?>
<form action="" method="get" class="printhide">
	<?php echo getString("hewmanager.form.hew");?>
	<select name="user">
		<?php 
			foreach($users as $u){
				if ($user == $u->user_uri){
					$currentHEW = $u->firstname." ".$u->lastname;
					printf("<option value='%s' selected='selected'>%s (%s)</option>",$u->user_uri, $currentHEW, $u->hpname);
				} else {
					printf("<option value='%s'>%s (%s)</option>",$u->user_uri, $u->firstname." ".$u->lastname, $u->hpname);
				}
			}
		?>
	</select>
	<input type="submit" name="submit" value="<?php echo getString("hewmanager.form.searchbtn");?>"></input>
</form>

<?php 
if ($user == ""){
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
			if ($s->_CREATOR_URI_USER == $user){
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

$sql = "SELECT * FROM (";
$sql .= "SELECT 	A.Q_APPOINTMENTDATE,
				A._CREATOR_URI_USER,
				A.Q_USERID,
				CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
				A.Q_HEALTHPOINTID,
				php.hpname as patientlocation,
				'".getstring(PROTOCOL_ANCFOLLOW)."' AS protocol
		FROM ".TABLE_ANCFIRST." A
		LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON A.Q_USERID = R.Q_USERID AND A.Q_HEALTHPOINTID = R.Q_HEALTHPOINTID
		INNER JOIN healthpoint php ON php.hpcode = A.Q_HEALTHPOINTID
		WHERE A.Q_APPOINTMENTDATE > now()
		AND A.Q_APPOINTMENTDATE < DATE_ADD(now(), INTERVAL +31 DAY)
		AND A._CREATOR_URI_USER ='".$user."'";

$sql .= " UNION 
		SELECT 	A.Q_APPOINTMENTDATE,
				A._CREATOR_URI_USER,
				A.Q_USERID,
				CONCAT(R.Q_USERNAME,' ',R.Q_USERFATHERSNAME,' ',R.Q_USERGRANDFATHERSNAME) as patientname,
				A.Q_HEALTHPOINTID,
				php.hpname as patientlocation,
				'".getstring(PROTOCOL_ANCFOLLOW)."' AS protocol
		FROM ".TABLE_ANCFOLLOW." A
		LEFT OUTER JOIN ".TABLE_REGISTRATION." R ON A.Q_USERID = R.Q_USERID AND A.Q_HEALTHPOINTID = R.Q_HEALTHPOINTID
		INNER JOIN healthpoint php ON php.hpcode = A.Q_HEALTHPOINTID
		WHERE A.Q_APPOINTMENTDATE > now()
		AND A.Q_APPOINTMENTDATE < DATE_ADD(now(), INTERVAL +31 DAY)
		AND A._CREATOR_URI_USER ='".$user."'";

$sql .= ") C  ORDER BY Q_APPOINTMENTDATE";
// TODO add permissions

$result = $API->runSql($sql);

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

		while($task = mysql_fetch_array($result)){
			$d = strtotime($task['Q_APPOINTMENTDATE']);
			echo "<tr class='n'>";
			echo "<td nowrap>".displayAsEthioDate($d)."</td>";
			echo "<td nowrap>".$task['patientlocation']."/".$task['Q_USERID']."</td>";
			echo "<td nowrap>";
			if(trim($task['patientname']) == ""){
				echo getString('warning.patientreg');
			} else {
				echo $task['patientname'];
			}
			echo "</td>";
			echo "<td nowrap>".$task['protocol']."</td>";
			echo "</tr>";
		}
			
	?>
</table>

<?php 
include_once "includes/footer.php";
?>
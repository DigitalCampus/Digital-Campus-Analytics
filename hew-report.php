<?php
include_once "config.php";
$PAGE = "hew";

include_once "includes/header.php";

$days = optional_param("days",31,PARAM_INT);
$userid = optional_param("userid","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);

$users = $API->getUsers();

printf("<h2 class='printhide'>%s</h2>", getString("hewreport.title"));
$currentUser = $API->getUserById($userid);
?>
<form action="" method="get" class="printhide">
	<?php echo getString("hewreport.form.hew");?>
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
	<input type="submit" name="submit" value="<?php echo getString("hewreport.form.searchbtn");?>"></input>
</form>


To include here:
<ul>
<li>Overview of KPIs</li>
<li>Protocols submitted</li>
<li>Missing protocols/no registrations</li>
<li>Overdue tasks</li>
<li>Upcoming appointments (next month)</li>
<li>Upcoming deliveries (next month)</li>
<li>High risk patients</li>
</ul>
<?php 
if ($userid == ""){
	include_once "includes/footer.php";
	die;
} else if ($currentUser == null){
	include_once "includes/footer.php";
	die;
}

printf("<h2>%s</h2>", getString("hewreport.kpioverview",array($days)));

printf("<h2>%s</h2>", getString("hewreport.submitted",array($days)));

printf("<h2>%s</h2>", getString("hewreport.datacheck.registration",array($days)));

printf("<h2>%s</h2>", getString("hewreport.datacheck.missing",array($days)));

printf("<h2>%s</h2>", getString("hewreport.overdue",array($days)));

printf("<h2>%s</h2>", getString("hewreport.tasksdue",array($days)));

printf("<h2>%s</h2>", getString("hewreport.deliveriesdue",array($days)));

printf("<h2>%s</h2>", getString("hewreport.highrisk",array($days)));

include_once "includes/footer.php";
?>
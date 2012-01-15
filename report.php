<?php
include_once "config.php";
$PAGE = "report";

include_once "includes/header.php";

$days = optional_param("days",31,PARAM_INT);
$userid = optional_param("userid","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);

$users = $API->getUsers();

printf("<h2 class='printhide'>%s</h2>", getString("report.title"));
$currentUser = $API->getUserById($userid);
?>
<form action="" method="get" class="printhide">
	<?php echo getString("report.form.hew");?>
	<select name="userid">
		<?php 
			foreach($users as $u){
				if ($userid == $u->userid){
					$currentHEW = $u->firstname." ".$u->lastname;
					$currentHP = $u->hpname;
					$currentHPcode = $u->hpcode;
					printf("<option value='%d' selected='selected'>%s (%s)</option>",$u->userid, $currentHEW, $u->hpname);
				} else {
					printf("<option value='%d'>%s (%s)</option>",$u->userid, $u->firstname." ".$u->lastname, $u->hpname);
				}
			}
		?>
	</select>
	<input type="submit" name="submit" value="<?php echo getString("report.form.searchbtn");?>"></input>
</form>

<?php 
if ($userid == ""){
	include_once "includes/footer.php";
	die;
} else if ($currentUser == null){
	include_once "includes/footer.php";
	die;
}

printf("<h2>%s (%s)</h2>", $currentHEW, $currentHP);

printf("<h3>%s</h3>", getString("report.kpioverview"));
include_once('includes/report/kpioverview.php');

printf("<h3>%s</h3>", getString("report.submitted",array($days)));
include_once('includes/report/submitted.php');

printf("<h3>%s</h3>", getString("report.datacheck.registration"));
include_once('includes/report/datacheck.registration.php');

printf("<h3>%s</h3>", getString("report.datacheck.missing"));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("report.overdue"));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("report.tasksdue",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("report.deliveriesdue",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("report.highrisk"));
echo "to be added";// TODO 

include_once "includes/footer.php";
?>
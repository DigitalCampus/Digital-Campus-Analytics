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
					$currentHP = $u->hpname;
					$currentHPcode = $u->hpcode;
					printf("<option value='%d' selected='selected'>%s (%s)</option>",$u->userid, $currentHEW, $u->hpname);
				} else {
					printf("<option value='%d'>%s (%s)</option>",$u->userid, $u->firstname." ".$u->lastname, $u->hpname);
				}
			}
		?>
	</select>
	<input type="submit" name="submit" value="<?php echo getString("hewreport.form.searchbtn");?>"></input>
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

printf("<h3>%s</h3>", getString("hewreport.kpioverview",array($days)));
echo "to be added"; // TODO 

printf("<h3>%s</h3>", getString("hewreport.submitted",array($days)));
include_once('includes/hewreport/submitted.php');

printf("<h3>%s</h3>", getString("hewreport.datacheck.registration",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("hewreport.datacheck.missing",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("hewreport.overdue",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("hewreport.tasksdue",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("hewreport.deliveriesdue",array($days)));
echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("hewreport.highrisk",array($days)));
echo "to be added";// TODO 

include_once "includes/footer.php";
?>
<?php
include_once "config.php";
$PAGE = "report";

include_once "includes/header.php";

$days = optional_param("days",31,PARAM_INT);
$currentHPcode = optional_param("hpcode",$USER->hpcode,PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);


printf("<h2 class='printhide'>%s</h2>", getString("report.title"));
$hps = $API->getHealthPoints();

$currenthpname = "";
?>
<form action="" method="get" class="printhide">
	<select name="hpcode">
		<?php 
			foreach($hps as $hp){
				if ($hp->hpcode == $currentHPcode){
					$currenthpname =  $hp->hpname;
					printf("<option value='%d' selected='selected'>%s</option>",$hp->hpcode, $hp->hpname);
				} else {
					printf("<option value='%d'>%s</option>",$hp->hpcode, $hp->hpname);
				}
			}
		?>
	</select>
	<input type="submit" name="submit" value="<?php echo getString("report.form.searchbtn");?>"></input>
</form>

<?php 

printf("<h2>%s</h2>", $currenthpname);

printf("<h3>%s</h3>", getString("report.kpioverview"));
include_once('includes/report/kpioverview.php');

printf("<h3>%s</h3>", getString("report.submitted",array($days)));
include_once('includes/report/submitted.php');

printf("<h3>%s</h3>", getString("report.datacheck.registration"));
include_once('includes/report/datacheck.registration.php');

//printf("<h3>%s</h3>", getString("report.datacheck.missing"));
//echo "to be added";// TODO 

printf("<h3>%s</h3>", getString("report.overdue"));
include_once('includes/report/overdue.php'); 

printf("<h3>%s</h3>", getString("report.tasksdue",array($days)));
include_once('includes/report/tasksdue.php');

printf("<h3>%s</h3>", getString("report.deliveriesdue",array($days)));
include_once('includes/report/deliveries.php'); 

printf("<h3>%s</h3>", getString("report.highrisk"));
include_once('includes/report/highrisk.php'); 

include_once "includes/footer.php";
?>
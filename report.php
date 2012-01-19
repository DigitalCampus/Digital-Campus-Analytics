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
	<!-- select name="report">
		<option value="hew">HEW</option>
		<option value="hew">Supervisor</option>
		<option value="hew">Midwife</option>
	</select -->
	<input type="submit" name="submit" value="<?php echo getString("report.form.searchbtn");?>"></input>
</form>

<?php 

include_once('includes/report/hew.php');

include_once "includes/footer.php";
?>
<?php
include_once "config.php";
$PAGE = "report";

include_once "includes/header.php";


// work out report defaults
$report = new stdClass();
$report->startDate = "";

$days = optional_param("days",31,PARAM_INT);
$report->hpcodes = optional_param("hpcodes",$USER->hpcode,PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);

if($report->hpcodes == 'overall'){
	$report->hpcodes = $API->getUserHealthPointPermissions();
}



printf("<h2 class='printhide'>%s</h2>", getString("report.title"));
$hps = $API->getHealthPoints();

$currenthpname = getNameFromHPCodes($report->hpcodes);
?>
<form action="" method="get" class="printhide">
	<select name="hpcodes">
		<?php 
			displayHealthPointSelectList($report->hpcodes);
		?>
	</select>
	<!-- select name="report">
		<option value="hew">HEW</option>
		<option value="hew">Supervisor</option>
		<option value="hew">Midwife</option>
	</select -->
	<!-- select name="date">
		
	</select -->
	<input type="submit" name="submit" value="<?php echo getString("report.form.searchbtn");?>"></input>
</form>

<?php 

include_once('includes/report/hew.php');

include_once "includes/footer.php";
?>
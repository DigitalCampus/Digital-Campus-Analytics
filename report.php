<?php
include_once "config.php";
$PAGE = "report";
include_once "includes/header.php";

$today = new DateTime();
$report = new stdClass();
$days = optional_param("days",31,PARAM_INT);
$report->hpcodes = optional_param("hpcodes",$USER->hpcode,PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);
$reporttype = optional_param("reporttype","healthpost",PARAM_TEXT);
$date = optional_param("date",$today->format('d-M-Y'),PARAM_TEXT);

//work out start and end dates for report (based on the date given)
$reportdate = new DateTime($date);

$report->startDate = new DateTime($reportdate->format('01-M-Y 00:00:00'));
$report->endDate = new DateTime(lastOfMonth($reportdate)." 23:59:59");

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
	<select name="reporttype">
		<option value="healthpost">Health Post</option>
		<option value="supervisor">Supervisor</option>
		<option value="midwife">Midwife</option>
	</select>
	<select name="date">
	<?php 
		$d = new DateTime();
		for($i=0;$i<7;$i++){
			if($d->format('M Y') == $reportdate->format('M Y')){
				printf ("<option value='%s' selected='selected'>%s</option>",$d->format('d-M-Y'),$d->format('M Y'));
			} else {
				printf ("<option value='%s'>%s</option>",$d->format('d-M-Y'),$d->format('M Y'));
			}
			$d->sub(new DateInterval('P1M'));
		}
	?>
	</select>
	<input type="submit" name="submit" value="<?php echo getString("report.form.searchbtn");?>"></input>
</form>

<?php 
switch($reporttype){
	case 'healthpost':
		include_once('includes/report/healthpost.php');
		break;
	case 'supervisor':
		include_once('includes/report/supervisor.php');
		break;
	case 'midwife':
		include_once('includes/report/midwife.php');
		break;
}


include_once "includes/footer.php";
?>
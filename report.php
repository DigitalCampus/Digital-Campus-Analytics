<?php
include_once "config.php";
$PAGE = "report";
include_once "includes/header.php";
include_once "data/report-periods.php";

$days = optional_param('days',30,PARAM_INT);
$today = new DateTime();

$ethioToday = gregorianToEthio($today->format('Y'),$today->format('m'),$today->format('d'));

$submit = optional_param("submit","",PARAM_TEXT);
$reporttype = optional_param("reporttype","healthpost",PARAM_TEXT);
$reportid = optional_param("reportid",$ethioToday['year']."-".$ethioToday['month'],PARAM_TEXT);

$report = new stdClass();
$report = $reportperiod[$reportid];
$report->hpcodes = optional_param("hpcodes",$USER->hpcode,PARAM_TEXT);


if($report->hpcodes == 'all'){
	$report->hpcodes = $API->getUserHealthPointPermissions();
}

printf("<h2 class='printhide'>%s</h2>", getString("report.title"));
$hps = $API->getHealthPoints();

$currenthpname = getNameFromHPCodes($report->hpcodes);
?>
<script type="text/javascript">
 function reportTypeChanged(){
	if($('#reporttype').val() == 'current'){
		$('#reportid').attr('disabled', 'disabled');
	} else {
		$('#reportid').removeAttr('disabled');
	}
 }
</script>
<form action="" method="get" class="printhide">
	<select name="hpcodes">
		<?php 
			displayHealthPointSelectList($report->hpcodes);
		?>
	</select>
	<select id="reporttype" name="reporttype" onchange="reportTypeChanged()">
		<?php 
			if($reporttype == 'healthpost'){
				echo '<option value="healthpost" selected="selected">Health Post</option>';
			} else {
				echo '<option value="healthpost">Health Post</option>';
			}
			if($reporttype == 'mch'){
				echo '<option value="mch" selected="selected">MCH Report</option>';
			} else {
				echo '<option value="mch">MCH Report</option>';
			}
			if($reporttype == 'current'){
				echo '<option value="current" selected="selected">Current</option>';
			} else {
				echo '<option value="current">Current</option>';
			}
			if($reporttype == 'supervisor'){
				echo '<option value="supervisor" selected="selected">Supervisor</option>';
			} else {
				echo '<option value="supervisor">Supervisor</option>';
			}
			if($reporttype == 'midwife'){
				echo '<option value="midwife" selected="selected">Midwife</option>';
			} else {
				echo '<option value="midwife">Midwife</option>';
			}
		
		?>
	</select>
	<select name="reportid" id="reportid">
	<?php 
		foreach($reportperiod as $rpm=>$v){
			if ($reportid == $rpm){
				printf("<option value='%s' selected='selected'>%s</option>",$rpm,$v->text);
			} else {
				printf("<option value='%s'>%s</option>",$rpm,$v->text);
			}
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
	case 'current':
		include_once('includes/report/current.php');
		break;
	case 'mch':
		include_once('includes/report/mch.php');
		break;
}


include_once "includes/footer.php";
?>
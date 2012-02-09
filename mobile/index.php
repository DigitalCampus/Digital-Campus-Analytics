<?php 
include_once "../config.php";
$PAGE = "index";
include_once 'includes/header.php';

$datetoday = new DateTime();

$datemonthago = new DateTime();
$datemonthago->sub(new DateInterval('P1M'));

$date2monthago = new DateTime();
$date2monthago->sub(new DateInterval('P2M'));

$opts = array();
if($USER->getProp('permissions.role') == 'hew' || $USER->getProp('permissions.role') == 'midwife'){
	$opts['hpcodes'] = optional_param('hpcodes',$USER->hpcode,PARAM_TEXT);
} else {
	$opts['hpcodes'] = optional_param('hpcodes',$API->getUserHealthPointPermissions(),PARAM_TEXT);
}

if($opts['hpcodes'] == 'overall'){
	$opts['hpcodes'] = $API->getUserHealthPointPermissions();
}

$opts['startdate'] = $datemonthago->format('Y-m-d 00:00:00');
$opts['enddate'] = $datetoday->format('Y-m-d 23:59:59');

$anc1thismonth = $API->getANC1Defaulters($opts);
$anc2thismonth = $API->getANC2Defaulters($opts);
$nosubmittedthismonth = $API->getProtocolsSubmitted_Cache($opts);
//$tt1thismonth = $API->getTT1Defaulters($opts);

$opts['startdate'] = $date2monthago->format('Y-m-d 00:00:00');
$opts['enddate'] = $datemonthago->format('Y-m-d 23:59:59');

$anc1previousmonth = $API->getANC1Defaulters($opts);
$anc2previousmonth= $API->getANC2Defaulters($opts);
$nosubmittedpreviousmonth = $API->getProtocolsSubmitted_Cache($opts);
//$tt1previousmonth = $API->getTT1Defaulters($opts);

if($USER->getProp('permissions.role') != 'hew' && $USER->getProp('permissions.role') != 'midwife'){
?>
<form action="" method="get" class="printhide" name="hpselectform" name="hpselectform" style="text-align:center;padding:5px">
	<select name="hpcodes" onchange="document.hpselectform.submit();">
		<?php displayHealthPointSelectList($opts['hpcodes']);?>
	</select>
</form>
<?php 
}
?>
<h2><?php echo getstring('mobile.title.kpis'); ?></h2>
<div class="kpiheader">
	<div class="kpiscore"><?php echo getstring('mobile.kpi.heading.lastmonth'); ?></div>
	<div class="kpichange"><?php echo getstring('mobile.kpi.heading.previousmonth'); ?></div>
	<div class="kpitarget"><?php echo getstring('mobile.kpi.heading.target'); ?></div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle"><?php echo getstring('mobile.kpi.protocols'); ?></div>
	<div class="kpiscore"><?php echo $nosubmittedthismonth->count; ?></div>
	<div class="kpichange">
	<?php 
		$change = $nosubmittedthismonth->count - $nosubmittedpreviousmonth->count;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/> +%d</span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/> 0</span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/> %d</span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpitarget"><?php 
			// multiply the target no of protocosl by the number of hpcodes
			echo $CONFIG->props['target.protocols']*count(explode(',',$opts['hpcodes'])); ?></div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle"><?php echo getstring('mobile.kpi.anc1'); ?></div>
	<div class="kpiscore"><?php echo $anc1thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpichange">
	<?php 
		$change = $anc1thismonth[0]->nondefaulters - $anc1previousmonth[0]->nondefaulters;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/> +%d%%</span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/> 0%%</span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/> %d%%</span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpitarget"><?php echo $CONFIG->props['target.anc1']; ?>%</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle"><?php echo getstring('mobile.kpi.anc2'); ?></div>
	<div class="kpiscore"><?php echo $anc2thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpichange">
	<?php 
		$change = $anc2thismonth[0]->nondefaulters - $anc2previousmonth[0]->nondefaulters;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/> +%d%%</span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/> 0%%</span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/> %d%%</span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpitarget"><?php echo $CONFIG->props['target.anc2']; ?>%</div>
	<div style="clear:both;"></div>
</div>
<?php 

$ra = new RiskAssessment();
$risks = $ra->getRiskStatistics($opts);

$summary = array('none'=>0,'unavoidable'=>0,'single'=>0, 'multiple'=>0, 'total'=>0);

// loop through and update the counters for each patient:
foreach($risks as $k=>$v){
	$summary[$k] = $v;
	$summary['total'] += $v;
}
?>
<h2><?php echo getstring('mobile.title.risk'); ?></h2>
<div class="risk">
	<div class="risktitle"><?php echo getstring('risk.multiple'); ?></div>
	<div class="risktotal">
		<?php 
			echo $summary['multiple']; 
		?>
	</div>
	<div class="risktotal">
		<?php 
			if($summary['total'] != 0){
				printf(' (%2d%%)',$summary['multiple']*100/$summary['total'] );
			} else {
				printf(' (--%%)');
			} 
		?>
	</div>
	<div style='clear:both;'></div>
</div>
<div class="risk">
	<div class="risktitle"><?php echo getstring('risk.single'); ?></div>
	<div class="risktotal">
		<?php 
			echo $summary['single']; 
		?>
	</div>
	<div class="risktotal">
		<?php 
			if($summary['total'] != 0){
				printf(' (%2d%%)',$summary['single']*100/$summary['total'] );
			} else {
				printf(' (--%%)');
			} 
		?>
	</div>
	<div style='clear:both;'></div>
</div>
<div class="risk">
	<div class="risktitle"><?php echo getstring('risk.unavoidable'); ?></div>
	<div class="risktotal">
		<?php 
			echo $summary['unavoidable']; 
		?>
	</div>
	<div class="risktotal">
		<?php 
			if($summary['total'] != 0){
				printf(' (%2d%%)',$summary['unavoidable']*100/$summary['total'] );
			} else {
				printf(' (--%%)');
			} 
		?>
	</div>
	<div style='clear:both;'></div>
</div>
<div class="risk">
	<div class="risktitle"><?php echo getstring('risk.none'); ?></div>
	<div class="risktotal">
		<?php 
			echo $summary['none']; 
		?>
	</div>
	<div class="risktotal">
		<?php 
			if($summary['total'] != 0){
				printf(' (%2d%%)',$summary['none']*100/$summary['total'] );
			} else {
				printf(' (--%%)');
			} 
		?>
	</div>
	<div style='clear:both;'></div>
</div>
<?php 
include_once 'includes/footer.php';
?>
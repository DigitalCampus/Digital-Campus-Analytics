<?php

$datetoday = new DateTime();

$datemonthago = new DateTime();
$datemonthago->sub(new DateInterval('P30D'));

$date2monthago = new DateTime();
$date2monthago->sub(new DateInterval('P2M'));

$opts = array();
$opts['hpcodes'] = $API->getUserHealthPointPermissions(true);
$opts['nohps'] = count(explode(',',$API->getUserHealthPointPermissions()));
$opts['startdate'] = $datemonthago->format('Y-m-d 00:00:00');
$opts['enddate'] = $datetoday->format('Y-m-d 23:59:59');

$nosubmittedthismonth = $API->getProtocolsSubmitted_Cache($opts);

$opts['startdate'] = $date2monthago->format('Y-m-d 00:00:00');
$opts['enddate'] = $datemonthago->format('Y-m-d 00:00:00');

$nosubmittedpreviousmonth = $API->getProtocolsSubmitted_Cache($opts);

?>


<div class="kpiheader">
	<div class="kpiheadertitle">&nbsp;</div>
	<div class="kpiheadertitle">For the last month</div>
	<div class="kpiheadertitle">Change from previous month</div>
	<div class="kpiheadertitle">Target</div>
	<div style="clear:both;"></div>
</div>
<?php 
	$submitted = array('protocol.total',PROTOCOL_ANCFIRST, PROTOCOL_ANCFOLLOW, PROTOCOL_DELIVERY, PROTOCOL_PNC);
	foreach ($submitted as $s) {
?>
<div class="kpi">
	<div class="kpititle"><a href="kpi.php?kpi=submitted"><?php echo getstring($s); ?> visits</a></div>
	<div class="kpiscore"><?php echo $nosubmittedthismonth->count[$s]; ?></div>
	<div class="kpichange">
	<?php 
		$change = $nosubmittedthismonth->count[$s] - $nosubmittedpreviousmonth->count[$s];
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/> +%d</span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/> 0</span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/> %d</span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpitarget"><?php echo $nosubmittedthismonth->target[$s];?></div>
	<div style="clear:both;"></div>
</div>
<?php 
	}
	
$kpi = new KPI();
$avganc = $kpi->averageANCVisits($opts);
$avgpnc = $kpi->averagePNCVisits($opts);
?>
<div class="kpi">
	<div class="kpiavg">Average No. ANC visits per patient:</div>
	<div class="kpiavgscore"><?php echo $avganc; ?></div>
	<div style="clear:both;"></div>
</div>

<div class="kpi">
	<div class="kpiavg">Average No. PNC visits per patient:</div>
	<div class="kpiavgscore"><?php echo $avgpnc; ?></div>
	<div style="clear:both;"></div>
</div>

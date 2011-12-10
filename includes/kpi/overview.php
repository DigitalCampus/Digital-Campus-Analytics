<?php

$datetoday = new DateTime();

$datemonthago = new DateTime();
$datemonthago->sub(new DateInterval('P1M'));

$date2monthago = new DateTime();
$date2monthago->sub(new DateInterval('P2M'));

$opts = array();
$opts['hps'] = $API->getUserHealthPointPermissions();
$opts['startdate'] = $datemonthago->format('Y-m-d');
$opts['enddate'] = $datetoday->format('Y-m-d');

$anc1thismonth = $API->getANC1Defaulters($opts);
$nosubmittedthismonth = $API->getProtocolsSubmitted($opts);

$opts = array();
$opts['hps'] = $API->getUserHealthPointPermissions();
$opts['startdate'] = $date2monthago->format('Y-m-d');
$opts['enddate'] = $datemonthago->format('Y-m-d');

$anc1previousmonth = $API->getANC1Defaulters($opts);
$nosubmittedpreviousmonth = $API->getProtocolsSubmitted($opts);

?>
<div class="kpiheader">
	<div class="kpiheadertitle">&nbsp;</div>
	<div class="kpiheadertitle">Your score<br/><small>for last month</small></div>
	<div class="kpiheadertitle">Change<br/><small>+/-</small></div>
	<div class="kpiheadertitle">Target</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle">Protocols Submitted</div>
	<div class="kpiscore"><?php echo $nosubmittedthismonth->count; ?></div>
	<div class="kpichange">
	<?php 
		$change = $nosubmittedthismonth->count - $nosubmittedpreviousmonth->count;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/> +%d</span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/> 0%%</span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/> %d%%</span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpitarget">50</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle">ANC1 on time</div>
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
	<div class="kpitarget">60%</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
<div class="kpititle">TT on time</div>
<div class="kpiscore">--</div>
<div class="kpichange">--</div>
<div class="kpitarget">--</div>
<div style="clear:both;"></div>
</div>
<div class="kpi">
<div class="kpititle">PNC on time</div>
<div class="kpiscore">--</div>
<div class="kpichange">--</div>
<div class="kpitarget">--</div>
<div style="clear:both;"></div>
</div>
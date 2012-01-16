<?php

$datetoday = new DateTime();

$datemonthago = new DateTime();
$datemonthago->sub(new DateInterval('P1M'));

$date2monthago = new DateTime();
$date2monthago->sub(new DateInterval('P2M'));

$opts = array();
$opts['hps'] = $API->getUserHealthPointPermissions();
$opts['startdate'] = $datemonthago->format('Y-m-d');
$opts['enddate'] = $datetoday->format('Y-m-d 23:59:59');

$anc1thismonth = $API->getANC1Defaulters($opts);
$anc2thismonth = $API->getANC2Defaulters($opts);
$nosubmittedthismonth = $API->getProtocolsSubmitted_Cache($opts);
$tt1thismonth = $API->getTT1Defaulters($opts);

$opts = array();
$opts['hps'] = $API->getUserHealthPointPermissions();
$opts['startdate'] = $date2monthago->format('Y-m-d');
$opts['enddate'] = $datemonthago->format('Y-m-d');

$anc1previousmonth = $API->getANC1Defaulters($opts);
$anc2previousmonth= $API->getANC2Defaulters($opts);
$nosubmittedpreviousmonth = $API->getProtocolsSubmitted_Cache($opts);
$tt1previousmonth = $API->getTT1Defaulters($opts);

?>
<div class="kpiheader">
	<div class="kpiheadertitle">&nbsp;</div>
	<div class="kpiheadertitle">For the last month</div>
	<div class="kpiheadertitle">Change from previous month</div>
	<div class="kpiheadertitle">Target</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle"><a href="kpi.php?kpi=submitted">Protocols Submitted</a></div>
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
	<div class="kpitarget">--</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle"><a href="kpi.php?kpi=anc1defaulters">ANC1 on time</a></div>
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
	<div class="kpititle"><a href="kpi.php?kpi=anc1defaulters">ANC2 on time</a></div>
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
	<div class="kpitarget">60%</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle"><a href="kpi.php?kpi=tt1defaulters">TT1 on time</a></div>
	<div class="kpiscore"><?php echo $tt1thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpichange">
	<?php 
		$change = $tt1thismonth[0]->nondefaulters - $tt1previousmonth[0]->nondefaulters;
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
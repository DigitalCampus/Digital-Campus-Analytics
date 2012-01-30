<?php


$previousstartdate = clone $report->startDate;
$previousstartdate->sub(new DateInterval('P1M'));

$previousenddate = new DateTime(lastOfMonth($previousstartdate)." 23:59:59");

$opts = array();
$opts['hpcodes'] = $report->hpcodes;
$opts['startdate'] = $report->startDate->format('Y-m-d 00:00:00');
$opts['enddate'] = $report->endDate->format('Y-m-d 23:59:59');

$anc1thismonth = $API->getANC1Defaulters($opts);
$anc2thismonth = $API->getANC2Defaulters($opts);
$nosubmittedthismonth = $API->getProtocolsSubmitted_Cache($opts);
//$tt1thismonth = $API->getTT1Defaulters($opts);
$pnc1thismonth = $API->getPNC1Defaulters($opts);

$opts = array();
$opts['hpcodes'] = $report->hpcodes;
$opts['startdate'] = $previousstartdate->format('Y-m-d 00:00:00');
$opts['enddate'] = $previousenddate->format('Y-m-d 23:59:59');

$anc1previousmonth = $API->getANC1Defaulters($opts);
$anc2previousmonth= $API->getANC2Defaulters($opts);
$nosubmittedpreviousmonth = $API->getProtocolsSubmitted_Cache($opts);
//$tt1previousmonth = $API->getTT1Defaulters($opts);
$pnc1previousmonth = $API->getPNC1Defaulters($opts);

?>
<div class="kpireportheader" style="width:50%">
	<div class="kpireportheadertitle">&nbsp;</div>
	<div class="kpireportheadertitle"><?php echo $report->startDate->format('M Y')?></div>
	<div class="kpireportheadertitle"><?php echo $previousstartdate->format('M Y')?></div>
	<div class="kpireportheadertitle">Change</div>
	<div class="kpireportheadertitle">Target</div>
	<div style="clear:both;"></div>
</div>
<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=submitted">Protocols Submitted</a></div>
	<div class="kpireportscore"><?php echo $nosubmittedthismonth->count; ?></div>
	<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count; ?></div>
	<div class="kpireportchange">
	<?php 
		$change = $nosubmittedthismonth->count - $nosubmittedpreviousmonth->count;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget">--</div>
	<div style="clear:both;"></div>
</div>
<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=anc1defaulters">ANC1 on time</a></div>
	<div class="kpireportscore"><?php echo $anc1thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportscore"><?php echo $anc1previousmonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportchange">
	<?php 
		$change = $anc1thismonth[0]->nondefaulters - $anc1previousmonth[0]->nondefaulters;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.anc1']; ?>%</div>
	<div style="clear:both;"></div>
</div>
<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=anc1defaulters">ANC2 on time</a></div>
	<div class="kpireportscore"><?php echo $anc2thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportscore"><?php echo $anc2previousmonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportchange">
	<?php 
		$change = $anc2thismonth[0]->nondefaulters - $anc2previousmonth[0]->nondefaulters;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.anc2']; ?>%</div>
	<div style="clear:both;"></div>
</div>
<!-- div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=tt1defaulters">TT1 on time</a></div>
	<div class="kpireportscore"><?php //echo $tt1thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportchange">
	<?php 
		/*$change = $tt1thismonth[0]->nondefaulters - $tt1previousmonth[0]->nondefaulters;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/> +%d%%</span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/> 0%%</span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/> %d%%</span>",'images/decrease.png',$change);
	 	}*/
	?>
	</div>
	<div class="kpitarget"><?php //echo $CONFIG->props['target.tt1']; ?>%</div>
	<div style="clear:both;"></div>
</div -->

<!-- div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=pnc1defaulters">PNC1 on time</a></div>
	<div class="kpireportscore"><?php //echo $pnc1thismonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportscore"><?php //echo $pnc1previousmonth[0]->nondefaulters; ?>%</div>
	<div class="kpireportchange">
	<?php 
		/*$change = $pnc1thismonth[0]->nondefaulters - $pnc1previousmonth[0]->nondefaulters;
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s'class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s'class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}*/
	?>
	</div>
	<div class="kpireporttarget"><?php //echo $CONFIG->props['target.pnc1']; ?>%</div>
	<div style="clear:both;"></div>
</div -->
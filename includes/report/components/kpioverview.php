<?php


$opts = array();
$opts['hpcodes'] = $report->hpcodes;
$opts['startdate'] = $report->start;
$opts['enddate'] = $report->end;

$anc1thismonth = $API->getANC1Defaulters($opts);
$anc2thismonth = $API->getANC2Defaulters($opts);
$nosubmittedthismonth = $API->getProtocolsSubmitted_Cache($opts);

$opts = array();
$opts['hpcodes'] = $report->hpcodes;
$opts['startdate'] =  $report->prevstart;
$opts['enddate'] = $report->prevend;

$anc1previousmonth = $API->getANC1Defaulters($opts);
$anc2previousmonth= $API->getANC2Defaulters($opts);
$nosubmittedpreviousmonth = $API->getProtocolsSubmitted_Cache($opts);

?>
<div class="kpireportheader" style="width:50%">
	<div class="kpireportheadertitle">&nbsp;</div>
	<div class="kpireportheadertitle"><?php echo $report->text?></div>
	<div class="kpireportheadertitle"><?php echo $report->prevtext?></div>
	<div class="kpireportheadertitle">Change</div>
	<div class="kpireportheadertitle">Target</div>
	<div style="clear:both;"></div>
</div>
<?php 
	$submitted = array('protocol.total',PROTOCOL_ANCFIRST, PROTOCOL_ANCFOLLOW, PROTOCOL_DELIVERY, PROTOCOL_PNC);
	foreach ($submitted as $s) {
?>
	<div class="kpireport" style="width:50%">
		<div class="kpireporttitle"><a href="kpi.php?kpi=submitted"><?php echo getstring($s); ?> Submitted</a></div>
		<div class="kpireportscore"><?php echo $nosubmittedthismonth->count[$s]; ?></div>
		<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count[$s]; ?></div>
		<div class="kpireportchange">
		<?php 
			$change = $nosubmittedthismonth->count[$s] - $nosubmittedpreviousmonth->count[$s];
		 	if ($change > 0){
		 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
		 	} else if ($change == 0){
		 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
		 	} else if ($change < 0){
		 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
		 	}
		?>
		</div>
		<div class="kpireporttarget"><?php echo $nosubmittedthismonth->target[$s];?></div>
		<div style="clear:both;"></div>
	</div>
<?php 
	}
?>


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
	<div class="kpireporttarget"><?php echo $anc1thismonth[0]->target; ?>%</div>
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
	<div class="kpireporttarget"><?php echo $anc2thismonth[0]->target; ?>%</div>
	<div style="clear:both;"></div>
</div>
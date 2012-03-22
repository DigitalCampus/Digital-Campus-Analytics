<?php


$opts = array();
$opts['hpcodes'] = $report->hpcodes;
$opts['startdate'] = $report->start;
$opts['enddate'] = $report->end;

$anc1thismonth = $API->getANC1Defaulters($opts);
$anc2thismonth = $API->getANC2Defaulters($opts);
$nosubmittedthismonth = $API->getProtocolsSubmitted_Cache($opts);
//$tt1thismonth = $API->getTT1Defaulters($opts);
//$pnc1thismonth = $API->getPNC1Defaulters($opts);

$opts = array();
$opts['hpcodes'] = $report->hpcodes;
$opts['startdate'] =  $report->prevstart;
$opts['enddate'] = $report->prevend;

$anc1previousmonth = $API->getANC1Defaulters($opts);
$anc2previousmonth= $API->getANC2Defaulters($opts);
$nosubmittedpreviousmonth = $API->getProtocolsSubmitted_Cache($opts);
//$tt1previousmonth = $API->getTT1Defaulters($opts);
//$pnc1previousmonth = $API->getPNC1Defaulters($opts);

?>
<div class="kpireportheader" style="width:50%">
	<div class="kpireportheadertitle">&nbsp;</div>
	<div class="kpireportheadertitle"><?php echo $report->text?></div>
	<div class="kpireportheadertitle"><?php echo $report->prevtext?></div>
	<div class="kpireportheadertitle">Change</div>
	<div class="kpireportheadertitle">Target</div>
	<div style="clear:both;"></div>
</div>
<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=submitted">Protocols Submitted</a></div>
	<div class="kpireportscore"><?php echo $nosubmittedthismonth->count['total']; ?></div>
	<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count['total']; ?></div>
	<div class="kpireportchange">
	<?php 
		$change = $nosubmittedthismonth->count['total'] - $nosubmittedpreviousmonth->count['total'];
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.protocols']*count(explode(',',$opts['hpcodes']));?></div>
	<div style="clear:both;"></div>
</div>


<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=submitted">ANC1 Submitted</a></div>
	<div class="kpireportscore"><?php echo $nosubmittedthismonth->count[PROTOCOL_ANCFIRST]; ?></div>
	<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count[PROTOCOL_ANCFIRST]; ?></div>
	<div class="kpireportchange">
	<?php 
		$change = $nosubmittedthismonth->count[PROTOCOL_ANCFIRST] - $nosubmittedpreviousmonth->count[PROTOCOL_ANCFIRST];
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.anc1submitted']*count(explode(',',$opts['hpcodes']));?></div>
	<div style="clear:both;"></div>
</div>

<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=submitted">ANC Follow Up Submitted</a></div>
	<div class="kpireportscore"><?php echo $nosubmittedthismonth->count[PROTOCOL_ANCFOLLOW]; ?></div>
	<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count[PROTOCOL_ANCFOLLOW]; ?></div>
	<div class="kpireportchange">
	<?php 
		$change = $nosubmittedthismonth->count[PROTOCOL_ANCFOLLOW] - $nosubmittedpreviousmonth->count[PROTOCOL_ANCFOLLOW];
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.ancfollowsubmitted']*count(explode(',',$opts['hpcodes']));?></div>
	<div style="clear:both;"></div>
</div>

<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=submitted">Delivery Submitted</a></div>
	<div class="kpireportscore"><?php echo $nosubmittedthismonth->count[PROTOCOL_DELIVERY]; ?></div>
	<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count[PROTOCOL_DELIVERY]; ?></div>
	<div class="kpireportchange">
	<?php 
		$change = $nosubmittedthismonth->count[PROTOCOL_DELIVERY] - $nosubmittedpreviousmonth->count[PROTOCOL_DELIVERY];
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.deliverysubmitted']*count(explode(',',$opts['hpcodes']));?></div>
	<div style="clear:both;"></div>
</div>

<div class="kpireport" style="width:50%">
	<div class="kpireporttitle"><a href="kpi.php?kpi=submitted">PNC Submitted</a></div>
	<div class="kpireportscore"><?php echo $nosubmittedthismonth->count[PROTOCOL_PNC]; ?></div>
	<div class="kpireportscore"><?php echo $nosubmittedpreviousmonth->count[PROTOCOL_PNC]; ?></div>
	<div class="kpireportchange">
	<?php 
		$change = $nosubmittedthismonth->count[PROTOCOL_PNC] - $nosubmittedpreviousmonth->count[PROTOCOL_PNC];
	 	if ($change > 0){
	 		printf("<span class='increase'><img src='%s' class='kpichange'/></span>",'images/increase.png',$change);
	 	} else if ($change == 0){
	 		printf("<span class='equal'><img src='%s' class='kpichange'/></span>",'images/equal.png',$change);
	 	} else if ($change < 0){
	 		printf("<span class='decrease'><img src='%s' class='kpichange'/></span>",'images/decrease.png',$change);
	 	}
	?>
	</div>
	<div class="kpireporttarget"><?php echo $CONFIG->props['target.pncsubmitted']*count(explode(',',$opts['hpcodes']));?></div>
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
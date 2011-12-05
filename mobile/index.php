<?php 
include_once "../config.php";
$PAGE="index";
include_once 'includes/header.php';
?>
<div class="kpiheader">
	<div class="kpiscore">Your score<br/><small>for last month</small></div>
	<div class="kpichange">Change<br/><small>+/-</small></div>
	<div class="kpitarget">Target</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
	<div class="kpititle">ANC on time</div>
	<div class="kpiscore">30%</div>
	<div class="kpichange">+3%</div>
	<div class="kpitarget">60%</div>
	<div style="clear:both;"></div>
</div>
<div class="kpi">
<div class="kpititle">TT on time</div>
<div class="kpiscore">30%</div>
<div class="kpichange">-10%</div>
<div class="kpitarget">80%</div>
<div style="clear:both;"></div>
</div>
<div class="kpi">
<div class="kpititle">PNC on time</div>
<div class="kpiscore">30%</div>
<div class="kpichange">+5%</div>
<div class="kpitarget">70%</div>
<div style="clear:both;"></div>
</div>
<?php 
include_once 'includes/footer.php';
?>
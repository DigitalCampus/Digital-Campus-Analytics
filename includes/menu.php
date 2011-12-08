<div id="menu">

	<?php if($PAGE == "index") {
		echo getString("menu.home");
	} else {
		echo "<a href='".$CONFIG->homeAddress."index.php'>".getString("menu.home")."</a>";
	}?>
	|	
	<?php if($PAGE == "submitted") {
		echo getString("menu.submitted");
	} else {
		echo "<a href='".$CONFIG->homeAddress."submitted.php'>".getString("menu.submitted")."</a>";
	}?>
	|
	<?php if($PAGE == "kpi") {
		echo getString("menu.kpi");
	} else {
		echo "<a href='".$CONFIG->homeAddress."kpi.php'>".getString("menu.kpi")."</a>";
	}?>
	|
	<?php if($PAGE == "assessment") {
		echo getString("menu.assessment");
	} else {
		echo "<a href='".$CONFIG->homeAddress."assessment.php'>".getString("menu.assessment")."</a>";
	}?>
	|
	<?php if($PAGE == "map") {
		echo getString("menu.map");
	} else {
		echo "<a href='".$CONFIG->homeAddress."map.php'>".getString("menu.map")."</a>";
	}?>
	|
	<?php if($PAGE == "datacheck") {
		echo getString("menu.data-check");
	} else {
		echo "<a href='".$CONFIG->homeAddress."data-check.php'>".getString("menu.data-check")."</a>";
	}?>
	|
	<?php if($PAGE == "patient") {
		echo getString("menu.patient");
	} else {
		echo "<a href='".$CONFIG->homeAddress."patient.php'>".getString("menu.patient")."</a>";
	}?>
	|
	<?php if($PAGE == "hew") {
		echo getString("menu.hew");
	} else {
		echo "<a href='".$CONFIG->homeAddress."hew-report.php'>".getString("menu.hew")."</a>";
	}?>
</div>
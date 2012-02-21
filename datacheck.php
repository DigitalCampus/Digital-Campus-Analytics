<?php
require_once "config.php";
$PAGE = "datacheck";
require_once "includes/header.php";

$dc = new DataCheck();

?>
<h2><?php echo getString("datacheck.unregistered.title")?></h2>
<p><?php echo getString("datacheck.unregistered.info")?></p>
<?php 

	$unregistered = $dc->unregistered();
	if(count($unregistered)>0){
?>
	
<table class="admin">
		<tr>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($unregistered as $d){
				echo "<tr class='n'>";
				echo "<td nowrap>".$d->submittedname." at ".displayHealthPointName($d->protocolhpcode)."</td>";
				echo "<td nowrap>".displayHealthPointName($d->patienthpcode,$d->Q_USERID)."</td>";
				echo "<td nowrap>".getstring($d->protocol)."</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.unregistered.none")."</p>";
	}
?>

<h2><?php echo getString("datacheck.duplicate.protocol.title")?></h2>
<p><?php echo getString("datacheck.duplicate.protocol.info")?></p>
<?php 

	$dup = $dc->duplicates();
	if(count($dup)>0){
?>
	
<table class="admin">
		<tr>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($dup as $d){
				echo "<tr class='n'>";
				echo "<td nowrap>".$d->submittedname." at ".displayHealthPointName($d->protocolhpcode)."</td>";
				echo "<td nowrap>".displayHealthPointName($d->patienthpcode,$d->Q_USERID)."</td>";
				echo "<td nowrap>".getstring($d->protocol);
				if(isset($d->Q_FOLLOWUPNO)){
					echo " ".$d->Q_FOLLOWUPNO;
				}
				echo "</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.duplicate.protocol.none")."</p>";
	}
?>

<h2><?php echo getString("datacheck.missing.ancfirst.title")?></h2>
<p><?php echo getString("datacheck.missing.ancfirst.info")?></p>
<?php 

	$missing = $dc->missingANCFirst();
	if(count($missing)>0){
?>
	
<table class="admin">
		<tr>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($missing as $m){
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
							$CONFIG->homeAddress,
							$m->patienthpcode,
							$m->userid,
							$m->protocol
							);
				echo "<td nowrap>".$m->submittedname." at ".displayHealthPointName($m->protocolhpcode)."</td>";
				echo "<td nowrap>".displayHealthPointName($m->patienthpcode,$m->userid)."</td>";
				echo "<td nowrap>".getstring($m->protocol)."</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.missing.ancfirst.none")."</p>";
	}
?>
 
<h2><?php echo getString("datacheck.age.title")?></h2>
<?php 
$dc->age();
include_once "includes/footer.php";

?>
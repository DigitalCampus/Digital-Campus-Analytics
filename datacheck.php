<?php
require_once "config.php";
$PAGE = "datacheck";
require_once "includes/header.php";

$dc = new DataCheck();

?>
<h2><?php echo getString("datacheck.unregistered.title")?></h2>
<p><?php echo getString("datacheck.unregistered.info")?></p>
<?php 

	$unregistered = $dc->getDataCheck('unreg');
	if(count($unregistered)>0){
?>
	
<table class="admin">
		<tr>
			<th>Error Date</th>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($unregistered as $d){
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
								$CONFIG->homeAddress,
								$d->patienthpcode,
								$d->patid,
								$d->protocol
								);
				echo "<td nowrap>".$d->dcdate."</td>";
				echo "<td nowrap>".$d->submittedname." at ".displayHealthPointName($d->protocolhpcode)."</td>";
				echo "<td nowrap>(".$d->patienthpcode.") ".displayHealthPointName($d->patienthpcode,$d->patid)."</td>";
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

	$dup = $dc->getDataCheck('duplicate');
	if(count($dup)>0){
?>
	
<table class="admin">
		<tr>
			<th>Error Date</th>
			<th>Registered by/at</th>
			<th>Patient ID</th>
			<th>Protocol</th>
		</tr>
		<?php 	
			foreach ($dup as $d){
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
								$CONFIG->homeAddress,
								$d->patienthpcode,
								$d->patid,
								$d->protocol
								);
				echo "<td nowrap>".$d->dcdate."</td>";
				echo "<td nowrap>".$d->submittedname." at ".displayHealthPointName($d->protocolhpcode)."</td>";
				echo "<td nowrap>(".$d->patienthpcode.") ".displayHealthPointName($d->patienthpcode,$d->patid)."</td>";
				echo "<td nowrap>".getstring($d->protocol)."</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.duplicate.protocol.none")."</p>";
	}
?>

 
<h2><?php echo getString("datacheck.age.title")?></h2>

<?php

$ageyob = $dc->getDataCheck('ageyob');
if(count($ageyob)>0){
	?>
	
<table class="admin">
		<tr>
			<th>Error Date</th>
			<th>Patient ID</th>
		</tr>
		<?php 	
			foreach ($ageyob as $ay){
				echo "<tr class='l' title='Click to view full details'";
				printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s';\">",
								$CONFIG->homeAddress,
								$ay->patienthpcode,
								$ay->patid
								);
				echo "<td nowrap>".$ay->dcdate."</td>";
				echo "<td nowrap>(".$ay->patienthpcode.") ".displayHealthPointName($ay->patienthpcode,$ay->patid)."</td>";
				echo "</tr>";
			}
				
		?>
</table>
<?php 
	} else {
		echo "<p>".getString("datacheck.ageyob.protocol.none")."</p>";
	}

include_once "includes/footer.php";

?>
<?php 
	function generateDeliveryRow($title, $data){
		printf("<tr class='rrow'><td class='rqcell'>%s</td><td class='rdcell'>%s</td><tr>",$title,$data);
	}
	
	echo "<pre>";
	print_r($patient->delivery);
	echo "</pre>";
?>

<h3><?php echo getstring(PROTOCOL_DELIVERY);?></h3>

<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<th><?php echo getstring('table.heading.data');?></th>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<td class="rdcell"><?php printf('on %s by %s (%s)',displayAsEthioDate(strtotime($patient->delivery->CREATEDON)), $patient->delivery->submittedname, $patient->delivery->protocollocation);?></td>
</tr>
<?php 
	$rowArray = array(
					'Q_USERID' => $patient->delivery->Q_USERID,
					'Q_HEALTHPOINTID' => $patient->delivery->patientlocation,
					'Q_YEAROFBIRTH' => $patient->delivery->Q_YEAROFBIRTH,
					'Q_AGE' => $patient->delivery->Q_AGE
					);
	foreach ($rowArray as $k=>$v){
		generateDeliveryRow(getstring($k),$v);
	}
	
?>
<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.laborprocess');?></td>
</tr>

<?php 
	$rowArray = array(
					'Q_LABORONSETTIME' => getstring("Q_LABORONSETTIME.".$patient->delivery->Q_LABORONSETTIME),
					'Q_ECLAMPSIA' => getstring("Q_ECLAMPSIA.".$patient->delivery->Q_ECLAMPSIA),
					'Q_PROM' => getstring("Q_PROM.".$patient->delivery->Q_PROM),
					'Q_MECONIUM' => getstring("Q_MECONIUM.".$patient->delivery->Q_MECONIUM),
					'Q_PRESENTATION' => getstring("Q_PRESENTATION.".$patient->delivery->Q_PRESENTATION)
					);
	foreach ($rowArray as $k=>$v){
		generateDeliveryRow(getstring($k),$v);
	}
	
?>

<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.deliveryinfo');?></td>
</tr>
</table>
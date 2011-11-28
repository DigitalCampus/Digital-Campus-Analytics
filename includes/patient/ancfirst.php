<?php 
	function generateANCFirstRow($title, $data){
		printf("<tr class='rrow'><td class='rqcell'>%s</td><td class='rdcell'>%s</td><tr>",$title,$data);
	}
?>

<h3><?php echo getstring('protocol.ancfirst');?></h3>

<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<th><?php echo getstring('table.heading.data');?></th>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<td class="rdcell"><?php printf('on %s by %s (%s)',displayAsEthioDate(strtotime($patient->ancfirst->CREATEDON)), $patient->ancfirst->submittedname, $patient->ancfirst->protocollocation);?></td>
</tr>
<?php 
	$rowArray = array(
					'Q_USERID' => $patient->ancfirst->Q_USERID,
					'Q_HEALTHPOINTID' => $patient->ancfirst->patientlocation,
					'Q_YEAROFBIRTH' => $patient->ancfirst->Q_YEAROFBIRTH,
					'Q_AGE' => $patient->ancfirst->Q_AGE
					);
	foreach ($rowArray as $k=>$v){
		generateANCFirstRow(getstring($k),$v);
	}
	
?>
<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.currentpregnancy');?></td>
</tr>

<?php 
	$rowArray = array(
					'Q_ABDOMINALPAIN' => $patient->ancfirst->Q_ABDOMINALPAIN,
					'Q_BLEEDING' => $patient->ancfirst->Q_BLEEDING,
					'Q_FATIGUE' => $patient->ancfirst->Q_FATIGUE,
					'Q_FEVER' => $patient->ancfirst->Q_FEVER,
					'Q_HEADACHE' => $patient->ancfirst->Q_HEADACHE,
					'Q_OTHERHEALTHPROBLEMS' => $patient->ancfirst->Q_OTHERHEALTHPROBLEMS
					);
	foreach ($rowArray as $k=>$v){
		generateANCFirstRow(getstring($k),$v);
	}
?>
<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.previouspregnancy');?></td>
</tr>
<?php 

	$q_babyweight = "";
	if ($patient->ancfirst->Q_BABYWEIGHT != ""){
		$q_babyweight = getstring("Q_BABYWEIGHT.".$patient->ancfirst->Q_BABYWEIGHT);
	} 
	
	$temp = array();
	foreach($patient->ancfirst->Q_FPMETHOD as $vv){
		array_push($temp,getstring("Q_FPMETHOD.".strtolower($vv)));
	}
	$q_fpmethod = implode($temp,", ");
	
	$temp = array();
	foreach($patient->ancfirst->Q_WHOATTENDED as $vv){
		if(getstring("Q_WHOATTENDED.".$vv)){
			array_push($temp,getstring("Q_WHOATTENDED.".$vv));
		} else {
			array_push($temp,$vv);
		}
	}
	$q_whoattended = implode($temp,", ");
	
	$rowArray = array(
					'Q_GRAVIDA' => $patient->ancfirst->Q_GRAVIDA,
					'Q_PARITY' => $patient->ancfirst->Q_PARITY,
					'Q_BIRTHINTERVAL' => $patient->ancfirst->Q_BIRTHINTERVAL,
					'Q_LIVEBIRTHS' => $patient->ancfirst->Q_LIVEBIRTHS,
					'Q_STILLBIRTHS' => $patient->ancfirst->Q_STILLBIRTHS,
					'Q_YOUNGESTCHILD' => $patient->ancfirst->Q_YOUNGESTCHILD,
					'Q_INFANTDEATH' => $patient->ancfirst->Q_INFANTDEATH,
					'Q_ABORTION' => $patient->ancfirst->Q_ABORTION,
					'Q_LIVINGCHILDREN' => $patient->ancfirst->Q_LIVINGCHILDREN,
					'Q_PREECLAMPSIA' => $patient->ancfirst->Q_PREECLAMPSIA,
					'Q_BLEEDINGPREVPREG' => $patient->ancfirst->Q_BLEEDINGPREVPREG,
					'Q_CSECTION' => $patient->ancfirst->Q_CSECTION,
					'Q_VACUUMDELIVERY' => $patient->ancfirst->Q_VACUUMDELIVERY,
					'Q_NEWBORNDEATH' => $patient->ancfirst->Q_NEWBORNDEATH,
					'Q_PROLONGEDLABOR' => $patient->ancfirst->Q_PROLONGEDLABOR,
					'Q_FISTULA' => $patient->ancfirst->Q_FISTULA,
					'Q_MALPOSITION' => $patient->ancfirst->Q_MALPOSITION,
					'Q_TWIN' => $patient->ancfirst->Q_TWIN,
					'Q_BABYWEIGHT' => $q_babyweight,
					'Q_PREPOSTTERM' => $patient->ancfirst->Q_PREPOSTTERM,
					'Q_OTHERPREVPREG' => $patient->ancfirst->Q_OTHERPREVPREG,
					'Q_FAMILYPLAN' => $patient->ancfirst->Q_FAMILYPLAN,
					'Q_FPMETHOD' => $q_fpmethod,
					'Q_LMP' => displayAsEthioDate(strtotime($patient->ancfirst->Q_LMP)),
					'Q_EDD' => displayAsEthioDate(strtotime($patient->ancfirst->Q_EDD)),
					'Q_DELIVERYPLACE' => $patient->ancfirst->Q_DELIVERYPLACE,
					'Q_WHOATTENDED' => $q_whoattended,
					'Q_DELIVERYPLAN' => $patient->ancfirst->Q_DELIVERYPLAN,
					'Q_SOCIALSUPPORT' => $patient->ancfirst->Q_SOCIALSUPPORT,
					'Q_ECONOMICS' => $patient->ancfirst->Q_ECONOMICS,
					'Q_TRANSPORTATION' => $patient->ancfirst->Q_TRANSPORTATION
					);
	foreach ($rowArray as $k=>$v){
		generateANCFirstRow(getstring($k),$v);
	}
?>

<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.medicalhistory');?></td>
</tr>

<?php 
	$rowArray = array(
					'Q_DIABETES' => $patient->ancfirst->Q_DIABETES,
					'Q_TUBERCULOSIS' => $patient->ancfirst->Q_TUBERCULOSIS,
					'Q_HYPERTENSION' => $patient->ancfirst->Q_HYPERTENSION,
					'Q_MALARIA' => $patient->ancfirst->Q_MALARIA,
					'Q_BEDNETS' => $patient->ancfirst->Q_BEDNETS,
					'Q_TETANUS' => $patient->ancfirst->Q_TETANUS,
					'Q_IRONTABLETS' => $patient->ancfirst->Q_IRONTABLETS,
					'Q_IRONGIVEN' => $patient->ancfirst->Q_IRONGIVEN,
					'Q_HIV' => $patient->ancfirst->Q_HIV,
					'Q_HIVTREATMENT' => $patient->ancfirst->Q_HIVTREATMENT,
					'Q_FOLICACID' => $patient->ancfirst->Q_FOLICACID,
					'Q_MEBENDAZOL' => $patient->ancfirst->Q_MEBENDAZOL,
					'Q_DRUGS' => $patient->ancfirst->Q_DRUGS,
					'Q_DRUGSDESCRIPTION' => $patient->ancfirst->Q_DRUGSDESCRIPTION,
					'Q_OTHERHEALTHPROBLEMS' => $patient->ancfirst->Q_OTHERHEALTHPROBLEMS
					);
	foreach ($rowArray as $k=>$v){
		generateANCFirstRow(getstring($k),$v);
	}
	
?>
<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.examination');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_WEIGHT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_WEIGHT; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEIGHT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_HEIGHT; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BLOODPRESSURE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_SYSTOLICBP."/".$patient->ancfirst->Q_DIASTOLICBP; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PALLORANEMIA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_PALLORANEMIA; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_CARDIACPULSE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_CARDIACPULSE; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_EDEMA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_EDEMA; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FUNDALHEIGHT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FUNDALHEIGHT; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GESTATIONALAGE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_GESTATIONALAGE; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PRESENTATION');?></td>
	<td class="rdcell"><?php 
		if ($patient->ancfirst->Q_PRESENTATION != ""){
			echo getstring('Q_PRESENTATION.'.$patient->ancfirst->Q_PRESENTATION);
		} ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FETALHEARTRATEAUDIBLE');?></td>
	<td class="rdcell"><?php 
		if ($patient->ancfirst->Q_FETALHEARTRATEAUDIBLE != ""){
			echo getstring('Q_FETALHEARTRATEAUDIBLE.'.$patient->ancfirst->Q_FETALHEARTRATEAUDIBLE);
		}
	 ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FETALHEARTRATE24W');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FETALHEARTRATE24W; ?></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_APPOINTMENTDATE');?></td>
	<td class="rdcell"><?php echo displayAsEthioDate(strtotime($patient->ancfirst->Q_APPOINTMENTDATE)); ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IDCARD');?></td>
	<td class="rdcell"><?php echo getstring("Q_IDCARD.".$patient->ancfirst->Q_IDCARD); ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LOCATION');?></td>
	<td class="rdcell"><?php echo getstring("Q_LOCATION.".$patient->ancfirst->Q_LOCATION); ?></td>
</tr>
<?php if($patient->ancfirst->Q_GPSDATA_LAT != ""){
?>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GPSDATA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_GPSDATA_LAT.",".$patient->ancfirst->Q_GPSDATA_LNG; ?></td>
</tr>

<?php 
}
?>
</table>
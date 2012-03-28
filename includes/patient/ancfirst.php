<?php 
	function generateANCFirstRow($title, $data){
		printf("<tr class='rrow'><td class='rqcell'>%s</td><td class='rdcell'>%s</td><tr>",$title,$data);
	}
?>

<h3><?php echo getstring(PROTOCOL_ANCFIRST);?></h3>

<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<th><?php echo getstring('table.heading.data');?></th>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<td class="rdcell"><?php printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',date('H:i',strtotime($patient->ancfirst->CREATEDON)), 
																		date('D d M Y',strtotime($patient->ancfirst->CREATEDON)), 
																		displayAsEthioDate(strtotime($patient->ancfirst->CREATEDON)), 
																		$patient->ancfirst->submittedname, 
																		displayHealthPointName($patient->ancfirst->protocolhpcode));?></td>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('protocol.datevisitmade');?></td>
	<td class="rdcell"><?php printf('%1$s (%2$s)',displayAsEthioDate(strtotime($patient->ancfirst->TODAY)), 
																		date('D d M Y',strtotime($patient->ancfirst->TODAY)));?></td>
</tr>
<?php 
	$rowArray = array(
					'Q_USERID' => $patient->ancfirst->Q_USERID,
					'Q_HEALTHPOINTID' => displayHealthPointName($patient->ancfirst->patienthpcode),
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
					'Q_LMP' => displayAsEthioDate(strtotime($patient->ancfirst->Q_LMP))."<br/>".date('D d M Y',strtotime($patient->ancfirst->Q_LMP)),
					'Q_EDD' => displayAsEthioDate(strtotime($patient->ancfirst->Q_EDD))."<br/>".date('D d M Y',strtotime($patient->ancfirst->Q_EDD)),
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
					'Q_TETANUS' => getstring("Q_TETANUS.".$patient->ancfirst->Q_TETANUS),
					'Q_TT1' => ($patient->ancfirst->Q_TT1 != "") ? displayAsEthioDate(strtotime($patient->ancfirst->Q_TT1))."<br/>".date('D d M Y',strtotime($patient->ancfirst->Q_TT1)) : "",
					'Q_TT2' => ($patient->ancfirst->Q_TT2 != "") ? displayAsEthioDate(strtotime($patient->ancfirst->Q_TT2))."<br/>".date('D d M Y',strtotime($patient->ancfirst->Q_TT2)) : "",
					'Q_TETANUS' => getstring("Q_TETANUS.".$patient->ancfirst->Q_TETANUS),
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
<?php 
		
	$q_presentation = "";
	if ($patient->ancfirst->Q_PRESENTATION != ""){
		$q_presentation = getstring("Q_PRESENTATION.".$patient->ancfirst->Q_PRESENTATION);
	}
	$q_fetalheartrateaudible = "";
	if ($patient->ancfirst->Q_FETALHEARTRATEAUDIBLE != ""){
		$q_fetalheartrateaudible = getstring("Q_FETALHEARTRATEAUDIBLE.".$patient->ancfirst->Q_FETALHEARTRATEAUDIBLE);
	}
	
	$rowArray = array(
					'Q_WEIGHT' => $patient->ancfirst->Q_WEIGHT,
					'Q_HEIGHT' => $patient->ancfirst->Q_HEIGHT,
					'Q_BLOODPRESSURE' => $patient->ancfirst->Q_SYSTOLICBP."/".$patient->ancfirst->Q_DIASTOLICBP,
					'Q_PALLORANEMIA' => $patient->ancfirst->Q_PALLORANEMIA,
					'Q_CARDIACPULSE' => $patient->ancfirst->Q_CARDIACPULSE,
					'Q_EDEMA' => $patient->ancfirst->Q_EDEMA,
					'Q_FUNDALHEIGHT' => $patient->ancfirst->Q_FUNDALHEIGHT,
					'Q_GESTATIONALAGE' => $patient->ancfirst->Q_GESTATIONALAGE,
					'Q_PRESENTATION' => $q_presentation,
					'Q_FETALHEARTRATEAUDIBLE' => $q_fetalheartrateaudible,
					'Q_FETALHEARTRATE24W' => $patient->ancfirst->Q_FETALHEARTRATE24W
					);
	foreach ($rowArray as $k=>$v){
		generateANCFirstRow(getstring($k),$v);
	}

?>

<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>

<?php 
	
	$q_gpsdata = "";
	if ($patient->ancfirst->Q_GPSDATA_LAT != ""){
		$q_gpsdata = $patient->ancfirst->Q_GPSDATA_LAT.",".$patient->ancfirst->Q_GPSDATA_LNG;
	}
	$rowArray = array(
					'Q_APPOINTMENTDATE' => displayAsEthioDate(strtotime($patient->ancfirst->Q_APPOINTMENTDATE))."<br/>".date('D d M Y',strtotime($patient->ancfirst->Q_APPOINTMENTDATE)),
					'Q_IDCARD' => $patient->ancfirst->Q_IDCARD,
					'Q_LOCATION' => getstring("Q_LOCATION.".$patient->ancfirst->Q_LOCATION),
					'Q_GPSDATA' => $q_gpsdata,
					
	);
	foreach ($rowArray as $k=>$v){
		generateANCFirstRow(getstring($k),$v);
	}

?>

</table>
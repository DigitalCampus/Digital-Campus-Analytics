<h3><?php echo getstring('protocol.ancfirst');?></h3>

<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<th><?php echo getstring('table.heading.data');?></th>
	<th><?php echo getstring('table.heading.risk');?></th>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<td class="rdcell"><?php printf('on %s by %s (%s)',displayAsEthioDate(strtotime($patient->ancfirst->CREATEDON)), $patient->ancfirst->submittedname, $patient->ancfirst->protocollocation);?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_USERID');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_USERID; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEALTHPOINTID');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->patientlocation; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_YEAROFBIRTH');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_YEAROFBIRTH; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_AGE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_AGE; ?></td>
	<td class="rrcell"><?php 
		if(isset($patient->risks['protocol.ancfirst']['Q_AGE'])){
			echo $patient->risks['protocol.ancfirst']['Q_AGE']['rtype'];	
		}
	?></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.currentpregnancy');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_ABDOMINALPAIN');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_ABDOMINALPAIN; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BLEEDING');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_BLEEDING; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FATIGUE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FATIGUE; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FEVER');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FEVER; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEADACHE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_HEADACHE; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_OTHERHEALTHPROBLEMS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_OTHERHEALTHPROBLEMS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.previouspregnancy');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GRAVIDA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_GRAVIDA; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PARITY');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_PARITY; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BIRTHINTERVAL');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_BIRTHINTERVAL; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LIVEBIRTHS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_LIVEBIRTHS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_STILLBIRTHS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_STILLBIRTHS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_YOUNGESTCHILD');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_YOUNGESTCHILD; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_INFANTDEATH');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_INFANTDEATH; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_ABORTION');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_ABORTION; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LIVINGCHILDREN');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_LIVINGCHILDREN; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PREECLAMPSIA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_PREECLAMPSIA; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BLEEDINGPREVPREG');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_BLEEDINGPREVPREG; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_CSECTION');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_CSECTION; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_VACUUMDELIVERY');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_VACUUMDELIVERY; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_NEWBORNDEATH');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_NEWBORNDEATH; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PROLONGEDLABOR');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_PROLONGEDLABOR; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FISTULA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FISTULA; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MALPOSITION');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_MALPOSITION; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TWIN');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_TWIN; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BABYWEIGHT');?></td>
	<td class="rdcell"><?php 
	if ($patient->ancfirst->Q_BABYWEIGHT != ""){
		echo getstring("Q_BABYWEIGHT.".$patient->ancfirst->Q_BABYWEIGHT);  
	}?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PREPOSTTERM');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_PREPOSTTERM; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_OTHERPREVPREG');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_OTHERPREVPREG; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FAMILYPLAN');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FAMILYPLAN; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FPMETHOD');?></td>
	<td class="rdcell"><?php 
		$temp = array();
		foreach($patient->ancfirst->Q_FPMETHOD as $vv){
			array_push($temp,getstring("Q_FPMETHOD.".strtolower($vv)));
		}
		echo implode($temp,", ");
	?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LMP');?></td>
	<td class="rdcell"><?php echo displayAsEthioDate(strtotime($patient->ancfirst->Q_LMP)); ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_EDD');?></td>
	<td class="rdcell"><?php echo displayAsEthioDate(strtotime($patient->ancfirst->Q_EDD)); ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DELIVERYPLACE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_DELIVERYPLACE; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_WHOATTENDED');?></td>
	<td class="rdcell"><?php 
		$temp = array();
		foreach($patient->ancfirst->Q_WHOATTENDED as $vv){
			if(getstring("Q_WHOATTENDED.".$vv)){
				array_push($temp,getstring("Q_WHOATTENDED.".$vv));
			} else {
				array_push($temp,$vv);
			}
		}
		echo implode($temp,", ");
	
	?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DELIVERYPLAN');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_DELIVERYPLAN; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_SOCIALSUPPORT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_SOCIALSUPPORT; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_ECONOMICS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_ECONOMICS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TRANSPORTATION');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_TRANSPORTATION; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.medicalhistory');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DIABETES');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_DIABETES; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TUBERCULOSIS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_TUBERCULOSIS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HYPERTENSION');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_HYPERTENSION; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MALARIA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_MALARIA; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BEDNETS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_BEDNETS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TETANUS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_TETANUS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IRONTABLETS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_IRONTABLETS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IRONGIVEN');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_IRONGIVEN; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HIV');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_HIV; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HIVTREATMENT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_HIVTREATMENT; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FOLICACID');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FOLICACID; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MEBENDAZOL');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_MEBENDAZOL; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DRUGS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_DRUGS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DRUGSDESCRIPTION');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_DRUGSDESCRIPTION; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_OTHERHEALTHPROBLEMS');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_OTHERHEALTHPROBLEMS; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.examination');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_WEIGHT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_WEIGHT; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEIGHT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_HEIGHT; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BLOODPRESSURE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_SYSTOLICBP."/".$patient->ancfirst->Q_DIASTOLICBP; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PALLORANEMIA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_PALLORANEMIA; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_CARDIACPULSE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_CARDIACPULSE; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_EDEMA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_EDEMA; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FUNDALHEIGHT');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FUNDALHEIGHT; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GESTATIONALAGE');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_GESTATIONALAGE; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PRESENTATION');?></td>
	<td class="rdcell"><?php 
		if ($patient->ancfirst->Q_PRESENTATION != ""){
			echo getstring('Q_PRESENTATION.'.$patient->ancfirst->Q_PRESENTATION);
		} ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FETALHEARTRATEAUDIBLE');?></td>
	<td class="rdcell"><?php 
		if ($patient->ancfirst->Q_FETALHEARTRATEAUDIBLE != ""){
			echo getstring('Q_FETALHEARTRATEAUDIBLE.'.$patient->ancfirst->Q_FETALHEARTRATEAUDIBLE);
		}
	 ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FETALHEARTRATE24W');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_FETALHEARTRATE24W; ?></td>
	<td class="rrcell"></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_APPOINTMENTDATE');?></td>
	<td class="rdcell"><?php echo displayAsEthioDate(strtotime($patient->ancfirst->Q_APPOINTMENTDATE)); ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IDCARD');?></td>
	<td class="rdcell"><?php echo getstring("Q_IDCARD.".$patient->ancfirst->Q_IDCARD); ?></td>
	<td class="rrcell"></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LOCATION');?></td>
	<td class="rdcell"><?php echo getstring("Q_LOCATION.".$patient->ancfirst->Q_LOCATION); ?></td>
	<td class="rrcell"></td>
</tr>
<?php if($patient->ancfirst->Q_GPSDATA_LAT != ""){
?>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GPSDATA');?></td>
	<td class="rdcell"><?php echo $patient->ancfirst->Q_GPSDATA_LAT.",".$patient->ancfirst->Q_GPSDATA_LNG; ?></td>
	<td class="rrcell"></td>
</tr>

<?php 
}
?>
</table>
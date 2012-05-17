<?php 
	function generateANCOpenRow($title){
		printf("<tr class='rrow'><td class='rqcell'>%s</td>",$title);
	}
	function generateANCCell($data){
		printf("<td class='rdcell'>%s</td>",$data);
	}
	function generateANCCloseRow(){
		printf("</tr>");
	}
?>

<h3><?php echo getstring(PROTOCOL_ANC);?></h3>

<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<?php 
		for($x=0;$x <count($anc); $x++ ){
			echo "<th>".getstring('table.heading.data')."</th>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<?php 
		for($x=0;$x <count($anc); $x++ ){
			echo "<td class='rdcell'>";
				printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',date('H:i',strtotime($anc[$x]->CREATEDON)), 
														date('D d M Y',strtotime($anc[$x]->CREATEDON)), 
														displayAsEthioDate(strtotime($anc[$x]->CREATEDON)), 
														$anc[$x]->submittedname, 
														displayHealthPointName($anc[$x]->protocolhpcode));
			echo "</td>";
		}
	?>
</tr>

<tr class="rrow">
<td class="rqcell"><?php echo getstring('protocol.datevisitmade');?></td>
	<?php 
		for($x=0;$x <count($anc); $x++ ){
			echo "<td class='rdcell'>";
			if (isset($anc[$x])){
				printf('%1$s (%2$s)',displayAsEthioDate(strtotime($anc[$x]->TODAY)), 
																		date('D d M Y',strtotime($anc[$x]->TODAY)));
			}
			echo "</td>";
		}
	?>
</tr>

<?php 
	$rows = array(	'Q_USERID',	
					'Q_HEALTHPOINTID',	
					'Q_YEAROFBIRTH',
					'Q_AGE' 
					);
	
	foreach($rows as $r){
		generateANCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($anc); $x++ ){
			$data[$x] = array(
							'Q_USERID' => $anc[$x]->Q_USERID,
							'Q_HEALTHPOINTID'  => displayHealthPointName($anc[$x]->patienthpcode),
							'Q_YEAROFBIRTH'  => $anc[$x]->Q_YEAROFBIRTH,
							'Q_AGE'  => $anc[$x]->Q_AGE
			);
		}
		for($x=0;$x <count($anc); $x++ ){
			generateANCCell($data[$x][$r]);
		}
		generateANCCloseRow();
	}	

?>	


<tr class='rrow'>
	<td colspan="<?php echo 1+count($anc);?>" class="sh"><?php echo getstring('section.currentpregnancy');?></td>
</tr>

<?php 
	$rows = array(	'Q_ABDOMINALPAIN',
					'Q_BLEEDING',
					'Q_FATIGUE',
					'Q_FEVER',
					'Q_HEADACHE',
					'Q_OTHERHEALTHPROBLEMS'
					);
	
	foreach($rows as $r){
		generateANCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($anc); $x++ ){
			$data[$x] = array(
							'Q_ABDOMINALPAIN' => $anc[$x]->Q_ABDOMINALPAIN,
							'Q_BLEEDING' => $anc[$x]->Q_BLEEDING,
							'Q_FATIGUE' => $anc[$x]->Q_FATIGUE,
							'Q_FEVER' => $anc[$x]->Q_FEVER,
							'Q_HEADACHE' => $anc[$x]->Q_HEADACHE,
							'Q_OTHERHEALTHPROBLEMS' => $anc[$x]->Q_OTHERHEALTHPROBLEMS
							);
		}
		for($x=0;$x <count($anc); $x++ ){
			generateANCCell($data[$x][$r]);
		}
		generateANCCloseRow();
	}	

?>	


<tr class='rrow'>
	<td colspan="<?php echo 1+count($anc);?>" class="sh"><?php echo getstring('section.previouspregnancy');?></td>
</tr>

<?php 
	$rows = array(	
					'Q_GRAVIDA',
					'Q_PARITY',
					'Q_BIRTHINTERVAL',
					'Q_LIVEBIRTHS',
					'Q_STILLBIRTHS',
					'Q_YOUNGESTCHILD',
					'Q_INFANTDEATH',
					'Q_ABORTION',
					'Q_LIVINGCHILDREN',
					'Q_PREECLAMPSIA',
					'Q_BLEEDINGPREVPREG',
					'Q_CSECTION',
					'Q_VACUUMDELIVERY',
					'Q_NEWBORNDEATH',
					'Q_PROLONGEDLABOR',
					'Q_FISTULA',
					'Q_MALPOSITION',
					'Q_TWIN',
					'Q_BABYWEIGHT',
					'Q_PREPOSTTERM',
					'Q_OTHERPREVPREG',
					'Q_FAMILYPLAN',
					'Q_FPMETHOD',
					'Q_LMP',
					'Q_EDD',
					'Q_DELIVERYPLACE',
					'Q_WHOATTENDED',
					'Q_DELIVERYPLAN',
					'Q_SOCIALSUPPORT',
					'Q_ECONOMICS',
					'Q_TRANSPORTATION'
					);
	
	foreach($rows as $r){
		generateANCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($anc); $x++ ){
			$temp = array();
			foreach($anc[$x]->Q_WHOATTENDED as $vv){
				if(getstring("Q_WHOATTENDED.".$vv)){
					array_push($temp,getstring("Q_WHOATTENDED.".$vv));
				} else {
					array_push($temp,$vv);
				}
			}
			$q_whoattended = implode($temp,", ");
			
			$temp = array();
			foreach($anc[$x]->Q_FPMETHOD as $vv){
				if(getstring("Q_FPMETHOD.".$vv)){
					array_push($temp,getstring("Q_FPMETHOD.".$vv));
				} else {
					array_push($temp,$vv);
				}
			}
			$q_fpmethod = implode($temp,", ");
			
			$data[$x] = array(
							'Q_GRAVIDA' => $anc[$x]->Q_GRAVIDA,
							'Q_PARITY' => $anc[$x]->Q_PARITY,
							'Q_BIRTHINTERVAL' => $anc[$x]->Q_BIRTHINTERVAL,
							'Q_LIVEBIRTHS' => $anc[$x]->Q_LIVEBIRTHS,
							'Q_STILLBIRTHS' => $anc[$x]->Q_STILLBIRTHS,
							'Q_YOUNGESTCHILD' => $anc[$x]->Q_YOUNGESTCHILD,
							'Q_INFANTDEATH' => $anc[$x]->Q_INFANTDEATH,
							'Q_ABORTION' => $anc[$x]->Q_ABORTION,
							'Q_LIVINGCHILDREN' => $anc[$x]->Q_LIVINGCHILDREN,
							'Q_PREECLAMPSIA' => $anc[$x]->Q_PREECLAMPSIA,
							'Q_BLEEDINGPREVPREG' => $anc[$x]->Q_BLEEDINGPREVPREG,
							'Q_CSECTION' => $anc[$x]->Q_CSECTION,
							'Q_VACUUMDELIVERY' => $anc[$x]->Q_VACUUMDELIVERY,
							'Q_NEWBORNDEATH' => $anc[$x]->Q_NEWBORNDEATH,
							'Q_PROLONGEDLABOR' => $anc[$x]->Q_PROLONGEDLABOR,
							'Q_FISTULA' => $anc[$x]->Q_FISTULA,
							'Q_MALPOSITION' => $anc[$x]->Q_MALPOSITION,
							'Q_TWIN' => $anc[$x]->Q_TWIN,
							'Q_BABYWEIGHT' => $anc[$x]->Q_BABYWEIGHT,
							'Q_PREPOSTTERM' => $anc[$x]->Q_PREPOSTTERM,
							'Q_OTHERPREVPREG' => $anc[$x]->Q_OTHERPREVPREG,
							'Q_FAMILYPLAN' => $anc[$x]->Q_FAMILYPLAN,
							'Q_FPMETHOD' => $q_fpmethod,
							'Q_LMP' => displayAsEthioDate(strtotime($anc[$x]->Q_LMP))."<br/>".date('D d M Y',strtotime($anc[$x]->Q_LMP)),
							'Q_EDD' => displayAsEthioDate(strtotime($anc[$x]->Q_EDD))."<br/>".date('D d M Y',strtotime($anc[$x]->Q_EDD)),
							'Q_DELIVERYPLACE' => $anc[$x]->Q_DELIVERYPLACE,
							'Q_WHOATTENDED' => $q_whoattended,
							'Q_DELIVERYPLAN' => $anc[$x]->Q_DELIVERYPLAN,
							'Q_SOCIALSUPPORT' => $anc[$x]->Q_SOCIALSUPPORT,
							'Q_ECONOMICS' => $anc[$x]->Q_ECONOMICS,
							'Q_TRANSPORTATION' => $anc[$x]->Q_TRANSPORTATION
							);
		}
		for($x=0;$x <count($anc); $x++ ){
			generateANCCell($data[$x][$r]);
		}
		generateANCCloseRow();
	}	

?>	


<tr class='rrow'>
	<td colspan="<?php echo 1+count($anc);?>" class="sh"><?php echo getstring('section.medicalhistory');?></td>
</tr>

<?php 
	$rows = array(	
					'Q_DIABETES',
					'Q_TUBERCULOSIS',
					'Q_HYPERTENSION',
					'Q_MALARIA',
					'Q_BEDNETS',
					'Q_TETANUS',
					'Q_TT1',
					'Q_TT2',
					'Q_TETANUS',
					'Q_IRONTABLETS',
					'Q_IRONGIVEN',
					'Q_HIV',
					'Q_HIVTREATMENT',
					'Q_FOLICACID',
					'Q_MEBENDAZOL',
					'Q_DRUGS',
					'Q_DRUGSDESCRIPTION',
					'Q_OTHERHEALTHPROBLEMS'
					);
	
	foreach($rows as $r){
		generateANCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($anc); $x++ ){
			$data[$x] = array(
							'Q_DIABETES' => $anc[$x]->Q_DIABETES,
							'Q_TUBERCULOSIS' => $anc[$x]->Q_TUBERCULOSIS,
							'Q_HYPERTENSION' => $anc[$x]->Q_HYPERTENSION,
							'Q_MALARIA' => $anc[$x]->Q_MALARIA,
							'Q_BEDNETS' => $anc[$x]->Q_BEDNETS,
							'Q_TETANUS' => getstring("Q_TETANUS.".$anc[$x]->Q_TETANUS),
							'Q_TT1' => ($anc[$x]->Q_TT1 != "") ? displayAsEthioDate(strtotime($anc[$x]->Q_TT1))."<br/>".date('D d M Y',strtotime($anc[$x]->Q_TT1)) : "",
							'Q_TT2' => ($anc[$x]->Q_TT2 != "") ? displayAsEthioDate(strtotime($anc[$x]->Q_TT2))."<br/>".date('D d M Y',strtotime($anc[$x]->Q_TT2)) : "",
							'Q_TETANUS' => getstring("Q_TETANUS.".$anc[$x]->Q_TETANUS),
							'Q_IRONTABLETS' => $anc[$x]->Q_IRONTABLETS,
							'Q_IRONGIVEN' => $anc[$x]->Q_IRONGIVEN,
							'Q_HIV' => $anc[$x]->Q_HIV,
							'Q_HIVTREATMENT' => $anc[$x]->Q_HIVTREATMENT,
							'Q_FOLICACID' => $anc[$x]->Q_FOLICACID,
							'Q_MEBENDAZOL' => $anc[$x]->Q_MEBENDAZOL,
							'Q_DRUGS' => $anc[$x]->Q_DRUGS,
							'Q_DRUGSDESCRIPTION' => $anc[$x]->Q_DRUGSDESCRIPTION,
							'Q_OTHERHEALTHPROBLEMS' => $anc[$x]->Q_OTHERHEALTHPROBLEMS
							);
		}
		for($x=0;$x <count($anc); $x++ ){
			generateANCCell($data[$x][$r]);
		}
		generateANCCloseRow();
	}	
?>	

<tr class='rrow'>
	<td colspan="<?php echo 1+count($anc);?>" class="sh"><?php echo getstring('section.examination');?></td>
</tr>

<?php 
	$rows = array(	
					'Q_WEIGHT',
					'Q_HEIGHT',
					'Q_BLOODPRESSURE',
					'Q_PALLORANEMIA',
					'Q_CARDIACPULSE',
					'Q_EDEMA',
					'Q_FUNDALHEIGHT',
					'Q_GESTATIONALAGE',
					'Q_PRESENTATION',
					'Q_FETALHEARTRATEAUDIBLE',
					'Q_FETALHEARTRATE24W'
					);
	
	foreach($rows as $r){
		generateANCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($anc); $x++ ){
			$data[$x] = array(
							'Q_WEIGHT' => $anc[$x]->Q_WEIGHT,
							'Q_HEIGHT' => $anc[$x]->Q_HEIGHT,
							'Q_BLOODPRESSURE' => $anc[$x]->Q_SYSTOLICBP."/".$anc[$x]->Q_DIASTOLICBP,
							'Q_PALLORANEMIA' => $anc[$x]->Q_PALLORANEMIA,
							'Q_CARDIACPULSE' => $anc[$x]->Q_CARDIACPULSE,
							'Q_EDEMA' => $anc[$x]->Q_EDEMA,
							'Q_FUNDALHEIGHT' => $anc[$x]->Q_FUNDALHEIGHT,
							'Q_GESTATIONALAGE' => $anc[$x]->Q_GESTATIONALAGE,
							'Q_PRESENTATION' => $anc[$x]->Q_PRESENTATION,
							'Q_FETALHEARTRATEAUDIBLE' => $anc[$x]->Q_FETALHEARTRATEAUDIBLE,
							'Q_FETALHEARTRATE24W' => $anc[$x]->Q_FETALHEARTRATE24W
							);
		}
		for($x=0;$x <count($anc); $x++ ){
			generateANCCell($data[$x][$r]);
		}
		generateANCCloseRow();
	}	
?>	

<tr class='rrow'>
	<td colspan="<?php echo 1+count($anc);?>" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>

<?php 
	$rows = array(	
					'Q_APPOINTMENTDATE',
					'Q_IDCARD',
					'Q_LOCATION',
					'Q_GPSDATA',
					);
	
	foreach($rows as $r){
		generateANCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($anc); $x++ ){
			$data[$x] = array(
							'Q_APPOINTMENTDATE' => displayAsEthioDate(strtotime($anc[$x]->Q_APPOINTMENTDATE))."<br/>".date('D d M Y',strtotime($anc[$x]->Q_APPOINTMENTDATE)),
							'Q_IDCARD' => $anc[$x]->Q_IDCARD,
							'Q_LOCATION' => getstring("Q_LOCATION.".$anc[$x]->Q_LOCATION),
							'Q_GPSDATA' => ($anc[$x]->Q_GPSDATA_LAT != "") ? Q_GPSDATA_LAT."/".Q_GPSDATA_LNG :"",
							);
		}
		for($x=0;$x <count($anc); $x++ ){
			generateANCCell($data[$x][$r]);
		}
		generateANCCloseRow();
	}	
?>	

</table>
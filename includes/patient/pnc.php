<?php 
	function generatePNCOpenRow($title){
		printf("<tr class='rrow'><td class='rqcell'>%s</td>",$title);
	}
	function generatePNCCell($data){
		printf("<td class='rdcell'>%s</td>",$data);
	}
	function generatePNCCloseRow(){
		printf("</tr>");
	}
	
	//echo "<pre>";
	//print_r($patient->pnc);
	//echo "</pre>";
?>

<h3><?php echo getstring(PROTOCOL_PNC);?></h3>
<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<?php 
		for($x=0;$x <count($pnc); $x++ ){
			echo "<th>".getstring('table.heading.data')."</th>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<?php 
		for($x=0;$x <count($pnc); $x++ ){
			echo "<td class='rdcell'>";
				printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',date('H:i',strtotime($pnc[$x]->CREATEDON)), 
														date('D d M Y',strtotime($pnc[$x]->CREATEDON)), 
														displayAsEthioDate(strtotime($pnc[$x]->CREATEDON)), 
														$pnc[$x]->submittedname, 
														displayHealthPointName($pnc[$x]->protocolhpcode));
			echo "</td>";
		}
	?>
</tr>

<tr class="rrow">
<td class="rqcell"><?php echo getstring('protocol.datevisitmade');?></td>
	<?php 
		for($x=0;$x <count($pnc); $x++ ){
			echo "<td class='rdcell'>";
				printf('%1$s (%2$s)',displayAsEthioDate(strtotime($pnc[$x]->TODAY)), 
																		date('D d M Y',strtotime($pnc[$x]->TODAY)));
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
		generatePNCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($pnc); $x++ ){
			$data[$x] = array(
							'Q_USERID' => $pnc[$x]->Q_USERID,
							'Q_HEALTHPOINTID'  => displayHealthPointName($pnc[$x]->patienthpcode),
							'Q_YEAROFBIRTH'  => $pnc[$x]->Q_YEAROFBIRTH,
							'Q_AGE'  => $pnc[$x]->Q_AGE
			);
		}
		for($x=0;$x <count($pnc); $x++ ){
			generatePNCCell($data[$x][$r]);
		}
		generatePNCCloseRow();
	}	
?>
<tr class='rrow'>
 	<td colspan="<?php echo 1+count($pnc);?>" class="sh"><?php echo getstring('section.pnc.deliverysummary');?></td>
</tr>
<?php 
	$rows = array(	'Q_DELIVERYDATE',
						'Q_NOBABIES',	
						'Q_DELIVERYMODE',
						'Q_DELIVERYSITE',
						'Q_BIRTHATTENDANT',
						'Q_COMPLICATIONS',
						'Q_MATERNALDEATH'
	);
	
	foreach($rows as $r){
		generatePNCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($pnc); $x++ ){
			$temp = array();
			foreach($pnc[$x]->Q_BIRTHATTENDANT as $vv){
				if(getstring("Q_WHOATTENDED.".$vv)){
					array_push($temp,getstring("Q_WHOATTENDED.".$vv));
				} else {
					array_push($temp,$vv);
				}
			}
			$q_birthattendant = implode($temp,", ");
			$data[$x] = array(
								'Q_DELIVERYDATE' => displayAsEthioDate(strtotime($pnc[$x]->Q_DELIVERYDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_DELIVERYDATE)),
								'Q_NOBABIES' => $pnc[$x]->Q_NOBABIES,
								'Q_DELIVERYMODE' => $pnc[$x]->Q_DELIVERYMODE,
								'Q_DELIVERYSITE' => $pnc[$x]->Q_DELIVERYSITE,
								'Q_BIRTHATTENDANT' => $q_birthattendant,
								'Q_COMPLICATIONS' => $pnc[$x]->Q_COMPLICATIONS,
								'Q_MATERNALDEATH' => $pnc[$x]->Q_MATERNALDEATH
			);
		}
		for($x=0;$x <count($pnc); $x++ ){
			generatePNCCell($data[$x][$r]);
		}
		generatePNCCloseRow();
	}

 
?>
<tr class='rrow'>
 <td colspan="<?php echo 1+count($pnc);?>" class="sh"><?php echo getstring('section.pnc.mother');?></td>
</tr>
<?php 

	$rows = array(	'Q_MOTHERCONDITION',
							'Q_LOCHIACOLOUR',	
							'Q_LOCHIAAMOUNT',
							'Q_LOCHIAODOUR',
							'Q_TEMPERATURE',
							'Q_BLOODPRESSURE',
							'Q_CARDIACPULSE',
							'Q_PALLORANEMIA',
							'Q_LEAKINGURINE',
							'Q_GENITALIAEXTERNA',
							'Q_IRONSUPPL',
							'Q_VITASUPPL',
							'Q_TETANUS',
							'Q_TT1',
							'Q_TT2',
							'Q_FPCOUNSELED',
							'Q_FPACCEPTED',
							'Q_HIV',
							'Q_HIVTREATMENT',
							'Q_DRUGS',
							'Q_DRUGSDESCRIPTION',
							'Q_COMMENTSMOTHER'
					);
	
	foreach($rows as $r){
		generatePNCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($pnc); $x++ ){
			$data[$x] = array(
							   'Q_MOTHERCONDITION' => $pnc[$x]->Q_MOTHERCONDITION,
							   'Q_LOCHIACOLOUR' => $pnc[$x]->Q_LOCHIACOLOUR,
							   'Q_LOCHIAAMOUNT' => $pnc[$x]->Q_LOCHIAAMOUNT,
							   'Q_LOCHIAODOUR' => $pnc[$x]->Q_LOCHIAODOUR,
							   'Q_TEMPERATURE' => $pnc[$x]->Q_TEMPERATURE,
							  'Q_BLOODPRESSURE' => $pnc[$x]->Q_SYSTOLICBP."/".$pnc[$x]->Q_DIASTOLICBP,
							   'Q_CARDIACPULSE' => $pnc[$x]->Q_CARDIACPULSE,
							   'Q_PALLORANEMIA' => $pnc[$x]->Q_PALLORANEMIA,
							   'Q_LEAKINGURINE' => $pnc[$x]->Q_LEAKINGURINE,
							   'Q_GENITALIAEXTERNA' => $pnc[$x]->Q_GENITALIAEXTERNA,
							   'Q_IRONSUPPL' => $pnc[$x]->Q_IRONSUPPL,
							   'Q_VITASUPPL' => $pnc[$x]->Q_VITASUPPL,
							   'Q_TETANUS' => $pnc[$x]->Q_TETANUS,
							   'Q_TT1' => ($pnc[$x]->Q_TT1 != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_TT1))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_TT1)) : "",
							   'Q_TT2' => ($pnc[$x]->Q_TT2 != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_TT2))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_TT2)) : "",
							   'Q_FPCOUNSELED' => $pnc[$x]->Q_FPCOUNSELED,
							   'Q_FPACCEPTED' => $pnc[$x]->Q_FPACCEPTED,
							   'Q_HIV' => $pnc[$x]->Q_HIV,
							   'Q_HIVTREATMENT' => $pnc[$x]->Q_HIVTREATMENT,
							   'Q_DRUGS' => $pnc[$x]->Q_DRUGS,
							   'Q_DRUGSDESCRIPTION' => $pnc[$x]->Q_DRUGSDESCRIPTION,
							   'Q_COMMENTSMOTHER' => $pnc[$x]->Q_COMMENTSMOTHER
			);
		}
		for($x=0;$x <count($pnc); $x++ ){
			generatePNCCell($data[$x][$r]);
		}
		generatePNCCloseRow();
	}
?>
<tr class='rrow'>
 	<td colspan="<?php echo 1+count($pnc);?>" class="sh"><?php echo getstring('section.pnc.baby');?></td>
</tr>
<?php 
	// check now many babies there are
	$nobabies = 0;
	foreach ($pnc as $p){
		$nobabies = max($nobabies, count($p->Q_BABIES));
	}
	if($nobabies == 0){
		generatePNCOpenRow("");
		for($x=0;$x <count($pnc); $x++ ){
			generatePNCCell("<span class='error'>No babies entered</span>");
		}
		generatePNCCloseRow();
	}
	for($i = 0; $i<$nobabies; $i++){
		printf("<tr class='rrow'><td colspan='%d' class='sh'>Baby %d</td></tr>",1+count($pnc),$i+1);
		
		$rows = array(	'Q_BABYCONDITION',
									'Q_DEATHDATE',	
									'Q_DEATHCOMMENTS',
									'Q_BABYBREATHING',
									'Q_NEWBORNWEIGHT',
									'Q_NEWBORNHEIGHT',
									'Q_NEWBORNHEADCIRCUM',
									'Q_CORDCONDITION',
									'Q_IMMUNO_BCG',
									'Q_IMMUNO_BCG_LASTDATE',
									'Q_IMMUNO_OPV',
									'Q_IMMUNO_OPV_LASTDATE',
									'Q_IMMUNO_PENTA',
									'Q_IMMUNO_PENTA_LASTDATE',
									'Q_IMMUNO_IPTI',
									'Q_IMMUNO_IPTI_LASTDATE',
									'Q_BABYMUMBOND',
									'Q_BABYBREASTFEED',
									'Q_HIV_BABY',
									'Q_ARV_HIV_BABY',
									'Q_COMMENTSBABY'
		);
		
		foreach($rows as $r){
			generatePNCOpenRow(getstring($r));
			$data = array();
			for($x=0;$x <count($pnc); $x++ ){
				if(isset($pnc[$x])){
					$data[$x] = array(
									'Q_BABYCONDITION' => $pnc[$x]->Q_BABIES[$i]->Q_BABYCONDITION,
								   'Q_DEATHDATE' => ($pnc[$x]->Q_BABIES[$i]->Q_DEATHDATE != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_BABIES[$i]->Q_DEATHDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_BABIES[$i]->Q_DEATHDATE)) : "",
								   'Q_DEATHCOMMENTS' => $pnc[$x]->Q_BABIES[$i]->Q_DEATHCOMMENTS,
								   'Q_BABYBREATHING' => $pnc[$x]->Q_BABIES[$i]->Q_BABYBREATHING,
								   'Q_NEWBORNWEIGHT' => $pnc[$x]->Q_BABIES[$i]->Q_NEWBORNWEIGHT,
								   'Q_NEWBORNHEIGHT' => $pnc[$x]->Q_BABIES[$i]->Q_NEWBORNHEIGHT,
								   'Q_NEWBORNHEADCIRCUM' => $pnc[$x]->Q_BABIES[$i]->Q_NEWBORNHEADCIRCUM,
								   'Q_CORDCONDITION' => $pnc[$x]->Q_BABIES[$i]->Q_CORDCONDITION,
								   'Q_IMMUNO_BCG' => $pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_BCG,
								   'Q_IMMUNO_BCG_LASTDATE' => ($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_BCG_LASTDATE != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_BCG_LASTDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_BCG_LASTDATE)) : "",
								   'Q_IMMUNO_OPV' => $pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_OPV,
								   'Q_IMMUNO_OPV_LASTDATE' => ($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_OPV_LASTDATE != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_OPV_LASTDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_OPV_LASTDATE)) : "",
								   'Q_IMMUNO_PENTA' => $pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_PENTA,
								   'Q_IMMUNO_PENTA_LASTDATE' => ($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_PENTA_LASTDATE != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_PENTA_LASTDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_PENTA_LASTDATE)) : "",
								   'Q_IMMUNO_IPTI' => $pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_IPTI,
								   'Q_IMMUNO_IPTI_LASTDATE' => ($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_IPTI_LASTDATE != "") ? displayAsEthioDate(strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_IPTI_LASTDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_BABIES[$i]->Q_IMMUNO_IPTI_LASTDATE)) : "",
								   'Q_BABYMUMBOND' => $pnc[$x]->Q_BABIES[$i]->Q_BABYMUMBOND,
								   'Q_BABYBREASTFEED' => $pnc[$x]->Q_BABIES[$i]->Q_BABYBREASTFEED,
								   'Q_HIV_BABY' => $pnc[$x]->Q_BABIES[$i]->Q_HIV_BABY,
								   'Q_ARV_HIV_BABY' => $pnc[$x]->Q_BABIES[$i]->Q_ARV_HIV_BABY,
								   'Q_COMMENTSBABY' => $pnc[$x]->Q_BABIES[$i]->Q_COMMENTSBABY,
					);
				}
			}
			for($x=0;$x <count($pnc); $x++ ){
				if(isset($pnc[$x])){
					generatePNCCell($data[$x][$r]);
				}
			}
			generatePNCCloseRow();
		}
	}
?>
<tr class='rrow'>
 	<td colspan="2" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
<?php 
	$rows = array(	'Q_APPOINTMENTDATE',
								'Q_IDCARD',	
								'Q_LOCATION',
								'Q_GPSDATA'
	);
	
	foreach($rows as $r){
		generatePNCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($pnc); $x++ ){
			$q_gpsdata = "";
			if ($pnc[$x]->Q_GPSDATA_LAT != ""){
				$q_gpsdata = $pnc[$x]->Q_GPSDATA_LAT.",".$pnc[$x]->Q_GPSDATA_LNG;
			}
			$data[$x] = array(
					'Q_APPOINTMENTDATE' => displayAsEthioDate(strtotime($pnc[$x]->Q_APPOINTMENTDATE))."<br/>".date('D d M Y',strtotime($pnc[$x]->Q_APPOINTMENTDATE)),
					'Q_IDCARD' => $pnc[$x]->Q_IDCARD,
					'Q_LOCATION' => getstring("Q_LOCATION.".$pnc[$x]->Q_LOCATION),
					'Q_GPSDATA' => $q_gpsdata,
					
				);
		}
		for($x=0;$x <count($pnc); $x++ ){
			generatePNCCell($data[$x][$r]);
		}
		generatePNCCloseRow();
	}	

?>
</table>
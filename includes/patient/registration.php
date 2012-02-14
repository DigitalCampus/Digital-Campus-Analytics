<h3><?php echo getstring(PROTOCOL_REGISTRATION);?></h3>

<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<th><?php echo getstring('table.heading.data');?></th>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<td class="rdcell"><?php printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',date('H:i',strtotime($patient->CREATEDON)), 
																		date('D d M Y',strtotime($patient->CREATEDON)), 
																		displayAsEthioDate(strtotime($patient->CREATEDON)), 
																		$patient->submittedname, 
																		displayHealthPointName($patient->protocolhpcode));?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_NAME');?></td>
	<td class="rdcell"><?php echo $patient->Q_USERNAME," ",$patient->Q_USERFATHERSNAME," ",$patient->Q_USERGRANDFATHERSNAME; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_USERID');?></td>
	<td class="rdcell"><?php echo $patient->Q_USERID; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEALTHPOINTID');?></td>
	<td class="rdcell"><?php echo displayHealthPointName($patient->patienthpcode); ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('dob');?><br/><?php echo getstring('ethiocal');?></td>
	<td class="rdcell"><?php echo $patient->Q_DAYOFBIRTH,", ",getstring("Q_MONTHOFBIRTH.".$patient->Q_MONTHOFBIRTH),", ",$patient->Q_YEAROFBIRTH; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_AGE');?></td>
	<td class="rdcell"><?php echo $patient->Q_AGE; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_SEX');?></td>
	<td class="rdcell"><?php echo $patient->Q_SEX; ?></td>
</tr>
<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.socioeconomic');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MARITALSTATUS');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_MARITALSTATUS != ""){
		echo getstring("Q_MARITALSTATUS.".$patient->Q_MARITALSTATUS);
	} ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_EDUCATION');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_EDUCATION != ""){
		echo getstring("Q_EDUCATION.".$patient->Q_EDUCATION); 
	}?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MOBILEPHONE');?></td>
	<td class="rdcell"><?php 
		echo $patient->Q_MOBILEPHONE; 
		if ($patient->Q_MOBILENUMBER != ""){
			echo ", number: ".$patient->Q_MOBILENUMBER;
		}
	
	?>
	</td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_OCCUPATION');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_OCCUPATION != ""){
		echo getstring("Q_OCCUPATION.".$patient->Q_OCCUPATION);
	} ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOUSEROOF');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_HOUSEROOF != ""){
		echo getstring("Q_HOUSEROOF.".$patient->Q_HOUSEROOF); 
	}?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOUSEWALL');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_HOUSEWALL != ""){
		echo getstring("Q_HOUSEWALL.".$patient->Q_HOUSEWALL); 
	}?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOUSEELECTRICITY');?></td>
	<td class="rdcell"><?php echo $patient->Q_HOUSEELECTRICITY; ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOMEAPPLIANCES');?></td>
	<td class="rdcell"><?php 
		$temp = array();
		foreach($patient->Q_HOMEAPPLIANCES as $vv){
			if(getstring("Q_HOMEAPPLIANCES.".$vv)){
				array_push($temp,getstring("Q_HOMEAPPLIANCES.".$vv));
			} else {
				array_push($temp,$vv);
			}
		}
		echo implode($temp,", ");
	
	?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOMESANITATION');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_HOMESANITATION != ""){	
		echo getstring("Q_HOMESANITATION.".$patient->Q_HOMESANITATION); 
	}?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOMEWATERSOURCE');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_HOMEWATERSOURCE != ""){
		echo getstring("Q_HOMEWATERSOURCE.".$patient->Q_HOMEWATERSOURCE); 
	}?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HOMEFUELSOURCE');?></td>
	<td class="rdcell"><?php 
	if ($patient->Q_HOMEFUELSOURCE != ""){
		echo getstring("Q_HOMEFUELSOURCE.".$patient->Q_HOMEFUELSOURCE); 
	}?></td>
</tr>
<tr class='rrow'>
	<td colspan="3" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IDCARD');?></td>
	<td class="rdcell"><?php echo getstring("Q_IDCARD.".$patient->Q_IDCARD); ?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LOCATION');?></td>
	<td class="rdcell"><?php echo getstring("Q_LOCATION.".$patient->Q_LOCATION); ?></td>
</tr>
<?php if($patient->Q_GPSDATA_LAT != ""){
?>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GPSDATA');?></td>
	<td class="rdcell"><?php echo $patient->Q_GPSDATA_LAT.",".$patient->Q_GPSDATA_LNG; ?></td>
</tr>

<?php 
}
?>
</table>


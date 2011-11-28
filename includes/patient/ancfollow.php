<?php 
	$i = array(0,1,2);
?>

<h3><?php echo getstring('protocol.ancfollow');?></h3>
<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<?php 
		foreach($i as $x){
			echo "<th>".getstring('table.heading.data')."</th>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				printf('on %s by %s (%s)',displayAsEthioDate(strtotime($ancfollow[$x]->CREATEDON)), $ancfollow[$x]->submittedname, $ancfollow[$x]->protocollocation);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_USERID');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_USERID;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEALTHPOINTID');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->patientlocation;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_YEAROFBIRTH');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_YEAROFBIRTH;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_AGE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_AGE;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($i)*2;?>" class="sh"><?php echo getstring('section.currentpregnancy');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_ABDOMINALPAIN');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_ABDOMINALPAIN;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BLEEDING');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_BLEEDING;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FATIGUE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_FATIGUE;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FEVER');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_FEVER;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEADACHE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_HEADACHE;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_OTHERHEALTHPROBLEMS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_OTHERHEALTHISSUES;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DELIVERYPLAN');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_DELIVERYPLAN.".$ancfollow[$x]->Q_DELIVERYPLAN);
			}
			echo "</td>";
		}
	?>
</tr>

<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_LMP');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo displayAsEthioDate(strtotime($ancfollow[$x]->Q_LMP));
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_EDD');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo displayAsEthioDate(strtotime($ancfollow[$x]->Q_EDD));
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_SOCIALSUPPORT');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_SOCIALSUPPORT.".$ancfollow[$x]->Q_SOCIALSUPPORT);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_ECONOMICS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_ECONOMICS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TRANSPORTATION');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_TRANSPORTATION;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($i)*2;?>" class="sh"><?php echo getstring('section.medicalhistory');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DIABETES');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_DIABETES;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TUBERCULOSIS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_TUBERCULOSIS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HYPERTENSION');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_HYPERTENSION;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MALARIA');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_MALARIA;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BEDNETS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_BEDNETS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TETANUS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_TETANUS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IRONTABLETS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_IRONTABLETS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IRONGIVEN');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_IRONGIVEN.".$ancfollow[$x]->Q_IRONGIVEN);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HIV');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_HIV.".$ancfollow[$x]->Q_HIV);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HIVTREATMENT');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_HIVTREATMENT;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FOLICACID');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_FOLICACID;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_MEBENDAZOL');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_MEBENDAZOL;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IODIZEDSALTS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_IODIZEDSALTS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DRUGS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_DRUGS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DRUGSDESCRIPTION');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_DRUGSDESCRIPTION;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_OTHERHEALTHPROBLEMS');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_OTHERHEALTHPROBLEMS;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($i)*2;?>" class="sh"><?php echo getstring('section.examination');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_WEIGHT');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_WEIGHT;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEIGHT');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring('Q_HEIGHT.'.$ancfollow[$x]->Q_HEIGHT);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_BLOODPRESSURE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_SYSTOLICBP."/".$ancfollow[$x]->Q_DIASTOLICBP;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PALLORANEMIA');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring('Q_PALLORANEMIA.'.$ancfollow[$x]->Q_PALLORANEMIA);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_CARDIACPULSE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_CARDIACPULSE;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_EDEMA');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring('Q_EDEMA.'.$ancfollow[$x]->Q_EDEMA);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FUNDALHEIGHT');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_FUNDALHEIGHT;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GESTATIONALAGE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_GESTATIONALAGE;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_PRESENTATION');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x]) && $ancfollow[$x]->Q_PRESENTATION != ""){
				echo getstring('Q_PRESENTATION.'.$ancfollow[$x]->Q_PRESENTATION);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FETALHEARTRATEAUDIBLE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])&& $ancfollow[$x]->Q_FETALHEARTRATEAUDIBLE != ""){
				echo getstring('Q_FETALHEARTRATEAUDIBLE.'.$ancfollow[$x]->Q_FETALHEARTRATEAUDIBLE);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_FETALHEARTRATE24W');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo $ancfollow[$x]->Q_FETALHEARTRATE24W;
			}
			echo "</td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($i)*2;?>" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IDCARD');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_IDCARD.".$ancfollow[$x]->Q_IDCARD);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_APPOINTMENTDATE');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo displayAsEthioDate(strtotime($ancfollow[$x]->Q_APPOINTMENTDATE));
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_LOCATION');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_LOCATION.".$ancfollow[$x]->Q_LOCATION);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_GPSDATA');?></td>
	<?php 
		foreach($i as $x){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				if ($ancfollow[$x]->Q_GPSDATA_LAT != ""){
					echo $ancfollow[$x]->Q_GPSDATA_LAT.",".$ancfollow[$x]->Q_GPSDATA_LNG; 
				}
			}
			echo "</td>";
		}
	?>
</tr>
</table>
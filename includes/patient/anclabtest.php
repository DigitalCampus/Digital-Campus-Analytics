<h3><?php echo getstring(PROTOCOL_ANCLABTEST);?></h3>
<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<th>".getstring('table.heading.data')."</th><th>".getstring('table.heading.risk')."</th>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',	date('H:i',strtotime($anclabtest[$i]->CREATEDON)),
															date('D d M Y',strtotime($anclabtest[$i]->CREATEDON)),
															displayAsEthioDate(strtotime($anclabtest[$i]->CREATEDON)),
															$anclabtest[$i]->submittedname,
															displayHealthPointName($anclabtest[$i]->protocolhpcode));
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_USERID');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_USERID;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEALTHPOINTID');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo displayHealthPointName($anclabtest[$i]->patienthpcode);
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_AGE');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_AGE;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_YEAROFBIRTH');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_YEAROFBIRTH;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($anclabtest)*2;?>" class="sh"><?php echo getstring('section.labanalysis');?></td>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_STOOLEXAMINATION');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_STOOLEXAMINATION;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_URINEANALYSIS');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_URINEANALYSIS;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_BLOODFILM');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_BLOODFILM;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_URINEPROTEIN');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_URINEPROTEIN;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_URINEGLUCOSE');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_URINEGLUCOSE;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($anclabtest)*2;?>" class="sh"><?php echo getstring('section.hematology');?></td>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_HEMOGLOBINLEVEL');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_HEMOGLOBINLEVEL;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_HEMATOCRITLEVEL');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_HEMATOCRITLEVEL;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_BLOODGROUP');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_BLOODGROUP;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_RHFACTOR');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_RHFACTOR;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($anclabtest)*2;?>" class="sh"><?php echo getstring('section.serology');?></td>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_PREGNANCYTEST');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_PREGNANCYTEST ;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_SYPHILIS');?></td>
	<?php 
		for($i=0;$i <count($anclabtest); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anclabtest[$i])){
				echo $anclabtest[$i]->Q_SYPHILIS ;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
</table>
<h3><?php echo getstring('protocol.anctransfer');?></h3>
<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<th>".getstring('table.heading.data')."</th><th>".getstring('table.heading.risk')."</th>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				printf('on %s by %s (%s)',displayAsEthioDate(strtotime($anctransfer[$i]->CREATEDON)), $anctransfer[$i]->submittedname, $anctransfer[$i]->protocollocation);
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_USERID');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_USERID;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_HEALTHPOINTID');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->patientlocation;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_AGE');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_AGE;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_YEAROFBIRTH');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_YEAROFBIRTH;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($anctransfer)*2;?>" class="sh"><?php echo getstring('section.previouspregnancy');?></td>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_GRAVIDA');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_GRAVIDA;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_PARITY');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_PARITY;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_BIRTHINTERVAL');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_BIRTHINTERVAL;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_LIVEBIRTHS');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_LIVEBIRTHS;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_STILLBIRTHS');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_STILLBIRTHS;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_YOUNGESTCHILD');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_YOUNGESTCHILD;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_INFANTDEATH');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_INFANTDEATH;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_ABORTION');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_ABORTION;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_LIVINGCHILDREN');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_LIVINGCHILDREN;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_PREECLAMPSIA');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_PREECLAMPSIA;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_BLEEDINGPREVPREG');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_BLEEDINGPREVPREG;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_CSECTION');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_CSECTION;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_VACUUMDELIVERY');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_VACUUMDELIVERY;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_NEWBORNDEATH');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_NEWBORNDEATH;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_PROLONGEDLABOR');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_PROLONGEDLABOR;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_FISTULA');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_FISTULA;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_MALPOSITION');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_MALPOSITION;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_TWIN');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_TWIN;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_BABYWEIGHT');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_BABYWEIGHT;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_PREPOSTTERM');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_PREPOSTTERM;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_FAMILYPLAN');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_FAMILYPLAN;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_FPMETHOD');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			$temp = array();
			foreach($anctransfer[$i]->Q_FPMETHOD as $vv){
				if(getstring("Q_FPMETHOD.".$vv)){
					array_push($temp,getstring("Q_FPMETHOD.".$vv));
				} else {
					array_push($temp,$vv);
				}
			}
			echo implode($temp,", ");
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>


<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_DELIVERYPLACE');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_DELIVERYPLACE;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_WHOATTENDED');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			$temp = array();
			foreach($anctransfer[$i]->Q_WHOATTENDED as $vv){
				if(getstring("Q_WHOATTENDED.".$vv)){
					array_push($temp,getstring("Q_WHOATTENDED.".$vv));
				} else {
					array_push($temp,$vv);
				}
			}
			echo implode($temp,", ");
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($anctransfer)*2;?>" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_IDCARD');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_IDCARD;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_LOCATION');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				echo $anctransfer[$i]->Q_LOCATION;
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
<tr class="rrow">
<td class="rqcell"><?php echo getstring('Q_GPSDATA');?></td>
	<?php 
		for($i=0;$i <count($anctransfer); $i++ ){
			echo "<td class='rdcell'>";
			if (isset($anctransfer[$i])){
				if ($anctransfer[$i]->Q_GPSDATA_LAT != ""){
					echo $anctransfer[$i]->Q_GPSDATA_LAT.",".$anctransfer[$i]->Q_GPSDATA_LNG; 
				}
			}
			echo "</td>";
			echo "<td class='rrcell'></td>";
		}
	?>
</tr>
</table>
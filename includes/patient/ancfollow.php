<?php 
	$i = array(0,1,2);
	function generateANCFollowOpenRow($title){
		printf("<tr class='rrow'><td class='rqcell'>%s</td>",$title);
	}
	function generateANCFollowCell($data){
		printf("<td class='rdcell'>%s</td>",$data);
	}
	function generateANCFollowCloseRow(){
		printf("</tr>");
	}
?>

<h3><?php echo getstring(PROTOCOL_ANCFOLLOW);?></h3>
<table class='rtable'>
<tr class='rrow'>
	<th><?php echo getstring('table.heading.question');?></th>
	<?php 
		for($x=0;$x <count($ancfollow); $x++ ){
			echo "<th>".getstring('table.heading.data')."</th>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('protocolsubmitted');?></td>
	<?php 
		for($x=0;$x <count($ancfollow); $x++ ){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',date('H:i',strtotime($ancfollow[$x]->CREATEDON)), 
														date('D d M Y',strtotime($ancfollow[$x]->CREATEDON)), 
														displayAsEthioDate(strtotime($ancfollow[$x]->CREATEDON)), 
														$ancfollow[$x]->submittedname, 
														$ancfollow[$x]->protocollocation);
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
		generateANCFollowOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($ancfollow); $x++ ){
			$data[$x] = array(
							'Q_USERID' => $ancfollow[$x]->Q_USERID,
							'Q_HEALTHPOINTID'  => $ancfollow[$x]->patientlocation,
							'Q_YEAROFBIRTH'  => $ancfollow[$x]->Q_YEAROFBIRTH,
							'Q_AGE'  => $ancfollow[$x]->Q_AGE
			);
		}
		for($x=0;$x <count($ancfollow); $x++ ){
			generateANCFollowCell($data[$x][$r]);
		}
		generateANCFollowCloseRow();
	}	

?>

<tr class='rrow'>
	<td colspan="<?php echo 1+count($ancfollow);?>" class="sh"><?php echo getstring('section.currentpregnancy');?></td>
</tr>
<?php 
	$rows = array(	'Q_ABDOMINALPAIN',	
					'Q_BLEEDING',	
					'Q_FATIGUE',
					'Q_FEVER',
					'Q_HEADACHE',
					'Q_OTHERHEALTHPROBLEMS',
					'Q_DELIVERYPLAN',
					'Q_LMP',
					'Q_EDD',
					'Q_SOCIALSUPPORT',
					'Q_ECONOMICS',
					'Q_TRANSPORTATION'
					);
	
	foreach($rows as $r){
		generateANCFollowOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($ancfollow); $x++ ){
			$data[$x] = array(
							'Q_ABDOMINALPAIN' => $ancfollow[$x]->Q_ABDOMINALPAIN,
							'Q_BLEEDING'  => $ancfollow[$x]->Q_BLEEDING,
							'Q_FATIGUE'  => $ancfollow[$x]->Q_FATIGUE,
							'Q_FEVER'  => $ancfollow[$x]->Q_FEVER,
							'Q_HEADACHE'  => $ancfollow[$x]->Q_HEADACHE,
							'Q_OTHERHEALTHPROBLEMS'  => $patient->ancfollow[$x]->Q_OTHERHEALTHPROBLEMS,
							'Q_DELIVERYPLAN'  => $ancfollow[$x]->Q_DELIVERYPLAN,
							'Q_LMP'  => displayAsEthioDate(strtotime($ancfollow[$x]->Q_LMP))."<br/>".date('D d M Y',strtotime($ancfollow[$x]->Q_LMP)),
							'Q_EDD'  => displayAsEthioDate(strtotime($ancfollow[$x]->Q_EDD))."<br/>".date('D d M Y',strtotime($ancfollow[$x]->Q_EDD)),
							'Q_SOCIALSUPPORT'  => getstring("Q_SOCIALSUPPORT.".$ancfollow[$x]->Q_SOCIALSUPPORT),
							'Q_ECONOMICS' => $ancfollow[$x]->Q_ECONOMICS,	
							'Q_TRANSPORTATION' => $ancfollow[$x]->Q_TRANSPORTATION
			);
		}
		for($x=0;$x <count($ancfollow); $x++ ){
			generateANCFollowCell($data[$x][$r]);
		}
		generateANCFollowCloseRow();
	}	

?>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($ancfollow);?>" class="sh"><?php echo getstring('section.medicalhistory');?></td>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_DIABETES');?></td>
	<?php 
		for($x=0;$x <count($ancfollow); $x++ ){
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
		for($x=0;$x <count($ancfollow); $x++ ){
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
		for($x=0;$x <count($ancfollow); $x++ ){
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
		for($x=0;$x <count($ancfollow); $x++ ){
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
		for($x=0;$x <count($ancfollow); $x++ ){
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
		for($x=0;$x <count($ancfollow); $x++ ){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo getstring("Q_TETANUS.".$ancfollow[$x]->Q_TETANUS);
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TT1');?></td>
	<?php 
		for($x=0;$x <count($ancfollow); $x++ ){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo ($ancfollow[$x]->Q_TT1 != "") ? displayAsEthioDate(strtotime($ancfollow[$x]->Q_TT1))."<br/>".date('D d M Y',strtotime($ancfollow[$x]->Q_TT1)) : "";
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_TT2');?></td>
	<?php 
		for($x=0;$x <count($ancfollow); $x++ ){
			echo "<td class='rdcell'>";
			if (isset($ancfollow[$x])){
				echo ($ancfollow[$x]->Q_TT2 != "") ? displayAsEthioDate(strtotime($ancfollow[$x]->Q_TT2))."<br/>".date('D d M Y',strtotime($ancfollow[$x]->Q_TT2)) : "";
			}
			echo "</td>";
		}
	?>
</tr>
<tr class="rrow">
	<td class="rqcell"><?php echo getstring('Q_IRONTABLETS');?></td>
	<?php 
		for($x=0;$x <count($ancfollow); $x++ ){
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
		for($x=0;$x <count($ancfollow); $x++ ){
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
	<td colspan="<?php echo 1+count($ancfollow);?>" class="sh"><?php echo getstring('section.examination');?></td>
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
	<td colspan="<?php echo 1+count($ancfollow);?>" class="sh"><?php echo getstring('section.checklist');?></td>
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
				echo displayAsEthioDate(strtotime($ancfollow[$x]->Q_APPOINTMENTDATE))."<br/>".date('D d M Y',strtotime($ancfollow[$x]->Q_APPOINTMENTDATE));
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
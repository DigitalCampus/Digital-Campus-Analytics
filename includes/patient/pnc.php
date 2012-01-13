<?php 
	$i = array(0,1,2);
	function generatePNCOpenRow($title){
		printf("<tr class='rrow'><td class='rqcell'>%s</td>",$title);
	}
	function generatePNCCell($data){
		printf("<td class='rdcell'>%s</td>",$data);
	}
	function generatePNCCloseRow(){
		printf("</tr>");
	}
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
			if (isset($pnc[$x])){
				printf('%1$s %3$s (%2$s)<br/>%4$s (%5$s)',date('H:i',strtotime($pnc[$x]->CREATEDON)), 
														date('D d M Y',strtotime($pnc[$x]->CREATEDON)), 
														displayAsEthioDate(strtotime($pnc[$x]->CREATEDON)), 
														$pnc[$x]->submittedname, 
														$pnc[$x]->protocollocation);
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
		generatePNCOpenRow(getstring($r));
		$data = array();
		for($x=0;$x <count($pnc); $x++ ){
			$data[$x] = array(
							'Q_USERID' => $pnc[$x]->Q_USERID,
							'Q_HEALTHPOINTID'  => $pnc[$x]->patientlocation,
							'Q_YEAROFBIRTH'  => $pnc[$x]->Q_YEAROFBIRTH,
							'Q_AGE'  => $pnc[$x]->Q_AGE
			);
		}
		for($x=0;$x <count($pnc); $x++ ){
			generatePNCCell($data[$x][$r]);
		}
		generatePNCCloseRow();
	}	

	// TODO : add other fields
?>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($pnc);?>" class="sh"><?php echo getstring('section.pnc.deliverysummary');?></td>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($pnc);?>" class="sh"><?php echo getstring('section.pnc.mother');?></td>
</tr>
<tr class='rrow'>
	<td colspan="<?php echo 1+count($pnc);?>" class="sh"><?php echo getstring('section.pnc.baby');?></td>
</tr>
<tr class='rrow'>
	<td colspan="2" class="sh"><?php echo getstring('section.checklist');?></td>
</tr>
</table>
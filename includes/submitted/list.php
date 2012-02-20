<?php 
	$opts = array('days'=>$days,'start'=>$start,'limit'=>$limit);
	$submitted = $API->getProtocolsSubmitted_Cache($opts);
	
	printf("<h3>%s</h3>",getstring('submitted.total.count',array($submitted->count['total'],$days)));
	
	
	$hasnext = false;
	$hasprev = false;
	
	$newnext = 0;
	$newprev = 0;
	if($submitted->start+$submitted->limit <= $submitted->count['total']){
		$hasnext = true;
		$newnext = max($submitted->start + $submitted->limit,0);
		$nextlink = sprintf("%skpi.php?kpi=submitted&view=%s&days=%d&start=%d",$CONFIG->homeAddress,$view,$days,$newnext);
	}
	
	if ($start >0 ){
		$hasprev = true;
		$newprev = max($submitted->start - $submitted->limit,0);
		$prevlink = sprintf("%skpi.php?kpi=submitted&view=%s&days=%d&start=%d",$CONFIG->homeAddress,$view,$days,$newprev);
	}

	if($submitted->count['total'] > 0){
?>

<table class="taskman">
	<tr class="nav printhide">
		<td><?php 
			if($hasprev){
				printf("<a href='%s'>%s</a>",$prevlink,getstring('table.nav.previous'));
			}
		?></td>
		<td colspan="3">
			<?php 
				$a = $submitted->start+1;
				$b = min($submitted->count['total'],$submitted->start+$submitted->limit);
				echo getstring('table.nav.title',array($a,$b));
			 ?>
		</td>
		<td><?php 
			if($hasnext){
				printf("<a href='%s'>%s</a>",$nextlink,getstring('table.nav.next'));
			}
		?></td>
	</tr>
	<tr>
		<th><?php echo getString("submitted.th.date")?></th>
		<th><?php echo getString("submitted.th.patientid")?></th>
		<th><?php echo getString("submitted.th.patient")?></th>
		<th><?php echo getString("submitted.th.protocol")?></th>
		<th><?php echo getString("submitted.th.detail")?></th>
	</tr>
	<?php 

		foreach ($submitted->protocols as $s){
			$s->datestamp = strtotime($s->datestamp);
			echo "<tr class='l' title='Click to view full details'";
			printf("onclick=\"document.location.href='%spatient.php?hpcode=%s&patientid=%s&protocol=%s';\">",
							$CONFIG->homeAddress,
							$s->patienthpcode,
							$s->Q_USERID,
							$s->protocol
							);
			echo "<td nowrap>".date('H:i D d M Y',$s->datestamp)."<br/>".displayAsEthioDate($s->datestamp)."</td>";
			echo "<td nowrap>".displayHealthPointName($s->patienthpcode)."/".$s->Q_USERID."</td>";
			echo "<td nowrap>";
			if (trim($s->patientname) == ""){
				printf("<span class='error'>%s</span>",getstring("warning.patient.notregistered"));
			} else {
				echo $s->patientname;
			}
			echo "</td>";
			echo "<td nowrap>".getString($s->protocol)."</td>";
			echo "<td nowrap>".$s->submittedname." at ".displayHealthPointName($s->protocolhpcode)."</td>";
			echo "</tr>";
		}
			
	?>
</table>
<?php 
	} else {
		echo getstring('warning.norecords');
	}
?>
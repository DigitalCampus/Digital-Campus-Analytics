
<?php 

	function getANC($report, $number, $type){
		global $API;
		$sql = sprintf("SELECT * FROM (
						SELECT
						IF(@prev != CONCAT(a.hpcode,'_',a.userid), @rownum:=1, @rownum:=@rownum+1) as rownumber, 
							@prev:=CONCAT(a.hpcode,'_',a.userid), a.*
						FROM (
							SELECT 
							visitdate, 
							userid,
							hpcode,
							protocol,
							user_uri
							FROM cache_visit, (SELECT @rownum := 0, @prev:='') sq
							WHERE protocol = 'protocol.anc'
							ORDER BY hpcode,userid,visitdate
							) a) ord
					INNER JOIN user u ON ord.user_uri = u.user_uri
					INNER JOIN healthpoint hp ON hp.hpid = u.hpid
					WHERE visitdate >='%s' and visitdate<= '%s'
					AND hp.hpcode IN (%s)
					and rownumber =%d
					and hptype = '%s'",$report->start,$report->end, $report->hpcodes,$number,$type);

		$result = $API->runSql($sql);
		return mysql_num_rows($result);
	}


?>

<table>
<tr>
	<td>Report</td>
	<td>Health Post</td>
	<td>Health Centre</td>
	<td>Total</td>
</tr>

<tr>
	<td colspan="4">1. Antenatal Care Service</td>
</tr>

<tr>
	<td>First visit</td>
	<td><?php 
		$healthpost = getANC($report,1,'healthpost');
		echo $healthpost; 
		?></td>
	<td><?php $healthcentre = getANC($report,1,'healthcentre');
		echo $healthcentre; ?></td>
	<td><?php echo $healthpost+$healthcentre;?></td>
</tr> 

<tr>
	<td>Revisit 2</td>
	<td><?php 
		$healthpost = getANC($report,2,'healthpost');
		echo $healthpost; 
		?></td>
	<td><?php $healthcentre = getANC($report,2,'healthcentre');
		echo $healthcentre; ?></td>
	<td><?php echo $healthpost+$healthcentre;?></td>
</tr> 

<tr>
	<td>Revisit 3</td>
	<td><?php 
		$healthpost = getANC($report,3,'healthpost');
		echo $healthpost; 
		?></td>
	<td><?php $healthcentre = getANC($report,3,'healthcentre');
		echo $healthcentre; ?></td>
	<td><?php echo $healthpost+$healthcentre;?></td>
</tr> 

<tr>
	<td>Revisit 4</td>
	<td><?php 
		$healthpost = getANC($report,4,'healthpost');
		echo $healthpost; 
		?></td>
	<td><?php $healthcentre = getANC($report,4,'healthcentre');
		echo $healthcentre; ?></td>
	<td><?php echo $healthpost+$healthcentre;?></td>
</tr> 

<tr>
	<td colspan="4">2. Delivery Service</td>
</tr>
<tr>
	<td>Normal Delivery (SVD) (Q308)</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Abnormal/Instrumental deliveries (Forceps, Vacuum, Breach, etc) (Q310/311)</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Total Number of birth </td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Mothers died during delivery (Q304)</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Birth weight less than 2.5kgs</td>
	<td></td>
	<td></td>
	<td></td>
</tr>

<tr>
	<td colspan="4">3. Postnatal Care</td>
</tr>
<tr>
	<td>First visit (seen only within First 42 days)</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Revisit 2</td>
	<td></td>
	<td></td>
	<td></td>
</tr>

<tr>
	<td colspan="4">4. Abortion </td>
</tr>
<tr>
	<td>Total number of spontaneous abortions</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Total number of induced abortions</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>
<div id="anc1defaulters" class="summary">
<h2><?php echo getstring('dashboard.anc1defaulters.title');?></h2>
<?php 

/*
 * ANC1 defaulters
 */

// get all the submitted ANC1 protocols from frist day of the month 6 months ago
$sql = "SELECT 	p._URI, 
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.Q_LMP, 
				p._CREATION_DATE as createdate, 
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC1_DUE_BY_END." DAY) AS ANC1DUEBY 
		FROM ".ANCFIRST." p 
		WHERE p._CREATION_DATE > date_format(curdate() - interval 6 month,'%Y-%m-01 00:00:00')
		ORDER BY p._CREATION_DATE ASC";

// set up summary/results array/objects
$date = new DateTime();
$date->sub(new DateInterval('P6M'));
$summary = array();
for ($i=0; $i<7 ;$i++){
	$summary[$date->format('M-Y')] = new stdClass;
	$summary[$date->format('M-Y')]->defaulter = 0;
	$summary[$date->format('M-Y')]->nondefaulter = 0;
	$date->add(new DateInterval('P1M'));
}

// exec query and loop through results
// if creation date > ANC1DUEBY then defaulter, group by month/year of ANC1DUEBY
// otherwise non defaulter
$results = $API->runSql($sql);
while($row = mysql_fetch_array($results)){
	$date = new DateTime($row['createdate']);
	$arrayIndex = $date->format('M-Y');

	if ($row['createdate'] > $row['ANC1DUEBY'] ){
		$summary[$arrayIndex]->defaulter++;
	} else {
		$summary[$arrayIndex]->nondefaulter++;
	}
}

print_r($summary)
?>
</div>



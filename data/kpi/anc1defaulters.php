<?php 

/*
 * ANC1 defaulters
 */
$anc1_defaulters = array();
$sql = "SELECT 	p._URI, 
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.Q_LMP, 
				p._CREATION_DATE, 
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC1_DUE_BY_END." DAY) AS ANC1DUEBY 
		FROM ".ANCFIRST." p ";

// loop through
// if creation date > ANC1DUEBY then defaulter, group by month/year of ANC1DUEBY 
// otherwise non defaulter



?>




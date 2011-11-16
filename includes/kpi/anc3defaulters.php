<?php 
/*
* ANC3 defaulters
*/
$anc2_defaulters = array();
// all those who had an ANC follow up visit
$sql = "SELECT 	p._URI,
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.Q_LMP, 
				p._CREATION_DATE, 
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC3_DUE_BY_START." DAY) AS ANC3_DUE_BY_START,
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC3_DUE_BY_END." DAY) AS ANC3_DUE_BY_END
		FROM ".ANCFOLLOW." p";
echo $sql;
echo "<br/>";
// all those who had an ANC1 but not a second visit and didn't have termination protocol entered before ANC was due
$sql = "SELECT 	p._URI,
				p.Q_USERID, 
				p.Q_HEALTHPOINTID, 
				p.Q_LMP, 
				p._CREATION_DATE, 
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC3_DUE_BY_START." DAY) AS ANC3_DUE_BY_START, 
				DATE_ADD(p.Q_LMP, INTERVAL ".ANC3_DUE_BY_END." DAY) AS ANC3_DUE_BY_END 
		FROM ".ANCFIRST." p
		LEFT OUTER JOIN ".ANCFOLLOW." f ON f.Q_USERID = p.Q_USERID AND f.Q_HEALTHPOINTID = p.Q_HEALTHPOINTID
		WHERE f.Q_USERID IS NULL";
// @TODO add constraint about terminations
echo $sql;



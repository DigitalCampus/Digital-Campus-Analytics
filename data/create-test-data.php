<?php 
include_once '../config.php';
//header('Content-Disposition: attachment; filename="data.sql"');
header("Content-type: text/plain; charset:UTF8");

$today = new DateTime();

$yesterday = new DateTime();
$yesterday->sub(new DateInterval('P1D'));

$oneweekago = new DateTime();
$oneweekago->sub(new DateInterval('P7D'));

$twoweeksago = new DateTime();
$twoweeksago->sub(new DateInterval('P14D'));

$onemonthago = new DateTime();
$onemonthago->sub(new DateInterval('P1M'));

$sixweeksago =  new DateTime();
$sixweeksago->sub(new DateInterval('P42D'));

$dates = array($today,$yesterday,$oneweekago,$twoweeksago,$onemonthago,$sixweeksago);

$users = array();
$users['rishan'] = new stdClass();
$users['rishan']->hpcode = '1003'; //Negash
$users['rishan']->uid = 'uid:rishan|2011-10-03T09:00:52.972+0000';

$users['mihret'] = new stdClass();
$users['mihret']->hpcode = '1002'; //Gemad
$users['mihret']->uid = 'uid:mihret|2011-10-03T09:00:52.971+0000';

$users['zenebu'] = new stdClass();
$users['zenebu']->hpcode = '1009'; //Frewoyni
$users['zenebu']->uid = 'uid:zenebu|2011-10-01T07:53:50.572+0000';

?>
-- empty tables
DELETE FROM <?php echo TABLE_REGISTRATION; ?>;
DELETE FROM <?php echo TABLE_REG_HOMEAPPLIANCES; ?>;
DELETE FROM <?php echo TABLE_ANCFIRST; ?>;
DELETE FROM <?php echo TABLE_ANCFIRST_FPMETHOD; ?>;
DELETE FROM <?php echo TABLE_ANCFIRST_ATTENDED; ?>;
DELETE FROM <?php echo TABLE_ANCFOLLOW; ?>;
DELETE FROM <?php echo TABLE_ANCTRANSFER; ?>;
DELETE FROM <?php echo TABLE_ANCTRANSFER_FPMETHOD; ?>;
DELETE FROM <?php echo TABLE_ANCTRANSFER_ATTENDED; ?>;
DELETE FROM <?php echo TABLE_ANCLABTEST; ?>;
DELETE FROM <?php echo TABLE_DELIVERY; ?>;
DELETE FROM <?php echo TABLE_DELIVERY_ATTENDED; ?>;
DELETE FROM <?php echo TABLE_DELIVERY_BABY; ?>;


-- Create some test registrations

-- couple each for 
-- negash (1003 - rishan: uid:rishan|2011-10-03T09:00:52.972+0000) 
-- gemad (1002 - mihret: uid:mihret|2011-10-03T09:00:52.971+0000) 
-- Frewoyni (1009 - zenebu: uid:zenebu|2011-10-01T07:53:50.572+0000)
INSERT INTO <?php echo TABLE_REGISTRATION; ?> 
		(_URI,								
		_CREATOR_URI_USER,	
		_CREATION_DATE,	
		_LAST_UPDATE_DATE,
		TODAY,	
		Q_HEALTHPOINTID,	
		Q_USERID,	
		Q_USERNAME,
		Q_USERFATHERSNAME,
		Q_USERGRANDFATHERSNAME)
VALUES
<?php 
	$counter = 1;
	$max = count($users) * count($dates);
	foreach($users as $u){
		$uuid = 1;
		foreach($dates as $d){
			$date = $d->format('Y-m-d');
			printf("('uuid:%s','%s','%s','%s','%s','%s','%s','Test','Patient','%s/%s')",uniqid(),$u->uid,$date,$date,$date,$u->hpcode,$uuid,$u->hpcode,$uuid);
			if ($counter < $max){
				printf(",\n");
			}
			$counter++;
			$uuid++;
		}
		
	}
?>		

-- data for testing ANC1 on time
INSERT INTO <?php echo TABLE_ANCFIRST; ?> 
		(_URI,								
		_CREATOR_URI_USER,	
		_CREATION_DATE,	
		_LAST_UPDATE_DATE,
		TODAY,	
		Q_HEALTHPOINTID,	
		Q_USERID,
		Q_LMP)
VALUES
	('uuid:<?php echo uniqid(); ?>',
		'<?php echo $users['rishan']->uid; ?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $users['rishan']->hpcode; ?>',
		'1',
		'<?php $temp = new DateTime(); $temp->sub(new DateInterval('P'.(ANC1_DUE_BY_END + 42).'D')); echo $temp->format('Y-m-d');?>'
	),
	('uuid:<?php echo uniqid(); ?>',
		'<?php echo $users['rishan']->uid; ?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $users['rishan']->hpcode; ?>',
		'2',
		'<?php $temp = new DateTime(); $temp->sub(new DateInterval('P'.(ANC1_DUE_BY_END + 43).'D')); echo $temp->format('Y-m-d');?>'
	),
	('uuid:<?php echo uniqid(); ?>',
		'<?php echo $users['rishan']->uid; ?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $sixweeksago->format('Y-m-d')?>',
		'<?php echo $users['rishan']->hpcode; ?>',
		'3',
		'<?php $temp = new DateTime(); $temp->sub(new DateInterval('P'.(ANC1_DUE_BY_END + 41).'D')); echo $temp->format('Y-m-d');?>'
	),
	('uuid:<?php echo uniqid(); ?>',
		'<?php echo $users['mihret']->uid; ?>',
		'<?php echo $oneweekago->format('Y-m-d')?>',
		'<?php echo $oneweekago->format('Y-m-d')?>',
		'<?php echo $oneweekago->format('Y-m-d')?>',
		'<?php echo $users['mihret']->hpcode; ?>',
		'3',
		'<?php $temp = new DateTime(); $temp->sub(new DateInterval('P'.(ANC1_DUE_BY_END + 7).'D')); echo $temp->format('Y-m-d');?>'
	)
	,
	('uuid:<?php echo uniqid(); ?>',
		'<?php echo $users['mihret']->uid; ?>',
		'<?php echo $oneweekago->format('Y-m-d')?>',
		'<?php echo $oneweekago->format('Y-m-d')?>',
		'<?php echo $oneweekago->format('Y-m-d')?>',
		'<?php echo $users['mihret']->hpcode; ?>',
		'6',
		'<?php $temp = new DateTime(); $temp->sub(new DateInterval('P'.(ANC1_DUE_BY_END + 8).'D')); echo $temp->format('Y-m-d');?>'
	)
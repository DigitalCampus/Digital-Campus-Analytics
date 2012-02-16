<?php 

$date = new DateTime();

$reportperiod = array();

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.1'). " 2005";
$rpm->start = $date->setDate(2012, 9, 11)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 10, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.12')."/".getstring('ethio.month.13'). " 2004";
$rpm->prevstart = $date->setDate(2012, 8, 7)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 9, 10)->format('Y-m-d 23:59:59');
$reportperiod['2005-01'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.12')."/".getstring('ethio.month.13'). " 2004";
$rpm->start = $date->setDate(2012, 8, 7)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 9, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.11'). " 2004";
$rpm->prevstart = $date->setDate(2012, 7, 8)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 8, 6)->format('Y-m-d 23:59:59');
$reportperiod['2004-12'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.11'). " 2004";
$rpm->start = $date->setDate(2012, 7, 8)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 8, 6)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.10'). " 2004";
$rpm->prevstart = $date->setDate(2012, 6, 8)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 7, 7)->format('Y-m-d 23:59:59');
$reportperiod['2004-11'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.10'). " 2004";
$rpm->start = $date->setDate(2012, 6, 8)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 7, 7)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.9'). " 2004";
$rpm->prevstart = $date->setDate(2012, 5, 9)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 6, 7)->format('Y-m-d 23:59:59');
$reportperiod['2004-10'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.9'). " 2004";
$rpm->start = $date->setDate(2012, 5, 9)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 6, 7)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.8'). " 2004";
$rpm->prevstart = $date->setDate(2012, 4, 9)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 5, 8)->format('Y-m-d 23:59:59');
$reportperiod['2004-9'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.8'). " 2004";
$rpm->start = $date->setDate(2012, 4, 9)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 5, 8)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.7'). " 2004";
$rpm->prevstart = $date->setDate(2012, 3, 10)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 4, 8)->format('Y-m-d 23:59:59');
$reportperiod['2004-8'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.7'). " 2004";
$rpm->start = $date->setDate(2012, 3, 10)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 4, 8)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.6'). " 2004";
$rpm->prevstart = $date->setDate(2012, 2, 9)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 3, 9)->format('Y-m-d 23:59:59');
$reportperiod['2004-7'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.6'). " 2004";
$rpm->start = $date->setDate(2012, 2, 9)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 3, 9)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.5'). " 2004";
$rpm->prevstart = $date->setDate(2012, 1, 10)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 2, 8)->format('Y-m-d 23:59:59');
$reportperiod['2004-6'] = $rpm;



$rpm = new stdClass();
$rpm->text = getstring('ethio.month.5'). " 2004";
$rpm->start = $date->setDate(2012, 1, 10)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 2, 8)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.4'). " 2004";
$rpm->prevstart = $date->setDate(2011, 12, 11)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2012, 1, 9)->format('Y-m-d 23:59:59');
$reportperiod['2004-5'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.2'). " 2004 - ". getstring('ethio.month.4'). " 2005";
$rpm->start = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 1, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.11'). " 2003 - ". getstring('ethio.month.1'). " 2004";
$rpm->prevstart = $date->setDate(2011, 7, 8)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 10, 11)->format('Y-m-d 23:59:59');
$reportperiod['2004-q2'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.4'). " 2004";
$rpm->start = $date->setDate(2011, 12, 11)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 1, 9)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.3'). " 2004";
$rpm->prevstart = $date->setDate(2011, 11, 11)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 12, 10)->format('Y-m-d 23:59:59');
$reportperiod['2004-4'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.3'). " 2004";
$rpm->start = $date->setDate(2011, 11, 11)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2011, 12, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.2'). " 2004";
$rpm->prevstart = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 11, 10)->format('Y-m-d 23:59:59');
$reportperiod['2004-3'] = $rpm;

$rpm = new stdClass();
$rpm->text = getstring('ethio.month.2'). " 2004";
$rpm->start = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2011, 11, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.1'). " 2004";
$rpm->prevstart = $date->setDate(2011, 9, 12)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 10, 11)->format('Y-m-d 23:59:59');
$reportperiod['2004-2'] = $rpm;

?>
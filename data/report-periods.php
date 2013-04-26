<?php 

$date = new DateTime();
$today = new DateTime();
$temp = new DateTime();
$reportperiod = array();


// Gunbet 2005
if($today > $temp->setDate(2013, 5, 9)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.9'). " 2005";
	$rpm->start = $date->setDate(2013, 5, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2013, 6, 7)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.8'). " 2005";
	$rpm->prevstart =$date->setDate(2013, 4, 9)->format('Y-m-d 00:00:00');
	$rpm->prevend =$date->setDate(2013, 5, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2005-09'] = $rpm;
}

// Miazia 2005
if($today > $temp->setDate(2013, 4, 9)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.8'). " 2005";
	$rpm->start = $date->setDate(2013, 4, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2013, 5, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.7'). " 2005";
	$rpm->prevstart =$date->setDate(2013, 3, 10)->format('Y-m-d 00:00:00');
	$rpm->prevend =$date->setDate(2013, 4, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2005-08'] = $rpm;
}

// Megabit 2005
if($today > $temp->setDate(2013, 3, 10)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.7'). " 2005";
	$rpm->start = $date->setDate(2013, 3, 10)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2013, 4, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.6'). " 2005";
	$rpm->prevstart = $date->setDate(2013, 2, 8)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2013, 3, 9)->format('Y-m-d 23:59:59');
	$reportperiod['2005-07'] = $rpm;
}

// Yekatit 2005
if($today > $temp->setDate(2013, 2, 8)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.6'). " 2005";
	$rpm->start = $date->setDate(2013, 2, 8)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2013, 3, 9)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.5'). " 2005";
	$rpm->prevstart = $date->setDate(2013, 1, 9)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2013, 2, 7)->format('Y-m-d 23:59:59');
	$reportperiod['2005-06'] = $rpm;
}

// Tiri 2005
if($today > $temp->setDate(2013, 1, 9)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.5'). " 2005";
	$rpm->start = $date->setDate(2013, 1, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2013, 2, 7)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.4'). " 2005";
	$rpm->prevstart = $date->setDate(2012, 12, 10)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2013, 1, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2005-05'] = $rpm;
}

// Tahsas 2005
if($today > $temp->setDate(2012, 12, 10)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.4'). " 2005";
	$rpm->start = $date->setDate(2012, 12, 10)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2013, 1, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.3'). " 2005";
	$rpm->prevstart = $date->setDate(2012, 11, 10)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 12, 9)->format('Y-m-d 23:59:59');
	$reportperiod['2005-04'] = $rpm;
}

// Hidar 2005
if($today > $temp->setDate(2012, 11, 10)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.3'). " 2005";
	$rpm->start = $date->setDate(2012, 11, 10)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 12, 9)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.2'). " 2005";
	$rpm->prevstart = $date->setDate(2012, 10, 11)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 11, 9)->format('Y-m-d 23:59:59');
	$reportperiod['2005-03'] = $rpm;
}

// Tikimti 2005
if($today > $temp->setDate(2012, 10, 11)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.2'). " 2005";
	$rpm->start = $date->setDate(2012, 10, 11)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 11, 9)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.1'). " 2005";
	$rpm->prevstart = $date->setDate(2012, 9, 11)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 10, 10)->format('Y-m-d 23:59:59');
	$reportperiod['2005-02'] = $rpm;
}


// Meskerem 2005
if($today > $temp->setDate(2012, 9, 11)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.1'). " 2005";
	$rpm->start = $date->setDate(2012, 9, 11)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 10, 10)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.12')."/".getstring('ethio.month.13'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 8, 7)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 9, 10)->format('Y-m-d 23:59:59');
	$reportperiod['2005-01'] = $rpm;
}

// Nehase/Pagumein 2004
if($today > $temp->setDate(2012, 8, 7)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.12')."/".getstring('ethio.month.13'). " 2004";
	$rpm->start = $date->setDate(2012, 8, 7)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 9, 10)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.11'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 7, 8)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 8, 6)->format('Y-m-d 23:59:59');
	$reportperiod['2004-12'] = $rpm;
}

// Hamle 2004
if($today > $temp->setDate(2012, 7, 8)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.11'). " 2004";
	$rpm->start = $date->setDate(2012, 7, 8)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 8, 6)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.10'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 6, 8)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 7, 7)->format('Y-m-d 23:59:59');
	$reportperiod['2004-11'] = $rpm;
}

// report Q4 2004
if($today > $temp->setDate(2012, 6, 8)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.8'). " 2004 - ". getstring('ethio.month.10'). " 2004";
	$rpm->start = $date->setDate(2012, 4, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 7, 7)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.5'). " 2004 - ". getstring('ethio.month.7'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 1, 10)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 4, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2004-q4'] = $rpm;
}

// Sene 2004
if($today > $temp->setDate(2012, 6, 8)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.10'). " 2004";
	$rpm->start = $date->setDate(2012, 6, 8)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 7, 7)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.9'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 5, 9)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 6, 7)->format('Y-m-d 23:59:59');
	$reportperiod['2004-10'] = $rpm;
}

// Gunbet 2004
if($today > $temp->setDate(2012, 5, 9)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.9'). " 2004";
	$rpm->start = $date->setDate(2012, 5, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 6, 7)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.8'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 4, 9)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 5, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2004-9'] = $rpm;
}

// Miazia 2004
if($today > $temp->setDate(2012, 4, 8)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.8'). " 2004";
	$rpm->start = $date->setDate(2012, 4, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 5, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.7'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 3, 10)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 4, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2004-8'] = $rpm;
}

// report Q3 2004
if($today > $temp->setDate(2012, 1, 10)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.5'). " 2004 - ". getstring('ethio.month.7'). " 2004";
	$rpm->start = $date->setDate(2012, 1, 10)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 4, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.2'). " 2004 - ". getstring('ethio.month.4'). " 2004";
	$rpm->prevstart = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 1, 9)->format('Y-m-d 23:59:59');
	$reportperiod['2004-q3'] = $rpm;
}

// Megabit 2004
if($today > $temp->setDate(2012, 3, 10)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.7'). " 2004";
	$rpm->start = $date->setDate(2012, 3, 10)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 4, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.6'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 2, 9)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 3, 9)->format('Y-m-d 23:59:59');
	$reportperiod['2004-7'] = $rpm;
}

// Yekatit 2004 
if($today > $temp->setDate(2012, 2, 9)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.6'). " 2004";
	$rpm->start = $date->setDate(2012, 2, 9)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 3, 9)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.5'). " 2004";
	$rpm->prevstart = $date->setDate(2012, 1, 10)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 2, 8)->format('Y-m-d 23:59:59');
	$reportperiod['2004-6'] = $rpm;
}

// Tiri 2004
if($today > $temp->setDate(2012, 1, 10)){
	$rpm = new stdClass();
	$rpm->text = getstring('ethio.month.5'). " 2004";
	$rpm->start = $date->setDate(2012, 1, 10)->format('Y-m-d 00:00:00');
	$rpm->end = $date->setDate(2012, 2, 8)->format('Y-m-d 23:59:59');
	$rpm->prevtext = getstring('ethio.month.4'). " 2004";
	$rpm->prevstart = $date->setDate(2011, 12, 11)->format('Y-m-d 00:00:00');
	$rpm->prevend = $date->setDate(2012, 1, 9)->format('Y-m-d 23:59:59');
	$reportperiod['2004-5'] = $rpm;
}
// report Q2 2004
$rpm = new stdClass();
$rpm->text = getstring('ethio.month.2'). " 2004 - ". getstring('ethio.month.4'). " 2004";
$rpm->start = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 1, 9)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.11'). " 2003 - ". getstring('ethio.month.1'). " 2004";
$rpm->prevstart = $date->setDate(2011, 7, 8)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 10, 11)->format('Y-m-d 23:59:59');
$reportperiod['2004-q2'] = $rpm;

// Tahsas 2004
$rpm = new stdClass();
$rpm->text = getstring('ethio.month.4'). " 2004";
$rpm->start = $date->setDate(2011, 12, 11)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2012, 1, 9)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.3'). " 2004";
$rpm->prevstart = $date->setDate(2011, 11, 11)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 12, 10)->format('Y-m-d 23:59:59');
$reportperiod['2004-4'] = $rpm;

// Hidar 2004
$rpm = new stdClass();
$rpm->text = getstring('ethio.month.3'). " 2004";
$rpm->start = $date->setDate(2011, 11, 11)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2011, 12, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.2'). " 2004";
$rpm->prevstart = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 11, 10)->format('Y-m-d 23:59:59');
$reportperiod['2004-3'] = $rpm;

// Tikimti 2004
$rpm = new stdClass();
$rpm->text = getstring('ethio.month.2'). " 2004";
$rpm->start = $date->setDate(2011, 10, 12)->format('Y-m-d 00:00:00');
$rpm->end = $date->setDate(2011, 11, 10)->format('Y-m-d 23:59:59');
$rpm->prevtext = getstring('ethio.month.1'). " 2004";
$rpm->prevstart = $date->setDate(2011, 9, 12)->format('Y-m-d 00:00:00');
$rpm->prevend = $date->setDate(2011, 10, 11)->format('Y-m-d 23:59:59');
$reportperiod['2004-2'] = $rpm;

?>
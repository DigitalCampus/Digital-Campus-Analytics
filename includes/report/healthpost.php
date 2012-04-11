<?php 

printf("<h2>%s: %s</h2>", getstring('report.title.healthpost'),$currenthpname);

printf("<h3>%s</h3>", getString("report.kpioverview"));
include_once('components/kpioverview.php');

$report->temp = new stdClass();
printf("<h3>%s</h3>", getString("report.submitted.bar",array($report->text)));
$report->temp->protocol = 'protocol.total';
$report->temp->id = 'submitted-bar-div';
include('components/submitted-bar.php');

printf("<h3>%s</h3>", getString("report.submitted.anc.bar",array($report->text)));
$report->temp->protocol = 'protocol.anc';
$report->temp->id = 'submitted-anc-bar-div';
include('components/submitted-bar.php');

printf("<h3>%s</h3>", getString("report.submitted.delivery.bar",array($report->text)));
$report->temp->protocol = 'protocol.delivery';
$report->temp->id = 'submitted-delivery-bar-div';
include('components/submitted-bar.php');

printf("<h3>%s</h3>", getString("report.submitted.pnc.bar",array($report->text)));
$report->temp->protocol = 'protocol.pnc';
$report->temp->id = 'submitted-pnc-bar-div';
include('components/submitted-bar.php');

printf("<h3>%s</h3>", getString("report.submitted.range",array($report->text)));
include_once('components/submitted-range.php');





?>
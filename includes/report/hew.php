<?php 

printf("<h2>%s</h2>", $currenthpname);

printf("<h3>%s</h3>", getString("report.kpioverview"));
include_once('components/kpioverview.php');

printf("<h3>%s</h3>", getString("report.deliveriesdue",array($days)));
include_once('components/deliveries.php');

printf("<h3>%s</h3>", getString("report.highrisk"));
include_once('components/highrisk.php');

printf("<h3>%s</h3>", getString("report.submitted",array($days)));
include_once('components/submitted.php');

printf("<h3>%s</h3>", getString("report.datacheck.registration"));
include_once('components/datacheck.registration.php');

printf("<h3>%s</h3>", getString("report.overdue"));
include_once('components/overdue.php');

printf("<h3>%s</h3>", getString("report.tasksdue",array($days)));
include_once('components//tasksdue.php');



?>
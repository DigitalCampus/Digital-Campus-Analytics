<?php 

printf("<h2>%s: %s</h2>", getstring('report.title.supervisor'),$currenthpname);

printf("<h3>%s</h3>", getString("report.kpioverview"));
include_once('components/kpioverview.php');

printf("<h3>%s</h3>", getString("report.submitted.bar",array($report->startDate->format('d-M-Y'),$report->endDate->format('d-M-Y'))));
include_once('components/submitted-bar.php');
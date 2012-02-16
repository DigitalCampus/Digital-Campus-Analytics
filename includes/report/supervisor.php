<?php 

printf("<h2>%s: %s</h2>", getstring('report.title.supervisor'),$currenthpname);

printf("<h3>%s</h3>", getString("report.kpioverview"));
include_once('components/kpioverview.php');

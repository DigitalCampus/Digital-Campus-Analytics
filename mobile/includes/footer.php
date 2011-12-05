</div> <!-- end #content -->
</div> <!-- end #page -->
</body>
</html>

<?php 
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
writeToLog("info","pagehit",$_SERVER["REQUEST_URI"], $total_time, $CONFIG->mysql_queries_time, $CONFIG->mysql_queries_count);

$API->cleanUpDB();
?>
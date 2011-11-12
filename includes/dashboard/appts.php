<div id="appointmentsummary" class="summary">
	Missing appointments (not done within expected time)
	histogram of each health post?
<!-- ?php 
	$tasks = $API->getAllTasks($duein);
?>
<h2>
<?php 
	switch ($duein){
		case 7: 
			echo getstring('dash.tasks.summary.header',array("<span style='color:green'>7 days</span>","<a href='?duein=14'>14 days</a>","<a href='?duein=31'>31 days</a>"));
			break;
		case 14: 
			echo getstring('dash.tasks.summary.header',array("<a href='?duein=7'>7 days</a>","<span style='color:green'>14 days</span>","<a href='?duein=31'>31 days</a>"));
			break;
		case 31: 
			echo getstring('dash.tasks.summary.header',array("<a href='?duein=7'>7 days</a>","<a href='?duein=14'>14 days</a>","<span style='color:green'>31 days</span>"));
			break;
			
	}
?>
</h2>
<h3>Total: <?php echo count($tasks);?></h3>
<p><a href="task-manager.php">View task list details</a></p>
<h3>By Type:</h3>
<?php 
	$summary = array();
	foreach($tasks as $t){
		if(array_key_exists($t->protocol,$summary)){
			$summary[$t->protocol] += 1;
		} else {
			$summary[$t->protocol] = 1;
		}
	}
	echo "<ul>";
	foreach($summary as $key => $val){
		echo "<li>".$key." : ".$val."</li>";
	}
	echo "</ul>";
?>

<h3>By Health Post:</h3>
<?php 
	$summary = array();
	foreach($tasks as $t){
		if(array_key_exists($t->location,$summary)){
			$summary[$t->location] += 1;
		} else {
			$summary[$t->location] = 1;
		}
	}	
	echo "<ul>";
	foreach($summary as $key => $val){
		echo "<li>".$key." : ".$val."</li>";
	}
	echo "</ul>";
?> -->
</div>

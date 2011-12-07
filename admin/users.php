<?php
require_once "../config.php";
require_once "../includes/header.php";

// only allow access by admins
if($USER->getProp('permissions.admin') != "true"){
	writeToLog('warning','adminpage','accessdenied');
	echo getString ("warning.accessdenied");
	die;
}

include_once('../includes/menu-admin.php');

$users = $API->getUsers(true);
//echo "<pre>";
//print_r($users);
//echo "</pre>";
?>

<h2>Users</h2>
<table class="admin">
<tr>
<th>UserName</th>
<th>Name</th>
<th>Location - Health Post</th>
<th>District</th>
<th>Options</th>
</tr>
<?php 
	foreach ($users as $u){
		echo "<tr class='n'>";
		echo "<td nowrap>".$u->username."</td>";
		echo "<td nowrap>".$u->firstname." ".$u->lastname."</td>";
		echo "<td nowrap>".$u->hpname."</td>";
		echo "<td nowrap>".$u->dname."</td>";
		echo "<td nowrap><a href='edituser.php?userid=".$u->userid."'>[Edit]</a></td>";
		echo "</tr>";
	}
?>
</table>
<?php 
include_once "../includes/footer.php";
?>
<?php
include_once "config.php";
global $USER,$MSG;
$PAGE="user";

include_once "includes/header.php";
$submit = optional_param('submit','',PARAM_TEXT);

if($submit != ""){
	$password = optional_param('password','',PARAM_TEXT);
	$confirmpassword = optional_param('confirmpassword','',PARAM_TEXT);
	if($password != $confirmpassword){
		array_push($MSG,"Passwords don't match");
	} else {
		if($API->userChangePassword($USER->userid,$password)){
			array_push($MSG,"Your password has been changed");
			writeToLog('info','passwordchanged','');
		} else {
			array_push($MSG,"There was an error changing your password");
		}
		
	}
}


?>
<h2>User edit page</h2>

<?php 
	if(!empty($MSG)){
	echo "<ul>";
	foreach ($MSG as $err){
		echo "<li>".$err."</li>";
    }
    echo "</ul>";
}
?>
<form method="post" action="">
<div class="formblock">
	<div class="formlabel">Username:</div>
	<div class="formfield"><input type="text" name="username" value="<?php echo $USER->username;?>" disabled="true"></input></div>
	<div class="forminfo">You cannot change your username</div>
</div>

<div class="formblock">
	<div class="formlabel">New password:</div>
	<div class="formfield"><input type="password" name="password"></input></div>
</div>

<div class="formblock">
	<div class="formlabel">Confirm new password:</div>
	<div class="formfield"><input type="password" name="confirmpassword"></input></div>
</div>
<div class="formblock">
	<div class="formlabel">&nbsp;</div>
	<div class="formfield"><input type="submit" name="submit" value="Change password"></input></div>
</div>
</form>
<?php 
include_once "includes/footer.php";
?>
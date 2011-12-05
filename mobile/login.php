<?php
include_once "../config.php";

global $PAGE,$CONFIG,$MSG,$API;

$PAGE = "login";
$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);
$ref = optional_param("ref",$CONFIG->homeAddress."mobile/index.php",PARAM_TEXT);

// check that user not already logged in
if(isset($USER->username)){
	header('Location: index.php');
	return;
}

if ($submit != ""){
	if(userLogin($username,$password)){
		$API->setUserProperty($USER->userid,'lastlogin',date('Y-m-d H:i:s'));
		header('Location: '. $ref);
		return;
	}
}

include_once "includes/header.php";

printf("<h1>%s</h1>",getstring("mobile.login.title"));
if(!empty($MSG)){
	echo "<div class='error'><ul>";
	foreach ($MSG as $err){
		echo "<li>".$err."</li>";
    }
    echo "</ul></div>";
}

?>

<form method="post" action="">
	<div class="formblock">
		<div class="formlabel"><?php echo getstring('login.username'); ?></div>
		<div class="formfield"><input type="text" name="username"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("login.password"); ?></div>
		<div class="formfield"><input type="password" name="password"></input></div>
	</div>
	<div class="formblock">
		<div class="formfield"><input type="submit" name="submit" value="<?php echo getstring("login.submit.button"); ?>"></input></div>
	</div>
</form>

<?php 
include_once "includes/footer.php";
?>
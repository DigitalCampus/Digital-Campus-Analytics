<?php
require_once "../config.php";
require_once "../includes/header.php";

// only allow access by admins
if($USER->getProp('permissions.admin') != "true"){
	writeToLog('warning','adminpage','accessdenied');
	echo getString ("warning.accessdenied");
	die;
}
$userid = required_param("userid",PARAM_INT);
$user = $API->getUserByID($userid);
$API->getUserProperties($user);
$healthpoints = $API->getHealthPoints(true);

$submit = optional_param('submit','',PARAM_TEXT);
if($submit != ""){
	// check if password shoudl be changed
	$password = optional_param('password','',PARAM_TEXT);
	$confirmpassword = optional_param('confirmpassword','',PARAM_TEXT);
	if ($password != "") {
		if($password != $confirmpassword){
			array_push($MSG,"Passwords don't match");
		} else {
			if($API->userChangePassword($userid, $password)){
				array_push($MSG,"Password has been changed");
				writeToLog('info','passwordchanged','for: '.$user->username);
			} else {
				array_push($MSG,"There was an error changing your password");
			}
		}
	}
	$firstname = optional_param('firstname',$user->firstname,PARAM_TEXT);
	$lastname = optional_param('lastname',$user->lastname ,PARAM_TEXT);
	$user_uri = optional_param('user_uri',$user->user_uri ,PARAM_TEXT);
	$hpid = optional_param('hpid',$user->hpid ,PARAM_TEXT);
	if($API->updateUser($userid, $firstname, $lastname, $user_uri, $hpid)){
		array_push($MSG,"Account details updated");
		
		//update the user properties
		$propnames = optional_param('propname',"",PARAM_TEXT);
		$propvalues = optional_param('propvalue',"",PARAM_TEXT);
		for($i =0 ; $i <count($propnames); $i++){
			if($propnames[$i] != ""){
				$API->setUserProperty($userid, $propnames[$i], $propvalues[$i]);
			}
		}
		
		// reload the user
		$user = $API->getUserByID($userid);
		$API->getUserProperties($user);
	}
}

include_once('../includes/menu-admin.php');

	if(!empty($MSG)){
	echo "<div class='error'><ul>";
	foreach ($MSG as $err){
		echo "<li>".$err."</li>";
    }
    echo "</ul></div>";
}
?>
<h2>Edit user</h2>
<form method="post" action="">

<div class="formblock">
<div class="formlabel">Username:</div>
<div class="formfield"><input type="text" name="username" value="<?php echo $user->username;?>" disabled="true"></input></div>
<div class="forminfo">You cannot change the username</div>
</div>

<div class="formblock">
<div class="formlabel">First name:</div>
<div class="formfield"><input type="text" name="firstname" value="<?php echo $user->firstname;?>"></input></div>
</div>

<div class="formblock">
<div class="formlabel">Last name:</div>
<div class="formfield"><input type="text" name="lastname" value="<?php echo $user->lastname;?>"></input></div>
</div>

<div class="formblock">
<div class="formlabel">User URI:</div>
<div class="formfield"><input type="text" name="user_uri" value="<?php echo $user->user_uri;?>"></input></div>
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
<div class="formlabel">Health Point:</div>
<div class="formfield">
	<select name="hpid">
	<option value="0" selected="selected"/>
	<?php 
	foreach($healthpoints as $hp){
		if($user->hpid == $hp->hpid){
			printf("<option value='%d' selected='selected'>%s</option>",$hp->hpid,$hp->hpname);
		} else {
			printf("<option value='%d'>%s</option>",$hp->hpid,$hp->hpname);
		}
	}
	//echo "<pre>";
	///print_r($healthpoints); 
	//echo "</pre>";
		
	?>
	</select>
</div>
</div>

<div class="formblock">
	<div class="formlabel">User properties:</div>
	<div class="formfield">
		<?php  
		foreach($user->props as $k=>$v){
			printf("<div class='userformprop'>");
			printf("<div class='userformpropname'><input type='text' name='propname[]' value='%s'/></div>",$k);
			printf("<div class='userformpropvalue'><input type='text' name='propvalue[]' value='%s'/></div>",$v);
			printf("</div>");
		}
		printf("<div class='userformprop'>");
		printf("<div class='userformpropname'><input type='text' name='propname[]' value=''/></div>");
		printf("<div class='userformpropvalue'><input type='text' name='propvalue[]' value=''/></div>");
		printf("</div>");
		?>
	</div>
</div>

<div class="formblock">
	<div class="formlabel">&nbsp;</div>
	<div class="formfield"><input type="submit" name="submit" value="Save changes"></input></div>
</div>
</form>

<?php 
include_once "../includes/footer.php";
?>
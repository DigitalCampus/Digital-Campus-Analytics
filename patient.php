<?php
require_once "config.php";

$hpcode = optional_param("hpcode","",PARAM_TEXT);
$patientid = optional_param("patientid","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);
$protocol = optional_param("protocol","registration",PARAM_TEXT);

$healthposts = $API->getHealthPoints();

$TITLE = getString("patientmanager.title");

if($hpcode != "" && $patientid != ""){
	$patient = $API->getPatient(array('hpcode'=>$hpcode,'patid'=>$patientid));	
	if (isset($patient) && $patient->regcomplete){
		$TITLE = sprintf("%s %s - %s",$patient->patientlocation, $patient->Q_USERID, $patient->Q_USERNAME. " ". $patient->Q_USERFATHERSNAME);
	} else if (!$patient->regcomplete) {
		$TITLE = getstring("warning.patientreg");
	}
}

$PAGE = "patient";
require_once "includes/header.php";

printf("<h2 class='printhide'>%s</h2>", getString("patientmanager.title"));
?>
<form action="" method="get" class="printhide">
	<input type="hidden" name="protocol" value="registration"/>
	<?php echo getString("patientmanager.form.healthpost");?>
	<select name="hpcode">
		<?php 
			foreach($healthposts as $hp){
				if ($hpcode == $hp->hpcode){
					printf("<option value='%s' selected='selected'>%s</option>",$hp->hpcode, $hp->hpname);
				} else {
					printf("<option value='%s'>%s</option>",$hp->hpcode, $hp->hpname);
				}
			}
		?>
	</select>
	<?php echo getString("patientmanager.form.patientid");?>
	<input type="text" name="patientid" size="6" value="<?php echo $patientid; ?>"></input>
	<input type="submit" name="submit" value="<?php echo getString("patientmanager.form.searchbtn");?>"></input>
</form>

<?php 
if (!isset($patient) && $submit == ""){
	include_once "includes/footer.php";
	die;
} else if (!isset($patient)){
	echo getString('warning.norecords');
	include_once "includes/footer.php";
	die;
} 

echo "<h3/>".$TITLE."</h3>";
echo "<span class='printhide'>";
if(!$patient->regcomplete){
	echo getstring('protocol.registration');
} else if($protocol == "registration" && $patient){
	echo "<span class='selected'>".getstring('protocol.registration')."</span>";
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=registration'>%s</a>",$patientid,$hpcode, getstring('protocol.registration'));
}
printf(" | ");
if(!isset($patient->ancfirst)){
	echo getstring('protocol.ancfirst');
} else if ($protocol == 'ancfirstvisit'){
	echo "<span class='selected'>".getstring('protocol.ancfirst')."</span>";
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=ancfirstvisit'>%s</a>",$patientid,$hpcode,getstring('protocol.ancfirst'));
}
printf(" | ");
if(!isset($patient->ancfollow[0]) && !isset($patient->ancfollow[1]) && !isset($patient->ancfollow[2])){
	echo getstring('protocol.ancfollow');
} else if ($protocol == "ancfollowup"){
	echo "<span class='selected'>".getstring('protocol.ancfollow')."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=ancfollowup'>%s</a>",$patientid,$hpcode,getstring('protocol.ancfollow'));
}
printf(" | ");
if(count($patient->anctransfer)==0){
	echo getstring('protocol.anctransfer');
} else if ($protocol == "anctransfer"){
	echo "<span class='selected'>".getstring('protocol.anctransfer')."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=anctransfer'>%s</a>",$patientid,$hpcode,getstring('protocol.anctransfer'));
}
printf(" | ");
if(count($patient->anclabtest)==0){
	echo getstring('protocol.anclabtest');
} else if ($protocol == "anclabtest"){
	echo "<span class='selected'>".getstring('protocol.anclabtest')."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=anclabtest'>%s</a>",$patientid,$hpcode,getstring('protocol.anclabtest'));
}
/*printf(" | ");
if(!isset($patient->delivery)){
	echo getstring('protocol.delivery');
} else if ($protocol == "delivery"){
	echo "<span class='selected'>".getstring('protocol.delivery')."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=delivery'>%s</a>",$patientid,$hpcode,getstring('protocol.delivery'));
}*/
echo "</span>";

include_once('includes/patient/risk.php');

if ($patient->regcomplete && $protocol=="registration"){
	include_once('includes/patient/registration.php');
}

/*
 * ANC First Visit Procotol
 */
if ($patient->ancfirst && $protocol=="ancfirstvisit"){
	include_once('includes/patient/anc1.php');
} 

/*
 * ANC follow ups
 */
if ($patient->ancfollow && $protocol=="ancfollowup"){
	$ancfollow = $patient->ancfollow;
	include('includes/patient/ancfollow.php');
} 

/*
 * ANC Transfers
 */
if ($patient->anctransfer && $protocol=="anctransfer"){
	$anctransfer = $patient->anctransfer;
	include('includes/patient/anctransfer.php');
} 

/*
 * ANC Lab Tests
 */
if ($patient->anclabtest && $protocol=="anclabtest"){
	$anclabtest = $patient->anclabtest;
	include('includes/patient/anclabtest.php');
} 

/*
 * Labour/Delivery

if ($patient->delivery){
	include_once('includes/patient/delivery.php');
}  */




include_once "includes/footer.php";
?>
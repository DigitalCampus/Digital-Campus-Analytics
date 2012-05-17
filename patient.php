<?php
require_once "config.php";

$hpcode = optional_param("hpcode","",PARAM_TEXT);
$patientid = optional_param("patientid","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);
$protocol = optional_param("protocol",PROTOCOL_REGISTRATION,PARAM_TEXT);

$healthposts = $API->getHealthPoints();

$TITLE = getString("patientmanager.title");

if($hpcode != "" && $patientid != ""){
	$patient = $API->getPatient(array('hpcode'=>$hpcode,'patid'=>$patientid));	
	if (isset($patient) && $patient != false && $patient->regcomplete){
		$TITLE = sprintf("%s %s - %s",displayHealthPointName($patient->patienthpcode), $patient->Q_USERID, $patient->Q_USERNAME. " ". $patient->Q_USERFATHERSNAME);
	} else if ($patient != false && !$patient->regcomplete) {
		$TITLE = getstring("warning.patient.notregistered");
	}
}

$PAGE = "patient";
require_once "includes/header.php";

printf("<h2 class='printhide'>%s</h2>", getString("patientmanager.title"));
?>
<form action="" method="get" class="printhide">
	<input type="hidden" name="protocol" value="<?php echo PROTOCOL_REGISTRATION; ?>"/>
	<?php echo getString("patientmanager.form.healthpost");?>
	<select name="hpcode">
		<?php 
			foreach($healthposts as $hp){
				if ($hpcode == $hp->hpcode){
					printf("<option value='%s' selected='selected'>%s</option>",$hp->hpcode, displayHealthPointName($hp->hpcode));
				} else {
					printf("<option value='%s'>%s</option>",$hp->hpcode, displayHealthPointName($hp->hpcode));
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
} else if (!isset($patient) || $patient == false){
	printf("<span class='error'>%s</span>",getString('warning.patient.notfound'));
	include_once "includes/footer.php";
	die;
} 

echo "<h3/>".$TITLE."</h3>";
echo "<span class='printhide'>";
if(!$patient->regcomplete){
	echo getstring(PROTOCOL_REGISTRATION);
} else if($protocol == PROTOCOL_REGISTRATION && $patient){
	echo "<span class='selected'>".getstring(PROTOCOL_REGISTRATION)."</span>";
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=%s'>%s</a>",$patientid,$hpcode, PROTOCOL_REGISTRATION, getstring(PROTOCOL_REGISTRATION));
}
printf(" | ");
if(count($patient->anc)==0){
	echo getstring(PROTOCOL_ANC);
} else if ($protocol == PROTOCOL_ANC){
	echo "<span class='selected'>".getstring(PROTOCOL_ANC)."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=%s'>%s</a>",$patientid,$hpcode,PROTOCOL_ANC,getstring(PROTOCOL_ANC));
}
printf(" | ");
if(count($patient->anclabtest)==0){
	echo getstring(PROTOCOL_ANCLABTEST);
} else if ($protocol == PROTOCOL_ANCLABTEST){
	echo "<span class='selected'>".getstring(PROTOCOL_ANCLABTEST)."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=%s'>%s</a>",$patientid,$hpcode,PROTOCOL_ANCLABTEST, getstring(PROTOCOL_ANCLABTEST));
}
printf(" | ");
if(!isset($patient->delivery)){
	echo getstring(PROTOCOL_DELIVERY);
} else if ($protocol == PROTOCOL_DELIVERY){
	echo "<span class='selected'>".getstring(PROTOCOL_DELIVERY)."</span>"; 
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=%s'>%s</a>",$patientid,$hpcode,PROTOCOL_DELIVERY, getstring(PROTOCOL_DELIVERY));
}
printf(" | ");
if(count($patient->pnc)==0){
	echo getstring(PROTOCOL_PNC);
} else if ($protocol == PROTOCOL_PNC){
	echo "<span class='selected'>".getstring(PROTOCOL_PNC)."</span>";
} else {
	printf("<a href='?patientid=%s&hpcode=%s&protocol=%s'>%s</a>",$patientid,$hpcode,PROTOCOL_PNC, getstring(PROTOCOL_PNC));
}
echo "</span>";

include_once('includes/patient/risk.php');

if ($patient->regcomplete && $protocol == PROTOCOL_REGISTRATION){
	include_once('includes/patient/registration.php');
}

/*
 * ANC 
 */
if ($patient->anc && $protocol==PROTOCOL_ANC){
	$anc = $patient->anc;
	include('includes/patient/anc.php');
}  

/*
 * ANC Lab Tests
 */
if ($patient->anclabtest && $protocol==PROTOCOL_ANCLABTEST){
	$anclabtest = $patient->anclabtest;
	include('includes/patient/anclabtest.php');
} 

/*
 * Labour/Delivery
 */
if ($patient->delivery && $protocol==PROTOCOL_DELIVERY){
	include('includes/patient/delivery.php');
} 

/*
 * TODO add PNC
 */
if ($patient->pnc && $protocol==PROTOCOL_PNC){
	$pnc = $patient->pnc;
	include('includes/patient/pnc.php');
}
include_once "includes/footer.php";
?>
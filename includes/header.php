<?php 

//first detect if using mobile device and redirect accordingly
$uagent_obj = new uagent_info();
if($uagent_obj->DetectIphone() || $uagent_obj->DetectAndroidPhone()){
	header('Location: '.$CONFIG->homeAddress.'mobile/');
	die;
}

if ($PAGE != "login"){
	checkLogin();
}

$lang = optional_param("lang","",PARAM_TEXT);
if ($lang != ""){
	setLang($lang,true);
}
header("Content-Type: text/html; charset=UTF-8"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title><?php 
    	if(isset($TITLE)){
    		echo $TITLE;
    	} else {
    		echo getstring("app.name");
    	}
    	?>
    </title>
    <link rel="StyleSheet" href="<?php echo $CONFIG->homeAddress; ?>/includes/style.css" type="text/css" media="screen">
    <link rel="StyleSheet" href="<?php echo $CONFIG->homeAddress; ?>/includes/printstyle.css" type="text/css" media="print">
    <link rel="shortcut icon" href="<?php echo $CONFIG->homeAddress; ?>/images/favicon.ico"/>
    <?php 
    	global $HEADER;
    	echo $HEADER;
    ?>	
    <script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $CONFIG->googleanalytics; ?>']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
</head>
<body <?php 
	global $BODY_ATT;
	echo $BODY_ATT?>>
<div id="page">
	<div id="header">
		<div id="title">
			<img style="vertical-align:middle" src="<?php echo $CONFIG->homeAddress; ?>images/dc_logo.png"/>
			<h1><?php echo getstring("app.name");?></h1>
		</div>
		<div id="header-right">
			<div id="logininfo">
				<?php 
					if ($PAGE != "login"){
						echo getstring("header.loggedinas");
						echo "<a href='".$CONFIG->homeAddress."user.php'>".$USER->getUsername()."</a>";	
						echo " <a href='".$CONFIG->homeAddress."logout.php'>".getstring("header.logout")."</a>";
					} else {
						echo getstring("header.notloggedin");
					}	
					if($USER->getProp('permissions.admin') == "true"){
						echo " <a href='".$CONFIG->homeAddress."admin/'>admin</a>";
					}	
				?>
			</div>
			<div id="langchange">
				<form action="" method="post" name="langform" id="langform">
				<select name="lang" onchange="document.langform.submit();">
					<?php 
						foreach ($CONFIG->langs as $key => $value){
							if (isset($_SESSION["session_lang"]) &&  $_SESSION["session_lang"] == $key){
								echo "<option value='".$key."' selected='selected'>".$value."</option>";
							} else {
								echo "<option value='".$key."'>".$value."</option>";
							}
						}
					?>
				</select>
				</form>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div> <!-- end header -->
	
	<?php 
	if ($PAGE != "login"){
		include_once $CONFIG->homePath.'includes/menu.php';
	}	

	$dc = new DataCheck();
	// display warning message about invalid data
	if($dc->summary() && $PAGE != 'login'){
	?>
	<div id="datacheckwarning" class="datawarning printhide">
		<img src="<?php echo $CONFIG->homeAddress; ?>images/warning.png" align="left"></img><?php echo getString('warning.datacheck', array($CONFIG->homeAddress.'datacheck.php'));?>
	</div>
	<?php 
	}
?>

<div id="content">



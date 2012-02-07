<?php 

if ($PAGE != "login"){
	checkLogin("mobile/");
}

$lang = optional_param("lang","",PARAM_TEXT);
if ($lang != ""){
	setLang($lang,true);
}

header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html manifest="DigitalCampusAnalytics.manifest">
<html>
<head>
    <title><?php echo getstring("app.name");?></title>
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <link rel="StyleSheet" href="<?php echo $CONFIG->homeAddress; ?>mobile/includes/style.css" type="text/css" media="screen">
</head>
<body>
<div id="page">
	<div id="header">
		<div id="header-logo">
			<img style="vertical-align:middle" src="<?php echo $CONFIG->homeAddress; ?>mobile/images/dclogo.png"/>
		</div>
		<div id="header-title">
			<h1><?php echo getstring("mobile.app.name");?></h1>
		</div>
		<div id="header-right">
			<div id="langchange">
				<form action="" method="post" name="langform" id="langform">
				<select name="lang" onchange="document.langform.submit();">
					<?php 
						$langs = json_decode($CONFIG->props['langs.available'],true);
						foreach ($langs as $k => $v){
							if (isset($_SESSION["session_lang"]) &&  $_SESSION["session_lang"] == $k){
								echo "<option value='".$k."' selected='selected'>".$v."</option>";
							} else {
								echo "<option value='".$k."'>".$v."</option>";
							}
						}
					?>
				</select>
				</form>
			</div>
			<div id="logininfo">
				<?php 
				if ($PAGE != "login"){
					echo $USER->getUsername();
					echo " <a href='".$CONFIG->homeAddress."mobile/logout.php'>".getstring("header.logout")."</a>";
				}
				?>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div> <!-- end #header -->
	<?php 
	if($PAGE != 'login'){
		include_once $CONFIG->homePath.'mobile/includes/menu.php';
	}
	?>
	<div id="content">
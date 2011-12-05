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
<!--  html manifest="DigitalCampusAnalytics.manifest"-->
<html>
<head>
    <title><?php echo getstring("app.name");?></title>
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <link rel="StyleSheet" href="<?php echo $CONFIG->homeAddress; ?>mobile/includes/style.css" type="text/css" media="screen">
</head>
<body>
<div id="page">
	<div id="header">
		<div id="title">
			<img style="vertical-align:middle" src="<?php echo $CONFIG->homeAddress; ?>images/dc_logo.png"/>
			<h1><?php echo getstring("mobile.app.name");?></h1>
		</div>
		<div id="header-right">
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
	</div> <!-- end #header -->
	<?php 
	if($PAGE != 'login'){
		include_once $CONFIG->homePath.'mobile/includes/menu.php';
	}
	?>
	<div id="content">
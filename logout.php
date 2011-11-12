<?php
    include_once("config.php");
    clearSession();
    writeToLog('info','logout','user logged out');
    header('Location: index.php');
?>

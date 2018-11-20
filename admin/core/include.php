<?php
session_start();
include("config.php");
include("functions.php");
//$userid = authenticate_certificate();
$userid = "fu";
if(empty($userid))
	$userid = authenticate();
?>

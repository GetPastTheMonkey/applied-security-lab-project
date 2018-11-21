<?php
session_start();
include("config.php");
include("functions.php");
$userid = authenticate_certificate();
$authentication_by_certificate = !empty($userid);
if(empty($userid))
	$userid = authenticate();
?>

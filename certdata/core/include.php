<?php

include("config.php");
include("dbconnx.php");
include("functions.php");

// Require client to be trusted
if(!isset($_SERVER["SSL_CLIENT_VERIFY"]))
	error_500("Could not verify client. Possible misconfiguration of the server...");

if($_SERVER["SSL_CLIENT_VERIFY"] != "SUCCESS")
	error_403();

if(!in_array($_SERVER["SSL_CLIENT_M_SERIAL"], $CONFIG["TRUSTED"]) AND !isset($skip))
	error_403();

?>

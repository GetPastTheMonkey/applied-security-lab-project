<?php

include("core/include.php");

// Request method must be POST
if($_SERVER["REQUEST_METHOD"] != "POST") error_405("POST", "This mehtod only supports POST requests");

// Require a certificate ID as POST variable that is numeric
if(!isset($_POST["serial"])) error_400("No 'serial' parameter given");
if(!is_numeric($_POST["serial"])) error_400("Parameter 'serial' must be a numeric value");
$serial = $mysqli->real_escape_string($_POST["serial"]);

// Try to set the revocation date
$mysqli->query("UPDATE certificates SET revoked=NOW() WHERE serial_nr='{$serial}' AND revoked IS NULL LIMIT 1");

// Check if it was successful
if($mysqli->affected_rows != 1)
	error_404("Could not find a non-revoked certificate with this serial number");
else
	error_200("Certificate with serial number {$serial} successfully revoked");

?>

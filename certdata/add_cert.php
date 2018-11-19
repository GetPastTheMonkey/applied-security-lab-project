<?php

include("core/include.php");

// Require POST method
if($_SERVER["REQUEST_METHOD"] != "POST") error_405("POST", "This mehtod only supports POST requests");

// Require serial, user, pkcs12 and purpose as POST variables
if(!isset($_POST["serial"]) OR !isset($_POST["user"]) OR !isset($_POST["pkcs12"]) OR !isset($_POST["salt"]) OR !isset($_POST["purpose"]))
	error_400("Some parameters are not set. Make sure that the following parameters are set: serial, user, pkcs12, salt, purpose");

// Require serial to be numeric
if(!is_numeric($_POST["serial"])) error_400("Parameter 'serial' must be a numeric value");

// Escape all parameters
$serial = $mysqli->real_escape_string($_POST["serial"]);
$user = $mysqli->real_escape_string($_POST["user"]);
$pkcs12 = $mysqli->real_escape_string($_POST["pkcs12"]);
$salt = $mysqli->real_escape_string($_POST["salt"]);
$purpose = $mysqli->real_escape_string($_POST["purpose"]);

// Check if serial is the next serial number
$next = $mysqli->query("SELECT MAX(serial_nr) AS max FROM certificates")->fetch_assoc()["max"];
if(is_null($next)) $next = 0;
else $next++;

if($serial != $next) error_409("The given serial number is not the next serial number. Possibly another user already used the given serial number.");

$mysqli->query("INSERT INTO certificates (serial_nr, user, pkcs12, salt, purpose, created) VALUES ('{$serial}', '{$user}', '{$pkcs12}', '{$salt}', '{$purpose}', NOW())");

if($mysqli->affected_rows != 1)
	error_500("Could not register certificate. Please try again later...");
else
	error_200("Certificate successfully registered");

?>

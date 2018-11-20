<?php

include("core/include.php");

// Require POST method
if($_SERVER["REQUEST_METHOD"] != "POST")
	error_405("POST", "This method only supports POST requests");

// Require uid, session_id and ip_address
if(!isset($_POST["uid"]) OR !isset($_POST["session_id"]) OR !isset($_POST["ip_address"]))
	error_400("Some parameters are not set. Make sure that the following parameters are set: uid, session_id, ip_address");

// Escape all parameters
$uid = $mysqli->real_escape_string($_POST["uid"]);
$session_id = $mysqli->real_escape_string($_POST["session_id"]);
$ip_address = $mysqli->real_escape_string($_POST["ip_address"]);

// Generate token
$token = bin2hex(random_bytes(64));

// Insert into database
$mysqli->query("INSERT INTO logins (uid, session_id, token, ip_address, timestamp) VALUES ('{$uid}', '{$session_id}', '{$token}', '{$ip_address}', NOW())");

print_json($token);

?>

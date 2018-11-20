<?php

include("core/include.php");

// Require POST method
if($_SERVER["REQUEST_METHOD"] != "POST")
	error_405("POST", "This method only supports POST requests");

if(!isset($_POST["session_id"]) OR !isset($_POST["token"]))
	error_400("Parameters 'session_id' and 'token' must be set");

if(empty($_POST["session_id"]) OR empty($_POST["token"]))
	error_400("Parameters 'session_id' and 'token' may not be empty");

$session_id = $mysqli->real_escape_string($_POST["session_id"]);
$token = $mysqli->real_escape_string($_POST["token"]);

$mysqli->query("UPDATE logins SET expired=NOW() WHERE session_id='{$session_id}' AND token='{$token}' AND expired IS NULL");

if($mysqli->affected_rows != 1)
	error_404("No non-expired session with this data exists");

error_200("Login successfully expired");

?>

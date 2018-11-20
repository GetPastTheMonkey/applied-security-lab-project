<?php

include("core/include.php");

if(!isset($_GET["session_id"]) OR !isset($_GET["token"]))
	error_400("Parameters 'session_id' and 'token' must be set");

if(empty($_GET["session_id"]) OR empty($_GET["token"]))
	error_400("Parameters 'session_id' and 'token' may not be empty");

$session_id = $mysqli->real_escape_string($_GET["session_id"]);
$token = $mysqli->real_escape_string($_GET["token"]);

$res = $mysqli->query("SELECT uid, expired FROM logins WHERE session_id='{$session_id}' AND token='{$token}' LIMIT 1");

if($res->num_rows != 1)
	error_404("No user with this authentication data");

$data = $res->fetch_assoc();

if(!is_null($data["expired"]))
	error_404("Login expired");

$res = $mysqli->query("SELECT admin_id FROM admins WHERE uid='{$data["uid"]}' LIMIT 1");

if($res->num_rows != 1)
	error_404("Not in admins table");

print_json($res->fetch_assoc());

?>

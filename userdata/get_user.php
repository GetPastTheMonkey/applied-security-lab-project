<?php

include("core/include.php");

if(!isset($_GET["user"]))
	error_400("Parameter 'user' not set");

if(empty($_GET["user"]))
	error_400("Parameter 'user' may not be empty");

$user = $mysqli->real_escape_string($_GET["user"]);

$res = $mysqli->query("SELECT * FROM users WHERE uid LIKE '{$user}' LIMIT 1");

if($res->num_rows != 1) error_404("No user with this username");

print_json($res->fetch_assoc());

?>

<?php

include("core/include.php");

if(!isset($_GET["admin"]))
	error_400("Parameter 'admin' not set");

if(empty($_GET["admin"]))
	error_400("Parameter 'admin' may not be empty");

$admin = $mysqli->real_escape_string($_GET["admin"]);

$res = $mysqli->query("SELECT * FROM admins WHERE admin_id LIKE '{$admin}' LIMIT 1");

if($res->num_rows != 1) error_404("No admin with this username");

print_json($res->fetch_assoc());

?>

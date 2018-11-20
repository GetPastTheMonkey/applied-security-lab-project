<?php

include("core/include.php");

// Require POST method
if($_SERVER["REQUEST_METHOD"] != "POST")
	error_405("POST", "This method only supports POST requests");

// Require uid, lastname, firstnme, email, pwd, salt
if(!isset($_POST["admin_id"]) OR !isset($_POST["lastname"]) OR !isset($_POST["firstname"]) OR !isset($_POST["email"]) OR !isset($_POST["pwd"]) OR !isset($_POST["salt"]))
	error_400("Some parameters are not set. Make sure that the following parameters are set: admin_id, lastname, firstname, email, pwd, salt");

// Escape all parameters
$admin_id = $mysqli->real_escape_string($_POST["admin_id"]);
$lastname = $mysqli->real_escape_string($_POST["lastname"]);
$firstname = $mysqli->real_escape_string($_POST["firstname"]);
$email = $mysqli->real_escape_string($_POST["email"]);
$pwd = $mysqli->real_escape_string($_POST["pwd"]);
$salt = $mysqli->real_escape_string($_POST["salt"]);

$mysqli->query("UPDATE admins SET lastname='{$lastname}', firstname='{$firstname}', email='{$email}', pwd='{$pwd}', salt='{$salt}' WHERE admin_id='{$admin_id}' LIMIT 1");

if($mysqli->affected_rows != 1)
	error_404("No such admin (could not update)");

error_200("Admin successfully updated");

?>

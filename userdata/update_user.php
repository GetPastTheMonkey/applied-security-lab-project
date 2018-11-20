<?php

include("core/include.php");

// Require POST method
if($_SERVER["REQUEST_METHOD"] != "POST")
	error_405("POST", "This method only supports POST requests");

// Require uid, lastname, firstnme, email, pwd
if(!isset($_POST["uid"]) OR !isset($_POST["lastname"]) OR !isset($_POST["firstname"]) OR !isset($_POST["email"]) OR !isset($_POST["pwd"]))
	error_400("Some parameters are not set. Make sure that the following parameters are set: uid, lastname, firstname, email, pwd");

// Escape all parameters
$uid = $mysqli->real_escape_string($_POST["uid"]);
$lastname = $mysqli->real_escape_string($_POST["lastname"]);
$firstname = $mysqli->real_escape_string($_POST["firstname"]);
$email = $mysqli->real_escape_string($_POST["email"]);
$pwd = $mysqli->real_escape_string($_POST["pwd"]);

$mysqli->query("UPDATE users SET lastname='{$lastname}', firstname='{$firstname}', email='{$email}', pwd='{$pwd}' WHERE uid='{$uid}' LIMIT 1");

if($mysqli->affected_rows != 1)
	error_404("No such user (could not update)");

error_200("User successfully updated");

?>

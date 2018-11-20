<?php

include("core/include.php");

if($_SERVER["REQUEST_METHOD"] != "POST")
	error_405("POST", "This method only supports POST requests");

if(!isset($_POST["serial"]))
	error_400("No 'serial' parameter given");

$response = certdata("revoke_cert.php", array("serial" => $_POST["serial"]));

print_json($response);

?>

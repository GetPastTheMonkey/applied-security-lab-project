<?php

include("core/include.php");

if(!isset($_GET["serial"]))
	error_400("No 'serial' parameter given");

$response = certdata("get_cert.php?serial={$_GET["serial"]}");

print_json($response);

?>

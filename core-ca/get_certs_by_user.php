<?php

include("core/include.php");

if(!isset($_GET["user"]))
	error_400("No 'user' parameter given");

$response = certdata("get_certs_by_user.php?user={$_GET["user"]}");

print_json($response);

?>

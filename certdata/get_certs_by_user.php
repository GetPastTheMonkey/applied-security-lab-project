<?php

include("core/include.php");

// Require a user ID as GET variable
if(!isset($_GET["user"])) error_400("No 'user' parameter given");
$user = $mysqli->real_escape_string($_GET["user"]);

$res = $mysqli->query("SELECT * FROM certificates WHERE user LIKE '{$user}' ORDER BY serial_nr");

$certs = array();

while($c = $res->fetch_assoc()) {
	$c["pkcs12"] = urlencode($c["pkcs12"]);
	$certs[] = $c;
}

print_json($certs);

?>

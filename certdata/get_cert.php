<?php

$skip = "";

include("core/include.php");

// Require a certificate ID as GET variable that is numeric
if(!isset($_GET["serial"])) error_400("No 'serial' parameter given");
if(!is_numeric($_GET["serial"])) error_400("Parameter 'serial' must be a numeric value");
$serial = $mysqli->real_escape_string($_GET["serial"]);

// Try to get the certificate data
$res = $mysqli->query("SELECT * FROM certificates WHERE serial_nr='{$serial}' LIMIT 1");

if($res->num_rows != 1) error_404("No certificate exists with this serial number");

$cert = $res->fetch_assoc();

$cert["pkcs12"] = urlencode($cert["pkcs12"]);

print_json(array("cert_data" => $cert));

?>

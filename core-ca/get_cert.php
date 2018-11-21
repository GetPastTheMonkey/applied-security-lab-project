<?php

include("core/include.php");

if(!isset($_GET["serial"]))
	error_400("No 'serial' parameter given");

if($_GET["serial"] == 0) {
	// The root certificate was requested. Load it from file system
	// Load CA data
	$ca_key = array(file_get_contents("/etc/ssl/cakey.pem"), "uGGdpTGZ5DspRmR15xjY");
	$ca_cert = file_get_contents("/etc/ssl/cacert.pem");
	$shared_secret = bin2hex(random_bytes(64));

	openssl_pkcs12_export($ca_cert, $ca_pkcs12, $ca_key, $shared_secret);

	$response = array("cert_data" => array(
		"serial_nr" => 0,
		"user" => NULL,
		"pkcs12" => urlencode($ca_pkcs12),
		"purpose" => NULL,
		"created" => NULL,
		"revoked" => NULL,
		"salt" => $shared_secret
	));
} else {
	// A certificate other than the root cert was requested, need to get it from certdata
	$response = certdata("get_cert.php?serial={$_GET["serial"]}");
}

print_json($response);

?>

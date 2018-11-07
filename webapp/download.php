<?php

include("core/include.php");

require_login();

$title = "Certificate Download";

try {
	// Check if GET parameter serial is set
	if(!isset($_GET["serial"]) OR empty($_GET["serial"])) {
		throw new Exception("Parameter <code>serial</code> must be given, but it is not");
	}

	// Check if serial is a numerical value
	if(!is_numeric($_GET["serial"])) {
		throw new Exception("Parameter <code>serial</code> must be a numerical value, but it is not");
	}

	$serial = $mysqli->real_escape_string(round($_GET["serial"]));

	// Check if serial exists
	$res = $mysqli->query("SELECT user, pkcs12, revoked FROM certificates WHERE serial_nr='{$serial}' LIMIT 1");

	if($res->num_rows != 1) {
		throw new Exception("Certificate with serial number {$serial} does not exist");
	}

	$cert = $res->fetch_assoc();

	// Check if user is the owner
	if($cert["user"] != $userid) {
		throw new Exception("Certificate with serial number {$serial} is not yours");
	}

	// Check if certificate is not revoked
	if(!is_null($cert["revoked"])) {
		throw new Exception("Certificate with serial number {$serial} has been revoked. Downloading revoked certificates is not possible");
	}

	// At this point, the user requested a valid vertificate that he owns. Thus, downloading it is allowed.

	// Set Content-Type, Content-Transfer-Encoding and Content-Disposition headers
	header("Content-Type: application/x-pkcs12");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Disposition: atachment; filename=\"pkcs12_{$userid}_{$serial}.p12\"");

	// Echo content
	echo $cert["pkcs12"];
} catch(Exception $e) {
	$title = "Certificate Download";
	$content = '<div class="alert alert-danger">'.$e->getMessage().'</div>
	<p><a href="list_certs.php" class="btn btn-primary">Back to certificate list</a></p>';
	echo content_to_html($content, $title);
}

?>

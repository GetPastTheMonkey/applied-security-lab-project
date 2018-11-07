<?php

include("core/include.php");

require_login();

$title = "Certificate Revocation";

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

	// Check if serial exists and if user is the owner
	$res = $mysqli->query("SELECT user FROM certificates WHERE serial_nr='{$serial}' LIMIT 1");
	
	if($res->num_rows != 1) {
		throw new Exception("Certificate with serial number {$serial} does not exist");
	}

	if($res->fetch_assoc()["user"] != $userid) {
		throw new Exception("Certificate with serial number {$serial} is not yours");
	}

	$mysqli->query("UPDATE certificates SET revoked=NOW() WHERE serial_nr='{$serial}' LIMIT 1");

	$content = '<div class="alert alert-success"><span class="fa fa-fw fa-check"></span> Certificate with serial '.$serial.' successfully revoked</div>';
} catch(Exception $e) {
	$content = '<div class="alert alert-danger"><span class="fa fa-fw fa-times"></span> '.$e->getMessage().'</div>';
}

$content .= '<p><a href="list_certs.php" class="btn btn-primary">Back to certificate list</a></p>';

echo content_to_html($content, $title);

?>

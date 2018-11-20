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

	$serial = round($_GET["serial"]);

	// Check if serial exists and if user is the owner
	$cert = core_ca("get_cert.php?serial={$serial}");

	if(!isset($cert["cert_data"]))
		throw new Exception("Could not load certificate data for this serial number. Either the serial number does not exist or there was an internal error in ca.api.imovie.local or certdata.api.imovie.local");

	if($cert["cert_data"]["user"] != $userid)
		throw new Exception("Certificate with this serial number is not yours");

	$result = core_ca("revoke_cert.php", array(
		"serial" => $serial
	));

	if($result["status"]["status_code"] == 200) {
		$content = '<div class="alert alert-success"><span class="fa fa-fw fa-check"></span> Certificate with serial '.$serial.' successfully revoked</div>';
	} else {
		error_500("Could not revoke certificate with this serial number. Response from certdata.api.imovie.local via ca.api.imovie.local: <pre>".print_r($result, true)."</pre>");
	}
} catch(Exception $e) {
	$content = '<div class="alert alert-danger"><span class="fa fa-fw fa-times"></span> '.$e->getMessage().'</div>';
}

$content .= '<p><a href="list_certs.php" class="btn btn-primary">Back to certificate list</a></p>';

echo content_to_html($content, $title);

?>

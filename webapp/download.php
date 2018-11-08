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
	$res = $mysqli->query("SELECT user, pkcs12, salt, revoked FROM certificates WHERE serial_nr='{$serial}' LIMIT 1");

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

	// Check if POST or not
	if(($_SERVER["REQUEST_METHOD"] == "POST") AND isset($_POST["pw"]) AND !empty($_POST["pw"])) {
		// The user requested a download

		// Decrypt PKCS12 first

		///////////////////////////////////////////////////////////
		// DO NOT CHANGE THESE LINES, THIS WILL BREAK THE SYSTEM //
		$pepper = "848cfdc57e446d02d26c0beac803a69cc7dd96d240134778f9c4f27d685f1dc2d544decd90a4d9e63920c820587f3030daa4332d9bb121e62e2e6e27ec80a5a0";
		///////////////////////////////////////////////////////////

		if(!openssl_pkcs12_read($cert["pkcs12"], $pkcs12_arr, $cert["salt"].$pepper)) {
			error_500("Could not decrypt PKCS#12. Please tell a CA administrator immediately!");
		}

		// Set Content-Type, Content-Transfer-Encoding and Content-Disposition headers
		header("Content-Type: application/x-pkcs12");
		header("Content-Transfer-Encoding: Binary");
		header("Content-Disposition: atachment; filename=\"pkcs12_{$userid}_{$serial}.p12\"");

		// Encrypt PKCS#12 for user to download
		openssl_pkcs12_export($pkcs12_arr["cert"], $pkcs12, $pkcs12_arr["pkey"], $_POST["pw"]);
		// Echo content
		echo $pkcs12;
	} else {
		// Show form to the user to enter download password
		$title = "Certificate Download";
		$content = '<p>Please enter a password to encrypt the PKCS#12 file. For safety reasons, this should not be your account password!</p>
		<form action="" method="POST">
			<p><input type="password" name="pw" placeholder="Encryption password" required /></p>
			<p><input type="submit" value="Download PKCS#12 File" /></p>
		</form>';
		echo content_to_html($content, $title);
	}
} catch(Exception $e) {
	$title = "Certificate Download";
	$content = '<div class="alert alert-danger">'.$e->getMessage().'</div>
	<p><a href="list_certs.php" class="btn btn-primary">Back to certificate list</a></p>';
	echo content_to_html($content, $title);
}

?>

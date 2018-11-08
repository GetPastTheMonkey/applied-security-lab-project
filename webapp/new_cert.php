<?php

include("core/include.php");

require_login();

$title = "Create New Certificate";

if(($_SERVER["REQUEST_METHOD"] == "POST") AND isset($_POST["confirm"]) AND isset($_POST["purpose"]) AND !empty($_POST["purpose"])) {
	$purpose = $mysqli->real_escape_string($_POST["purpose"]);

	// Load user data from database
	$res = $mysqli->query("SELECT firstname, lastname, email FROM users WHERE uid LIKE '{$userid}' LIMIT 1");
	if($res->num_rows != 1) {
		error_500("Could not load user data. Please check the SQL query.");
	}
	$data = $res->fetch_assoc();

	///////////////////////////////////////////////////////////
	// DO NOT CHANGE THESE LINES, THIS WILL BREAK THE SYSTEM //
	$pepper = "848cfdc57e446d02d26c0beac803a69cc7dd96d240134778f9c4f27d685f1dc2d544decd90a4d9e63920c820587f3030daa4332d9bb121e62e2e6e27ec80a5a0";
	$salt = bin2hex(random_bytes(64));
	$encrypt_pw = $salt.$pepper;
	///////////////////////////////////////////////////////////

	// Create an array with the certificate data
	$dn = array(
		"countryName" => "CH",
		"stateOrProvinceName" => "Zurich",
		"localityName" => "Zurich",
		"organizationName" => "iMovie",
		"organizationalUnitName" => "iMovie CA",
		"commonName" => $data["firstname"]." ".$data["lastname"],
		"emailAddress" => $data["email"]
	);

	// SSL Config
	$sslconfig = array(
		"config" => "/etc/ssl/openssl.cnf",
		"private_key_bits" => 4096,
		"private_key_type" => OPENSSL_KEYTYPE_RSA,
		"digest_alg" => "sha512",
		"x509_extensions" => "usr_cert"
	);

	// Create a new private key
	$privkey = openssl_pkey_new($sslconfig);

	// Generate a CSR
	$csr = openssl_csr_new($dn, $privkey, $sslconfig);

	// Load serial number
	$serial = $mysqli->query("SELECT MAX(serial_nr) AS max FROM certificates")->fetch_assoc()["max"];
	if(is_null($serial)) $serial = 1;
	else $serial++;

	// Sign the signing request
	$ca_key = array(file_get_contents("/etc/ssl/CA/private/cakey.pem"), "uGGdpTGZ5DspRmR15xjY");
	$ca_cert = file_get_contents("/etc/ssl/CA/cacert.pem");

	$x509 = openssl_csr_sign($csr, $ca_cert, $ca_key, 365, $sslconfig, $serial);

	openssl_pkcs12_export($x509, $pkcs12, $privkey, $encrypt_pw);

	$pkcs12 = $mysqli->real_escape_string($pkcs12);

	$mysqli->query("INSERT INTO certificates (serial_nr, user, pkcs12, salt, purpose, created) VALUES ({$serial}, '{$userid}', '{$pkcs12}', '{$salt}', '{$purpose}', NOW())");

	$content = '<div class="alert alert-success"><span class="fa fa-fw fa-check"></span> Certificate created. Download it <a href="download.php?serial='.$serial.'">here</a></div>
<p><a href="list_certs.php" class="btn btn-primary">Back to certificate list</a></p>';
} else {
	$content = '<form method="POST">
		<input type="hidden" name="confirm" value="1" />
		<p><input type="text" name="purpose" placeholder="Purpose of the certificate" required /></p>
		<p><input type="submit" value="Create New Certificate" /></p>
	</form>';
}

echo content_to_html($content, $title);

?>

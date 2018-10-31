<?php

include("core/include.php");

require_login();

$title = "Create New Certificate";

if(($_SERVER["REQUEST_METHOD"] == "POST") AND isset($_POST["confirm"])) {
	// Load user data from database
	$res = $mysqli->query("SELECT firstname, lastname, email FROM users WHERE uid LIKE '{$userid}' LIMIT 1");
	if($res->num_rows != 1) {
		error_500("Could not load user data. Please check the SQL query.");
	}
	$data = $res->fetch_assoc();

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

	// Create a new private key
	$privkey = openssl_pkey_new(array(
		"private_key_bits" => 4096,
		"private_key_type" => OPENSSL_KEYTYPE_RSA
	));

	// Generate a CSR
	$csr = openssl_csr_new($dn, $privkey, array("digest_alg" => "sha512"));

	// Sign the signing request
	$ca_key = array(file_get_contents("/etc/ssl/CA/private/cakey.pem"), "uGGdpTGZ5DspRmR15xjY");
	$ca_cert = file_get_contents("/etc/ssl/CA/cacert.pem");
	$x509 = openssl_csr_sign($csr, $ca_cert, $ca_key, 365, array("digest_alg" => "sha512"));

	// DEBUG OUTPUT
	ob_start();
	var_dump($x509);
	$content .= ob_get_clean();
} else {
	$content = '<form method="POST">
		<input type="hidden" name="confirm" value="1" />
		<input type="submit" value="Create New Certificate" />
	</form>';
}

echo content_to_html($content, $title);

?>

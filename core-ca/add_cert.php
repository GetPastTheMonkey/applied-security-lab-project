<?php

include("core/include.php");

if($_SERVER["REQUEST_METHOD"] != "POST")
	error_405("POST", "This method only supports POST requests");

// Check if all parameters are present
if(!isset($_POST["user"]) OR !isset($_POST["name"]) OR !isset($_POST["mail"]) OR !isset($_POST["secret"]) OR !isset($_POST["purpose"]))
	error_400("Some parameters are not set. Make sure that the following parameters are set: user, name, mail, secret, purpose");

$user = $_POST["user"];
$secret = $_POST["secret"];
$purpose = $_POST["purpose"];
$name = urldecode($_POST["name"]);
$mail = $_POST["mail"];

// Create an array with the certificate data
$dn = array(
	"countryName" => "CH",
	"stateOrProvinceName" => "Zurich",
	"localityName" => "Zurich",
	"organizationName" => "iMovie",
	"organizationalUnitName" => "iMovie CA",
	"commonName" => $name,
	"emailAddress" => $mail
);

// SSL Config
$sslconfig = array(
	"config" => "/etc/ssl/openssl.cnf",
	"private_key_bits" => 4096,
	"private_key_type" => OPENSSL_KEYTYPE_RSA,
	"digest_alg" => "sha512",
	"x509_extensions" => "usr_cert"
);

// Load CA data
$ca_key = array(file_get_contents("/etc/ssl/cakey.pem"), "uGGdpTGZ5DspRmR15xjY");
$ca_cert = file_get_contents("/etc/ssl/cacert.pem");

while(true) {
	// Get next serial number
	$serial = certdata("get_next_serial.php");
	if(http_response_code() != 200) print_json($serial);

	// Generate pseudo-random salt
	$salt = bin2hex(random_bytes(64));
	$encrypt_pw = $salt.$pepper;

	// Create a new private key
	$privkey = openssl_pkey_new($sslconfig);

	// Generate a CSR
	$csr = openssl_csr_new($dn, $privkey, $sslconfig);

	// Sign the signing request
	$x509 = openssl_csr_sign($csr, $ca_cert, $ca_key, 365, $sslconfig, $serial);

	openssl_pkcs12_export($x509, $pkcs12, $privkey, $encrypt_pw);

	// Try to write to database
	$response = certdata("add_cert.php", array(
		"serial" => $serial,
		"user" => $user,
		"pkcs12" => urlencode($pkcs12),
		"salt" => $salt,
		"purpose" => $purpose
	));

	// Response successful? -> Break
	if(http_response_code() == 200) break;
	elseif($response["status"]["status_code"] != 409)
		error($response["status"]["status_code"], $response["status"]["message"], $response["status"]["additional_information"]);
}

print_json($serial);

?>

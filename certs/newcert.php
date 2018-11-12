<?php

$url = "mysubdomain.imovie.local"; // URL to create a certificate for
$serial = 6; // Serial of the new certificate
$length = 20; // Length of the encryption key

////////////////////////////////////////////
// DO NOT CHANGE ANYTHING AFTER THIS LINE //
////////////////////////////////////////////

// Create an array with the certificate data
$dn = array(
	"countryName" => "CH",
	"stateOrProvinceName" => "Zurich",
	"localityName" => "Zurich",
	"organizationName" => "iMovie",
	"organizationalUnitName" => "iMovie CA",
	"commonName" => $url
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

// Sign the signing request
$ca_key = array(file_get_contents("CA/cakey.pem"), trim(file_get_contents("CA/encryptkey.txt")));
$ca_cert = file_get_contents("CA/cacert.pem");
$x509 = openssl_csr_sign($csr, $ca_cert, $ca_key, 365, $sslconfig, $serial);

// Generate pseudo-random encrypt_key
$encrypt_key = bin2hex(random_bytes($length / 2));

// File export
mkdir($url);
openssl_x509_export_to_file($x509, "{$url}/{$url}_cert.pem");
openssl_pkey_export_to_file($privkey, "{$url}/{$url}_pkey.pem", $encrypt_key);
file_put_contents("{$url}/{$url}_encryptkey.txt", $encrypt_key);

?>

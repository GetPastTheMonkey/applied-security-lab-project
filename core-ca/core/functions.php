<?php

function print_json($content) {
	header("Content-type: application/json");
	$json = json_encode($content, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
	if($json === FALSE) error_500("Operation completed, but could not print json");
	else echo $json;
}

function error($code, $reason, $additional) {
	header("Content-type: application/json");
	http_response_code($code);
	echo json_encode(array(
		"status" => array(
			"message" => $reason,
			"status_code" => $code,
			"additional_information" => $additional
		)
	), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
	exit();
}

function error_200($msg=NULL) {
	error(200, "OK", $msg);
}

function error_400($msg=NULL) {
	error(400, "Bad Request", $msg);
}

function error_403($msg=NULL) {
	error(403, "Forbidden", $msg);
}

function error_404($msg=NULL) {
	error(404, "Not Found", $msg);
}

function error_405($allowed, $msg=NULL) {
	header("Allow: {$allowed}");
	error(405, "Method Not Allowed", $msg);
}

function error_409($msg=NULL) {
	error(409, "Conflict", $msg);
}

function error_418($msg=NULL) {
	error(418, "I'm a teapot", $msg);
}

function error_500($msg=NULL) {
	error(500, "Internal Error", $msg);
}

function certdata($url, $post=NULL) {
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => "https://certdata.api.imovie.local/{$url}",
		CURLOPT_RETURNTRANSFER => true,

		// Verify myself
		CURLOPT_SSLKEY => "/etc/ssl/ca.api.imovie.local_pkey.pem",
		CURLOPT_KEYPASSWD => "4cb0f20c100687eec6c7",
		CURLOPT_SSLCERT => "/etc/ssl/ca.api.imovie.local_cert.pem",

		// Verify certdata server
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_SSL_VERIFYHOST => 2,
		CURLOPT_CAINFO => "/etc/ssl/cacert.pem"
	);
	curl_setopt_array($ch, $options);

	if(is_array($post)) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	}

	$response = curl_exec($ch);
	$errno = curl_errno($ch);
	$code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
	curl_close($ch);

	if($errno != 0) error_500("Could not connect to certdata.api.imovie.local");

	$return = json_decode($response, true);
	if(is_null($return)) error_500("Could not decode response from certdata.api.imovie.local");

	http_response_code($code);

	return $return;
}

?>

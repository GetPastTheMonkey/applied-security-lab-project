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

?>

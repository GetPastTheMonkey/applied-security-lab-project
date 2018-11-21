<?php

function content_to_html($content, $title) {
	global $CONFIG;
	global $userid;
	if(!empty($userid)) {
		$sidebar = '
		<div id="small-profile">
			Logged in as <strong>'.$userid.'</strong>
		</div>
		<ul class="linkList">
			<li><a href="overview.php"><span class="fa fa-fw fa-dashboard"></span> Overview</a></li>
			<li><a href="list_certs.php"><span class="fa fa-fw fa-list"></span> List My Certificates</a></li>
			<li><a href="new_cert.php"><span class="fa fa-fw fa-plus"></span> Create New Certificate</a></li>';
			if(!$authentication_by_certificate) $sidebar .= '<li><a href="logout.php"><span class="fa fa-fw fa-sign-out"></span> Logout</a></li>';
		$sidebar .= '</ul>';
	} else {
		$sidebar = '<div id="small-profile">
			You are <strong>not</strong> logged in
		</div>
		<ul id="linkList">
			<li><a href="login.php"><span class="fa fa-fw fa-lg fa-arrow-right"></span> Login</a></li>
		</ul>';
	}
	$html = '<!DOCTYPE html>
<html>
	<head>
		<!-- Stylesheets -->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="css/fontawesome.min.css" />
		<link rel="stylesheet" type="text/css" href="css/style.php" />
		
		<!-- JavaScript -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/navControl.js"></script>
		
		<!-- Meta Tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<meta charset="UTF-8" />
		
		<!-- Other -->
		<title>'.$CONFIG["PAGE_TITLE"].'</title>
		<link rel="shortcut icon" type="image/x-icon" href="img/picto.png">
	</head>
	<body>
		<div id="page-container">
			<div id="sidebar-container">
				<a href="javascript:void(0)" class="closebtn" onclick="closeSidePanel()">&times;</a>
				<h2 onclick="location=\'./\';" style="cursor:pointer;">'.$CONFIG["PAGE_TITLE"].'</h2>
				<p class="text-center"><span class="fa fa-fw fa-film" style="font-size: 75px;"></span></p>
				'.$sidebar.'
			</div>
			<div id="main">
				<div id="top-nav">
					<div id="top-nav-control">
						Scrap League
						<a href="javascript:void(0)" onclick="toggleTopNav()"><span class="fa fa-lg fa-bars"></span></a>
					</div>
					<ul id="top-nav-ul">
						<li><a href="./"><span class="fa fa-fw fa-home"></span> Home</a></li>
						<li id="top-nav-panel-opener"><a href="javascript:void(0)" onclick="openSidePanel()"><span class="fa fa-fw fa-arrow-left"></span> Open Personal Panel</a></li>
					</ul>
				</div>
				<h1 id="main-title">'.$title.'</h1>
				<div id="content">
					'.$content.'
				</div>
				<footer>
					<p>&copy; 2018 iMovie. All rights reserved. Version '.$CONFIG["VERSION"].' ('.date("d M Y, H:i T", strtotime($CONFIG["TIMESTAMP"])).')</p>
				</footer>
			</div>
		</div>
	</body>
</html>';
	return $html;
}

function require_login() {
	global $userid;
	if(empty($userid)) {
		header("Location: login.php");
		die();
	}
}

function authenticate(){
	if(!array_key_exists("token", $_COOKIE))
		return "";
	$result = userdata("authenticate_admin.php?session_id=".session_id()."&token={$_COOKIE["token"]}");
	if(isset($result["admin_id"]))
		return $result["admin_id"];
	return "";
}

function authenticate_certificate() {
	// Check if the user submitted a client certificate
	if(!isset($_SERVER["SSL_CLIENT_VERIFY"])) {
		return "";
	}

	// Check if the given client certificate is valid
	if($_SERVER["SSL_CLIENT_VERIFY"] != "SUCCESS") {
		return "";
	}

	// Check if the serial number is numeric
	if(!is_numeric(hexdec($_SERVER["SSL_CLIENT_M_SERIAL"]))) {
		error_500("Client certificate does not have a numerical value as serial number");
	}

	$serial = round(hexdec($_SERVER["SSL_CLIENT_M_SERIAL"]));

	// Load certificate data
	$cert = core_ca("get_cert.php?serial={$serial}");

	// Check if certificate exists
	if(!isset($cert["cert_data"])) {
		return "";
	}

	$cert = $cert["cert_data"];

	// Check if the certificate has been revoked
	if(!is_null($cert["revoked"])) {
		return "";
	}

	// At this point, the user has a valid certificate
	$admin = userdata("get_admin.php?admin={$cert["user"]}");

	if(!isset($admin["admin_id"]))
		return "";

	return $admin["admin_id"];
}

function error_403() {
	$content = '<div class="alert alert-danger">You must be logged in to be able to view this page</div>';
	$title = 'Error 403 - Forbidden';
	http_response_code(403);
	echo content_to_html($content, $title);
	die();
}

function error_500($msg = '') {
	if(!empty($msg)) {
		$msg = '<p>The following debug message was given</p><hr /><p>'.$msg.'</p>';
	}
	$content = '<div class="alert alert-danger">
		<p>There has been an internal server error. We are working hard to resolve the issue. Sorry for the inconvenience...</p>
		'.$msg.'
	</div>';
	$title = 'Error 500 - Internal Server Error';
	http_response_code(500);
	echo content_to_html($content, $title);
	die();
}

function core_ca($url, $post=NULL) {
	return server_request("ca.api.imovie.local", $url, $post);
}

function userdata($url, $post=NULL) {
	return server_request("userdata.api.imovie.local", $url, $post);
}

function server_request($host, $url, $post=NULL) {
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => "https://{$host}/{$url}",
		CURLOPT_RETURNTRANSFER => true,

		// Verify myself
		CURLOPT_SSLKEY => "/etc/ssl/admin.imovie.local_pkey.pem",
		CURLOPT_KEYPASSWD => "e4d7233a14c200a6cb9f",
		CURLOPT_SSLCERT => "/etc/ssl/admin.imovie.local_cert.pem",

		// Verify other server
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

	if($errno != 0) error_500("Could not connect to {$host}");

	$return = json_decode($response, true);
	if(is_null($return)) error_500("Could not decode response from {$host}: <pre>{$response}</pre>");

	return $return;
}

?>

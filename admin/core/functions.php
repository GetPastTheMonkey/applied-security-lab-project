<?php

function content_to_html($content, $title) {
	global $CONFIG;
	global $userid;
	global $mysqli;
	if(!empty($userid)) {
		$sidebar = '
		<div id="small-profile">
			Logged in as <strong>'.$userid.'</strong>
		</div>
		<ul class="linkList">
			<li><a href="overview.php"><span class="fa fa-fw fa-dashboard"></span> Overview</a></li>
			<li><a href="list_certs.php"><span class="fa fa-fw fa-list"></span> List My Certificates</a></li>
			<li><a href="new_cert.php"><span class="fa fa-fw fa-plus"></span> Create New Certificate</a></li>
			<li><a href="logout.php"><span class="fa fa-fw fa-sign-out"></span> Logout</a></li>
		</ul>';
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
	global $mysqli;
	$token = $mysqli->real_escape_string($_COOKIE["token"]);
	$result = $mysqli->query("SELECT uid FROM logins WHERE session_id LIKE '".session_id()."' AND token LIKE '{$token}' AND expired IS NULL LIMIT 1");
	if(!$result) {
		error_500('Database error while authenticating: '.$mysqli->error);
		exit();
	} elseif($result->num_rows != 1) {
		return "";
	}
	return $result->fetch_assoc()["uid"];
}

function authenticate_certificate() {
	global $mysqli;

	// Check if the user submitted a client certificate
	if(!isset($_SERVER["SSL_CLIENT_VERIFY"])) {
		return "";
	}

	// Check if the given client certificate is valid
	if($_SERVER["SSL_CLIENT_VERIFY"] != "SUCCESS") {
		return "";
	}

	// Check if the serial number is numeric
	if(!is_numeric($_SERVER["SSL_CLIENT_M_SERIAL"])) {
		error_500("Client certificate does not have a numerical value as serial number");
	}

	$serial = $mysqli->real_escape_string(round($_SERVER["SSL_CLIENT_M_SERIAL"]));

	// Load certificate data
	$res = $mysqli->query("SELECT user, revoked FROM certificates WHERE serial_nr='{$serial}' LIMIT 1");

	// Check if certificate exists
	if($res->num_rows != 1) {
		return "";
	}

	$cert = $res->fetch_assoc();

	// Check if the certificate has been revoked
	if(!is_null($cert["revoked"])) {
		return "";
	}

	// At this point, the user has a valid certificate
	return $cert["user"];
}

function generate_token($length, $charset=NULL){
	if($charset === NULL){
		$charset = "0123456789abcdefghijklmnopqrstuvwxyz";
	}
	$token = "";
	for($i = 0; $i < $length; $i++){
		$token .= $charset[rand(0, strlen($charset) - 1)];
	}
	return $token;
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

?>

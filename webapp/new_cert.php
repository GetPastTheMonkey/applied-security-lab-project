<?php

include("core/include.php");

require_login();

$title = "Create New Certificate";

if(($_SERVER["REQUEST_METHOD"] == "POST") AND isset($_POST["confirm"]) AND isset($_POST["purpose"]) AND !empty($_POST["purpose"])) {
	// Load user data from database
	$data = userdata("get_user.php?user={$userid}");

	/////////////////////////////////////////////////////////
	// DO NOT CHANGE THIS LINE, THIS WILL BREAK THE SYSTEM //
	$pepper = "848cfdc57e446d02d26c0beac803a69cc7dd96d240134778f9c4f27d685f1dc2d544decd90a4d9e63920c820587f3030daa4332d9bb121e62e2e6e27ec80a5a0";
	/////////////////////////////////////////////////////////

	$serial = core_ca("add_cert.php", array(
		"user" => $userid,
		"name" => $data["firstname"]." ".$data["lastname"],
		"mail" => $data["email"],
		"secret" => $pepper,
		"purpose" => $_POST["purpose"]
	));

	if(!is_numeric($serial))
		error_500("Could not create certificate. Please check output of core CA: <pre>".print_r($serial, true)."</pre>");

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

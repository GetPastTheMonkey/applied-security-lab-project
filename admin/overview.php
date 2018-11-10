<?php

include("core/include.php");

require_login();

$title = "CA Stats and Overview";

$next = $mysqli->query("SELECT MAX(serial_nr) AS max FROM certificates")->fetch_assoc()["max"] + 1;
$issued = $mysqli->query("SELECT COUNT(*) AS c FROM certificates")->fetch_assoc()["c"];
$revoked = $mysqli->query("SELECT COUNT(*) AS c FROM certificates WHERE revoked IS NOT NULL")->fetch_assoc()["c"];

$content = '<div class="row text-center">
	<div class="col-sm-4">
		<h2>Current Serial Number</h2>
		<p style="font-size: 75px;">'.$next.'</p>
	</div>
	<div class="col-sm-4">
		<h2>Number of issued certs</h2>
		<p style="font-size: 75px;">'.$issued.'</p>
	</div>
	<div class="col-sm-4">
		<h2>Number of revoked certs</h2>
		<p style="font-size: 75px;">'.$revoked.'</p>
	</div>
</div>';

echo content_to_html($content, $title);

?>

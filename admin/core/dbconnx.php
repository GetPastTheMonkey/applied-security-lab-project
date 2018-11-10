<?php
$mysqli = new mysqli($CONFIG["DB_HOST"], $CONFIG["DB_USER"], $CONFIG["DB_PASSWORD"], $CONFIG["DB_NAME"]);
if($mysqli->connect_errno) {
	die("Database error: ".$mysqli->connect_errno." - ".$mysqli->connect_error);
}
$mysqli->set_charset("utf8");
?>
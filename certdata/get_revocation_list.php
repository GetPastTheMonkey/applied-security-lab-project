<?php

include("core/include.php");

$res = $mysqli->query("SELECT serial_nr FROM certificates WHERE revoked IS NOT NULL ORDER BY serial_nr");

$arr = array();
while($d = $res->fetch_assoc())
	$arr[] = $d["serial_nr"];

print_json($arr);

?>

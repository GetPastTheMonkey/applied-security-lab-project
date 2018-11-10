<?php

include("core/include.php");

$res = $mysqli->query("SELECT serial_nr FROM certificates WHERE revoked IS NOT NULL ORDER BY serial_nr");

$list = array();
while($d = $res->fetch_assoc()) {
	$list[] = $d["serial_nr"];
}

header("Content-type: application/json");
print json_encode($list, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

?>

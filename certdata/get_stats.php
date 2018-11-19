<?php

include("core/include.php");

$next = $mysqli->query("SELECT MAX(serial_nr) AS max FROM certificates")->fetch_assoc()["max"];
if(is_null($next)) $next = 0;
else $next++;

$total = $mysqli->query("SELECT COUNT(*) AS c FROM certificates")->fetch_assoc()["c"];
$revoked = $mysqli->query("SELECT COUNT(*) AS c FROM certificates WHERE revoked IS NOT NULL")->fetch_assoc()["c"];
$valid = $total - $revoked;

print_json(array(
	"stats" => array(
		"next_serial" => $next,
		"count_total" => $total,
		"count_valid" => $valid,
		"count_revoked" => $revoked
	)
));

?>

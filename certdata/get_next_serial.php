<?php

include("core/include.php");

$next = $mysqli->query("SELECT MAX(serial_nr) AS max FROM certificates")->fetch_assoc()["max"];
if(is_null($next)) $next = 0;
else $next++;

print_json($next);

?>

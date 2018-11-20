<?php

include("core/include.php");

$response = certdata("get_stats.php");

print_json($response);

?>

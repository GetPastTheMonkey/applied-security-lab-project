<?php

include("core/include.php");

$response = certdata("get_revocation_list.php");

print_json($response);

?>

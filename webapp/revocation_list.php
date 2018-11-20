<?php

include("core/include.php");

$list = core_ca("get_revocation_list.php");

header("Content-type: application/json");

print json_encode($list, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

?>

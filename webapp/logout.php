<?php

include("core/include.php");

if(!empty($userid)) {
	setcookie("token", "", 1);
	$mysqli->query("UPDATE logins SET expired=NOW() WHERE session_id LIKE '".session_id()."' AND token LIKE '{$_COOKIE["token"]}' LIMIT 1");
}

header("Location: ./");

?>
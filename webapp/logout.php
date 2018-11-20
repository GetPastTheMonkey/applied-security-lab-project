<?php

include("core/include.php");

if(!empty($userid)) {
	$res = userdata("expire_login.php", array(
		"session_id" => session_id(),
		"token" => $_COOKIE["token"]
	));

	if($res["status"]["status_code"] != 200)
		error_500("Could not expire login. Check response from userdata:<pre>".print_r($res, true)."</pre>");
	else setcookie("token", "", 1);
}

header("Location: ./");

?>

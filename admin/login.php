<?php
include("core/include.php");

$redir_url = "./";

if(!empty($userid)) {
	header("Location: {$redir_url}");
	exit();
}

$title = "{$CONFIG["PAGE_TITLE"]} Login";

$form = '<form action="" method="POST">
	<div class="form-group">
		<label for="login-form-username">Admin Username</label>
		<input type="text" class="form-control" id="login-form-username" name="username" placeholder="Username" required>
	</div>
	<div class="form-group">
		<label for="login-form-password">Password</label>
		<input type="password" class="form-control" id="login-form-password" name="password" placeholder="Password" required>
	</div>
	<button type="submit" class="btn btn-primary">Login</button>
</form>';

if(($_SERVER["REQUEST_METHOD"] == "POST") AND isset($_POST["username"]) AND isset($_POST["password"]) AND !empty($_POST["username"]) AND !empty($_POST["password"])) {
	try {
		$username = $_POST["username"];
		$data = $userdata("get_admin.php?admin={$username}");

		if(isset($data["status"]))
			throw new Exception();
		
		// Check if user pw was correct
		if($data["pwd"] != hash("sha512", $data["salt"].$_POST['password']))
			throw new Exception();

		// Add login to userdata
		$token = userdata("add_login.php", array(
			"uid" => $data["admin_id"],
			"session_id" => session_id(),
			"ip_address" => $_SERVER["REMOTE_ADDR"]
		));

		if(is_array($token))
			error_500("Could not log login. Please try again later. If this issue comes up again, please inform an administrator.");

		// Send token cookie to user
		setcookie("token", $token);

		// Forward user
		header("Location: {$redir_url}");
	} catch(Exception $e) {
		$content = '<div class="alert alert-warning">
			Mail / Password combination incorrect
		</div>
		'.$form;
	}
} else {
	$content  = $form;
}


echo content_to_html($content, $title);
?>

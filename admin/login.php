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
		$username = $mysqli->real_escape_string($_POST["username"]);
		$res = $mysqli->query("SELECT admin_id, pwd, salt FROM admins WHERE admin_id LIKE '{$username}' LIMIT 1");
		
		if(!$res) {
			error_500('There was an error with the database query. Please contact an administrator with the followin error message:<br /><code>'.$mysqli->error.'</code>');
		} elseif($res->num_rows != 1) {
			throw new Exception("Username / Password combination incorrect");
		}
		
		$data = $res->fetch_assoc();
		
		// Check if user pw was correct
		if($data["pwd"] != hash("sha512", $data["salt"].$_POST['password'])){
			throw new Exception("Username / Password combination incorrect");
		}
		
		// Create new token
		$token = generate_token(128);
		
		// Update DB and user cookie
		setcookie("token", $token);
		$mysqli->query("INSERT INTO logins (uid, session_id, token, timestamp, ip_address) VALUES ('{$data["admin_id"]}', '".session_id()."', '{$token}', NOW(), '{$_SERVER["REMOTE_ADDR"]}')");
		
		// Forward user
		header("Location: {$redir_url}");
	} catch(Exception $e) {
		$content = '<div class="alert alert-warning">
			'.$e->getMessage().'
		</div>
		'.$form;
	}
} else {
	$content  = $form;
}


echo content_to_html($content, $title);
?>

<?php

include("core/include.php");

$title = "iMovie CA Admin";

if(empty($userid)) {
	$content = '<div class="alert alert-warning">Please login to your admin account in order to create, revoke and download certificates, as well as view CA statistics.</div>
	<p><a href="login.php" class="btn btn-primary">Login</a></p>';
} elseif(($_SERVER["REQUEST_METHOD"] == "POST") AND isset($_POST["lastname"]) AND !empty($_POST["lastname"]) AND isset($_POST["firstname"]) AND !empty($_POST["firstname"]) AND isset($_POST["email"]) AND !empty($_POST["email"]) AND isset($_POST["pwd"]) AND !empty($_POST["pwd"])) {
	$redir_url = "./";

	$user = userdata("get_admin.php?admin={$userid}");

	if($user["pwd"] != hash("sha512", $user["salt"].$_POST["pwd"])) {
		header("Location: {$redir_url}?status=wrong_pw");
		exit();
	}

	if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		header("Location: {$redir_url}?status=incorrect_mail");
		exit();
	}

	// Check if should update password
	if(isset($_POST["new_pw_1"]) AND isset($_POST["new_pw_2"]) AND !empty($_POST["new_pw_1"]) AND !empty($_POST["new_pw_2"])) {
		if($_POST["new_pw_1"] != $_POST["new_pw_2"]) {
			header("Location: {$redir_ulr}?status=new_pw_mismatch");
			exit();
		}
		$user["salt"] = bin2hex(random_bytes(64));
		$user["pwd"] = hash("sha512", $user["salt"].$_POST["new_pw_1"]);
	}

	$user["lastname"] = htmlentities($_POST["lastname"]);
	$user["firstname"] = htmlentities($_POST["firstname"]);
	$user["email"] = htmlentities($_POST["email"]);

	$res = userdata("update_admin.php", $user);

	header("Location: {$redir_url}?status=ok");
	exit();
} else {
	$data = userdata("get_admin.php?admin={$userid}");

	$info = '';
	$status = isset($_GET["status"]) ? $_GET["status"] : '';
	switch($status) {
	case "wrong_pw":
		$info = '<div class="alert alert-warning" style="max-width: 700px;">Password incorrect</div>'; break;
	case "incorrect_mail":
		$info = '<div class="alert alert-warning" style="max-width: 700px;">Invalid mail address</div>'; break;
	case "new_pw_mismatch":
		$info = '<div class="alert alert-warning" style="max-width: 700px;">New passwords do not match</div>'; break;
	case "ok":
		$info = '<div class="alert alert-success" style="max-width: 700px;">Data successfully updated</div>'; break;
	}

	$content = '<h2>Update Your Data</h2>
	<p class="lead">Make sure that your data is always up to date. In case of any incorrect information, please also revoke the corresponding certificates, if needed.</p>
	<form action="" method="POST">
		<div class="table-responsive">
			<table class="table table-striped table-hover" style="max-width: 700px;">
				<colgroup>
					<col width="50%" />
					<col width="50%" />
				</colgroup>
				<tr>
					<th>Attribute</th>
					<th>Value</th>
				</tr>
				<tr>
					<td>User ID</td>
					<td><input type="text" value="'.$userid.'" disabled /></td>
				</tr>
				<tr>
					<td>First Name</td>
					<td><input type="text" name="firstname" value="'.$data["firstname"].'" placeholder="First Name" required /></td>
				</tr>
				<tr>
					<td>Last Name</td>
					<td><input type="text" name="lastname" value="'.$data["lastname"].'" placeholder="Last Name" required /></td>
				</tr>
				<tr>
					<td>Mail address</td>
					<td><input type="email" name="email" value="'.$data["email"].'" placeholder="Mail address" required /></td>
				</tr>
				<tr>
					<td>New Password</td>
					<td><input type="password" name="new_pw_1" placeholder="Leave blank if not needed" /></td>
				</tr>
				<tr>
					<td>Confirm new Password</td>
					<td><input type="password" name="new_pw_2" placeholder="Leave blank if not needed" /></td>
				</tr>
				<tr>
					<td>Current password</td>
					<td><input type="password" name="pwd" placeholder="Confirm with Password" required /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Submit changes" class="btn btn-primary" /></td>
				</tr>
			</table>
		</div>
	</form>
	'.$info;
}
echo content_to_html($content, $title);

?>

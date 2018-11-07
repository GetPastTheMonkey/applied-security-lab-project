<?php

include("core/include.php");

require_login();

$title = "My Certificates";

$res = $mysqli->query("SELECT * FROM certificates WHERE user='{$userid}' ORDER BY serial_nr ASC");

if($res->num_rows == 0) {
	// User does not have any certificates yet
	$content = '<div class="alert alert-info">You don\'t have any certificates yet. You can generate one <a href="new_cert.php">here</a></div>';
} else {
	// User already has certificates
	$valid_table = '';
	$revoked_table = '';

	while($c = $res->fetch_assoc()) {
		if(is_null($c["revoked"])) {
			// Certificate has not been revoked
			$valid_table .= '<tr>
				<td style="text-align:right;">'.$c["serial_nr"].'</td>
				<td>'.$c["purpose"].'</td>
				<td>'.$c["created"].'</td>
				<td class="text-center"><a href="download.php?serial='.$c["serial_nr"].'"><span class="fa fa-fw fa-download"></span></a></td>
				<td class="text-center"><a href="revoke.php?serial='.$c["serial_nr"].'"><span class="fa fa-fw fa-ban"></span></a></td>
			</tr>';
		} else {
			// Certificate has been revoked
			$revoked_table .= '<tr>
				<td style="text-align:right;">'.$c["serial_nr"].'</td>
				<td>'.$c["purpose"].'</td>
				<td>'.$c["created"].'</td>
				<td>'.$c["revoked"].'</td>
			</tr>';
		}
	}

	if(empty($valid_table)) {
		$valid_table = '<div class="alert alert-info">You don\'t have any valid certificates right now. You can create new certificates <a href="new_cert.php">here</a></div>';
	} else {
		$valid_table = '<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tr>
					<th style="text-align:right;">Serial</th>
					<th>Purpose</th>
					<th>Created</th>
					<th class="text-center">Download</th>
					<th class="text-center">Revoke</th>
				</tr>
				'.$valid_table.'
			</table>
		</div>';
	}

	if(empty($revoked_table)) {
		$revoked_table = '<div class="alert alert-info">You don\'t have any revoked certificates right now. If you lose your private key (or if it is stolen), please revoke your certificate immediately!</div>';
	} else {
		$revoked_table = '<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tr>
					<th style="text-align:right;">Serial</th>
					<th>Purpose</th>
					<th>Created</th>
					<th>Revoked</th>
				</tr>
				'.$revoked_table.'
			</table>
		</div>';
	}

	$content = '<div class="row">
		<div class="col-sm-6">
			<h2>Valid Certificates</h2>
			'.$valid_table.'
		</div>
		<div class="col-sm-6">
			<h2>Revoked Certificates</h2>
			'.$revoked_table.'
		</div>
	</div>';

}

echo content_to_html($content, $title);

?>

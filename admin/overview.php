<?php

include("core/include.php");

require_login();

$title = "CA Stats and Overview";

$stats = core_ca("get_stats.php");

if(!isset($stats["stats"]))
	error_500("Could not get stats from ca.api.imovie.local. Check response: <pre>".print_r($stats, true)."</pre>");

$content = '<div class="row text-center">
	<div class="col-sm-3">
		<h2>Next Serial</h2>
		<p style="font-size: 75px;">'.dechex((float) $stats["stats"]["next_serial"]).'</p>
	</div>
	<div class="col-sm-3">
		<h2>Issued certs</h2>
		<p style="font-size: 75px;">'.$stats["stats"]["count_total"].'</p>
	</div>
	<div class="col-sm-3">
		<h2>Valid certs</h2>
		<p style="font-size: 75px;">'.$stats["stats"]["count_valid"].'</p>
	</div>
	<div class="col-sm-3">
		<h2>Revoked certs</h2>
		<p style="font-size: 75px;">'.$stats["stats"]["count_revoked"].'</p>
	</div>
</div>';

echo content_to_html($content, $title);

?>

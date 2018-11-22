<?php

$time = date("Y-m-d-H-i");
$base_folder = "/home/backup_user";
$backup_folder = $base_folder."/".$time;

if(!is_dir($backup_folder))
	mkdir($backup_folder);

$host_config = array(
	array(
		"host" => "www.imovie.local",
		"server" => "webapp",
		"user" => "user"
	),
	array(
		"host" => "ca.api.imovie.local",
		"server" => "core-ca",
		"user" => "user"
	),
	array(
		"host" => "certdata.api.imovie.local",
		"server" => "certdata",
		"user" => "user"
	),
	array(
		"host" => "userdata.api.imovie.local",
		"server" => "userdata",
		"user" => "user"
	)
);

function scp_folder($host, $user, $remote_folder, $local_folder) {
	// Remote server
	system("scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -r {$user}@{$host}:{$remote_folder} {$local_folder}", $ret);
	if($ret) echo "[WARNING] 'scp -r {$user}@{$host}:{$remote_folder} {$local_folder}' returned {$ret}\n";
}

function ssh_file($host, $user, $remote_command, $local_file) {
	// Remote server
	system("ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null {$user}@{$host} \"{$remote_command}\" > {$local_file}", $ret);
	if($ret) echo "[WARNING] 'ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null {$user}@{$host} \"{$remote_command}\" > {$local_file}' returned {$ret}\n";
}

foreach($host_config as $current) {
	// Copy /var/log/apache2/
	$server_dir = $backup_folder."/".$current["server"];

	if(!is_dir($server_dir))
		mkdir($server_dir);

	// apache logs
	scp_folder($current["host"], $current["user"], "/var/log/apache2", $server_dir);

	// lastlog
	ssh_file($current["host"], $current["user"], "lastlog", "{$server_dir}/lastlog.log");

	// var/www/html source code
	scp_folder($current["host"], $current["user"], "/var/www/html", $server_dir);

	// copy apache config files
	scp_folder($current["host"], $current["user"], "/etc/apache2/sites-available", $server_dir);
}

?>

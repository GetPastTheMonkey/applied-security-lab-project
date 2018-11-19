<?php

# Setup script to set up server

echo "\n";
for($i = 1; $i < 66; $i++) echo "*";
echo "\n* WELCOME TO THE HANDY SERVER SETUP FOR OUR SUPER DUPER PROJECT *\n";
for($i = 1; $i < 66; $i++) echo "*";
echo "\n\n";

if(exec("whoami") !== "root") {
	echo "Plase run this script with sudo rights\n";
	exit(1);
}

$config = array(
	"webapp" => array(
		"ip" => "192.168.1.10",
		"urls" => array(
			"www.imovie.local" => "webapp",
			"admin.imovie.local" => "admin"
		),
		"database" => NULL
	),
	"coreca" => array(
		"ip" => "192.168.1.13",
		"urls" => array(
			"ca.api.imovie.local" => "core-ca"
		),
		"database" => NULL
	),
	"userdata" => array(
		"ip" => "192.168.1.11",
		"urls" => array(
			"userdata.api.imovie.local" => "userdata"
		),
		"database" => "userdata.sql"
	),
	"certdata" => array(
		"ip" => "192.168.1.12",
		"urls" => array(
			"certdata.api.imovie.local" => "certdata"
		),
		"database" => "certdata.sql"
	)
);

$print_usage = FALSE;

if(!isset($argv[1])) {
	echo "Please give me an argument...\n\n";
	$print_usage = TRUE;
} elseif(!isset($config[$argv[1]])) {
	echo "{$argv[1]} is not a valid server.\n\n";
	$print_usage = TRUE;
}

if($print_usage) {
	echo "What server would you like to set up?\n";
	echo "\t- webapp: Sets up the web frontend for users and administrators\n";
	echo "\t- coreca: Sets up the Core CA server\n";
	echo "\t- userdata: Sets up the user database and API\n";
	echo "\t- certdata: Sets up the certificate database and API\n\n";
	exit(1);
}

$config = $config[$argv[1]];

function rec_copy($src, $dst) {
	$dir = opendir($src);
	while(($file = readdir($dir)) !== FALSE) {
		if(($file != ".") AND ($file != "..")) {
			if(is_dir($src."/".$file)) {
				mkdir($dst."/".$file);
				rec_copy($src."/".$file, $dst."/".$file);
			} else {
				if(!copy($src."/".$file, $dst."/".$file))
					throw new Exception("Could not copy file {$file} from {$src} to {$dst}");
			}
		}
	}
	closedir($dir);
}

try {
	// Update and upgrade with apt
	system("sudo apt-get update && sudo apt-get upgrade --with-new-pkgs --yes", $ret);
	if($ret) throw new Exception("Could not run apt to update packages");

	// Make sure all PHP things are installed
	system("sudo apt-get install php --yes", $ret);
	if($ret) throw new Exception("Could not install PHP-related packages");

	// Clean apt cache and unused packages
	system("sudo apt-get autoremove --yes && sudo apt-get autoclean && sudo apt-get clean", $ret);
	if($ret) throw new Exception("Could not clean apt");

	// Copy hosts file
	if(!copy("configs/hosts", "/etc/hosts")) throw new Exception("Could not copy hosts file");

	// Copy cacert.pem
	if(!copy("certs/CA/cacert.pem", "/etc/ssl/cacert.pem")) throw new Exception("Could not copy CA certificate");

	// Remove apache2 default configs
	system("sudo a2dissite 000-default.conf", $ret);
	if($ret) throw new Exception("Could not dissite 000-default.conf");
	if(!unlink("/etc/apache2/sites-available/000-default.conf"))
		throw new Exception("Could not remove apache default site 000-default.conf");
	if(!unlink("/etc/apache2/sites-available/default-ssl.conf"))
		throw new Exception("Could not remove apache default site default-ssl.conf");

	// Remove apache default page
	if(!unlink("/var/www/html/index.html"))
		throw new Exception("Could not remove apache default page");

	// Enable SSL module of apache server
	system("sudo a2enmod ssl", $ret);
	if($ret) throw new Exception("Could not enmod ssl module");

	// Copy and ensite default redirect page
	if(!copy("configs/vhosts/default-redirect.conf", "/etc/apache2/sites-available/default-redirect.conf"))
		throw new Exception("Could not copy default-redirect.conf");
	system("sudo a2ensite default-redirect.conf", $ret);
	if($ret) throw new Exception("Could not ensite default-redirect.conf");

	// For each URL -> Copy pkey, cert and apache conf
	foreach($config["urls"] as $url => $dir) {
		// Copy URL certificate
		if(!copy("certs/{$url}/{$url}_cert.pem", "/etc/ssl/{$url}_cert.pem"))
			throw new Exception("Could not copy certificate of URL {$url}");

		// Copy URL private key
		if(!copy("certs/{$url}/{$url}_pkey.pem", "/etc/ssl/{$url}_pkey.pem"))
			throw new Exception("Could not copy private key of URL {$url}");

		// Copy URL apache conf and ensite
		if(!copy("configs/vhosts/{$url}.conf", "/etc/apache2/sites-available/{$url}.conf"))
			throw new Exception("Could not copy apache conf of URL {$url}");
		system("sudo a2ensite {$url}.conf", $ret);
		if($ret) throw new Exception("Could not ensite {$url}.conf");

		// Copy code to /var/www/html/{$url}
		if(!mkdir("/var/www/html/{$url}"))
			throw new Exception("Could not create directory /var/www/html/{$url}");
		rec_copy($dir, "/var/www/html/{$url}");
	}

	// SQL stuff
	if(!is_null($config["database"])) {
		// Need to install mysql-server
		system("sudo apt-get install mysql-server php-mysql --yes", $ret);
		if($ret) throw new Exception("Could not install mysql-server");

		// Run SQL file
		system("mysql -uroot -proot < database/{$config["database"]}", $ret);
		if($ret) throw new Exception("Could not run mysql script");
	}

	// Write IP configuration
	$ip_configs = "network:
    ethernets:
        enp0s3:
            addresses: [{$config["ip"]}/24]
            gateway4: 192.168.1.1
            dhcp4: no
    version: 2\n";
	if(file_put_contents("/etc/netplan/50-cloud-init.yaml", $ip_configs) === FALSE)
		throw new Exception("Could not write .yaml file for netplan configuration");
	system("sudo netplan apply", $ret);
	if($ret) throw new Exception("Could not apply netplan config");

	// Restart apache server (password needed)
	system("sudo apache2ctl restart", $ret);
	if($ret) throw new Exception("Could not restart apache server");

	echo "All went well :) Have fun...\n\n";
	echo "Hint: Make sure that you have propperly set up the internal network in the VM settings\n\n";
} catch(Exception $e) {
	echo "ERROR: ".$e->getMessage()."\n";
	exit(-1);
}

?>

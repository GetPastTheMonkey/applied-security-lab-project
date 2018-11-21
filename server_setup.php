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
		"needs_curl" => TRUE,
		"ca_pkey" => FALSE,
		"database" => NULL,
		"firewall_close_port_80" => FALSE,
		"firewall_port_443_exception" => NULL
	),
	"coreca" => array(
		"ip" => "192.168.1.13",
		"urls" => array(
			"ca.api.imovie.local" => "core-ca"
		),
		"needs_curl" => TRUE,
		"ca_pkey" => TRUE,
		"database" => NULL,
		"firewall_close_port_80" => TRUE,
		"firewall_port_443_exception" => "192.168.1.10"
	),
	"userdata" => array(
		"ip" => "192.168.1.11",
		"urls" => array(
			"userdata.api.imovie.local" => "userdata"
		),
		"needs_curl" => FALSE,
		"ca_pkey" => FALSE,
		"database" => "userdata.sql",
		"firewall_close_port_80" => TRUE,
		"firewall_port_443_exception" => "192.168.1.10"
	),
	"certdata" => array(
		"ip" => "192.168.1.12",
		"urls" => array(
			"certdata.api.imovie.local" => "certdata"
		),
		"needs_curl" => FALSE,
		"ca_pkey" => FALSE,
		"database" => "certdata.sql",
		"firewall_close_port_80" => TRUE,
		"firewall_port_443_exception" => "192.168.1.13"
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

	// Check if should copy CA private key
	if($config["ca_pkey"]) {
		if(!copy("certs/CA/cakey.pem", "/etc/ssl/cakey.pem"))
			throw new Exception("Could not copyi CA private key");
	}

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

	// Check if curl is needed
	if($config["needs_curl"]) {
		system("sudo apt-get install php-curl --yes", $ret);
		if($ret) throw new Exception("Could not install php-curl");
	}

	// Firewall confguration
	system("sudo iptables --flush", $ret);
	if($ret) throw new Exception("Could not flush iptables");

	// Add our own set of rules
	if($config["firewall_close_port_80"]) {
		// Block all traffic on port 80 (HTTP)
		system("sudo iptables -A INPUT -p tcp --dport 80 -j DROP", $ret);
		if($ret) throw new Exception("Could not close HTTP port 80 in iptables");
	}

	if(!is_null($config["firewall_port_443_exception"])) {
		// Open port 443 (HTTPS) only for exception
		system("sudo iptables -A INPUT -p tcp ! -s {$config["firewall_port_443_exception"]} --dport 443 -j DROP", $ret);
		if($ret) throw new Exception("Could not open HTTPS port 443 for only host {$config["firewall_port_443_exception"]}");
	}

	// DDoS protection, by javapipe.com

	// Drop invalid packets
	system("sudo iptables -t mangle -A PREROUTING -m conntrack --ctstate INVALID -j DROP", $ret);
	if($ret) throw new Exception("Could not block invalid packets");

	// Drop all non-SYN packets that are new
	system("sudo iptables -t mangle -A PREROUTING -p tcp ! --syn -m conntrack --ctstate NEW -j DROP", $ret);
	if($ret) throw new Exception("Could not block non-SYN new TCP packets");

	// Drop all packets with suspicious MSS value
	system("sudo iptables -t mangle -A PREROUTING -p tcp -m conntrack --ctstate NEW -m tcpmss ! --mss 536:65535 -j DROP", $ret);
	if($ret) throw new Exception("Could not drop packets with suspicious MSS value");

	// Drop all packets with bogus TCP flags
	$flags = array(
		"FIN,SYN,RST,PSH,ACK,URG NONE",
		"FIN,SYN FIN,SYN",
		"SYN,RST SYN,RST",
		"FIN,RST FIN,RST",
		"FIN,ACK FIN",
		"ACK,URG URG",
		"ACK,FIN FIN",
		"ACK,PSH PSH",
		"ALL ALL",
		"ALL NONE",
		"ALL FIN,PSH,URG",
		"ALL SYN,FIN,PSH,URG",
		"ALL SYN,RST,ACK,FIN,URG"
	);

	foreach($flags AS $i => $f) {
		system("sudo iptables -t mangle -A PREROUTING -p tcp --tcp-flags {$f} -j DROP", $ret);
		if($ret) throw new Exception("Could not block packets with bogus TCP flags #{$i}");
	}

	// Drop ICMP protocol
	system("sudo iptables -t mangle -A PREROUTING -p icmp -j DROP", $ret);
	if($ret) throw new Exception("Could not drop all ICMP packets");

	// Drop fragments
	system("sudo iptables -t mangle -A PREROUTING -f -j DROP", $ret);
	if($ret) throw new Exception("Could not drop all fragments");

	// Limit connections per source IP
	system("sudo iptables -A INPUT -p tcp -m connlimit --connlimit-above 111 -j REJECT --reject-with tcp-reset", $ret);
	if($ret) throw new Exception("Could not limit number of connections per source IP");

	// Limit RST packets
	system("sudo iptables -A INPUT -p tcp --tcp-flags RST RST -m limit --limit 2/s --limit-burst 2 -j ACCEPT", $ret);
	if($ret) throw new Exception("Could not limit RST packets #1");
	system("sudo iptables -A INPUT -p tcp --tcp-flags RST RST -j DROP", $ret);
	if($ret) throw new Exception("Could not limit RST packets #2");

	// Limit new TCP connections per second per source IP
	system("sudo iptables -A INPUT -p tcp -m conntrack --ctstate NEW -m limit --limit 60/s --limit-burst 20 -j ACCEPT", $ret);
	if($ret) throw new Exception("Could not limit new conns per source IP #1");
	system("sudo iptables -A INPUT -p tcp -m conntrack --ctstate NEW -j DROP", $ret);
	if($ret) throw new Exception("Could not limit new conns per source IP #2");

	// Anti SSH-brute force
	system("sudo iptables -A INPUT -p tcp --dport ssh -m conntrack --ctstate NEW -m recent --set", $ret);
	if($ret) throw new Exception("Could not protect against SSH brute force #1");
	system("sudo iptables -A INPUT -p tcp --dport ssh -m conntrack --ctstate NEW -m recent --update --seconds 60 --hitcount 10 -j DROP", $ret);
	if($ret) throw new Exception("Could not protect against SSH brute force #2");

	// Anti port scanning
	system("sudo iptables -N port-scanning", $ret);
	if($ret) throw new Exception("Could not protect against port scanning #1");
	system("sudo iptables -A port-scanning -p tcp --tcp-flags SYN,ACK,FIN,RST RST -m limit --limit 1/s --limit-burst 2 -j RETURN", $ret);
	if($ret) throw new Exception("Could not protect against port scanning #2");
	system("sudo iptables -A port-scanning -j DROP", $ret);
	if($ret) throw new Exception("Could not protect against port scanning #3");

	// End of DDoS protection, by javapipe.com

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

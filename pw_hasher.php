<?php

if($argc != 2) {
	echo "Only 1 parameter please...\n";
	exit(1);
}

$pw = $argv[1];

$salt = bin2hex(random_bytes(64));
$hashed = hash("sha512", $salt.$pw);

file_put_contents("./pw.txt", "Salt:
{$salt}

Hash:
{$hashed}");

echo "Put hashed password and salt in pw.txt\n";

?>

<?php
session_start();
include("config.php");
include("dbconnx.php");
include("functions.php");
$userid = authenticate();
?>
<?php
$env = parse_ini_file('.env');
$mysql = new mysqli($env["DBhost"], $env["DBuser"], $env["DBpass"], $env["DBname"]);

// Check connection
if ($mysql->connect_errno) {
  echo "Failed to connect to MySQL: " . $mysql->connect_error;
  exit();
}

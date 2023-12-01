<?php

use Dotenv\Dotenv;

require __DIR__ . '\..\vendor\autoload.php';

Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Local
$host = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");
/*
// Online
$host = "aws.connect.psdb.cloud";
$username = "hlvou2sylhvcpj8b8oib";
$password = "pscale_pw_bNWpPyS1IL2z7rzAsaHNBqM8pcC0PozCsGgUnBsR9jK";
$database = "dfsms";
// for Windows
$ssl = __DIR__ . "/cacert.pem";
// for MacOS
// $ssl = "/etc/ssl/cert.pem";
 */

$con = mysqli_init();
//mysqli_ssl_set($con, NULL, NULL, $ssl, NULL, NULL);

$con->real_connect($host, $username, $password, $database);

if ($con->connect_error) {
    echo 'Not connected to the database';
    echo 'Error: ' . $con->connect_error;
} else {
    echo 'Connected successfully';
}

?>
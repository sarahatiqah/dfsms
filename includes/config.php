<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "aws.connect.psdb.cloud";
$username = "hlvou2sylhvcpj8b8oib";
$password = "pscale_pw_bNWpPyS1IL2z7rzAsaHNBqM8pcC0PozCsGgUnBsR9jK";
$database = "dfsms";
$ssl = "/etc/ssl/cert.pem";

$con = mysqli_init();
mysqli_ssl_set($con, NULL, NULL, $ssl, NULL, NULL);

$con->real_connect($host, $username, $password, $database);

if ($con->connect_error) {
    echo 'Not connected to the database';
    echo 'Error: ' . $con->connect_error;
} else {
    echo 'Connected successfully';
}
?>
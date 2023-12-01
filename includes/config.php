<?php

use Dotenv\Dotenv;

require __DIR__ . '\..\vendor\autoload.php';

Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
//     Local
//     $host = getenv("DB_HOST");
//     $username = getenv("DB_USER");
//     $password = getenv("DB_PASSWORD");
//     $database = getenv("DB_NAME");

//     Online
    $host = getenv("AWS_HOST");
    $username = getenv("AWS_USER");
    $password = getenv("AWS_PASSWORD");
    $database = getenv("AWS_DB_NAME");

//     for Windows
    $ssl = __DIR__ . "/cacert.pem";
//     for MacOS
//     $ssl = "/etc/ssl/cert.pem";

    $con = mysqli_init();
    mysqli_ssl_set($con, NULL, NULL, $ssl, NULL, NULL);

    $con->real_connect($host, $username, $password, $database);

    if ($con->connect_error) {
        throw new Exception('Not connected to the database. Error: ' . $con->connect_error);
    } else {
        echo 'Connected successfully';
    }
} catch (Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}

?>
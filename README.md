# Dairy Farm Shop Management System

## Database Setup (Online)
1. In 'includes' folder, open 'config.php' file.
2. If you are on MacOS, find the `$ssl = __DIR__ . "/cacert.pem";` line. Then replace it with `$ssl = "/etc/ssl/cert.pem";`
3. If you are on Windows, ignore the above step.

## Database Setup (Local)
If there are any issues with connecting to the database online, use localhost instead:
1. Create a database with name dfsms.
2. Import `dfsms.sql` file (given inside the repository) in the database you're using.
3. In 'includes' folder, open 'config.php' file.
4. Replace the whole file with this code:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "root";
$database = "dfsms";

$con = mysqli_init();
$con->real_connect($host, $username, $password, $database);

if ($con->connect_error) {
    echo 'Not connected to the database';
    echo 'Error: ' . $con->connect_error;
} else {
    echo 'Connected successfully';
}
?>
```
5. Change the username and password values according to your database.

## How to run the Dairy Farm Shop Management System Project (DFSMS)
1. Clone repository

2. Copy dfsms folder

3. Paste inside root directory (for xampp xampp/htdocs, for wamp wamp/www, for lamp var/www/html)

4. Open PHPMyAdmin (http://localhost/phpmyadmin)

5. Run the script http://localhost/dfsms

## Account Credentials for Login
### Admin Login

- Username: admin

- Password: Test@123


### User Login

- Username: Yogesh

- Password: 12345

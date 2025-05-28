<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "feedback";
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Database connection failed");
}
?>
<?php 

$servername = "localhost";
$username = "root";
$password = "";
$database = "geoauth";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("<script>alert('Connection Failed.')</script>: " . $conn->connect_error);
}
    ?>
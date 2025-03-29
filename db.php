<?php
$servername = "localhost";
$username = "root";
$password = "root@123"; // password
$dbname = "banana_game"; // database name

// MySQL connection 
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection checking
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
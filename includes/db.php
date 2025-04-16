<?php
$host = "localhost";
$username = "root"; // change if different
$password = "";     // change if different
$database = "library_database";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

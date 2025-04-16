<?php
$servername = "localhost";
$username = "root"; // Default username
$password = "sqlr00T"; // Default password (empty if not set)
$database = "RippleTeeDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

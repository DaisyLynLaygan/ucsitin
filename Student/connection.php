<?php

$host = "localhost";
$db_username = "root";  
$password = "";    
$database = "sitin";   

// Create connection
$conn = new mysqli($host, $db_username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

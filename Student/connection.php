<?php

$host = "localhost";
<<<<<<< HEAD
$db_username = "root";  
$password = "";    
$database = "ucsitin";   

// Create connection
$conn = new mysqli($host, $db_username, $password, $database);
=======
$username = "root";  // Default XAMPP MySQL username
$password = "";      // Leave empty if no password is set in XAMPP
$database = "sitin"; // Make sure this matches your database name

// Establish connection
$conn = new mysqli($host, $username, $password, $database);
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

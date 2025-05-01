<?php

$host = "localhost";
$username = "root";  // Default XAMPP MySQL username
$password = "";      // Leave empty if no password is set in XAMPP
<<<<<<< HEAD
$database = "ucsitin"; // Make sure this matches your database name
=======
$database = "sitin"; // Make sure this matches your database name
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1

// Establish connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

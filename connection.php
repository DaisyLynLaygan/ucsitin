<?php
/*
//Connection to database

$connection = mysqli_connect("localhost","root","sitin");

if (!$connection){
    echo "<script>alert('Not connected to database')</script>";
}

error_reporting(0); */


$host = "localhost";
$username = "root";  // Default XAMPP MySQL username
$password = "";      // Leave empty if no password is set in XAMPP
$database = "sitin"; // Make sure this matches your database name

// Establish connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

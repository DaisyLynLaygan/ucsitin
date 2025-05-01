<?php
session_start();
include './connection.php';

// Check if the user is logged in
if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}

$idno = $_SESSION['idno'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $sitinId = $_POST['sitin_id'] ?? '';  // Fetch the sitin_id
    $feedbackText = $_POST['feedback'] ?? '';  // Fetch the feedback

    // Validate input
    if (empty($sitinId) || empty($feedbackText)) {
        // Error: Feedback or Sit-in ID is missing
        echo "error";  // Return error response if validation fails
        exit;
    }

    // Get current date and time
    $feedbackDate = date("Y-m-d H:i:s");  // Current date and time in MySQL datetime format

    // Prepare SQL statement to insert feedback into the database
    $sql = "INSERT INTO feedback (sit_in_id, feedback, feedback_date) 
            VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("iss", $sitinId, $feedbackText, $feedbackDate);  // Adding $feedbackDate

        // Execute the query
        if ($stmt->execute()) {
            echo "success";  // Return success response if execution succeeds
        } else {
            echo "error";  // Return error response if there is a failure
        }

        $stmt->close();
    } else {
        echo "error";  // If statement preparation fails
    }

    // Close the connection
    $conn->close();
}
?>
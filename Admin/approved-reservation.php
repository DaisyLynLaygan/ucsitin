<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Validate incoming POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['action'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $action = $_POST['action'];

    // Ensure reservation ID is valid
    if ($reservation_id > 0) {
        if ($action === 'approve') {
            // Update status to approved with a message
            $stmt = $conn->prepare("UPDATE reservations SET status = 'approved', message = 'Sit-in approved' WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'disapprove') {
            // Update status to disapproved
            $stmt = $conn->prepare("UPDATE reservations SET status = 'disapproved', message = 'Reservation disapproved' WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect back after action
header("Location: view-reservation.php");
exit();

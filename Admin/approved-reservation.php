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
            // First, update reservation status and message
            $stmt = $conn->prepare("UPDATE reservations SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();

            // Then, fetch reservation details for sitin insert
            $stmt = $conn->prepare("SELECT user_id, reason, lab FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $idno = $row['user_id'];
                $purpose = $row['reason'];
                $labFull = $row['lab'];

                // Extract room number from "Lab 203"
                $roomNumber = trim(str_replace('Lab', '', $labFull));

                // Insert into sitin table
                $insertStmt = $conn->prepare("INSERT INTO sit_in (idno, purpose, laboratory) VALUES (?, ?, ?)");
                $insertStmt->bind_param("sss", $idno, $purpose, $roomNumber);
                $insertStmt->execute();
                $insertStmt->close();
            }

            $stmt->close();
        } elseif ($action === 'disapprove') {
            $stmt = $conn->prepare("UPDATE reservations SET status = 'disapproved' WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect back after action
header("Location: view-reservation.php");
exit();

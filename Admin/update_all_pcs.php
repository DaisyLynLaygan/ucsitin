<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab = $_POST['lab'] ?? '';
    $status = $_POST['status'] ?? '';

    $allowed_statuses = ['Available', 'Used', 'Maintenance'];
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid status selected.");
    }

    // Update all PCs in the selected lab
    $stmt = $conn->prepare("UPDATE pcs SET status = ? WHERE lab = ?");
    $stmt->bind_param("ss", $status, $lab);
    if ($stmt->execute()) {
        // ✅ Update this to match your actual lab_management filename (use dash if that’s what your file is named)
        header("Location: lab-management.php?lab=" . urlencode($lab));
        exit();
    } else {
        echo "Error updating records.";
    }
} else {
    // Redirect if not a POST request
    header("Location: lab-management.php");
    exit();
}

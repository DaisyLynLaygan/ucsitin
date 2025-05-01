<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pc_id = $_POST['pc_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $lab = $_POST['lab'] ?? '';

    // Validate input
    if (!$pc_id || !$status) {
        die("Missing required data.");
    }

    $allowed_statuses = ['Available', 'Used', 'Maintenance'];
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid status selected.");
    }

    // Update status
    $stmt = $conn->prepare("UPDATE pcs SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $pc_id);
    if ($stmt->execute()) {
        header("Location: lab_management.php?lab=" . urlencode($lab));
        exit();
    } else {
        echo "Failed to update status.";
    }
} else {
    header("Location: lab_management.php");
    exit();
}
?>

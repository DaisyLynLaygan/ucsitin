<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lab']) && isset($_POST['status'])) {
    $lab = $_POST['lab'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE pcs SET status = ? WHERE lab = ?");
    $stmt->bind_param("ss", $status, $lab);
    $stmt->execute();
}

// ✅ Fix redirect filename (no space!)
header("Location: Lab-Management.php?lab=" . urlencode($_POST['lab']));
exit();

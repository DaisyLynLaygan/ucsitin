<?php
session_start();
include './connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idno = $_POST['idno'];
    $feedback = trim($_POST['feedback']);

    if (empty($feedback)) {
        echo "empty";
        exit;
    }

    $stmt = $conn->prepare("UPDATE sit_in SET action = 'Submitted', feedback = ? WHERE idno = ? ORDER BY date DESC LIMIT 1");
    $stmt->bind_param("si", $feedback, $idno);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>

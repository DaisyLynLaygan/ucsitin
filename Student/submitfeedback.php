<?php
session_start();
include './connection.php';

$idno = $_POST['idno'];
$sitin_id = $_POST['sitin_id'];
$feedback = $_POST['feedback'];

$stmt = $conn->prepare("UPDATE sit_in SET feedback = ?, feedback_status = 'Submitted' WHERE idno = ? AND sitin_id = ?");
$stmt->bind_param("sii", $feedback, $idno, $sitin_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>

<?php
include '../student/connection.php';
header('Content-Type: application/json');

$response = ["success" => false];

$sql = "UPDATE student_session SET remaining_session = 30";

if ($conn->query($sql) === TRUE) {
    $response["success"] = true;
} else {
    $response["error"] = $conn->error;
}

// Debugging: Echo JSON response
echo json_encode($response);
$conn->close();
?>

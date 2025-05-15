<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sitInId = $_POST['sit_in_id'];

    // Get sit-in record and student ID
    $sql = "SELECT ss.idno, s.points, s.remaining_session
            FROM student_sitin ss
            INNER JOIN student_session s ON ss.idno = s.idno
            WHERE ss.sit_in_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sitInId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $idno = $data['idno'];
        $newPoints = $data['points'] + 1;
        $newSessions = $data['remaining_session'] + floor($newPoints / 3);
        $remainingPoints = $newPoints % 3;

        // Update points and remaining sessions in student_session table
        $update = $conn->prepare("UPDATE student_session SET points = ?, remaining_session = ? WHERE idno = ?");
        $update->bind_param("iis", $remainingPoints, $newSessions, $idno);
        $update->execute();

        // Mark sit-in record as rewarded
        $updateSitIn = $conn->prepare("UPDATE student_sitin SET rewarded = 1 WHERE sit_in_id = ?");
        $updateSitIn->bind_param("i", $sitInId);
        $updateSitIn->execute();

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Record not found"]);
    }
}
$conn->close();
?>

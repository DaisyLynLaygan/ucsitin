<?php
require 'connection.php';

if (isset($_GET['lab'])) {
    $lab = $_GET['lab'];

    $stmt = $conn->prepare("SELECT pc_no FROM pcs WHERE lab = ? AND LOWER(status) = 'available'");
    $stmt->bind_param("s", $lab);
    $stmt->execute();
    $result = $stmt->get_result();

    $pcs = [];
    while ($row = $result->fetch_assoc()) {
        $pcs[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($pcs);
}
?>

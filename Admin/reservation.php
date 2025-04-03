<?php
include 'db_connection.php'; // or your actual DB connection file

$sql = "SELECT * FROM reservations";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => "Lab {$row['lab']} - {$row['language']}",
        'start' => "{$row['date']}T{$row['start_time']}",
        'end' => "{$row['date']}T{$row['end_time']}"
    ];
}

echo json_encode($events);
?>

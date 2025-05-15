<?php
include '../student/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sit_in_id = $_POST['sit_in_id'];
    $logout_time = date("Y-m-d H:i:s");

    $update = $conn->prepare("UPDATE student_sitin SET `sit-logout` = ?, status = 'Completed' WHERE sit_in_id = ?");
    $update->bind_param("si", $logout_time, $sit_in_id);

    if ($update->execute()) {
        echo "<script>
            alert('Logout time recorded successfully!');
            window.location.href = 'current-sit-in.php';
        </script>";
    } else {
        echo "<script>
            alert('Failed to record logout time.');
            window.location.href = 'current-sit-in.php';
        </script>";
    }

    $update->close();
    $conn->close();
} else {
    header("Location: current-sit-in.php");
    exit();
}
?>

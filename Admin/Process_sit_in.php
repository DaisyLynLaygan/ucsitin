<?php
include '../student/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idno = $_POST['idno'];
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];
    $sit_login = date("Y-m-d H:i:s"); 
    $status = "Pending";

    // Check if the student already has a pending sit-in
    $pendingCheck = $conn->prepare("SELECT COUNT(*) AS pending_count FROM student_sitin WHERE idno = ? AND status = 'Pending'");
    $pendingCheck->bind_param("s", $idno);
    $pendingCheck->execute();
    $pendingResult = $pendingCheck->get_result();
    $pendingRow = $pendingResult->fetch_assoc();
    $pendingCount = $pendingRow['pending_count'];
    $pendingCheck->close();

    if ($pendingCount > 0) {
        echo "<script>
            alert('You already have a pending sit-in. Please wait for approval before creating a new one.');
            window.location.href = 'search.php';
        </script>";
        $conn->close();
        exit();
    }

    // Check if the student still has remaining sessions
    $sessionCheck = $conn->prepare("SELECT remaining_session FROM student_session WHERE idno = ?");
    $sessionCheck->bind_param("s", $idno);
    $sessionCheck->execute();
    $sessionResult = $sessionCheck->get_result();
    $sessionRow = $sessionResult->fetch_assoc();
    $remaining_session = $sessionRow ? $sessionRow['remaining_session'] : 0;
    $sessionCheck->close();

    if ($remaining_session > 0) {
        $conn->begin_transaction();

        try {
            // Insert Sit-In record
            $stmt = $conn->prepare("INSERT INTO student_sitin (idno, purpose, lab, `sit-login`, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $idno, $purpose, $lab, $sit_login, $status);
            $stmt->execute();
            $stmt->close();

            // Deduct 1 session
            $updateSession = $conn->prepare("UPDATE student_session SET remaining_session = remaining_session - 1 WHERE idno = ?");
            $updateSession->bind_param("s", $idno);
            $updateSession->execute();
            $updateSession->close();

            $conn->commit();

            echo "<script>
                alert('Sit In recorded successfully! 1 session deducted.');
                window.location.href = 'current-sit-in.php';
            </script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>
                alert('Error during Sit In. Please try again.');
                window.location.href = 'current-sit-in.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('No remaining sessions. Sit In not allowed.');
            window.location.href = 'current-sit-iin.php';
        </script>";
    }

    $conn->close();
} else {
    header("Location: current-sit-in.php");
    exit();
}
?>

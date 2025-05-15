<?php
include '../student/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $laboratory = $_POST['laboratory'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    
    // Get all PCs in the selected laboratory with their availability status
    $stmt = $conn->prepare("SELECT pc_number, status FROM pc_availability WHERE laboratory = ?");
    $stmt->bind_param("s", $laboratory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get reservations for the selected date and time
    $res_stmt = $conn->prepare("SELECT pc_number FROM reservations 
                              WHERE laboratory = ? AND reservation_date = ? AND time_slot = ? 
                              AND status IN ('pending', 'approved')");
    $res_stmt->bind_param("sss", $laboratory, $date, $time);
    $res_stmt->execute();
    $res_result = $res_stmt->get_result();
    $reserved_pcs = [];
    
    while ($row = $res_result->fetch_assoc()) {
        $reserved_pcs[] = $row['pc_number'];
    }
    
    // Display PC cards
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pc_number = $row['pc_number'];
            $pc_status = $row['status'];
            
            // Determine final status
            if ($pc_status !== 'available') {
                $status = 'unavailable';
                $status_reason = 'PC marked as ' . $pc_status;
            } elseif (in_array($pc_number, $reserved_pcs)) {
                $status = 'unavailable';
                $status_reason = 'Already reserved';
            } else {
                $status = 'available';
                $status_reason = '';
            }
            
            echo '<div class="col-md-2 col-4">';
            echo '<div class="pc-card ' . $status . '" data-pc-number="' . $pc_number . '"';
            if ($status === 'unavailable') {
                echo ' title="' . htmlspecialchars($status_reason) . '"';
            }
            echo '>';
            echo '<div class="pc-number">PC ' . $pc_number . '</div>';
            echo '<span class="pc-status status-' . $status . '">' . ucfirst($status) . '</span>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="col-12 text-center"><p class="text-muted">No PCs found in this laboratory</p></div>';
    }
    
    $stmt->close();
    $res_stmt->close();
    $conn->close();
}
?>
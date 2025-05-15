<?php
session_start();
$pageTitle = "View Reservations";
include 'header.php';
include '../student/connection.php';

// Handle approval/disapproval
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $reservation_id = $_POST['reservation_id'];
        
        // Update reservation status
        $stmt = $conn->prepare("UPDATE reservations SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        
        if ($stmt->execute()) {
            // Get reservation details to update pc_availability
            $get_stmt = $conn->prepare("SELECT laboratory, pc_number FROM reservations WHERE id = ?");
            $get_stmt->bind_param("i", $reservation_id);
            $get_stmt->execute();
            $result = $get_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $reservation = $result->fetch_assoc();
                $laboratory = $reservation['laboratory'];
                $pc_number = $reservation['pc_number'];
                
                // Mark PC as reserved
                $update_stmt = $conn->prepare("UPDATE pc_availability SET status = 'reserved' WHERE laboratory = ? AND pc_number = ?");
                $update_stmt->bind_param("si", $laboratory, $pc_number);
                $update_stmt->execute();
                $update_stmt->close();
            }
            $get_stmt->close();
            
            echo "<script>alert('Reservation approved successfully!'); window.location.href='view-reservation.php';</script>";
        } else {
            echo "<script>alert('Error approving reservation: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } elseif (isset($_POST['disapprove'])) {
        $reservation_id = $_POST['reservation_id'];
        
        // Update reservation status without action_timestamp
        $stmt = $conn->prepare("UPDATE reservations SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        
        if ($stmt->execute()) {
            // Get reservation details to update pc_availability
            $get_stmt = $conn->prepare("SELECT laboratory, pc_number FROM reservations WHERE id = ?");
            $get_stmt->bind_param("i", $reservation_id);
            $get_stmt->execute();
            $result = $get_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $reservation = $result->fetch_assoc();
                $laboratory = $reservation['laboratory'];
                $pc_number = $reservation['pc_number'];
                
                // Mark PC as available again
                $update_stmt = $conn->prepare("UPDATE pc_availability SET status = 'available' WHERE laboratory = ? AND pc_number = ?");
                $update_stmt->bind_param("si", $laboratory, $pc_number);
                $update_stmt->execute();
                $update_stmt->close();
            }
            $get_stmt->close();
            
            echo "<script>alert('Reservation rejected successfully!'); window.location.href='view-reservation.php';</script>";
        } else {
            echo "<script>alert('Error rejecting reservation: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Get only pending reservations
$reservations = [];
$stmt = $conn->prepare("SELECT r.*, s.idno, CONCAT(s.lastname, ', ', s.firstname) AS student_name 
                       FROM reservations r 
                       JOIN student s ON r.student_id = s.idno 
                       WHERE r.status = 'pending'
                       ORDER BY r.reservation_date DESC, r.time_slot ASC");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>

<style>
    .container {
        max-width: 1400px;
        margin: 20px auto;
        padding: 0 15px;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .page-title {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .table-container {
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .table th {
        background-color:   #3f51b5;
        color: white;
        padding: 12px 15px;
        text-align: center;
    }
    
    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-align: center;
        vertical-align: middle;
    }
    
    .table tr:last-child td {
        border-bottom: none;
    }
    
    .table tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-pending {
        background-color: #4f46e5;
        color: white;
    }
    
    .badge-approved {
        background-color: #28a745;
        color: white;
    }
    
    .badge-rejected {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-completed {
        background-color:#CB9DF0;
        color: white;
    }
    
    .action-btns {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
        
        .action-btns {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Reservation Requests</h1>
    </div>
    <div>
        <a href="logs.php" class="nav-btn btn-primary">
            <i class="fas fa-desktop"></i> Logs
        </a>
    </div>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
            <thead>
    <tr>
        <th>Student ID</th>  <!-- Changed from Reservation ID -->
        <th>Student</th>
        <th>Laboratory</th>
        <th>PC Number</th>
        <th>Date</th>
        <th>Time In</th>
        <th>Purpose</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($reservations)): ?>
        <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= htmlspecialchars($reservation['idno']) ?></td>  <!-- Changed from id to idno -->
                <td><?= htmlspecialchars($reservation['student_name']) ?></td>
                
                                <td><?= htmlspecialchars($reservation['laboratory']) ?></td>
                                <td><?= htmlspecialchars($reservation['pc_number']) ?></td>
                                <td><?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></td>
                                <td><?= htmlspecialchars($reservation['time_slot']) ?></td>
                                <td><?= htmlspecialchars($reservation['purpose']) ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    switch(strtolower($reservation['status'])) {
                                        case 'pending':
                                            $status_class = 'badge-pending';
                                            break;
                                        case 'approved':
                                            $status_class = 'badge-approved';
                                            break;
                                        case 'rejected':
                                            $status_class = 'badge-rejected';
                                            break;
                                        case 'completed':
                                            $status_class = 'badge-completed';
                                            break;
                                        default:
                                            $status_class = 'badge-pending';
                                    }
                                    ?>
                                    <span class="badge <?= $status_class ?>"><?= ucfirst($reservation['status']) ?></span>
                                </td>
                                
                                <td>
                                    <?php if ($reservation['status'] == 'pending'): ?>
                                        <div class="action-btns">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <button type="submit" name="approve" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <button type="submit" name="disapprove" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this reservation?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">No actions available</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <h5 class="text-muted">No reservation requests found</h5>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php
session_start();
$pageTitle = "Reservation Logs";
include 'header.php';
include '../student/connection.php';

// Get all non-pending reservations (approved, rejected, completed)
$reservations = [];
$stmt = $conn->prepare("SELECT r.*, s.idno, CONCAT(s.lastname, ', ', s.firstname) AS student_name 
                       FROM reservations r 
                       JOIN student s ON r.student_id = s.idno 
                       WHERE r.status != 'pending'
                       ORDER BY r.processed_at DESC, r.reservation_date DESC");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>

<!-- Rest of your HTML remains the same -->
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
        background-color: #3f51b5;;
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
        background-color:  #4f46e5;
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
    
    .reason-text {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Reservation Logs</h1>
    </div>
    <div>
        <a href="view-reservation.php" class="nav-btn btn-primary">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student</th>
                        <th>Laboratory</th>
                        <th>PC Number</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservations)): ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['idno']) ?></td>
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
                                
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No reservation logs found</h5>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


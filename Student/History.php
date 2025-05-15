<?php
include 'header.php';
$idno = $_SESSION['idno']; // Student's ID
$pageTitle = "My Sit-In History";
include '../student/connection.php';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $sit_in_id = $_POST['sit_in_id'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("INSERT INTO feedback (sit_in_id, idno, feedback, date_submitted) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $sit_in_id, $idno, $feedback);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for your feedback!'); window.location.href='history.php';</script>";
    } else {
        echo "<script>alert('Failed to submit feedback. Please try again.');</script>";
    }
    $stmt->close();
}

// Date Range Filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Base query
$where = "WHERE ss.idno = ? AND ss.status = 'Completed'";
$dateParams = '';
if ($startDate && $endDate) {
    $where .= " AND DATE(ss.`sit-login`) BETWEEN '$startDate' AND '$endDate'";
    $dateParams = "&start_date=$startDate&end_date=$endDate";
}

// Get total number of records
$totalResult = $conn->prepare("SELECT COUNT(*) as total FROM student_sitin ss $where");
$totalResult->bind_param("s", $idno);
$totalResult->execute();
$totalRows = $totalResult->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch records
$sql = "SELECT ss.sit_in_id, ss.purpose, ss.lab, ss.`sit-login`, ss.`sit-logout`, f.feedback
        FROM student_sitin ss
        LEFT JOIN feedback f ON ss.sit_in_id = f.sit_in_id
        $where
        ORDER BY ss.`sit-logout` DESC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idno);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
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
        background-color: #3f51b5;
        color: white;
        padding: 12px 15px;
        text-align: left;
        position: sticky;
        top: 0;
    }
    .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .table tr:last-child td {
        border-bottom: none;
    }
    .table tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #7f8c8d;
    }
    .empty-state i {
        font-size: 40px;
        margin-bottom: 15px;
        color: #bdc3c7;
    }
    .pagination-container {
        background: white;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .page-item.active .page-link {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }
    .page-link {
        color: #2c3e50;
    }
    .feedback-btn {
        min-width: 120px;
    }
    .submitted-badge {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }
    .filter-btn-group {
        display: flex;
        gap: 5px;
    }
    .filter-btn {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 style="font-size: 18px; font-weight: 500; color:  #3f51b5;">
                </i>My Sit-In History
                </h2>
                <span class="badge bg-warning fs-6">
                    <i class="fas fa-list-check me-1"></i><?= number_format($totalRows); ?> Records
                </span>
            </div>
            <hr style="border-color: #3f51b5; opacity: 0.5;">
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-dark filter-btn" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter me-1"></i> Filters
            </button>
        </div>
    </div>

    <!-- Filter Collapse -->
    <div class="row mb-4 collapse" id="filterCollapse">
        <div class="col-12">
            <div class="card shadow-sm" style="border-color: #3f51b5">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <!-- Date Range Filter -->
                        <div class="col-md-8 offset-md-2">
                            <label class="form-label fw-bold" style="color: #3f51b5;">Date Range</label>
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?= htmlspecialchars($startDate) ?>" placeholder="Start Date" style="border-color:  #3f51b5;">
                                <span class="input-group-text" style="background-color:  #3f51b5; color: white;">to</span>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?= htmlspecialchars($endDate) ?>" placeholder="End Date" style="border-color: #3f51b5;">
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="col-12 d-flex justify-content-center gap-2">
                            <button type="submit" class="btn text-white" style="background-color: #3f51b5;">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="history.php" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Records Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Purpose</th>
                            <th>Lab</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time In</th>
                            <th class="text-center">Time Out</th>
                            <th class="text-center">Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php $modalId = "feedbackModal" . $row['sit_in_id']; ?>
                            <tr>
                                <td><?= htmlspecialchars($row['purpose']) ?></td>
                                <td><?= htmlspecialchars($row['lab']) ?></td>
                                <td class="text-center"><?= date("M d, Y", strtotime($row['sit-login'])) ?></td>
                                <td class="text-center"><?= date("h:i A", strtotime($row['sit-login'])) ?></td>
                                <td class="text-center"><?= date("h:i A", strtotime($row['sit-logout'])) ?></td>
                                <td class="text-center">
                                    <?php if (empty($row['feedback'])): ?>
                                        <button class="btn btn-primary btn-sm feedback-btn" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                                            <i class="fas fa-comment-dots me-1"></i> Give Feedback
                                        </button>
                                    <?php else: ?>
                                        <span class="submitted-badge">
                                            <i class="fas fa-check-circle me-1"></i> Submitted
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <h5>No sit-in records found</h5>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Pagination -->
    <div class="row">
        <div class="col-12">
            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= max(1, $page - 1); ?><?= $dateParams ?>">
                                <i class="fas fa-chevron-left me-1"></i> Previous
                            </a>
                        </li>
                        
                        <?php
                        // Show page numbers
                        $visiblePages = 5; // Number of pages to show around current page
                        $startPage = max(1, $page - floor($visiblePages/2));
                        $endPage = min($totalPages, $startPage + $visiblePages - 1);
                        
                        if ($startPage > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1'.$dateParams.'">1</a></li>';
                            if ($startPage > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $active = $i == $page ? 'active' : '';
                            echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.$dateParams.'">'.$i.'</a></li>';
                        }
                        
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page='.$totalPages.$dateParams.'">'.$totalPages.'</a></li>';
                        }
                        ?>
                        
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= min($totalPages, $page + 1); ?><?= $dateParams ?>">
                                Next <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modals -->
<?php 
$result->data_seek(0); // Reset pointer to beginning
while ($row = $result->fetch_assoc()): 
    $modalId = "feedbackModal" . $row['sit_in_id'];
    if (empty($row['feedback'])): ?>
    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-comment me-2"></i>Submit Feedback
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="sit_in_id" value="<?= $row['sit_in_id'] ?>">
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Tell us about your experience in <?= htmlspecialchars($row['lab']) ?> on <?= date("M d, Y", strtotime($row['sit-login'])) ?></label>
                        <textarea name="feedback" class="form-control" placeholder="Share your experience..." rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_feedback" class="btn text-white" style="background-color: #2c3e50;">
                        <i class="fas fa-paper-plane me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
<?php endwhile; ?>


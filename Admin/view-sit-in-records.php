<?php
include 'header.php';
$pageTitle = "View Sit-In Records";

// Date Range Filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Base query
$where = "WHERE ss.status = 'Completed'";
$dateParams = '';
if ($startDate && $endDate) {
    $where .= " AND DATE(ss.`sit-login`) BETWEEN '$startDate' AND '$endDate'";
    $dateParams = "&start_date=$startDate&end_date=$endDate";
}

// Capture the purpose and lab filter values
$purposeFilter = isset($_GET['purpose']) ? $_GET['purpose'] : '';
$labFilter = isset($_GET['lab']) ? $_GET['lab'] : '';

// Add conditions for lab and purpose filters if selected
if ($labFilter) {
    $where .= " AND ss.lab = '$labFilter'";
    $dateParams .= "&lab=$labFilter";
}

if ($purposeFilter) {
    $where .= " AND ss.purpose = '$purposeFilter'";
    $dateParams .= "&purpose=$purposeFilter";
}

// Handle reward point assignment
if (isset($_POST['give_reward'])) {
    $sit_in_id = $_POST['sit_in_id'];
    $idno = $_POST['idno'];
    
    // Check if this sit-in already has reward points
    $checkQuery = "SELECT reward_points_given FROM student_sitin WHERE sit_in_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $sit_in_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkRow = $checkResult->fetch_assoc();
    
    if ($checkRow['reward_points_given'] == 0) {
        // Update student's total points earned
        $updateQuery = "UPDATE student_session SET total_points_earned = total_points_earned + 1 WHERE idno = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("s", $idno);
        $updateStmt->execute();
        
        // Mark this sit-in as rewarded
        $markQuery = "UPDATE student_sitin SET reward_points_given = 1 WHERE sit_in_id = ?";
        $markStmt = $conn->prepare($markQuery);
        $markStmt->bind_param("i", $sit_in_id);
        $markStmt->execute();
        
        // Check for point conversion
        checkPointConversion($conn, $idno);
        
        echo "<script>alert('Reward point added successfully!'); window.location.href='view-sit-in-records.php?page=$page$dateParams';</script>";
    } else {
        echo "<script>alert('This sit-in record already has reward points.'); window.location.href='view-sit-in-records.php?page=$page$dateParams';</script>";
    }
}

function checkPointConversion($conn, $idno) {
    // Get current total points
    $pointsQuery = "SELECT total_points_earned FROM student_session WHERE idno = ?";
    $pointsStmt = $conn->prepare($pointsQuery);
    $pointsStmt->bind_param("s", $idno);
    $pointsStmt->execute();
    $pointsResult = $pointsStmt->get_result();
    $pointsRow = $pointsResult->fetch_assoc();
    $currentPoints = $pointsRow['total_points_earned'];
    
    // Calculate how many sessions to add (3 points = 1 session)
    $sessionsToAdd = floor($currentPoints / 3);
    
    if ($sessionsToAdd > 0) {
        // Calculate how many points we've already converted (to avoid double conversion)
        $convertedQuery = "SELECT IFNULL(SUM(points_used), 0) as total_converted FROM reward_conversions WHERE idno = ?";
        $convertedStmt = $conn->prepare($convertedQuery);
        $convertedStmt->bind_param("s", $idno);
        $convertedStmt->execute();
        $convertedResult = $convertedStmt->get_result();
        $convertedRow = $convertedResult->fetch_assoc();
        $alreadyConverted = $convertedRow['total_converted'];
        
        $pointsAvailable = $currentPoints - $alreadyConverted;
        $sessionsToAdd = floor($pointsAvailable / 3);
        
        if ($sessionsToAdd > 0) {
            $pointsToDeduct = $sessionsToAdd * 3;
            
            // Add sessions
            $updateSessionsQuery = "UPDATE student_session SET remaining_session = remaining_session + ? WHERE idno = ?";
            $updateSessionsStmt = $conn->prepare($updateSessionsQuery);
            $updateSessionsStmt->bind_param("is", $sessionsToAdd, $idno);
            $updateSessionsStmt->execute();
            
            // Record the conversion
            $conversionQuery = "INSERT INTO reward_conversions (idno, points_used, sessions_gained, conversion_date) VALUES (?, ?, ?, NOW())";
            $conversionStmt = $conn->prepare($conversionQuery);
            $conversionStmt->bind_param("sii", $idno, $pointsToDeduct, $sessionsToAdd);
            $conversionStmt->execute();
        }
    }
}

// Get total number of records after applying the filters
$totalResult = $conn->query("SELECT COUNT(*) as total FROM student_sitin ss $where");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch filtered records
$sql = "SELECT ss.sit_in_id, ss.idno, s.firstname, s.middlename, s.lastname, 
               ss.purpose, ss.lab, ss.`sit-login`, ss.`sit-logout`, ss.reward_points_given
        FROM student_sitin ss
        INNER JOIN student s ON ss.idno = s.idno
        $where
        ORDER BY ss.sit_in_id DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<style>
    .filter-btn-group {
        display: flex;
        gap: 5px;
    }
    .filter-btn {
        padding: 5px 10px;
        font-size: 12px;
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
        color: #7c3aed;
    }
    .empty-state i {
        font-size: 40px;
        margin-bottom: 15px;
        color:#7c3aed;
    }
    .pagination-container {
        background: white;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .page-item.active .page-link {
        background-color:#7c3aed;
        border-color: #7c3aed;
    }
    .page-link {
        color:#7c3aed;
    }
    .reward-btn {
        min-width: 120px;
    }
    .rewarded-badge {
        background-color: #F0C1E1;
        color:rgb(21, 87, 46);
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }
</style>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              
                <h2 style="font-size: 18px; font-weight: 500; color: #303f9f;">
                Completed Sit-In Records
        </h2>
                <span class="badge bg-warning fs-6">
                    <i class="fas fa-list-check me-1"></i><?= number_format($totalRows); ?> Records
                </span>
            </div>
            <hr style="border-color: #CB9DF0; opacity: 0.5;">
        </div>
    </div>

    <!-- Action Buttons with Filter Toggle -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end gap-2">
            <div class="filter-btn-group">
                <button class="btn btn-dark filter-btn" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-filter"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle filter-btn" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export"></i> Generate Report
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="generate-report.php?format=pdf&lab=<?= urlencode($labFilter) ?>&purpose=<?= urlencode($purposeFilter) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>">
                            <i class="fas fa-file-pdf text-danger me-1"></i> Export PDF
                        </a></li>
                        <li><a class="dropdown-item" href="generate-report.php?format=excel&lab=<?= urlencode($labFilter) ?>&purpose=<?= urlencode($purposeFilter) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>">
                            <i class="fas fa-file-excel text-success me-1"></i> Export Excel
                        </a></li>
                        <li><a class="dropdown-item" href="generate-report.php?format=csv&lab=<?= urlencode($labFilter) ?>&purpose=<?= urlencode($purposeFilter) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>">
                            <i class="fas fa-file-csv text-primary me-1"></i> Export CSV
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Controls (Collapsible) -->
    <div class="row mb-4 collapse" id="filterCollapse">
        <div class="col-12">
            <div class="card shadow-sm" style="border-color:  #4f46e5;">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <!-- Lab Filter -->
                        <div class="col-md-4">
                            <label for="lab" class="form-label fw-bold" style="color: #4f46e5;">Lab</label>
                            <select name="lab" id="lab" class="form-select" style="border-color: #4f46e5;">
                                <option value="">All Labs</option>
                                <?php
                                    $labsQuery = $conn->query("SELECT DISTINCT lab FROM student_sitin WHERE status = 'Completed'");
                                    while ($lab = $labsQuery->fetch_assoc()) {
                                        echo "<option value='" . $lab['lab'] . "' " . ($lab['lab'] == $labFilter ? 'selected' : '') . ">" . $lab['lab'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <!-- Purpose Filter -->
                        <div class="col-md-4">
                            <label for="purpose" class="form-label fw-bold" style="color: #7c3aed;">Purpose</label>
                            <select name="purpose" id="purpose" class="form-select" style="border-color: #7c3aed;">
                                <option value="">All Purposes</option>
                                <?php
                                    $purposesQuery = $conn->query("SELECT DISTINCT purpose FROM student_sitin WHERE status = 'Completed'");
                                    while ($purpose = $purposesQuery->fetch_assoc()) {
                                        echo "<option value='" . $purpose['purpose'] . "' " . ($purpose['purpose'] == $purposeFilter ? 'selected' : '') . ">" . $purpose['purpose'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <!-- Date Range Filter -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="color: #7c3aed;">Date Range</label>
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?= htmlspecialchars($startDate) ?>" placeholder="Start Date" style="border-color: #7c3aed;">
                                <span class="input-group-text" style="background-color: #7c3aed; color: white;">to</span>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?= htmlspecialchars($endDate) ?>" placeholder="End Date" style="border-color: #7c3aed;">
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn text-white" style="background-color: #7c3aed;">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="view-sit-in-records.php" class="btn btn-outline-secondary">
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
                            <th class="text-center">#</th>
                            <th>Student</th>
                            <th>Purpose</th>
                            <th>Lab</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time In</th>
                            <th class="text-center">Time Out</th>
                            <th class="text-center">Reward</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $count = $offset + 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $count++ ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold"><?= htmlspecialchars($row['idno']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']) ?></small>
                                    </div>
                                </td>
                                <td>
                                        <?= htmlspecialchars($row['purpose']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['lab']) ?></td>
                                <td class="text-center"><?= date("M d, Y", strtotime($row['sit-login'])) ?></td>
                                <td class="text-center"><?= date("h:i A", strtotime($row['sit-login'])) ?></td>
                                <td class="text-center"><?= date("h:i A", strtotime($row['sit-logout'])) ?></td>
                                <td class="text-center">
                                    <?php if ($row['reward_points_given']): ?>
                                        <span class="rewarded-badge">
                                            <i class="fas fa-check-circle me-1"></i>Rewarded
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="sit_in_id" value="<?= $row['sit_in_id'] ?>">
                                            <input type="hidden" name="idno" value="<?= $row['idno'] ?>">
                                            <button type="submit" name="give_reward" class="btn btn-sm btn-success reward-btn">
                                                <i class="fas fa-gift me-1"></i>Give Reward
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No completed sit-ins found</h5>
                                    <p class="text-muted small">Try adjusting your filters or check back later</p>
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

<?php $conn->close(); ?>
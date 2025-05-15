<?php
include 'header.php';
$pageTitle = "Feedback Report";
include '../student/connection.php';

// Get available labs and purposes for filtering
$labQuery = $conn->query("SELECT DISTINCT lab FROM student_sitin ORDER BY lab ASC");
$purposeQuery = $conn->query("SELECT DISTINCT purpose FROM student_sitin ORDER BY purpose ASC");

// Get selected filters
$selectedLab = isset($_GET['lab']) ? $conn->real_escape_string($_GET['lab']) : '';
$selectedPurpose = isset($_GET['purpose']) ? $conn->real_escape_string($_GET['purpose']) : '';
$viewType = isset($_GET['view']) && in_array($_GET['view'], [ 'table']) ? $_GET['view'] : 'cards';

// Build where clause
$whereClauses = [];
if (!empty($selectedLab)) {
    $whereClauses[] = "ss.lab = '$selectedLab'";
}
if (!empty($selectedPurpose)) {
    $whereClauses[] = "ss.purpose = '$selectedPurpose'";
}
$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Pagination setup
$limit = 10; 
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Count total feedback
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM feedback f 
    JOIN student_sitin ss ON f.sit_in_id = ss.sit_in_id $whereClause");
$totalFeedback = $totalResult->fetch_assoc()['total'];
$totalPages = max(1, ceil($totalFeedback / $limit));

// Foul words list
$foulWords = [
    "shit", "fuck", "asshole", "bitch", "bastard", "cunt", "dick", 
    "piss", "cock", "pussy", "fag", "whore", "slut", "damn", 
    "hell", "crap", "douche", "faggot", "jerk", "idiot", "retard"
];

// Censor Function
function censorFoulWords($text, $foulWords) {
    foreach ($foulWords as $word) {
        $pattern = '/(?<!\w)'.preg_quote($word, '/').'(?!\w)/i';
        $replacement = str_repeat('*', strlen($word));
        $text = preg_replace($pattern, $replacement, $text);
    }
    return $text;
}

// Check and update flagged feedback
$flaggedWordsFound = [];
$feedbacks = $conn->query("SELECT feedback_id, feedback FROM feedback WHERE flagged = 0");
while ($fb = $feedbacks->fetch_assoc()) {
    $feedbackText = strtolower($fb['feedback']);
    foreach ($foulWords as $word) {
        if (preg_match('/(?<!\w)'.preg_quote($word, '/').'(?!\w)/i', $feedbackText)) {
            $conn->query("UPDATE feedback SET flagged = 1 WHERE feedback_id = ".$fb['feedback_id']);
            $flaggedWordsFound[$word] = ($flaggedWordsFound[$word] ?? 0) + 1;
            break;
        }
    }
}

// Fetch feedbacks
$sql = "SELECT f.feedback, f.date_submitted, f.flagged,  
            s.firstname, s.lastname, s.course, s.profile_picture,  
            ss.lab, ss.purpose, ss.`sit-login`, ss.`sit-logout`  
     FROM feedback f  
     JOIN student_sitin ss ON f.sit_in_id = ss.sit_in_id  
     JOIN student s ON f.idno = s.idno  
     $whereClause  
     ORDER BY f.date_submitted DESC  
     LIMIT $start, $limit";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .feedback-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .feedback-table thead th {
            background-color: #3f51b5;
            color: white;
            padding: 12px 15px;
            vertical-align: middle;
            position: sticky;
            top: 0;
        }

        .feedback-table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #eee;
        }

        .feedback-table tbody tr:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }

        .feedback-table .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .feedback-table .student-info {
            min-width: 200px;
        }

        .feedback-table .feedback-cell {
            max-width: 300px;
            word-wrap: break-word;
        }

        .view-toggle {
            display: inline-flex;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #dee2e6;
            margin-left: 15px;
        }

        .view-toggle .btn {
            border-radius: 0;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
        }

        .view-toggle .btn.active {
            background-color: #4f46e5;
            color: white;
        }

        .view-toggle .btn:first-child {
            border-right: 1px solid #dee2e6;
        }

        .notification-bell {
            position: fixed;
            top: 15px;
            right: 20px;
            cursor: pointer;
            font-size: 24px;
            color:#7c3aed;
            z-index: 1050;
            transition: all 0.3s;
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .notification-bell:hover {
            color: #1a252f;
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .notification-bell .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e74c3c;
            color: white;
            font-size: 12px;
            padding: 3px 7px;
            border-radius: 50%;
        }

        .notification-dropdown {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 350px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 15px;
            z-index: 1000;
        }

      

        .badge {
            font-weight: 500;
            padding: 5px 10px;
        }

        .bg-danger {
            background-color: #e74c3c !important;
        }

        .bg-warning, .badge-pending, .badge-pending-status {
            background-color: #303f9f !important;
            color: #fff !important;
        }

        .pagination-container {
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .page-item.active .page-link {
            background-color:#7c3aed;
            border-color:#7c3aed
        }

        .page-link {
            color: #7c3aed;
            min-width: 38px;
            text-align: center;
        }

        .page-link:hover {
            color: #1a252f;
            background-color: #f8f9fa;
        }

        .empty-state {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 40px;
        }

        .empty-state i {
            color: #bdc3c7;
            font-size: 48px;
            margin-bottom: 15px;
        }

        .toggle-feedback {
            color: #7c3aed;
            text-decoration: none;
            transition: all 0.3s;
        }

        .toggle-feedback:hover {
            color: #7c3aed;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
        
            
            .filter-container {
                flex-direction: column;
                color:CB9DF0;
            }
        }
    </style>
</head>
<body>
    <!-- Notification Bell -->
    <div class="notification-bell" onclick="toggleNotifications()">
        <i class="fas fa-bell"></i>
        <?php
        $flaggedCount = $conn->query("SELECT COUNT(*) AS count FROM feedback WHERE flagged = 1")->fetch_assoc()['count'];
        if ($flaggedCount > 0): ?>
            <span class="badge"><?= $flaggedCount ?></span>
        <?php endif; ?>
    </div>

    <div id="notificationDropdown" class="notification-dropdown">
        <h5>
            <i class="fas fa-exclamation-triangle"></i>
            Flagged Feedback Alerts
        </h5>
        
        <?php if ($flaggedCount > 0): ?>
            <?php if (!empty($flaggedWordsFound)): ?>
                <div class="mb-3">
                    <strong>Detected Words:</strong>
                    <?php foreach ($flaggedWordsFound as $word => $count): ?>
                        <span class="badge bg-danger me-1"><?= str_repeat('*', strlen($word)) ?> (<?= $count ?>)</span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php
            $flaggedFeedbacks = $conn->query("SELECT f.feedback, f.date_submitted, s.firstname, s.lastname 
                                            FROM feedback f
                                            JOIN student s ON f.idno = s.idno
                                            WHERE f.flagged = 1
                                            ORDER BY f.date_submitted DESC
                                            LIMIT 10");
            
            if ($flaggedFeedbacks->num_rows > 0): ?>
                <?php while ($row = $flaggedFeedbacks->fetch_assoc()): 
                    $foundWords = [];
                    foreach ($foulWords as $word) {
                        if (preg_match('/(?<!\w)'.preg_quote($word, '/').'(?!\w)/i', strtolower($row['feedback']))) {
                            $foundWords[] = $word;
                        }
                    }
                    ?>
                    <div class="notification-item">
                        <div>
                            <strong><?= htmlspecialchars($row['firstname'].' '.$row['lastname']) ?></strong>
                            <?php if (!empty($foundWords)): ?>
                                <span class="ms-2">used: 
                                    <?php foreach ($foundWords as $word): ?>
                                        <span class="flagged-word"><?= str_repeat('*', strlen($word)) ?></span>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="feedback-preview" title="<?= htmlspecialchars($row['feedback']) ?>">
                            <?= htmlspecialchars(censorFoulWords($row['feedback'], $foulWords)) ?>
                        </div>
                        <div class="timestamp">
                            <?= date("M j, Y g:i a", strtotime($row['date_submitted'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
            
            <div class="mt-3 text-center">
                <a href="feedback-report.php?view=<?= $viewType ?>&flagged=1" class="btn btn-sm btn-outline-danger">
                    View All Flagged Feedback
                </a>
            </div>
        <?php else: ?>
            <div class="text-center py-3">
                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                <p>No flagged feedback found</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        
        <h2 style="font-size: 18px; font-weight: 500; color: #303f9f">
        <i class="fas fa-comments me-2"></i>Student Feedback Report
        </h2>
<br>
        <!-- Filter & Export Controls -->
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted">Showing <?= $totalFeedback ?> records</span>
                    <!-- View Toggle -->
                    <div class="view-toggle">
                        <a href="?view=cards<?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>"
                           class="btn <?= $viewType === 'cards' ? 'active' : '' ?>">
                            <i class="fas fa-th-large me-1"></i> Cards
                        </a>
                        <a href="?view=table<?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>"
                           class="btn <?= $viewType === 'table' ? 'active' : '' ?>">
                            <i class="fas fa-table me-1"></i> Table
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex justify-content-end gap-2">
                    <!-- Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn filter-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <form method="GET" class="dropdown-menu dropdown-menu-end p-3 shadow">
                            <input type="hidden" name="view" value="<?= $viewType ?>">
                            <div class="mb-3">
                                <label for="lab" class="form-label fw-bold">Select Lab</label>
                                <select name="lab" id="lab" class="form-select">
                                    <option value="">All Labs</option>
                                    <?php $labQuery->data_seek(0); while ($row = $labQuery->fetch_assoc()): ?>
                                        <option value="<?= $row['lab'] ?>" <?= ($selectedLab == $row['lab']) ? 'selected' : '' ?>>
                                            <?= $row['lab'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="purpose" class="form-label fw-bold">Select Purpose</label>
                                <select name="purpose" id="purpose" class="form-select">
                                    <option value="">All Purposes</option>
                                    <?php $purposeQuery->data_seek(0); while ($row = $purposeQuery->fetch_assoc()): ?>
                                        <option value="<?= $row['purpose'] ?>" <?= ($selectedPurpose == $row['purpose']) ? 'selected' : '' ?>>
                                            <?= $row['purpose'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i> Apply Filters
                                </button>
                                <a href="feedback-report.php?view=<?= $viewType ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Export Dropdown -->
                    <div class="dropdown">
                        <button class="btn export-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="export_feedback.php?format=csv&lab=<?= $selectedLab ?>&purpose=<?= $selectedPurpose ?>">
                                <i class="fas fa-file-csv text-success me-2"></i>CSV
                            </a></li>
                            <li><a class="dropdown-item" href="export_feedback.php?format=excel&lab=<?= $selectedLab ?>&purpose=<?= $selectedPurpose ?>">
                                <i class="fas fa-file-excel text-primary me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="export_feedback.php?format=pdf&lab=<?= $selectedLab ?>&purpose=<?= $selectedPurpose ?>">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive mb-4">
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Lab</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Feedback</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result->data_seek(0); // Reset pointer to start for table view
                        while ($row = $result->fetch_assoc()): 
                            $profilePic = !empty($row['profile_picture'])
                                ? '../student/uploads/' . htmlspecialchars($row['profile_picture'])
                                : '../student/uploads/default.png';
                            $sitInDate = date("M d, Y", strtotime($row['sit-login']));
                            $sitInTime = date("h:i A", strtotime($row['sit-login']));
                            $sitOutTime = date("h:i A", strtotime($row['sit-logout']));
                            $timeRange = $sitInTime . ' - ' . $sitOutTime;
                        ?>
                        <tr>
                            <td class="student-info">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $profilePic ?>" alt="Profile" class="profile-img">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['course']) ?></div>
                                        <?= $row['flagged'] ? '<span class="badge bg-danger mt-1">Flagged</span>' : '' ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['lab']) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td><?= $sitInDate ?></td>
                            <td><?= $timeRange ?></td>
                            <td class="feedback-cell">
                                <?php if ($row['flagged']): ?>
                                    <div class="censored-text">
                                        <?= nl2br(htmlspecialchars(censorFoulWords($row['feedback'], $foulWords))) ?>
                                    </div>
                                    <div class="original-text d-none text-danger">
                                        <?= nl2br(htmlspecialchars($row['feedback'])) ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0 mt-1 toggle-feedback">Show Original</button>
                                <?php else: ?>
                                    <div>
                                        <?= nl2br(htmlspecialchars($row['feedback'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= date("M j, Y", strtotime($row['date_submitted'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-4">
                <div class="mb-2">
                    <span class="text-muted small">
                        Showing <?= $start + 1 ?> to <?= min($start + $limit, $totalFeedback) ?> of <?= $totalFeedback ?> entries
                    </span>
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <!-- First Page -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" 
                               href="?page=1&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>"
                               aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        
                        <!-- Previous Page -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" 
                               href="?page=<?= max(1, $page - 1) ?>&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>"
                               aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" 
                                   href="?page=<?= $i ?>&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=<?= $totalPages ?>&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>">
                                    <?= $totalPages ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Page -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" 
                               href="?page=<?= min($totalPages, $page + 1) ?>&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>"
                               aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        
                        <!-- Last Page -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" 
                               href="?page=<?= $totalPages ?>&view=<?= $viewType ?><?= !empty($selectedLab) ? '&lab='.$selectedLab : '' ?><?= !empty($selectedPurpose) ? '&purpose='.$selectedPurpose : '' ?>"
                               aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php else: ?>
            <div class="empty-state text-center py-5">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4 class="fw-bold">No feedback found</h4>
                <p class="text-muted">Try adjusting your filters or check back later</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Toggle between censored and original feedback text (only for flagged feedback)
    document.querySelectorAll('.toggle-feedback').forEach(button => {
        button.addEventListener('click', function () {
            const parent = this.closest('div');
            const censored = parent.querySelector('.censored-text');
            const original = parent.querySelector('.original-text');

            if (original.classList.contains('d-none')) {
                censored.classList.add('d-none');
                original.classList.remove('d-none');
                this.textContent = "Show Censored";
            } else {
                original.classList.add('d-none');
                censored.classList.remove('d-none');
                this.textContent = "Show Original";
            }
        });
    });

    // Close notifications when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notificationDropdown');
        const bell = document.querySelector('.notification-bell');
        
        if (!bell.contains(e.target) && dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        }
    });

    // Notification toggle function
    function toggleNotifications() {
        let dropdown = document.getElementById("notificationDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        
        // Close when clicking outside
        if (dropdown.style.display === "block") {
            document.addEventListener('click', function closeDropdown(e) {
                if (!dropdown.contains(e.target) && e.target.className !== 'notification-bell') {
                    dropdown.style.display = "none";
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }
    }
    </script>
</body>
</html>

<?php $conn->close(); ?>

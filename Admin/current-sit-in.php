<?php
include 'header.php';
$pageTitle = "Student Sit-In Management";

// Search functionality
$studentData = null;
$remaining_session = 0;
$showModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = trim($_POST['search']);

    if (!empty($searchTerm)) {
        $stmt = $conn->prepare("SELECT idno, firstname, middlename, lastname, email, course, yearlevel FROM student WHERE idno = ?");
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $studentData = $result->fetch_assoc();

        if ($studentData) {
            $sessionStmt = $conn->prepare("SELECT remaining_session FROM student_session WHERE idno = ?");
            $sessionStmt->bind_param("s", $searchTerm);
            $sessionStmt->execute();
            $sessionResult = $sessionStmt->get_result();
            $sessionRow = $sessionResult->fetch_assoc();
            $remaining_session = $sessionRow ? $sessionRow['remaining_session'] : 0;
            $showModal = true;
        } else {
            echo "<script>alert('Student not found.');</script>";
        }
    } else {
        echo "<script>alert('Please enter an ID number to search.');</script>";
    }
}

// Pagination setup for current sit-ins
$limit = 10; // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total pending sit-ins
$totalResult = $conn->query("SELECT COUNT(*) as total FROM student_sitin WHERE status = 'Pending'");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated pending sit-ins
$sql = "SELECT ss.sit_in_id, ss.idno, s.firstname, s.middlename, s.lastname, 
               ss.purpose, ss.lab, ss.`sit-login`, ss.`sit-logout`
        FROM student_sitin ss
        INNER JOIN student s ON ss.idno = s.idno
        WHERE ss.status = 'Pending'
        ORDER BY ss.`sit-login` DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
?>

<style>
/* Clean, minimalist styling */
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 15px;
}

/* Simplified search bar */
.search-container {
    margin-bottom: 30px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.search-form {
    display: flex;
    gap: 10px;
}

.search-input {
    flex: 1;
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-btn {
    padding: 8px 20px;
    background-color:#4f46e5;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s;
}

.search-btn:hover {
    background-color: #1a252f;
}

/* Table styling */
.table-container {
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.table th {
    background-color:  #3f51b5;;
    color: white;
    padding: 12px 15px;
    text-align: left;
}

.table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover {
    background-color: ccccccccccccccc;
}

/* Status badge */
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

/* Action buttons */
.btn-action {
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 4px;
    background-color: #2c3e50;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-action:hover {
    background-color:#7c3aed;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 5px;
}

.page-btn {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}

.page-btn:hover:not(.disabled) {
    background-color: #f1f1f1;
}

.page-btn.disabled {
    color: #aaa;
    cursor: not-allowed;
}

/* Modal styling */
.modal-content {
    border-radius: 6px;
    border: none;
}

.modal-header {
    background-color: #7c3aed;
    color: white;
    padding: 15px;
    border-bottom: none;
}

.modal-title {
    font-size: 18px;
    font-weight: 500;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
}

.modal-footer {
    border-top: 1px solid #eee;
    padding: 15px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-secondary {
    background-color: #95a5a6;
    color: white;
}

.btn-primary {
    background-color:#7c3aed;
    color: white;
}

/* Empty state */
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
</style>

<div class="container">
    <!-- Simplified Search Section -->
    <div class="search-container">
        <form method="POST" action="" class="search-form">
            <input type="text" id="search" name="search" class="search-input" 
                   placeholder="Enter student ID number" required>
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Current Sit-In Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 style="font-size: 18px; font-weight: 500; color: #2c3e50;">
            Current Sit-In Requests
        </h2>
        <span class="badge bg-warning">
            <?= $totalRows ?> Pending
        </span>
    </div>

    <!-- Current Sit-In Table -->
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
                        <th class="text-center">Action</th>
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
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td><?= htmlspecialchars($row['lab']) ?></td>
                            <td class="text-center"><?= date("M d, Y", strtotime($row['sit-login'])) ?></td>
                            <td class="text-center"><?= date("h:i A", strtotime($row['sit-login'])) ?></td>
                            <td class="text-center">
                            <form action="process_sit_logout.php" method="POST">
                                    <input type="hidden" name="sit_in_id" value="<?= $row['sit_in_id'] ?>">
                                    <button type="submit" class="btn-action" 
                                            onclick="return confirm('Timeout this student?');">
                                        <i class="fas fa-sign-out-alt"></i> Timeout
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <h5 class="text-muted">No pending sit-ins found</h5>
                                <p class="text-muted small">Students will appear here when they check in</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <a href="?page=<?= max(1, $page - 1); ?>" 
           class="page-btn <?= $page <= 1 ? 'disabled' : ''; ?>">
            &laquo; Prev
        </a>
        <span class="page-btn" style="background-color: #f1f1f1;">
            <?= $page ?> of <?= $totalPages ?>
        </span>
        <a href="?page=<?= min($totalPages, $page + 1); ?>" 
           class="page-btn <?= $page >= $totalPages ? 'disabled' : ''; ?>">
            Next &raquo;
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Sit-In Modal -->
<div class="modal fade" id="sitInModal" tabindex="-1" aria-labelledby="sitInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Sit-In</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="process_sit_in.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="idno" value="<?= $studentData ? htmlspecialchars($studentData['idno']) : '' ?>">

                    <div class="form-group">
                        <label>Student</label>
                        <input type="text" class="form-control" value="<?= $studentData ? htmlspecialchars($studentData['firstname'] . ' ' . $studentData['lastname']) : '' ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Purpose</label>
                        <select name="purpose" class="form-select" required>
                            <option value="" disabled selected>Select Purpose</option>
                            <option value="C Programming">C Programming</option>
                            <option value="Java Programming">Java Programming</option>
                            <option value="C# Programming">C# Programming</option>
                            <option value="System Integration & Architecture">System Integration & Architecture</option>
                            <option value="Embeded Systems & IoT">Embeded Systems & IoT</option>
                            <option value="Digital Logic & Design">Digital Logic & Design</option>
                            <option value="Computer Application">Computer Application</option>
                            <option value="Database">Database</option>
                            <option value="Project Management">Project Management</option>
                            <option value="Python Programming">Python Programming</option>
                            <option value="Mobile Application">Mobile Application</option>
                            <option value="Web Design">Web Design</option>
                            <option value="Php Programming">Php Programming</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Lab</label>
                        <select name="lab" class="form-select" required>
                            <option value="" disabled selected>Select Laboratory</option>
                            <option value="Lab 524">Lab 524</option>
                            <option value="Lab 526">Lab 526</option>
                            <option value="Lab 528">Lab 528</option>
                            <option value="Lab 530">Lab 530</option>
                            <option value="Lab 542">Lab 542</option>
                            <option value="Lab 544">Lab 544</option>
                            <option value="Lab 517">Lab 517</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Remaining Sessions</label>
                        <input type="text" class="form-control" value="<?= $studentData ? $remaining_session : '' ?>" readonly>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Sit-In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto-trigger modal if search successful -->
<?php if ($showModal): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var sitInModal = new bootstrap.Modal(document.getElementById('sitInModal'));
        sitInModal.show();
    });
</script>
<?php endif; ?>

<?php $conn->close(); ?>
<?php
session_start();
$pageTitle = "List of Students";
include 'header.php'; 
include '../student/connection.php';

// Handle new student form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $idno = $_POST['idno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash("123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO student (idno, firstname, lastname, middlename, course, yearlevel, email, username, password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $idno, $firstname, $lastname, $middlename, $course, $yearlevel, $email, $username, $password);

    if ($stmt->execute()) {
        $sessionStmt = $conn->prepare("INSERT INTO student_session (idno, remaining_session) VALUES (?, 30)");
        $sessionStmt->bind_param("s", $idno);

        if ($sessionStmt->execute()) {
            echo "<script>alert('Student added successfully with 30 sessions!'); window.location.href='List-of-student.php';</script>";
        } else {
            echo "<script>alert('Student added, but failed to assign sessions. Error: " . $conn->error . "');</script>";
        }

        $sessionStmt->close();
    } else {
        echo "<script>alert('Error adding student. Error: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'lastname';
$allowedSorts = ['lastname', 'course', 'yearlevel'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'lastname';
}

// SEARCH FUNCTIONALITY
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = "WHERE (idno LIKE '%$search%' OR 
                          firstname LIKE '%$search%' OR 
                          lastname LIKE '%$search%' OR 
                          middlename LIKE '%$search%' OR 
                          course LIKE '%$search%' OR 
                          yearlevel LIKE '%$search%' OR 
                          email LIKE '%$search%' OR 
                          username LIKE '%$search%')";
}

$totalResult = $conn->query("SELECT COUNT(*) as total FROM student $searchCondition");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT idno, firstname, lastname, middlename, course, yearlevel, email, username, profile_picture 
        FROM student 
        $searchCondition
        ORDER BY $sort ASC 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if (isset($_GET['delete_id'])) {
    $idno = $_GET['delete_id'];
    // Delete related feedback first to satisfy foreign key constraint
    $conn->query("DELETE FROM feedback WHERE sit_in_id IN (SELECT sit_in_id FROM student_sitin WHERE idno = '$idno')");
    // Delete related sit-ins
    $conn->query("DELETE FROM student_sitin WHERE idno = '$idno'");
    $conn->query("DELETE FROM student_session WHERE idno = '$idno'");
    $deleteStmt = $conn->prepare("DELETE FROM student WHERE idno = ?");
    $deleteStmt->bind_param("s", $idno);

    if ($deleteStmt->execute()) {
        echo "<script>alert('Student deleted successfully!'); window.location.href='List-of-student.php';</script>";
    } else {
        echo "<script>alert('Error deleting student.');</script>";
    }
}

// Handle reset all sessions request
if (isset($_POST['reset_all_sessions'])) {
    $conn->query("UPDATE student_session SET remaining_session = 30, total_points_earned = 0");
    echo "<script>alert('All student sessions have been reset to 30 and points reset to 0!'); window.location.href='List-of-student.php';</script>";
}

// Handle reset session for a specific student
if (isset($_POST['reset_session'])) {
    $idno = $_POST['idno'];
    $conn->query("UPDATE student_session SET remaining_session = 30, total_points_earned = 0 WHERE idno = '$idno'");
    echo "<script>alert('Session for student ID $idno has been reset to 30 and points reset to 0!'); window.location.href='List-of-student.php';</script>";
}
?>

<style>
/* Consistent styling with current-sit-in.php */
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 15px;
}

/* Header styling */
h2 {
    font-size: 18px;
    font-weight: 500;
    color: #303f9f;
    margin-bottom: 20px;
}

/* Search and action container */
.actions-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.search-container {
    position: relative;
    flex-grow: 1;
    max-width: 500px;
}

.search-input {
    width: 100%;
    padding: 8px 15px;
    padding-right: 35px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color:#303f9f;
    cursor: pointer;
}

.search-clear:hover {
    color:#303f9f;
}

/* Dropdown styling */
.dropdown-toggle::after {
    display: none;
}

.dropdown-menu {
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.dropdown-item {
    padding: 8px 15px;
    font-size: 14px;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Button styling */
.btn {
    padding: 8px 15px;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.2s;
}

.btn-info {
    background-color: #303f9f;
    border-color: #3498db;
    color: white;
}

.btn-info:hover {
    background-color: #303f9f;
    border-color: #2980b9;
}

.btn-warning {
    background-color:  #CB9DF0;
    border-color: #303f9f;
    color: white;
}

.btn-warning:hover {
    background-color:  #CB9DF0;
    border-color:  #303f9f;
}

.btn-outline-secondary {
    border-color: #303f9f;
    color:#303f9f;
}

.btn-outline-secondary:hover {
    background-color: #303f9f;
    border-color: #303f9f;
}

/* Table styling */
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
    background-color:#303f9f;
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

/* Profile picture styling */
.rounded-circle {
    border: 2px solid #ecf0f1;
}

/* Modal styling */
.modal-header {
    background-color: #303f9f;
    color: white;
    padding: 15px;
}

.modal-title {
    font-size: 16px;
    font-weight: 500;
}

.modal-body {
    padding: 20px;
}

.form-label {
    font-weight: 500;
    color: #303f9f;
}

.form-control, .form-select {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .actions-container {
        flex-direction: column;
    }
    
    .search-container {
        max-width: 100%;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}

/* Empty state styling */
.text-muted {
    color: #7f8c8d !important;
}

/* Badge styling */
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.bg-warning {
    background-color: #f39c12;
}
</style>

<div class="container">
    <h2>Registered Students (<?= $totalRows; ?> Total)</h2>

    <div class="actions-container">
        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" action="" class="position-relative">
                <input type="text" 
                       class="form-control search-input" 
                       name="search" 
                       placeholder="Search students..." 
                       value="<?= htmlspecialchars($search) ?>"
                       aria-label="Search students">
                <input type="hidden" name="page" value="1">
                <input type="hidden" name="sort" value="<?= $sort ?>">
                <?php if (!empty($search)): ?>
                    <button type="button" class="search-clear" onclick="clearSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </form>
        </div>

         <!-- Actions Dropdown -->
    <div class="dropdown">
        <button class="btn btn-info dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            ➕ Actions
        </button>
        <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
            <!-- Add New Student -->
            <li>
                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </button>
                </li>
            
            <!-- Reset All Sessions -->
            <li>
                <form method="POST" class="dropdown-item">
                    <button type="submit" name="reset_all_sessions" class="btn btn-warning w-100">🔄 Reset All Sessions</button>
                </form>
            </li>
            
            <!-- Sorting Section -->
            <li>
                <h6 class="dropdown-header text-center text-info">Sort By:</h6>
                <form method="GET" class="dropdown-item">
                    <select name="sort" id="sort" class="form-select w-100" onchange="this.form.submit()">
                        <option value="lastname" <?= $sort == 'lastname' ? 'selected' : ''; ?>>Last Name (A-Z)</option>
                        <option value="course" <?= $sort == 'course' ? 'selected' : ''; ?>>Course (A-Z)</option>
                        <option value="yearlevel" <?= $sort == 'yearlevel' ? 'selected' : ''; ?>>Year Level (Ascending)</option>
                    </select>
                    <input type="hidden" name="page" value="<?= $page; ?>">
                    <?php if (!empty($search)): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                </form>
            </li>
        </ul>
    </div>
</div>
        
    <div class="table-container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Profile</th>
                        <th>Full Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['idno']); ?></td>
                                <td>
                                    <img src="../student/uploads/<?= !empty($row['profile_picture']) ? $row['profile_picture'] : 'default.png'; ?>" 
                                         class="rounded-circle" 
                                         width="50" 
                                         height="50"
                                         alt="<?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?>">
                                </td>
                                <td><?= htmlspecialchars($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']); ?></td>
                                <td><?= htmlspecialchars($row['course']); ?></td>
                                <td><?= htmlspecialchars($row['yearlevel']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td class="position-relative">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="edit-student.php?id=<?= $row['idno']; ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="List-of-student.php?delete_id=<?= $row['idno']; ?>" onclick="return confirm('Are you sure you want to delete this student?');">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="idno" value="<?= $row['idno']; ?>">
                                                    <button class="dropdown-item text-warning" type="submit" name="reset_session">
                                                        <i class="fas fa-sync"></i> Reset Session
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No students found</h5>
                                    <p class="text-muted small"><?= !empty($search) ? 'Try a different search term' : 'Add students to get started'; ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <?php if ($totalRows > 0): ?>
                <span class="text-muted small">
                    Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalRows) ?> of <?= $totalRows ?> entries
                </span>
            <?php endif; ?>
        </div>
        <div class="pagination">
            <a href="?page=<?= max(1, $page - 1); ?>&sort=<?= $sort; ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
               class="btn btn-outline-secondary <?= $page <= 1 ? 'disabled' : ''; ?>">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
            <span class="px-3 d-flex align-items-center">
                Page <?= $page ?> of <?= $totalPages ?>
            </span>
            <a href="?page=<?= min($totalPages, $page + 1); ?>&sort=<?= $sort; ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
               class="btn btn-outline-secondary <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel"><i class="fas fa-user-plus"></i> Add New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form method="POST">
                    <!-- Student ID -->
                    <div class="mb-3">
                        <label for="idno" class="form-label">Student ID</label>
                        <input type="text" class="form-control" name="idno" id="idno" placeholder="Enter ID number" required>
                    </div>

                    <!-- Name Fields -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middlename" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middlename" id="middlename" placeholder="Middle Name">
                        </div>
                    </div>

                    <!-- Course & Year Level -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-select" name="course" id="course" required>
                                <option value="" disabled selected>Select Course</option>
                                <option value="BSIT">BSIT</option>
                                <option value="BSCpE">BSCpE</option>
                                <option value="BSBA">BSBA</option>
                                <option value="BSCS">BSCS</option>
                                <option value="BSEd">BSEd</option>
                                <option value="BSHM">BSHM</option>
                                <option value="BSN">BSB</option>
                                <option value="BEEd">BEEd</option>
                                <option value="BSCE">BSCE</option>
                                <option value="BSME">BSME</option>
                                <option value="BSCRIM">BSCRIM</option>
                                <option value="BSCA">BSCA</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="yearlevel" class="form-label">Year Level</label>
                            <select class="form-select" name="yearlevel" id="yearlevel" required>
                                <option value="" disabled selected>Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email & Username -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mt-4">
                        <button type="submit" name="add_student" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Clear search and reload page
function clearSearch() {
    window.location.href = window.location.pathname + '?page=1&sort=<?= $sort ?>';
}

// Optional: Submit form on typing with debounce
document.querySelector('[name="search"]')?.addEventListener('input', function() {
    clearTimeout(this.searchTimer);
    this.searchTimer = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>


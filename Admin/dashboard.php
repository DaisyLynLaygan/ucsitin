<?php
include 'header.php';
$pageTitle = "Dashboard";
include '../student/connection.php';

// Fetch counts
$feedbackCount = $conn->query("SELECT COUNT(*) as total FROM feedback")->fetch_assoc()['total'];
$sitinCount = $conn->query("SELECT COUNT(*) as total FROM student_sitin WHERE status = 'pending'")->fetch_assoc()['total'];


// Check if we're viewing all announcements
$viewAll = isset($_GET['view_all']) && $_GET['view_all'] == 'true';

// Get announcements based on view mode
$daysLimit = 7;
$announceQuery = "SELECT * FROM announcements 
                 " . (!$viewAll ? "WHERE created_at >= NOW() - INTERVAL ? DAY " : "") . "
                 ORDER BY created_at DESC 
                 " . (!$viewAll ? "LIMIT 5" : "");
$announceStmt = $conn->prepare($announceQuery);
if (!$viewAll) {
    $announceStmt->bind_param("i", $daysLimit);
}
$announceStmt->execute();
$announceResult = $announceStmt->get_result();
$latestAnnouncements = $announceResult->fetch_all(MYSQLI_ASSOC);

// Query to get top labs by sit-ins
$topLabsQuery = "SELECT lab, COUNT(*) as total_sitins FROM student_sitin 
                WHERE `sit-logout` IS NOT NULL AND status = 'Completed'
                GROUP BY lab 
                ORDER BY total_sitins DESC 
                LIMIT 5";
$topLabsResult = $conn->query($topLabsQuery);
$topLabs = $topLabsResult->fetch_all(MYSQLI_ASSOC);

$labNames = [];
$labCounts = [];
foreach ($topLabs as $lab) {
    $labNames[] = $lab['lab'];
    $labCounts[] = $lab['total_sitins'];
}
// Handle announcement actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_announcement']) || isset($_POST['edit_announcement'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        
        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/announcements/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExt = pathinfo($_FILES['announcement_image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('announce_') . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            // Validate image file
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($fileExt), $allowedTypes)) {
                if (move_uploaded_file($_FILES['announcement_image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                    
                    // If editing, delete old image if exists
                    if (isset($_POST['edit_announcement']) && !empty($_POST['old_image'])) {
                        if (file_exists($_POST['old_image'])) {
                            unlink($_POST['old_image']);
                        }
                    }
                }
            }
        } elseif (isset($_POST['old_image']) && !empty($_POST['old_image'])) {
            $imagePath = $_POST['old_image'];
        }
        
        if (!empty($title) && !empty($content)) {
            if (isset($_POST['create_announcement'])) {
                $stmt = $conn->prepare("INSERT INTO announcements (title, content, image_path, visible_to_students) VALUES (?, ?, ?, 1)");
                $stmt->bind_param("sss", $title, $content, $imagePath);
            } else {
                $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, image_path = ? WHERE id = ?");
                $stmt->bind_param("sssi", $title, $content, $imagePath, $id);
            }
            
            if ($stmt->execute()) {
                $action = isset($_POST['create_announcement']) ? 'created' : 'updated';
                echo "<script>alert('Announcement {$action} successfully!'); window.location.href='dashboard.php';</script>";
            } else {
                echo "<script>alert('Error processing announcement.');</script>";
            }
        }
    }
}

if (isset($_GET['action'])) {
    $id = $_GET['id'];
    
    if ($_GET['action'] === 'delete') {
        // Delete associated image if exists
        $result = $conn->query("SELECT image_path FROM announcements WHERE id = $id");
        if ($result && $row = $result->fetch_assoc() && !empty($row['image_path'])) {
            if (file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }
        }
        
        $conn->query("DELETE FROM announcements WHERE id = $id");
        echo "<script>alert('Announcement deleted.'); window.location.href='dashboard.php';</script>";
    } elseif ($_GET['action'] === 'toggle') {
        $conn->query("UPDATE announcements SET visible_to_students = NOT visible_to_students WHERE id = $id");
        header("Location: dashboard.php");
        exit();
    } elseif ($_GET['action'] === 'delete_image') {
        $result = $conn->query("SELECT image_path FROM announcements WHERE id = $id");
        if ($result && $row = $result->fetch_assoc() && !empty($row['image_path'])) {
            if (file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }
            $conn->query("UPDATE announcements SET image_path = NULL WHERE id = $id");
            echo "<script>alert('Image deleted.'); window.location.href='dashboard.php';</script>";
        }
    }
}

// Query to get top labs by sit-ins
$topLabsQuery = "SELECT lab, COUNT(*) as total_sitins FROM student_sitin 
                WHERE `sit-logout` IS NOT NULL AND status = 'Completed'
                GROUP BY lab 
                ORDER BY total_sitins DESC 
                LIMIT 5";
$topLabsResult = $conn->query($topLabsQuery);
$topLabs = $topLabsResult->fetch_all(MYSQLI_ASSOC);

$labNames = [];
$labCounts = [];
foreach ($topLabs as $lab) {
    $labNames[] = $lab['lab'];
    $labCounts[] = $lab['total_sitins'];
}
?>

<style>
body {
    background: #f8fafc;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.dashboard-wrapper {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 25px;
    margin-top: 20px;
    padding: 0 25px 40px;
    flex: 1;
}

.main-content {
    flex: 1;
    min-width: 300px;
}

/* Compact Cards Section */
.compact-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
    margin-bottom: 25px;
}

.compact-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    flex: 1;
    min-width: 250px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.compact-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
}

.compact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.compact-card h4 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}

.compact-card h4 i {
    color: #4f46e5;
}

.compact-stat {
    font-size: 42px;
    font-weight: 700;
    color: #1e293b;
    margin: 10px 0;
    background: linear-gradient(45deg, #4f46e5, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.compact-label {
    font-size: 14px;
    color: #64748b;
    margin-top: 5px;
}

/* Leaderboard Container */
.leaderboard-container {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-top: 25px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.leaderboard-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.leaderboard-title {
    font-size: 24px;
    color: #1e293b;
    margin-bottom: 25px;
    text-align: center;
    position: relative;
    padding-bottom: 15px;
    font-weight: 700;
}

.leaderboard-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    border-radius: 2px;
}

/* Announcements Panel */
.announcements-panel {
    width: 100%;
    max-width: 400px;
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    height: auto;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
    border: 1px solid rgba(0,0,0,0.05);
}

.announcements-header {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.announcements-header h3 {
    font-size: 22px;
    margin: 0;
    color:#303f9f;
    display: flex;
    align-items: center;
    gap: 10px;
}

.create-announcement-btn {
    background: #4f46e5;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.create-announcement-btn:hover {
    background: #4338ca;
    transform: translateY(-2px);
}

/* Announcement Items */
.announcement-item {
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.announcement-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.announcement-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 18px;
    margin-bottom: 10px;
}

.announcement-date {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.announcement-excerpt {
    font-size: 15px;
    color: #334155;
    line-height: 1.6;
    margin-bottom: 15px;
}

/* Modal Styles */
.modal-content {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.modal-header {
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.modal-title {
    font-size: 24px;
    color: #1e293b;
    font-weight: 600;
}

.form-control {
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    padding: 12px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
}

.btn-primary {
    background: #4f46e5;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #4338ca;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #64748b;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #475569;
    transform: translateY(-2px);
}

/* Chart Container */
.chart-container {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-top: 25px;
    border: 1px solid rgba(0,0,0,0.05);
}

/* Leaderboard Items */
.leaderboard-item {
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.leaderboard-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.leaderboard-rank {
    color: #4f46e5;
    font-weight: 700;
}

.leaderboard-name {
    color: #1e293b;
    font-weight: 600;
}

.leaderboard-id {
    color: #64748b;
}

.leaderboard-stats {
    color: #334155;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-wrapper {
        padding: 15px;
    }
    
    .compact-card {
        min-width: 100%;
    }
    
    .announcements-panel {
        max-width: 100%;
    }
}

/* --- Top Students Leaderboard Custom Styles --- */
.leaderboard-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.leaderboard-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 18px 28px;
    border: 2px solid transparent;
    transition: box-shadow 0.2s, border 0.2s;
    position: relative;
}
.leaderboard-item .left {
    display: flex;
    align-items: center;
    gap: 18px;
}
.leaderboard-rank {
    font-size: 1.5rem;
    font-weight: 700;
    width: 36px;
    text-align: center;
    color: #1e293b;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.leaderboard-medal {
    font-size: 1.2rem;
    margin-top: 2px;
}
.leaderboard-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e0e7ef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: 600;
    color: #4f46e5;
    overflow: hidden;
}
.leaderboard-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.leaderboard-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.leaderboard-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    text-transform: lowercase;
}
.leaderboard-id {
    font-size: 0.95rem;
    color: #64748b;
}
.leaderboard-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    min-width: 90px;
}
.leaderboard-points {
    color: #f59e42;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 5px;
}
.leaderboard-sessions {
    color: #3b82f6;
    font-weight: 500;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 5px;
}
/* Top 3 backgrounds */
.leaderboard-item.rank-1 {
    background: linear-gradient(90deg, #CB9DF0 50%);
    border-color: #303f9f;
}
.leaderboard-item.rank-2 {
    background: linear-gradient(90deg, #CB9DF0  50%);
    border-color: #bcd0ff;
}
.leaderboard-item.rank-3 {
    background: linear-gradient(90deg, #CB9DF0   100%);
    border-color: #303f9f;
}

@media (max-width: 600px) {
    .leaderboard-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 16px 10px;
        gap: 10px;
    }
    .leaderboard-stats {
        align-items: flex-start;
    }
}

.view-mode-toggle {
    display: flex;
    gap: 10px;
}

.view-mode-btn {
    color: #303f9f;
    font-weight: 500;
    transition: color 0.2s;
}

.view-mode-btn.active {
    color:#303f9f !important;
}
</style>

<div class="dashboard-wrapper">
    <div class="main-content">
        <!-- Compact Cards Section -->
        <div class="compact-cards">
            <div class="compact-card">
                <h4><i class="fas fa-comment-alt"></i> Feedback Reports</h4>
                <div class="compact-stat"><?= $feedbackCount ?></div>
                <div class="compact-label">New feedback submissions</div>
                <a href="feedback-report.php" class="btn btn-primary" style="margin-top: 10px; display: inline-block;">View Reports</a>
            </div>

            <div class="compact-card">
                <h4><i class="fas fa-users"></i> Current Sit-In </h4>
                <div class="compact-stat"><?= $sitinCount ?></div>
                <div class="compact-label"></div>
                <a href="current-sit-in.php" class="btn btn-primary" style="margin-top: 10px; display: inline-block;">Manage Sit-Ins</a>
            </div>
        </div>

        <!-- Replace the Leaderboard Section in dashboard.php with this code -->

<!-- Top Students by Points & Sessions -->
<div class="leaderboard-container">
    <h3 class="leaderboard-title"><i class="fas fa-trophy"></i> Top Students</h3>
    <div class="leaderboard-list">
        <?php 
        // Query to get top students by points first, then by completed sessions
        $topStudentsQuery = "SELECT 
            s.idno,
            CONCAT(s.firstname, ' ', s.lastname) AS fullname,
            s.profile_picture,
            COALESCE(ss.total_points_earned, 0) AS total_points,
            COUNT(sit.sit_in_id) AS total_sessions
        FROM student s
        LEFT JOIN student_sitin sit ON s.idno = sit.idno AND sit.status = 'Completed'
        LEFT JOIN student_session ss ON s.idno = ss.idno
        GROUP BY s.idno
        ORDER BY 
            total_points DESC,
            total_sessions DESC
        LIMIT 5";
        
        $topStudentsResult = $conn->query($topStudentsQuery);
        $topStudents = $topStudentsResult ? $topStudentsResult->fetch_all(MYSQLI_ASSOC) : [];
        $medals = ['🥇','🥈','🥉'];
        $medalColors = ['#ffe066','#bcd0ff','#ffd6a0'];
        ?>
        
        <?php if (!empty($topStudents)): ?>
            <?php foreach ($topStudents as $index => $student): ?>
                <div class="leaderboard-item rank-<?= $index+1 ?>">
                    <div class="left">
                        <div class="leaderboard-rank">
                            <?= $index+1 ?>
                            <?php if ($index < 3): ?>
                                <span class="leaderboard-medal" style="color:<?= $medalColors[$index] ?>;"> <?= $medals[$index] ?> </span>
                            <?php endif; ?>
                        </div>
                        <div class="leaderboard-avatar">
                        <?php if (!empty($student['profile_picture'])): ?>
                            <img src="../student/uploads/<?= htmlspecialchars($student['profile_picture']) ?>" 
                                alt="<?= htmlspecialchars($student['fullname']) ?>" 
                                class="leaderboard-avatar-img"
                                onerror="this.onerror=null; this.src='../student/uploads/default.png'">
                        <?php else: ?>
                            <div class="avatar-initial"><?= substr($student['fullname'], 0, 1) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="leaderboard-info">
                            <div class="leaderboard-name"><?= htmlspecialchars($student['fullname']) ?></div>
                            <div class="leaderboard-id">ID: <?= htmlspecialchars($student['idno']) ?></div>
                        </div>
                    </div>
                    <div class="leaderboard-stats">
                        <div class="leaderboard-points">
                            <i class="fas fa-star"></i> <?= $student['total_points'] ?> pts
                        </div>
                        <div class="leaderboard-sessions">
                            <i class="fas fa-gift"></i> <?= $student['total_sessions'] ?> sessions
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="leaderboard-item">
                <p>No student data available yet</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Top Labs by Sit-Ins Section -->
<div class="leaderboard-container" style="margin-top: 20px;">
    <h3 class="leaderboard-title"><i class="fas fa-flask"></i> Top Labs by Sit-Ins</h3>
    <div class="chart-container" style="height: 250px; padding: 15px;">
        <canvas id="topLabsChart"></canvas>
    </div>
</div>
</div>
   

    <!-- Announcements Panel -->
    <div class="announcements-panel">
        <div class="announcements-header">
            <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
            <button class="create-announcement-btn" id="createAnnouncementBtn">
                <i class="fas fa-plus"></i> New
            </button>
        </div>
        <div class="view-mode-toggle">
            <a href="dashboard.php" class="view-mode-btn <?= !$viewAll ? 'active' : '' ?>"><i class="fas fa-clock"></i></a>
            <a href="dashboard.php?view_all=true" class="view-mode-btn <?= $viewAll ? 'active' : '' ?>">View All</a>
        </div>
        <?php if (!empty($latestAnnouncements)): ?>
            <div class="announcements-list">
                <?php foreach ($latestAnnouncements as $announcement): ?>
                    <div class="announcement-item <?= $announcement['visible_to_students'] ? '' : 'hidden-announcement' ?>">
                        <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                        <div class="announcement-date">
                            <i class="far fa-clock"></i> <?= date('M j, Y g:i a', strtotime($announcement['created_at'])) ?>
                            <?= $announcement['visible_to_students'] ? '<span style="color:green;"> (Visible)</span>' : '<span style="color:red;"> (Hidden)</span>' ?>
                        </div>
                        <div class="announcement-excerpt"><?= htmlspecialchars(substr($announcement['content'], 0, 120)) ?>...</div>
                        
                        <?php if (!empty($announcement['image_path'])): ?>
                            <img src="/uploads/<?= htmlspecialchars($announcement['image_path']) ?>" class="announcement-image-preview" alt="Announcement Image">
                        <?php endif; ?>
                        
                        <div class="announcement-actions">
                            <button class="announcement-action edit-btn" data-id="<?= $announcement['id'] ?>" data-title="<?= htmlspecialchars($announcement['title']) ?>" data-content="<?= htmlspecialchars($announcement['content']) ?>" data-image="/uploads/<?= htmlspecialchars($announcement['image_path'] ?? '') ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?action=toggle&id=<?= $announcement['id'] ?>" class="announcement-action">
                                <i class="fas fa-eye<?= $announcement['visible_to_students'] ? '' : '-slash' ?>"></i>
                            </a>
                            <a href="?action=delete&id=<?= $announcement['id'] ?>" class="announcement-action" onclick="return confirm('Delete this announcement?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="announcement-item">
                <p class="text-muted">No announcements found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Announcement Modal -->
<div id="announcementModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Create New Announcement</h3>
            <button class="close-btn" id="closeModal">&times;</button>
        </div>
        <form method="POST" id="announcementForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="announcementId">
            <input type="hidden" name="old_image" id="oldImage">
            <div class="modal-body">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="announcement_image">Image (optional)</label>
                    <input type="file" class="form-control" id="announcement_image" name="announcement_image" accept="image/*">
                    <small class="text-muted">Max size: 2MB. Allowed types: JPG, PNG, GIF</small>
                </div>
                <div class="image-preview-container" id="imagePreviewContainer" style="display:none;">
                    <h5>Current Image:</h5>
                    <img id="currentImagePreview" class="current-image">
                    <button type="button" class="remove-image-btn" id="removeImageBtn">Remove Image</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" name="create_announcement" id="submitBtn">Post Announcement</button>
            </div>
        </form>
    </div>
</div>

<script>
   
// Modal handling
const modal = document.getElementById('announcementModal');
const createBtn = document.getElementById('createAnnouncementBtn');
const closeBtn = document.getElementById('closeModal');
const cancelBtn = document.getElementById('cancelBtn');
const editButtons = document.querySelectorAll('.edit-btn');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const currentImagePreview = document.getElementById('currentImagePreview');
const removeImageBtn = document.getElementById('removeImageBtn');
const oldImageInput = document.getElementById('oldImage');

createBtn.addEventListener('click', () => {
    document.getElementById('modalTitle').textContent = 'Create New Announcement';
    document.getElementById('announcementForm').reset();
    document.getElementById('announcementId').value = '';
    oldImageInput.value = '';
    imagePreviewContainer.style.display = 'none';
    document.getElementById('submitBtn').name = 'create_announcement';
    document.getElementById('submitBtn').textContent = 'Post Announcement';
    modal.style.display = 'block';
});

editButtons.forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('modalTitle').textContent = 'Edit Announcement';
        document.getElementById('announcementId').value = button.dataset.id;
        document.getElementById('title').value = button.dataset.title;
        document.getElementById('content').value = button.dataset.content;
        document.getElementById('submitBtn').name = 'edit_announcement';
        document.getElementById('submitBtn').textContent = 'Update Announcement';
        
        // Handle image preview for editing
        if (button.dataset.image) {
            oldImageInput.value = button.dataset.image;
            currentImagePreview.src = button.dataset.image;
            imagePreviewContainer.style.display = 'block';
        } else {
            oldImageInput.value = '';
            imagePreviewContainer.style.display = 'none';
        }
        
        modal.style.display = 'block';
    });
});

removeImageBtn.addEventListener('click', () => {
    if (confirm('Are you sure you want to remove this image?')) {
        const announcementId = document.getElementById('announcementId').value;
        if (announcementId) {
            // If editing an existing announcement with image, redirect to delete the image
            window.location.href = `?action=delete_image&id=${announcementId}`;
        } else {
            // If creating new announcement, just clear the preview
            oldImageInput.value = '';
            currentImagePreview.src = '';
            imagePreviewContainer.style.display = 'none';
            document.getElementById('announcement_image').value = '';
        }
    }
});

closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

cancelBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Preview image when selected
document.getElementById('announcement_image').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            currentImagePreview.src = e.target.result;
            imagePreviewContainer.style.display = 'block';
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Auto-refresh every 30 seconds
function refreshDashboard() {
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const updatedNotifications = doc.getElementById('notifications')?.innerHTML;
            if (updatedNotifications) {
                document.getElementById('notifications').innerHTML = updatedNotifications;
            }
        });
}

setInterval(refreshDashboard, 30000);
// Top Labs Chart
new Chart(document.getElementById('topLabsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labNames) ?>,
        datasets: [{
            label: 'Total Sit-Ins',
            data: <?= json_encode($labCounts) ?>,
            backgroundColor: [
                'rgba(44, 62, 80, 0.7)',
                'rgba(52, 73, 94, 0.7)',
                'rgba(63, 85, 107, 0.7)',
                'rgba(74, 96, 120, 0.7)',
                'rgba(85, 107, 133, 0.7)'
            ],
            borderColor: [
                'rgba(44, 62, 80, 1)',
                'rgba(52, 73, 94, 1)',
                'rgba(63, 85, 107, 1)',
                'rgba(74, 96, 120, 1)',
                'rgba(85, 107, 133, 1)'
            ],
            borderWidth: 1,
            hoverBackgroundColor: [
                'rgba(44, 62, 80, 1)',
                'rgba(52, 73, 94, 1)',
                'rgba(63, 85, 107, 1)',
                'rgba(74, 96, 120, 1)',
                'rgba(85, 107, 133, 1)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' sit-ins';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    color: '#6c757d'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    color: '#6c757d'
                },
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>


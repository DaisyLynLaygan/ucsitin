<?php
include 'header.php';
$pageTitle = "Dashboard";
include '../student/connection.php';

// Get student data
$idno = $_SESSION['idno'];
$query = "SELECT s.firstname, s.lastname, ss.remaining_session, 
                  ss.total_points_earned
          FROM student s
          JOIN student_session ss ON s.idno = ss.idno
          WHERE s.idno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $idno);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Check if we're viewing all announcements
$viewAll = isset($_GET['view_all']) && $_GET['view_all'] == 'true';

// Get announcements based on view mode
$daysLimit = 7;
$announceQuery = "SELECT * FROM announcements 
                 WHERE visible_to_students = 1 
                 " . (!$viewAll ? "AND created_at >= NOW() - INTERVAL ? DAY " : "") . "
                 ORDER BY created_at DESC 
                 " . (!$viewAll ? "LIMIT 5" : "");
$announceStmt = $conn->prepare($announceQuery);
if (!$viewAll) {
    $announceStmt->bind_param("i", $daysLimit);
}
$announceStmt->execute();
$announceResult = $announceStmt->get_result();
$latestAnnouncements = $announceResult->fetch_all(MYSQLI_ASSOC);
?>

<style>
body {
    background: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #303f9f;
}

.dashboard-wrapper {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.main-content {
    flex: 1;
    min-width: 300px;
}

.announcements-panel {
    width: 100%;
    max-width: 350px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 20px;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-left: 4px solid #303f9f;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.stat-card h3 {
    font-size: 16px;
    color: #303f9f;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-card h3 i {
    color: #303f9f;
}

.stat-value {
    font-size: 32px;
    font-weight: 600;
    color: #303f9f;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #303f9f;
}

.stat-progress {
    height: 6px;
    background:#303f9f
    border-radius: 3px;
    margin-top: 15px;
    overflow: hidden;
}

.stat-progress-bar {
    height: 100%;
    background: #303f9f;
    border-radius: 3px;
}

.stat-status {
    font-size: 13px;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.stat-status i {
    font-size: 14px;
}

.status-success {
    color: #28a745;
}
.status-warning {
    color: #ffc107;
}
.status-danger {
    color: #dc3545;
}

/* Rules Cards */
.rules-section {
    margin-top: 20px;
}

.rules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.rule-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.rule-card-header {
    background: #2c3e50;
    color: white;
    padding: 15px 20px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rule-card-header h4 {
    margin: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rule-card-header i {
    transition: transform 0.3s;
}

.rule-card-header.collapsed i {
    transform: rotate(-90deg);
}

.rule-card-body {
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out, padding 0.3s ease;
}

.rule-card-body.expanded {
    padding: 20px;
    max-height: 1000px;
}

.rules-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.rules-list li {
    padding: 8px 0;
    border-bottom: 1px solid #f1f3f5;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.rules-list li:last-child {
    border-bottom: none;
}

.rules-list li i {
    color: #303f9f;
    margin-top: 3px;
    font-size: 14px;
}

.disciplinary-notice {
    background: #fff8e1;
    padding: 15px;
    border-radius: 6px;
    margin-top: 15px;
    border-left: 3px solid#303f9f;
}

.disciplinary-notice strong {
    color: #303f9f;
    display: block;
    margin-bottom: 8px;
}

/* Announcements */
.announcements-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f3f5;
}

.announcements-header h3 {
    margin: 0;
    font-size: 18px;
    color: #303f9f;
    display: flex;
    align-items: center;
    gap: 8px;
}

.view-toggle {
    display: flex;
    gap: 8px;
}

.view-toggle-btn {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
    border: none;
    background: #e9ecef;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s;
}

.view-toggle-btn.active {
    background:#303f9f;
    color: white;
}

.announcements-panel {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    height: auto;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.announcements-header {
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.announcements-header h3 {
    font-size: 20px;
    margin: 0;
    color:#303f9f;
}

.view-mode-toggle {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.view-mode-btn {
    padding: 6px 12px;
    border-radius: 20px;
    background:#303f9f;
    border: none;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
    text-decoration: none;
    color: #212529;
}

.view-mode-btn.active {
    background:#303f9f;
    color: white;
}

.view-mode-btn:hover {
    background: #303f9f;
}

.view-mode-btn.active:hover {
    background: #1a252f;
}

.announcements-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.announcement-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid#303f9f;
    position: relative;
    transition: all 0.2s ease;
}

.announcement-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.announcement-title {
    font-weight: 600;
    color: #303f9f;
    font-size: 16px;
    margin-bottom: 6px;
}

.announcement-date {
    font-size: 13px;
    color: #303f9f;
    margin-bottom: 8px;
}

.announcement-excerpt {
    font-size: 14px;
    color: #303f9f;
    margin-bottom: 10px;
}

.announcement-image-preview {
    max-width: 100%;
    max-height: 150px;
    margin-top: 10px;
    border-radius: 4px;
    display: block;
}

/* Modal styles for full announcement view */
.announcement-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.announcement-modal.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: white;
    border-radius: 10px;
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    transform: translateY(20px);
    transition: transform 0.3s;
}

.announcement-modal.active .modal-content {
    transform: translateY(0);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #f1f3f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    color: #303f9f;
    font-size: 20px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color:#303f9f;
    cursor: pointer;
    padding: 5px;
}

.modal-body {
    padding: 20px;
}

.modal-date {
    color: #303f9f;
    font-size: 14px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.modal-text {
    line-height: 1.6;
    color: #303f9f;
    white-space: pre-line;
}

.modal-image {
    max-width: 100%;
    max-height: 400px;
    margin: 15px 0;
    border-radius: 6px;
    display: block;
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        flex-direction: column;
    }
    
    .announcements-panel {
        max-width: 100%;
    }
}
</style>

<div class="dashboard-wrapper">
    <div class="main-content">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><i class="fas fa-clock"></i> Lab Sessions</h3>
                <div class="stat-value"><?= $student['remaining_session'] ?></div>
                <div class="stat-label">Remaining sessions</div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: <?= min(100, ($student['remaining_session'] / 10) * 100) ?>%"></div>
                </div>
                <div class="stat-status <?= $student['remaining_session'] <= 5 ? 'status-danger' : ($student['remaining_session'] <= 5 ? 'status-warning' : 'status-success') ?>">
                    <i class="fas fa-<?= $student['remaining_session'] <= 3 ? 'exclamation-circle' : ($student['remaining_session'] <= 5 ? 'info-circle' : 'check-circle') ?>"></i>
                    <?= $student['remaining_session'] <= 5 ? 'Low session count' : ($student['remaining_session'] <= 10 ? 'Getting low on sessions' : 'You\'re good to go!') ?>
                </div>
            </div>

            <div class="stat-card">
    <h3><i class="fas fa-star"></i> Reward Points</h3>
    <div class="stat-value"><?= $student['total_points_earned'] ?></div>
    <div class="stat-label">Total points earned</div>
    <div class="stat-progress">
        <div class="stat-progress-bar" style="width: <?= min(100, ($student['total_points_earned'] % 3) * 33.33) ?>%"></div>
    </div>
    <div class="stat-label"><?= $student['total_points_earned'] % 3 ?>/3 for next reward</div>
    <?php if ($student['total_points_earned'] >= 3 && $student['total_points_earned'] % 3 == 0): ?>
        <div class="stat-status status-success">
            <i class="fas fa-gift"></i> Bonus session earned!
        </div>
    <?php endif; ?>
</div>
    </div>

        <!-- Rules Section -->
        <div class="rules-section">
            <div class="rules-grid">
                <div class="rule-card">
                    <div class="rule-card-header" onclick="toggleRuleCard(this)">
                        <h4><i class="fas fa-clipboard-list"></i> Sit-In Rules</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="rule-card-body">
                        <ul class="rules-list">
                            <li><i class="fas fa-volume-mute"></i> Maintain silence, proper decorum, and discipline inside the laboratory.</li>
                            <li><i class="fas fa-gamepad"></i> Games are not allowed inside the lab.</li>
                            <li><i class="fas fa-globe"></i> Surfing the Internet is allowed only with the instructor's permission.</li>
                            <li><i class="fas fa-ban"></i> Accessing inappropriate websites is strictly prohibited.</li>
                            <li><i class="fas fa-trash-alt"></i> Deleting files or changing computer settings is a major offense.</li>
                            <li><i class="fas fa-clock"></i> Observe computer time usage; a 15-minute allowance is given for sit-ins.</li>
                            <li><i class="fas fa-utensils"></i> No chewing gum, eating, drinking, smoking, or vandalism.</li>
                        </ul>
                        <div class="disciplinary-notice">
                            <strong>DISCIPLINARY ACTION</strong>
                            <ul class="rules-list">
                                <li><i class="fas fa-exclamation-circle"></i> <strong>First Offense:</strong> Suspension recommendation to the Guidance Center.</li>
                                <li><i class="fas fa-exclamation-triangle"></i> <strong>Second Offense:</strong> Recommendation for more severe sanctions.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="rule-card">
                    <div class="rule-card-header" onclick="toggleRuleCard(this)">
                        <h4><i class="fas fa-laptop-code"></i> Lab Rules</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="rule-card-body">
                        <ul class="rules-list">
                            <li><i class="fas fa-volume-mute"></i> Maintain silence, proper decorum, and discipline inside the laboratory.</li>
                            <li><i class="fas fa-gamepad"></i> Games are not allowed inside the lab.</li>
                            <li><i class="fas fa-globe"></i> Surfing the Internet is allowed only with the permission of the instructor.</li>
                            <li><i class="fas fa-ban"></i> Accessing illicit websites is strictly prohibited.</li>
                            <li><i class="fas fa-trash-alt"></i> Deleting computer files and changing settings is a major offense.</li>
                            <li><i class="fas fa-clock"></i> Observe computer time usage carefully.</li>
                            <li><i class="fas fa-utensils"></i> No chewing gum, eating, drinking, smoking, or vandalism.</li>
                        </ul>
                        <div class="disciplinary-notice">
                            <strong>DISCIPLINARY ACTION</strong>
                            <ul class="rules-list">
                                <li><i class="fas fa-exclamation-circle"></i> First Offense - Suspension recommendation.</li>
                                <li><i class="fas fa-exclamation-triangle"></i> Second Offense - More severe sanctions.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Panel -->
    <div class="announcements-panel">
    <div class="announcements-header">
        <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
    </div>
    <div class="view-mode-toggle">
        <a href="dashboard.php" class="view-mode-btn <?= !$viewAll ? 'active' : '' ?>">Recent</a>
        <a href="dashboard.php?view_all=true" class="view-mode-btn <?= $viewAll ? 'active' : '' ?>">View All</a>
    </div>
    
    <?php if (!empty($latestAnnouncements)): ?>
        <div class="announcements-list">
            <?php foreach ($latestAnnouncements as $announcement): ?>
                <div class="announcement-item">
                    <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                    <div class="announcement-date">
                        <i class="far fa-clock"></i> <?= date('M j, Y g:i a', strtotime($announcement['created_at'])) ?>
                    </div>
                    <div class="announcement-excerpt"><?= htmlspecialchars(substr($announcement['content'], 0, 120)) ?>...</div>
                    
                    <?php if (!empty($announcement['image_path'])): ?>
                        <img src="<?= htmlspecialchars($announcement['image_path']) ?>" class="announcement-image-preview" alt="Announcement Image">
                    <?php endif; ?>
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
<!-- Announcement Modal -->
<div class="announcement-modal" id="announcementModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalAnnouncementTitle"></h3>
            <button class="close-btn" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-date"><i class="far fa-clock"></i> <span id="modalAnnouncementDate"></span></div>
            <div class="modal-text" id="modalAnnouncementContent"></div>
            <img src="" id="modalAnnouncementImage" class="modal-image" style="display: none;">
        </div>
    </div>
</div>

<script>
// Toggle rule cards
function toggleRuleCard(header) {
    const card = header.parentElement;
    const body = card.querySelector('.rule-card-body');
    const icon = header.querySelector('i');
    
    if (body.classList.contains('expanded')) {
        body.classList.remove('expanded');
        header.classList.add('collapsed');
    } else {
        body.classList.add('expanded');
        header.classList.remove('collapsed');
    }
}

// Show full announcement modal
function showFullAnnouncement(announcement) {
    const modal = document.getElementById('announcementModal');
    const title = document.getElementById('modalAnnouncementTitle');
    const date = document.getElementById('modalAnnouncementDate');
    const content = document.getElementById('modalAnnouncementContent');
    const image = document.getElementById('modalAnnouncementImage');
    
    title.textContent = announcement.title;
    date.textContent = new Date(announcement.created_at).toLocaleString();
    content.textContent = announcement.content;
    
    if (announcement.image_path) {
        image.src = announcement.image_path;
        image.style.display = 'block';
    } else {
        image.style.display = 'none';
    }
    
    modal.classList.add('active');
}

// Close modal
document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('announcementModal').classList.remove('active');
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('announcementModal')) {
        document.getElementById('announcementModal').classList.remove('active');
    }
});

// Make announcement items clickable
document.querySelectorAll('.announcement-item').forEach(item => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', function() {
        const announcement = {
            title: this.querySelector('.announcement-title').textContent,
            content: this.querySelector('.announcement-excerpt').textContent + '...', // In a real implementation, you'd fetch the full content
            created_at: this.querySelector('.announcement-date').textContent.replace('⏱ ', ''),
            image_path: this.querySelector('.announcement-image-preview')?.src
        };
        showFullAnnouncement(announcement);
    });
});
</script>


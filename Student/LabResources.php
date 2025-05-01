<?php
session_start();
include './connection.php';

if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}

$firstname = $_SESSION['firstname'] ?? '';
$lastname = $_SESSION['lastname'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? 'de.jpg';

// Fetch uploaded resources
$resources = [];
$result = $conn->query("SELECT * FROM resources");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Uploaded Resources</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fafafa;
            color: #6a0dad;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background-color: #6a0dad;
            padding: 20px;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }
        .sidebar .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 10px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 5px;
        }
        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
        }
        .section-box {
            background: #6a0dad;
            padding: 30px;
            border-radius: 15px;
        }
        h2 {
            color: #fafafa;
            margin-bottom: 30px;
        }
        .resource-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .resource-card {
            background-color: #fafafa;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            width: 200px;
        }
        .resource-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .resource-title {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .resource-size {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 15px;
        }
        .download-btn {
            background-color: #6a0dad;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .download-btn:hover {
            background-color: #6a0dad;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="profile-section">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" class="profile-pic">
        <p><?php echo htmlspecialchars($firstname . " " . $lastname); ?></p>
    </div>
    <ul>
    <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="announcements.php">View Announcements</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="LabResources.php">View Lab Resources</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="Leaderboard.php">Leaderboard</a></li>
            <li><a href="LabSchedule.php">Lab Schedule</a></li>
            <li><a href="logout.php">Log Out</a></li>
    </ul>
</div>
<div class="main-content">
    <div class="section-box">
        <h2><span style="font-size: 20px;">📁</span> Uploaded Resources</h2>
        <div class="resource-grid">
            <?php foreach ($resources as $res): ?>
                <div class="resource-card">
                    <div class="resource-icon">📄</div>
                    <div class="resource-title"><?php echo htmlspecialchars($res['filename']); ?></div>
                    <?php 
                    $file_path = 'uploads/' . $res['filename']; 
                    $size = file_exists($file_path) ? round(filesize($file_path) / 1024, 2) . ' KB' : 'File missing';
                    ?>
                    <div class="resource-size"><?php echo $size; ?></div>
                    <a class="download-btn" href="<?php echo $file_path; ?>" download>⬇️ Download</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>

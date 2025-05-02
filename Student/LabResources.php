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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Resources</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            background-color: #f4f4f4;
            font-family: 'Segoe UI', sans-serif;
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
        .main {
            margin-left: 260px;
            padding: 60px 20px;
            width: calc(100% - 260px);
            display: flex;
            justify-content: center;
        }
        .card {
            width: 100%;
            max-width: 900px;
            background-color: #f9f7ff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            text-align: center;
        }
        h2 {
            margin-top: 0;
            font-size: 28px;
            color: #333;
        }
        p {
            margin-bottom: 20px;
            color: #444;
        }
        .google-drive-btn {
            display: inline-block;
            background-color: #4285F4;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: background 0.2s ease;
        }
        .google-drive-btn i {
            margin-right: 8px;
        }
        .google-drive-btn:hover {
            background-color: #3073dc;
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

<div class="main">
    <div class="card">
        <h2>Resource Management</h2>
        <p>My Drive / Shared Resources</p>
        <a class="google-drive-btn" href="https://drive.google.com/drive/folders/18bx8UxVLv301SdCZqZNNu-uYfRhRhWAv?usp=drive_link" target="_blank">
            <i class="fas fa-folder-open"></i> Open Google Drive
        </a>
    </div>
</div>
</body>
</html>

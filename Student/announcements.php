<?php
include 'connection.php';

// Fetch all announcements from the database
$result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<style>
   body {
    display: flex;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background: #f4f4f4;
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
.content {
    margin-left: 240px;
    padding: 40px;
    width: calc(100% - 240px);
}

h2 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 30px;
}

.announcement-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
}

.announcement {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease;
    border-left: 6px solid #6a0dad;
}

.announcement:hover {
    transform: translateY(-5px);
}

.timestamp {
    font-size: 14px;
    color: #999;
    margin-bottom: 10px;
}

.announcement p {
    font-size: 16px;
    line-height: 1.6;
    color: #333;
    margin: 0 0 12px;
}

.author {
    font-size: 14px;
    color: #6a0dad;
    text-align: right;
}
</style>
<body>
    <div class="sidebar">
        <div class="profile">
            <img src="profile.png" alt="User Profile">
            <h3></h3>
        </div>
        <ul>
        <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="announcements.php">View Announcements</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">Sit-in History</a></li>
            <li><a href="LabResources.php">View Lab Resources</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="Leaderboard.php">Leaderboard</a></li>
            <li><a href="LabSchedule.php">Lab Schedule</a></li>
            <li><a href="logout.php">Log Out</a></li>
    </ul>
    </div>
    
    <div class="content">
        <h2>Announcements</h2>
        <div class="announcement-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="announcement">
                    <span class="timestamp"> <?php echo $row['date_posted']; ?> </span>
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <span class="author">Posted by: admin-CCS</span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

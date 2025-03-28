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
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f4f4f4;
}

.sidebar {
    width: 150px;
    background: #6a0dad;
    color: white;
    height: 100vh;
    padding: 20px;
}

.profile {
    text-align: center;
    margin-bottom: 20px;
}

.profile img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
    padding: 5px;
}

ul {
    list-style: none;
    padding: 0;
}

ul li {
    padding: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

ul li i {
    margin-right: 10px;
}
.sidebar ul li a {
            color: white;
            text-decoration: none;
            flex: 1;
}
ul li.active, ul li:hover {
    background: #5a0ca3;
    border-radius: 5px;
}

.logout {
    margin-top: 20px;
    color: #ff4b5c;
}

.content {
    flex: 1;
    padding: 20px;
}

h2 {
    color: #333;
    margin-bottom: 15px;
}

.announcement-container {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.announcement {
    background: #f5f5f5;
    padding: 10px;
    border-left: 5px solid #6a0dad;
    margin-bottom: 10px;
    border-radius: 5px;
}

.timestamp {
    font-size: 12px;
    color: purple;
    display: block;
}

.author {
    font-size: 12px;
    color: #666;
    float: right;
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
            <li><a href="profile.php">Profile</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="Labrules&Regulations.php">Lab Rules</a></li>
            <li><a href="announcements.php">Announcement</a></li>
            <li><a href="Reservation.php">Reservation</a></li>
            <li><a href="SitinHistory.php">History</a></li>
            <li><a href="ViewSession.php">Session</a></li>
            <li><a href="logout.php">Logout</a></li>
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

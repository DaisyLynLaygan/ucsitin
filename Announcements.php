<?php
session_start();
include 'database.php'; // Database connection

// Fetch announcements
$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: purple;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .main-content {
            margin-left: 270px;
            padding: 40px;
            width: calc(100% - 270px);
        }
        .announcement {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .info-section {
            background: lightblue;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="announcements.php">Announcements</a></li>
            <li><a href="sit_in_rules.php">Sit-in Rules</a></li>
            <li><a href="lab_rules.php">Lab Rules</a></li>
            <li><a href="history.php">Sit-in History</a></li>
            <li><a href="reservation.php">Reservation</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Announcements</h1>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="announcement">
                <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <small>Posted on: <?php echo $row['created_at']; ?></small>
            </div>
        <?php endwhile; ?>

        <div class="info-section">
            <h2>University of Cebu</h2>
            <p><strong>Main Office:</strong> Phone: (032) 255-7777</p>
            <p><strong>Registrar:</strong> Phone: (032) 253-9434 | (032) 255-7777 local: 4167</p>
            <p><strong>Email:</strong> main.collegeregistrar@uc.edu.ph</p>
            <p><strong>Address:</strong> Sanciangko St, Cebu City, 6000 Cebu</p>
            <h3>About Us</h3>
            <p>UniversityofCebu.net is a UC Online Blog Site. All articles and photos published on this site are those of the writers and are not intended to violate copyright laws. This site is for promotional purposes only.</p>
            <p>For queries about the university, please visit the official site or contact us through the provided information. Thank you!</p>
        </div>

        <div class="info-section">
            <h2>Mission & Vision</h2>
            <h3>Vision Statement</h3>
            <p>“Democratize quality education. Be the visionary and industry leader. Give hope and transform lives.”</p>
            <h3>Mission</h3>
            <p>“University of Cebu offers affordable and quality education responsive to the demands of local and international communities.”</p>
        </div>
    </div>
</body>
</html>

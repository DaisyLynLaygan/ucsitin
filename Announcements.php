<?php
session_start();
include 'connection.php'; // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Securely fetch announcements using prepared statements
$query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

// Error handling for statement preparation
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Fetch user details for sidebar profile
$username = $_SESSION['username'];
$userQuery = "SELECT firstname, lastname, profile_picture FROM student WHERE username = ?";
$userStmt = $conn->prepare($userQuery);

if ($userStmt) {
    $userStmt->bind_param("s", $username);
    if ($userStmt->execute()) {
        $userResult = $userStmt->get_result();
        $userProfile = $userResult->fetch_assoc();
    } else {
        die("User query execution failed: " . $userStmt->error);
    }
    $userStmt->close();
} else {
    die("User query preparation failed: " . $conn->error);
}

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
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: purple;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed; 
            top: 0;
            left: 0;
            overflow-y: auto; 
        }
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            cursor: pointer;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
            transition: background 0.3s;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            padding: 40px;
            width: calc(100% - 270px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin-left: 270px;
        }
        .announcement {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 80%;
        }
        .info-section {
            background: lightblue;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 80%;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="profile-section">
        <img src="<?php echo htmlspecialchars($userProfile['profile_picture'] ?? 'de.jpg'); ?>" alt="Profile Picture" class="profile-pic">
        <p><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></p>
    </div>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="SitinRules.php">Sit-in Rules</a></li>
        <li><a href="Labrules&Regulations.php">Lab Rules & Regulations</a></li>
        <li><a href="announcements.php">Announcement</a></li>
        <li><a href="Reservation.php">Reservation</a></li>
        <li><a href="SitinHistory.php">Sit-in History</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
<div class="main-content">
    <h1>Announcements</h1>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="announcement">
            <h2><?php echo htmlspecialchars($row['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            <small>Posted on: <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></small>
        </div>
    <?php endwhile; ?>
    <?php 
    $stmt->close();
    $conn->close();
    ?>

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

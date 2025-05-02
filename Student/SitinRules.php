<?php
session_start();
include './connection.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ensure the uploads directory exists
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory if it does not exist
}

// Fetch user profile from session or database
$userProfile = [
    'firstname' => $_SESSION['firstname'] ?? '',
    'lastname' => $_SESSION['lastname'] ?? '',
    'middlename' => $_SESSION['middlename'] ?? '',
    'profile_picture' => $_SESSION['profile_picture'] ?? 'de.jpg'
];

$remainingSessions = 30; // Example value, ideally retrieved from a database

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES["profile_picture"]["type"];
    $temp_file = $_FILES["profile_picture"]["tmp_name"];
    $file_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES["profile_picture"]["name"])); // Remove special characters
    $target_file = $upload_dir . $file_name;

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($temp_file, $target_file)) {
            $_SESSION['profile_picture'] = $target_file;
            $userProfile['profile_picture'] = $target_file;
        } else {
            echo "<script>alert('Error moving file. Please check folder permissions.');</script>";
        }
    } else {
        echo "<script>alert('Invalid file format. Please upload JPG, PNG, or GIF.');</script>";
    }
}

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['firstname'] = $_POST['firstname'] ?? $userProfile['firstname'];
    $_SESSION['lastname'] = $_POST['lastname'] ?? $userProfile['lastname'];
    $_SESSION['middlename'] = $_POST['middlename'] ?? $userProfile['middlename'];

    $userProfile['firstname'] = $_SESSION['firstname'];
    $userProfile['lastname'] = $_SESSION['lastname'];
    $userProfile['middlename'] = $_SESSION['middlename'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Rules</title>
    <style>
       body {
    display: flex;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background: linear-gradient(to right, #f5f7fa, #e6e9f0);
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
    margin-left: 270px;
    padding: 40px 20px;
    width: calc(100% - 270px);
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #f9f9f9;
}

.rules-container {
    background: white;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    width: 100%;
}

.rules-container h2 {
    font-size: 28px;
    margin-bottom: 10px;
    color: #6a0dad;
    text-align: center;

}

.rules-container h3 {
    font-size: 22px;
    margin-bottom: 8px;
    color: #555;
}

.rules-container p,
.rules-container ul {
    font-size: 16px;
    color: #333;
    line-height: 1.7;
}

.rules-container ul {
    padding-left: 20px;
    margin-top: 10px;
}

.rules-container ul li {
    margin-bottom: 8px;
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
    <div class="main-content">
        <div class="rules-container">
            <h2>Sit-In Rules</h2>
            <h3>University of Cebu</h3>
            <p><strong>COLLEGE OF INFORMATION & COMPUTER STUDIES</strong></p>
            <p>To maintain order and discipline, please follow these guidelines:</p>
            <br>
            <p><strong>LABORATORY RULES AND REGULATIONS</strong></p>
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <p>1. Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans and other personal pieces of equipment must be switched off.</p>
            <p>2. Games are not allowed inside the lab. This includes computer-related games, card games and other games that may disturb the operation of the lab.</p>
            <p>3. Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</p>
            <p>4. Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</p>
            <p>5. Deleting computer files and changing the set-up of the computer is a major offense.</p>
            <p>6. Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</p>
            <p>7. Observe proper decorum while inside the laboratory.</p>
            <ul>
                <li>Maintain silence and proper decorum.</li>
                <li>Games and unrelated activities are not allowed.</li>
                <li>Internet usage requires instructor approval.</li>
                <li>Unauthorized website access is prohibited.</li>
                <li>Do not alter or delete files on the computers.</li>
                <li>Follow computer time usage rules.</li>
                <li>Observe proper behavior inside the lab.</li>
                <li>No eating, drinking, or vandalism.</li>
            </ul>
            <p>8. Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</p>
            <p>9. Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</p>
            <p>10. Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</p>
            <p>11. For serious offense, the lab personnel may call the Civil Security Office (CSU) for assistance.</p>
            <p>12. Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant or instructor immediately.</p>
            <br>
            <p><strong>DISCIPLINARY ACTION</strong></p>
            <ul>
              <li>First Offense - The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</li>
              <li>Second and Subsequent Offenses - A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
            </ul>
        </div>
    </div>
</body>
</html>












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

// Handle profile picture upload with error trapping
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    if ($_FILES["profile_picture"]["error"] !== 0) {
        echo "<script>alert('Error uploading file. Please try again.');</script>";
    } else {
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
}

// Dummy data for lab rules (ideally, fetch from a database)
$labRules = [
    "No food or drinks allowed inside the lab.",
    "Always log in and log out when using lab computers.",
    "Do not install or uninstall any software without permission.",
    "Maintain silence and avoid disturbing others.",
    "Report any hardware or software issues to the lab administrator.",
    "Use the lab resources responsibly and ethically."
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: whitesmoke;
            margin: 0;
            overflow: hidden;
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
            padding: 10px;
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
            width: calc(100% - 270px); /* Adjusted for sidebar width */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centers content horizontally */
            justify-content: center; /* Centers content vertically */
            min-height: 100vh; /* Ensures it takes the full viewport height */
            margin-left: 270px; /* Pushes it to the right of the sidebar */
}
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: purple;
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
            padding: 10px;
            border-left: 5px solid purple;
            text-align: center;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
            .container {
                width: 90%;
            }
        }
        @media (max-width: 480px) {
            .sidebar {
                width: 180px;
            }
            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
            .container {
                width: 100%;
            }
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
        <div class="container">
            <h1>Lab Rules & Regulations</h1>
            <ul>
                <?php foreach ($labRules as $rule): ?>
                    <li><?php echo htmlspecialchars($rule); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>

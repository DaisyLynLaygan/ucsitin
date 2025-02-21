<?php
session_start();

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
    'profile_picture' => $_SESSION['profile_picture'] ?? 'default.png'
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
    <title>CCS Sit-in Monitoring Dashboard</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
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
        .hidden-input {
            display: none;
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
            margin-left: 270px;
            padding: 40px;
            width: calc(100% - 270px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 900px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($userProfile['profile_picture']); ?>" alt="Profile Picture" class="profile-pic" id="display-pic">
            <p><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></p>
        </div>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="#">Announcements</a></li>
            <li><a href="SitinRules.php">Sit-in Rules</a></li>
            <li><a href="#">Sit-in History</a></li>
            <li><a href="#">Remaining Sessions: <span id="sessionCount"><?php echo $remainingSessions; ?></span></a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Welcome to CCS Sit-in Monitoring</h1>
        <p>Manage your sit-in sessions and rules easily.</p>
        
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Sessions</h3>
                <p><?php echo $remainingSessions; ?></p>
            </div>
            <div class="card">
                <h3>Active Reservations</h3>
                <p>5</p>
            </div>
            <div class="card">
                <h3>Completed Sessions</h3>
                <p>10</p>
            </div>
        </div>
    </div>
</body>
</html>

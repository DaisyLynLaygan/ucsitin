<?php
session_start();

$check = $_SESSION['idno'] ==''? true : false;
if($check){
    header("Location: login.php");
}
echo $_SESSION['idno'];
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
    'profile_picture' => $_SESSION['profile_picture'] ??'',
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
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
            padding: 20px;
            width: calc(100% - 270px);
            display: flex;
            flex-direction: column;
        }
        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 10px;
        }
        .dashboard-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .column {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            width: 100%;
            height: 300px;
        }
        button {
            background-color: #4B0082;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        button:hover {
            background-color: #6A0DAD;
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
        <div class="top-section">
            <h2>Welcome, Bravo!</h2>
            <div>
                <button><i class="fas fa-bell"></i> Notifications</button>
                <button><i class="fas fa-calendar"></i> Calendar</button>
            </div>
        </div>
        <div class="dashboard-container">
            <div class="column">
                <h3>Sit-In Laboratory Rules</h3>
                <ul>
                    <li>No games, personal devices, or inappropriate content.</li>
                    <li>Do not alter computer settings or delete files.</li>
                    <li>Follow seating arrangements and deposit bags at the counter.</li>
                    <li>No food, drinks, or smoking in the lab.</li>
                    <li>Disturbances may lead to removal or security intervention.</li>
                    <li>Handle equipment with care.</li>
                    <li>Use headphones for audio.</li>
                    <li>Log out after use.</li>
                </ul>
            </div>
            <div class="column">
                <h3>Lab Usage Chart</h3>
                <canvas id="labUsageChart" class="chart-container"></canvas>
            </div>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('labUsageChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Lab 544', 'Lab 542', 'Lab 530', 'Lab 524', 'Lab 526', 'Lab 525'],
                datasets: [{
                    label: 'Sit-In Usage',
                    data: [10, 18, 7, 14, 9, 6],
                    backgroundColor: ['#9286f4', '#be92f8', '#e8a6f9', '#fbcce8', '#be92f8','#dec3f9']
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

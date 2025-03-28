<?php 
session_start();
include './connection.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from session
$username = $_SESSION['username'];
$userProfile = [
    'firstname' => $_SESSION['firstname'] ?? '',
    'lastname' => $_SESSION['lastname'] ?? '',
    'profile_picture' => $_SESSION['profile_picture'] ?? 'de.jpg'
];

// Fetch student session details from the database
$query = $conn->prepare("SELECT date, time_in, logout FROM student_sessions WHERE username = ? ORDER BY date DESC, time_in DESC");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

// Calculate remaining sit-in time (assuming 4 hours allowed per session)
$remainingTime = 240; // 4 hours in minutes

$sessions = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['date'];
    $timeIn = $row['time_in'];
    $logout = $row['logout'] ?? "Still Logged In";

    // Calculate elapsed time if logged out
    if ($logout !== "Still Logged In") {
        $elapsedMinutes = (strtotime($logout) - strtotime($timeIn)) / 60;
        $remainingTime -= $elapsedMinutes;
    }

    $sessions[] = [
        'date' => $date,
        'time_in' => $timeIn,
        'logout' => $logout,
        'remaining_time' => max(0, $remainingTime) . " minutes"
    ];
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sessions</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 200px;
            background-color: #6a0dad;
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
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
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
            margin-left: 220px;
            padding: 40px;
            width: calc(100% - 220px);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .session-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #6a0dad;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($userProfile['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
            <p><?php echo htmlspecialchars($userProfile['firstname'] . " " . $userProfile['lastname']); ?></p>
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

    <div class="main-content">
        <div class="session-container">
            <h2>Student Sit-in Sessions</h2>
            <p><strong>Remaining Time:</strong> <?php echo max(0, $remainingTime); ?> minutes</p>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Logout</th>
                    <th>Remaining Time</th>
                </tr>
                <?php foreach ($sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                    <td><?php echo htmlspecialchars($session['time_in']); ?></td>
                    <td><?php echo htmlspecialchars($session['logout']); ?></td>
                    <td><?php echo htmlspecialchars($session['remaining_time']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>

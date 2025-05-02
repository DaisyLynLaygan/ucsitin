<?php
session_start();
include './connection.php';

if (!isset($_SESSION['idno'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['idno'];
$firstname = $_SESSION['firstname'] ?? '';
$lastname = $_SESSION['lastname'] ?? '';
$profile_picture = $_SESSION['profile_picture'] ?? 'de.jpg';

// Get current user's points and sessions
$userQuery = $conn->query("SELECT points, sessions FROM student WHERE idNo = '$student_id'");
$user = $userQuery->fetch_assoc();
$userPoints = $user['points'] ?? 0;
$userSessions = $user['sessions'] ?? 0;

// Get all students sorted by points (descending)
$rankingQuery = $conn->query("SELECT idNo, CONCAT(firstname, ' ', lastname) AS name, points, sessions FROM student ORDER BY points DESC, sessions DESC");
$rankings = [];
$rank = 1;
$currentRank = 0;

while ($row = $rankingQuery->fetch_assoc()) {
    if ($row['idNo'] == $student_id) {
        $currentRank = $rank;
    }
    $row['rank'] = $rank++;
    $rankings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fafafa;
            color: white;
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
        .sidebar .profile-section {
            text-align: center;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 30px;
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
            margin-left: 260px;
            padding: 40px;
            width: 100%;
        }
        .section-box {
            background: #6a0dad;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .section-box h2 {
            margin-top: 0;
            color: #fafafa;
        }
        .ranking-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ranking-item {
            background-color: #6a0dad;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
            margin-right: 20px;
        }
        .ranking-item:last-child {
            margin-right: 0;
        }
        .ranking-item h3 {
            margin: 0;
            font-size: 18px;
            color: #fafafa;
        }
        .ranking-item p {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 0;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            color: white;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
        }
        table th {
            color: #fafafa;
        }
        .highlight {
            background-color: #94a3b8;
        }
        .badge {
            background-color: #2563eb;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 12px;
            margin-left: 8px;
            color: white;
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

    <div class="main-content">
        <div class="section-box">
            <h2>Leaderboard</h2>
            <div class="ranking-box">
                <div class="ranking-item">
                    <h3>Current Rank</h3>
                    <p>🥉 <?php echo $currentRank; ?></p>
                </div>
                <div class="ranking-item">
                    <h3>Points</h3>
                    <p><?php echo $userPoints; ?></p>
                </div>
                <div class="ranking-item">
                    <h3>Sessions</h3>
                    <p><?php echo $userSessions; ?></p>
                </div>
            </div>
        </div>

        <div class="section-box">
            <h2>Top Students</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student</th>
                        <th>Points</th>
                        <th>Sessions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankings as $student): ?>
                        <tr class="<?php echo $student['idNo'] == $student_id ? 'highlight' : ''; ?>">
                            <td>🏅 <?php echo $student['rank']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($student['name']); ?>
                                <?php if ($student['idNo'] == $student_id): ?>
                                    <span class="badge">You</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $student['points']; ?></td>
                            <td><?php echo $student['sessions']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

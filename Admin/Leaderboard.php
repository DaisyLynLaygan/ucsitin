<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get top 3 performers
$top_query = "SELECT 
    s.idNo, s.firstname, s.lastname, s.points, s.sessions,
    SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, si.sitin_time, si.sit_out_time))) AS total_duration
    FROM student s
    LEFT JOIN sit_in si ON s.idNo = si.idno
    GROUP BY s.idNo
    ORDER BY s.points DESC, total_duration DESC
    LIMIT 3";
$top_result = $conn->query($top_query);

// Get full leaderboard
$all_query = "SELECT 
    s.idNo, s.firstname, s.lastname, s.points, s.sessions,
    SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, si.sitin_time, si.sit_out_time))) AS total_duration
    FROM student s
    LEFT JOIN sit_in si ON s.idNo = si.idno
    GROUP BY s.idNo
    ORDER BY s.points DESC, total_duration DESC";
$all_result = $conn->query($all_query);

// Get recent rewards
$rewards_query = "SELECT r.rewarded_to, s.firstname, s.lastname, r.rewarded_at 
                  FROM reward_log r 
                  JOIN student s ON s.idNo = r.rewarded_to 
                  ORDER BY r.rewarded_at DESC LIMIT 5";
$rewards_result = $conn->query($rewards_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <style>
        body {
            margin: 0;
            background-color: whitesmoke;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-content {
            margin-left: 220px;
            padding: 40px;
        }
        h2 {
            color: #6a0dad;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .top-performers {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .medal-box {
            text-align: center;
            background-color: #f3eaff;
            padding: 30px 20px;
            border-radius: 12px;
            width: 200px;
            box-shadow: 0 0 8px rgba(106, 13, 173, 0.2);
        }
        .gold { background-color: #ffe100; }
        .silver { background-color: #d1d5db; }
        .bronze { background-color: #f97316; }
        .medal-box h3 {
            margin: 10px 0 5px;
            color: #1f1f3d;
        }
        .medal-box span {
            font-weight: bold;
            color: #1f1f3d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f3eaff;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 40px;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #6a0dad;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #ede4ff;
        }
        .section-title {
            font-size: 20px;
            margin: 30px 0 10px;
            color: #6a0dad;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2>🏆 Leaderboard</h2>
    <div class="top-performers">
        <?php
        $medals = ['gold', 'silver', 'bronze'];
        $ranks = ['1st', '2nd', '3rd'];
        $i = 0;
        while ($row = $top_result->fetch_assoc()) {
            $name = $row['firstname'] . ' ' . $row['lastname'];
            $points = $row['points'];
            echo '<div class="medal-box ' . $medals[$i] . '">';
            echo '<img src="medal_' . $medals[$i] . '.png" width="40"><br>';
            echo "<h3>$name</h3>";
            echo "<span>$points points</span><br>";
            echo "<strong>{$ranks[$i]}</strong>";
            echo '</div>';
            $i++;
        }
        ?>
    </div>

    <div class="section-title">All Students</div>
    <table>
        <thead>
        <tr>
            <th>Rank</th>
            <th>Student</th>
            <th>ID</th>
            <th>Points</th>
            <th>Sessions</th>
            <th>Duration</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $rank = 1;
        while ($row = $all_result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                <td><?= htmlspecialchars($row['idNo']) ?></td>
                <td><?= $row['points'] ?></td>
                <td><?= $row['sessions'] ?></td>
                <td><?= $row['total_duration'] ?? '00:00:00' ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="section-title">Recent Reward Activities</div>
    <table>
        <thead>
        <tr>
            <th>Student</th>
            <th>ID</th>
            <th>Time</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $rewards_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                <td><?= htmlspecialchars($row['rewarded_to']) ?></td>
                <td><?= date("Y-m-d H:i:s", strtotime($row['rewarded_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

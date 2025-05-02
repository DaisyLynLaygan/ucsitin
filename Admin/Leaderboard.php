<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Top 3 students based on POINTS
$top_query = "SELECT s.idNo, s.firstname, s.lastname, s.points, s.sessions FROM student s ORDER BY s.points DESC LIMIT 3";
$top_result = $conn->query($top_query);

// Full leaderboard based on POINTS
$all_query = "SELECT s.idNo, s.firstname, s.lastname, s.points, s.sessions FROM student s ORDER BY s.points DESC";
$all_result = $conn->query($all_query);

// Statistics: highest, lowest, average points
$stats_query = "SELECT MAX(points) AS max_points, MIN(points) AS min_points, AVG(points) AS avg_points FROM student";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            background-color: whitesmoke;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-content {
            margin-right: 220px;
            padding: 30px;
            align-items: center;
            width: 90%;
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
        .gold { background-color: #6a5acd; }
        .silver { background-color: #3f51b5; }
        .bronze { background-color: #dec3f9; }
        .default { background-color: #e0e0e0; }
        .medal-box h3 {
            margin: 10px 0 5px;
            color: #1f1f3d;
        }
        .medal-box span {
            font-weight: bold;
            color: #1f1f3d;
        }
        .trophy {
            font-size: 40px;
            margin-bottom: 10px;
            display: block;
        }
        .trophy.gold{ color: #ffd700; }
        .trophy.silver { color: #c0c0c0; }
        .trophy.bronze { color: #cd7f32; }
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
        .stat-boxes {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-box {
            flex: 1;
            background-color: #f3eaff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        canvas {
            background: #fff;
            border-radius: 10px;
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
            $medal_class = isset($medals[$i]) ? $medals[$i] : 'default';
            $rank_label = isset($ranks[$i]) ? $ranks[$i] : ($i + 1) . 'th';
            echo '<div class="medal-box ' . $medal_class . '">';
            echo '<i class="fas fa-trophy trophy ' . $medal_class . '"></i>';
            echo "<h3>$name</h3>";
            echo "<span>$points points</span><br>";
            echo "<strong>$rank_label</strong>";
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
        </tr>
        </thead>
        <tbody>
        <?php
        $rank = 1;
        $all_result->data_seek(0);
        while ($row = $all_result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                <td><?= htmlspecialchars($row['idNo']) ?></td>
                <td><?= $row['points'] ?></td>
                <td><?= $row['sessions'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="section-title">📊 Rewards Activities</div>
    <div class="stat-boxes">
        <div class="stat-box">
            <h3>Highest Points</h3>
            <p style="font-size: 24px; color: #6a0dad;"><?= $stats['max_points'] ?></p>
        </div>
        <div class="stat-box">
            <h3>Lowest Points</h3>
            <p style="font-size: 24px; color: #6a0dad;"><?= $stats['min_points'] ?></p>
        </div>
        <div class="stat-box">
            <h3>Average Points</h3>
            <p style="font-size: 24px; color: #6a0dad;"><?= number_format($stats['avg_points'], 2) ?></p>
        </div>
    </div>

    <canvas id="pointsChart" height="100"></canvas>
</div>
<script>
const ctx = document.getElementById('pointsChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        datasets: [{
            label: 'Points Trend',
            data: [5, 10, 15, 20, 25, 30], // dummy data, replace with real if needed
            borderColor: '#6a0dad',
            backgroundColor: 'rgba(106,13,173,0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6a0dad'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
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

<?php
include './connection.php';
include 'navbar.php';

// Today's Date
$today = date("Y-m-d");

// Get daily analytics
$todaySitins = $conn->query("SELECT COUNT(*) AS total FROM sit_in WHERE DATE(sit_in_time) = '$today'")->fetch_assoc()['total'];
$todayFeedbacks = $conn->query("SELECT COUNT(*) AS total FROM sit_in WHERE DATE(sit_in_time) = '$today' AND feedback IS NOT NULL")->fetch_assoc()['total'];
$todayReservations = $conn->query("SELECT COUNT(*) AS total FROM reservation WHERE DATE(date) = '$today'")->fetch_assoc()['total'];

// Sit-in purpose for today
$purposeData = $conn->query("SELECT purpose, COUNT(*) AS total FROM sit_in WHERE DATE(sit_in_time) = '$today' GROUP BY purpose");
$labels = [];
$counts = [];
while ($row = $purposeData->fetch_assoc()) {
    $labels[] = $row['purpose'];
    $counts[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f5f7fb;
        }
        .analytics-container {
            padding: 30px;
        }
        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card1 { background: #6a5acd; }
        .card2 { background: #3f51b5; }
        .card3 { background: #dec3f9; }

        .charts {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            min-width: 300px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        }
        .chart-box h4 {
            margin-bottom: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="analytics-container">
    <h2>📈 Daily Analytics - <?php echo date("F j, Y"); ?></h2>

    <div class="cards">
        <div class="card card1">👥 Sit-ins Today: <?php echo $todaySitins; ?></div>
        <div class="card card2">📝 Feedbacks: <?php echo $todayFeedbacks; ?></div>
        <div class="card card3">📅 Reservations: <?php echo $todayReservations; ?></div>
    </div>

    <div class="charts">
        <div class="chart-box">
            <h4>Sit-in Purpose Breakdown</h4>
            <?php if ($todaySitins > 0): ?>
        <canvas id="pieChart" width="300" height="300"></canvas>
    <?php else: ?>
        <p>No sit-in data to display for today.</p>
    <?php endif; ?>
        </div>
        <!-- Placeholder for bar or line chart -->
        <div class="chart-box">
            <h4>Daily Sit-in Trend</h4>
            <canvas id="lineChart"></canvas>
        </div>
    </div>
</div>

<script>
const pieChart = new Chart(document.getElementById('pieChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: ['#9286f4', '#be92f8', '#e8a6f9', '#fbcce8', '#dec3f9']
        }]
    }
});

const lineChart = new Chart(document.getElementById('lineChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['8AM', '10AM', '12PM', '2PM', '4PM'],
        datasets: [
            {
                label: 'Sit-ins',
                data: [3, 4, 7, 2, 5],
                borderColor: '#6a0dad',
                tension: 0.4,
                fill: false
            }
        ]
    }
});
</script>

</body>
</html>

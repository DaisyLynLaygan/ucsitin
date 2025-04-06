<?php
include './connection.php';
include 'navbar.php';

// Count statistics from your database
$totalStudents = $conn->query("SELECT COUNT(*) as count FROM student")->fetch_assoc()['count'];
$currentSitIn = $conn->query("SELECT COUNT(*) as count FROM sit_in WHERE sit_out_time IS NULL")->fetch_assoc()['count'];
$totalSitIn = $conn->query("SELECT COUNT(*) as count FROM sit_in")->fetch_assoc()['count'];

// Sit-in purpose breakdown
$purposeResult = $conn->query("SELECT purpose, COUNT(*) as count FROM sit_in GROUP BY purpose");
$purposes = [];
$counts = [];
while ($row = $purposeResult->fetch_assoc()) {
    $purposes[] = $row['purpose'];
    $counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
        }
        .stats-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .stats-header {
            background: #6a0dad;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .stat-item {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .stat-item b {
            color: #333;
        }
        canvas {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="stats-container">
    <div class="stats-header">📊 Statistics</div>

    <div class="stat-item"><b>Students Registered:</b> <?php echo $totalStudents; ?></div>
    <div class="stat-item"><b>Currently Sit-in:</b> <?php echo $currentSitIn; ?></div>
    <div class="stat-item"><b>Total Sit-in:</b> <?php echo $totalSitIn; ?></div>

    <canvas id="sitPurposeChart" width="400" height="400"></canvas>
</div>

<script>
const purposeData = {
    labels: <?php echo json_encode($purposes); ?>,
    datasets: [{
        data: <?php echo json_encode($counts); ?>,
        backgroundColor: ['#9286f4', '#be92f8', '#e8a6f9', '#fbcce8', '#dec3f9']
    }]
};

const ctx = document.getElementById('sitPurposeChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: purposeData,
    options: {
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.raw}`
                }
            }
        }
    }
});
</script>

</body>
</html>

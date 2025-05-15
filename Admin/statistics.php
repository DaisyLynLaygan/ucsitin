<?php 
$pageTitle = "Analytics & Statistics";
include 'header.php'; 

$today = date('Y-m-d');

// DAILY ANALYTICS
$totalTodayQuery = "SELECT COUNT(*) AS total FROM student_sitin WHERE DATE(`sit-login`) = '$today'";
$totalTodaySitIns = $conn->query($totalTodayQuery)->fetch_assoc()['total'];

$todayLabsQuery = "SELECT lab, COUNT(*) AS count FROM student_sitin WHERE DATE(`sit-login`) = '$today' GROUP BY lab";
$todayLabsResult = $conn->query($todayLabsQuery);

$todayLabs = [];
$todayCounts = [];
while ($row = $todayLabsResult->fetch_assoc()) {
    $todayLabs[] = $row['lab'];
    $todayCounts[] = $row['count'];
}

// STATISTICS
$totalSitInsQuery = "SELECT COUNT(*) AS total_sitins FROM student_sitin";
$totalStudentsQuery = "SELECT COUNT(DISTINCT idno) AS total_students FROM student_sitin";
$mostActiveLabQuery = "SELECT lab, COUNT(*) AS count FROM student_sitin GROUP BY lab ORDER BY count DESC LIMIT 1";
$dailyAverageQuery = "SELECT COUNT(*) / COUNT(DISTINCT DATE(`sit-login`)) AS daily_average FROM student_sitin";

$totalSitIns = $conn->query($totalSitInsQuery)->fetch_assoc()['total_sitins'];
$totalStudents = $conn->query($totalStudentsQuery)->fetch_assoc()['total_students'];
$mostActiveLabResult = $conn->query($mostActiveLabQuery)->fetch_assoc();
$mostActiveLab = $mostActiveLabResult['lab'] ?? 'N/A';
$mostActiveLabCount = $mostActiveLabResult['count'] ?? 0;
$dailyAverage = round($conn->query($dailyAverageQuery)->fetch_assoc()['daily_average'], 2);

$allLabsQuery = "SELECT lab, COUNT(*) AS count FROM student_sitin GROUP BY lab ORDER BY count DESC";
$allLabsResult = $conn->query($allLabsQuery);

$allLabs = [];
$allCounts = [];
while ($row = $allLabsResult->fetch_assoc()) {
    $allLabs[] = $row['lab'];
    $allCounts[] = $row['count'];
}

// Default view preference (can be stored in session or database for persistence)
$preferGraph = true; // Default to graph view
if (isset($_GET['view'])) {
    $preferGraph = ($_GET['view'] === 'graph');
}
?>

<style>
body {
    background-color: #ffffff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #212529;
    margin: 0;
    padding: 0;
}
.wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    justify-content: center;
}
.section {
    flex: 1 1 450px;
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    max-width: 600px;
}
.section h2 {
    color: #303f9f;
    text-align: center;
    margin-bottom: 15px;
}
.section p {
    text-align: center;
    color: #555;
    font-size: 14px;
    margin-bottom: 10px;
}
.sitins-counter {
    background: #303f9f;
    color: #ffffff;
    padding: 8px 15px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    border-radius: 8px;
    margin-bottom: 15px;
}
.stat-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    margin-top: 15px;
}
.card {
    flex: 1 1 180px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    margin: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.card h3 {
    color: #303f9f;
    font-size: 24px;
    margin: 0;
}
.card p {
    font-size: 14px;
    color: #666;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
}
th, td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
}
th {
    background-color: #212529;
    color: #fff;
}
.chart-container {
    margin-top: 20px;
    text-align: center;
    height: 300px;
}
.view-toggle {
    display: flex;
    justify-content: center;
    margin: 15px 0;
}
.view-toggle-btn {
    padding: 8px 15px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.3s;
    color: #303f9f;
    font-weight: 500;
}
.view-toggle-btn:first-child {
    border-radius: 5px 0 0 5px;
}
.view-toggle-btn:last-child {
    border-radius: 0 5px 5px 0;
}
.view-toggle-btn.active {
    background: #303f9f;
    color: white;
    border-color: #303f9f;
}
.view-toggle-btn:hover:not(.active) {
    background: #e9ecef;
}
.hidden {
    display: none;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="wrapper">
    <!-- Daily Analytics -->
    <div class="section">
        <div class="sitins-counter">
            <?php echo $totalTodaySitIns; ?> Sit-Ins Today
        </div>
        <h2 style="font-size: 18px; font-weight: 500; color: #2c3e50;">📅 Daily Analytics</h2>
        
        <p><?php echo date('F d, Y', strtotime($today)); ?></p>

        <?php if (count($todayLabs) > 0): ?>
            <div class="view-toggle">
                <button class="view-toggle-btn <?php echo $preferGraph ? 'active' : ''; ?>" 
                        onclick="toggleView('daily', 'graph')">Graph View</button>
                <button class="view-toggle-btn <?php echo !$preferGraph ? 'active' : ''; ?>" 
                        onclick="toggleView('daily', 'table')">Table View</button>
            </div>
            
            <div id="daily-graph" class="chart-container <?php echo $preferGraph ? '' : 'hidden'; ?>">
                <canvas id="dailyChart"></canvas>
            </div>
            
            <div id="daily-table" class="<?php echo $preferGraph ? 'hidden' : ''; ?>">
                <table>
                    <thead>
                        <tr><th>Lab</th><th>Sit-Ins Today</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todayLabs as $index => $lab): ?>
                            <tr>
                                <td><?php echo $lab; ?></td>
                                <td><?php echo $todayCounts[$index]; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align:center; color:#999;">No sit-ins recorded today.</p>
        <?php endif; ?>
    </div>

    <!-- System Statistics -->
    <div class="section">
        <h2 style="font-size: 18px; font-weight: 500; color: #2c3e50;">
        📊 System Statistics
        </h2>
        <div class="stat-cards">
            <div class="card">
                <h3><?php echo $totalSitIns; ?></h3>
                <p>Total Sit-Ins</p>
            </div>
            <div class="card">
                <h3><?php echo $totalStudents; ?></h3>
                <p>Unique Students</p>
            </div>
            <div class="card">
                <h3><?php echo $mostActiveLab; ?></h3>
                <p>Most Active Lab (<?php echo $mostActiveLabCount; ?>)</p>
            </div>
            <div class="card">
                <h3><?php echo $dailyAverage; ?></h3>
                <p>Avg Sit-Ins Per Day</p>
            </div>
        </div>

        <h2 style="font-size: 18px; font-weight: 500; color: #2c3e50;">🏆 Top Labs by Sit-Ins</h2>
        
        <?php if (count($allLabs) > 0): ?>
            <div class="view-toggle">
                <button class="view-toggle-btn <?php echo $preferGraph ? 'active' : ''; ?>" 
                        onclick="toggleView('overall', 'graph')">Graph View</button>
                <button class="view-toggle-btn <?php echo !$preferGraph ? 'active' : ''; ?>" 
                        onclick="toggleView('overall', 'table')">Table View</button>
            </div>
            
            <div id="overall-graph" class="chart-container <?php echo $preferGraph ? '' : 'hidden'; ?>">
                <canvas id="overallChart"></canvas>
            </div>
            
            <div id="overall-table" class="<?php echo $preferGraph ? 'hidden' : ''; ?>">
                <table>
                    <thead>
                        <tr><th>Lab</th><th>Total Sit-Ins</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allLabs as $index => $lab): ?>
                            <tr>
                                <td><?php echo $lab; ?></td>
                                <td><?php echo $allCounts[$index]; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align:center; color:#999;">No data available.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Toggle between graph and table views
function toggleView(section, viewType) {
    const graphElement = document.getElementById(`${section}-graph`);
    const tableElement = document.getElementById(`${section}-table`);
    const graphBtn = document.querySelector(`button[onclick="toggleView('${section}', 'graph')"]`);
    const tableBtn = document.querySelector(`button[onclick="toggleView('${section}', 'table')"]`);
    
    if (viewType === 'graph') {
        graphElement.classList.remove('hidden');
        tableElement.classList.add('hidden');
        graphBtn.classList.add('active');
        tableBtn.classList.remove('active');
        
        // Update URL parameter
        window.history.replaceState(null, null, '?view=graph');
    } else {
        graphElement.classList.add('hidden');
        tableElement.classList.remove('hidden');
        graphBtn.classList.remove('active');
        tableBtn.classList.add('active');
        
        // Update URL parameter
        window.history.replaceState(null, null, '?view=table');
    }
}

<?php if (count($todayLabs) > 0): ?>
// Daily chart
new Chart(document.getElementById('dailyChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($todayLabs); ?>,
        datasets: [{
            label: 'Sit-Ins per Lab Today',
            data: <?php echo json_encode($todayCounts); ?>,
            backgroundColor: [
                '#9286f4', '#be92f8', '#e8a6f9', '#fbcce8', '#dec3f9', '#F0C1E1'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Daily Sit-Ins by Lab' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
<?php endif; ?>

<?php if (count($allLabs) > 0): ?>
// Overall chart
new Chart(document.getElementById('overallChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($allLabs); ?>,
        datasets: [{
            data: <?php echo json_encode($allCounts); ?>,
            backgroundColor: [
                '#303f9f', '#303f9f', '#303f9f', '#303f9f', '#303f9f', '#303f9f'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            title: { display: true, text: 'Sit-Ins by Lab (All Time)' }
        }
    }
});
<?php endif; ?>
</script>


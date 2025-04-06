<?php include 'navbar.php'; ?>

<h1 style="font-size: 26px; font-weight: 600; margin-bottom: 30px; color: #2c3e50;">Welcome to CCS Sit-in Monitoring</h1>

<style>
body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.dashboard-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
}
.card {
    flex: 1 1 250px;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 20px;
    position: relative;
    border-left: 6px solid #9286f4;
}
.card h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #7f8c8d;
}
.card p {
    font-size: 28px;
    font-weight: bold;
    margin-top: 10px;
    color: #9286f4;
}
.dashboard-charts {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.chart-container {
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 20px;
    width: 300px;
    text-align: center;
}
.chart-container h3 {
    font-size: 14px;
    font-weight: 600;
    color: #7f8c8d;
    margin-bottom: 10px;
}
</style>

<div class="dashboard-cards">
    <div class="card">
        <h3>Active Reservations</h3>
        <p>5</p>
    </div>
    <div class="card">
        <h3>Completed Sessions</h3>
        <p>10</p>
        
    </div>
</div>

<div class="dashboard-charts">
    <div class="chart-container">
        <h3>Sit Purpose</h3>
        <canvas id="sitPurposeChart" width="280" height="280"></canvas>
    </div>
    <div class="chart-container">
        <h3>Laboratory Usage</h3>
        <canvas id="labChart" width="280" height="280"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dummy data; replace with PHP-generated values if needed
const purposeData = {
    labels: ['C#', 'C', 'Java', 'ASP.Net', 'Php'],
    datasets: [{
        data: [0, 0, 0, 0, 1],
        backgroundColor: ['#9286f4', '#be92f8', '#e8a6f9', '#fbcce8', '#dec3f9']
    }]
};

const labData = {
    labels: ['524', '526', '528', '530', '542', 'Mac'],
    datasets: [{
        data: [1, 0, 0, 0, 0, 0],
        backgroundColor: ['#9286f4', '#be92f8', '#e8a6f9', '#fbcce8', '#dec3f9']
    }]
};

const ctx1 = document.getElementById('sitPurposeChart').getContext('2d');
const sitPurposeChart = new Chart(ctx1, {
    type: 'pie',
    data: purposeData,
    options: {
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw}` } }
        }
    }
});

const ctx2 = document.getElementById('labChart').getContext('2d');
const labChart = new Chart(ctx2, {
    type: 'pie',
    data: labData,
    options: {
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: ctx => ` Laboratory ${ctx.label}: ${ctx.raw}` } }
        }
    }
});
</script>

</div> <!-- Closing main-content -->
</body>
</html>

<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get selected lab
$selectedLab = $_GET['lab'] ?? 'Lab 517';

// Fetch labs
$labs = ['Lab 517', 'Lab 524', 'Lab 526', 'Lab 528', 'Lab 530', 'Lab 542', 'Lab 544'];

// Fetch PC statuses
$pc_query = $conn->prepare("SELECT * FROM pcs WHERE lab = ?");
$pc_query->bind_param("s", $selectedLab);
$pc_query->execute();
$pc_result = $pc_query->get_result();

$pcs = [];
$total = 0;
$available = 0;
$used = 0;
$maintenance = 0;

while ($row = $pc_result->fetch_assoc()) {
    $pcs[] = $row;
    $total++;
    if ($row['status'] === 'Available') $available++;
    elseif ($row['status'] === 'Used') $used++;
    elseif ($row['status'] === 'Maintenance') $maintenance++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Management</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fafafa;
            color: white;
        }
        .main-content {
            margin-right: 220px;
            padding: 30px;
            align-items: center;
            width: 90%;

        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #6a0dad;
        }
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #6a0dad;
            padding-bottom: 10px;
        }
        .tabs a {
            text-decoration: none;
            color: #6a0dad;
            padding: 8px 12px;
        }
        .tabs a.active {
            color: #6a0dad;
            border-bottom: 3px solid #6a0dad;
        }
        .summary {
            margin: 10px 0 20px;
            font-size: 14px;
            color: #fafafa;
        }
        .status-circle {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .green { background-color: #22c55e; }
        .red { background-color: #ef4444; }
        .yellow { background-color: #eab308; }

        .lab-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #6a0dad;
        }
        .update-bar {
            background: #6a0dad;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .update-bar select, .update-bar button {
            padding: 8px 10px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .update-bar select {
            background-color: #6a0dad;
            color: #e2e8f0;
        }

        .update-bar button {
            background-color: #6a0dad;
            color: white;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
        }

        .pc-card {
            background-color: #6a0dad;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .pc-card i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #6a0dad;
        }

        .pc-card .label {
            font-size: 14px;
            font-weight: bold;
            color: #6a0dad;
        }

        .available { color: #22c55e; }
        .used { color: #ef4444; }
        .maintenance { color: #eab308; }

            .grid-wrapper {
            display: flex;
            justify-content: center;
                    margin-top: 20px;
                }
            .grid {
                width: 100%;
                max-width: 1000px;
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 15px;
            }
        </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="main-content">
        <h2>Computer Lab Management</h2>

        <div class="tabs">
            <?php foreach ($labs as $lab): ?>
                <a class="<?= $lab === $selectedLab ? 'active' : '' ?>" href="?lab=<?= urlencode($lab) ?>"><?= $lab ?></a>
            <?php endforeach; ?>
        </div>

        <div class="lab-title"><?= $selectedLab ?></div>
        <div class="update-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <form method="POST" action="update_all_pcs.php" style="display: flex; align-items: center; gap: 10px;">
        <input type="hidden" name="lab" value="<?= $selectedLab ?>">
        <label style="font-weight: 500;">Update All PCs in <?= $selectedLab ?></label>
        <select name="status">
            <option value="Available">Available</option>
            <option value="Used">Used</option>
            <option value="Maintenance">Maintenance</option>
        </select>
        <button type="submit">Update All</button>
    </form>

        <div class="summary" style="display: flex; align-items: center; gap: 20px; font-size: 14px;">
            <span><span class="status-circle green"></span> Available: <?= $available ?></span>
            <span><span class="status-circle red"></span> In Use: <?= $used ?></span>
            <span><span class="status-circle yellow"></span> Maintenance: <?= $maintenance ?></span>
            <span>Total PCs: <?= $total ?></span>
        </div>
    </div>
            <div class="grid-wrapper">
             <div class="grid">
            <?php foreach ($pcs as $pc): ?>
                <div class="pc-card <?= strtolower($pc['status']) ?>">
                    <i class="fas fa-desktop"></i>
                    <div class="label"><?= htmlspecialchars($pc['pc_name']) ?></div>
                    <div class="<?= strtolower($pc['status']) ?>"><?= $pc['status'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

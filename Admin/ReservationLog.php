<?php
session_start();
include 'connection.php';
include 'navbar.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$status_filter = $_GET['status'] ?? 'All';
$lab_filter = $_GET['lab'] ?? 'All';
$date_filter = $_GET['date'] ?? '';

$query = "SELECT r.*, s.firstname, s.lastname 
          FROM reservations r 
          JOIN student s ON r.user_id = s.idNo 
          WHERE 1=1";

if ($status_filter !== 'All') {
    $query .= " AND r.status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($lab_filter !== 'All') {
    $query .= " AND r.lab = '" . $conn->real_escape_string($lab_filter) . "'";
}
if (!empty($date_filter)) {
    $query .= " AND DATE(r.date) = '" . $conn->real_escape_string($date_filter) . "'";
}
$query .= " ORDER BY r.date DESC";

$result = $conn->query($query);
$labs = ['Lab 517', 'Lab 524', 'Lab 526', 'Lab 528', 'Lab 530', 'Lab 542', 'Lab 544'];
$statuses = ['Approved', 'Pending', 'Declined'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Logs</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: whitesmoke;
        }
        .main-content {
            margin-right: 0600px;
            padding: 30px;
            align-items: center;
            width: 90%;

        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            color: #6a0dad;
            margin-bottom: 30px;
        }
        .filter-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        select, input[type="date"], button {
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
        }
        select, input[type="date"] {
            background-color: #f3eaff;
            color: #1f1f3d;
        }
        button {
            background-color: #6a0dad;
            color: white;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            background-color: #f3eaff;
            border-radius: 10px;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(106, 13, 173, 0.2);
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
        .status-approved {
            color: #22c55e;
            font-weight: bold;
        }
        .status-pending {
            color: #eab308;
            font-weight: bold;
        }
        .status-declined {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="main-content">
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Reservation Logs</h2>
    <form method="GET" class="filter-bar" style="display: flex; align-items: center; gap: 12px;">
        <select name="status">
            <option value="All" <?= $status_filter == 'All' ? 'selected' : '' ?>>All Statuses</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $status_filter == $status ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach; ?>
        </select>

        <select name="lab">
            <option value="All" <?= $lab_filter == 'All' ? 'selected' : '' ?>>All Labs</option>
            <?php foreach ($labs as $lab): ?>
                <option value="<?= $lab ?>" <?= $lab_filter == $lab ? 'selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">
        <button type="submit">Filter</button>
    </form>
</div>
    <table>
        <thead>
        <tr>
            <th>Student</th>
            <th>ID</th>
            <th>Purpose</th>
            <th>Lab</th>
            <th>PC</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['lab']) ?></td>
                <td><?= htmlspecialchars($row['language']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['start_time']) ?></td>
                <td class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
